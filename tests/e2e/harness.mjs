/**
 * Lance le jeu dans Chrome headless, servi par « php -S », avec une horloge virtuelle :
 * on avance image par image bien plus vite que le temps réel. Le son reste coupé : aucun geste réel du joueur ne débloque l'AudioContext.
 *
 * Chrome : CHROME_PATH, sinon /usr/bin/google-chrome.
 */
import { spawn } from 'node:child_process';
import { once } from 'node:events';
import { createServer } from 'node:net';
import { fileURLToPath } from 'node:url';
import { chromium } from 'playwright-core';

const ROOT = fileURLToPath(new URL('../..', import.meta.url));

async function freePort() {
    const server = createServer().listen(0);
    await once(server, 'listening');
    const { port } = server.address();
    server.close();
    return port;
}

/** Remplace requestAnimationFrame et performance.now par une horloge pilotée par le test. */
function installVirtualClock() {
    let now = 0;
    let queue = [];
    performance.now = () => now;
    window.requestAnimationFrame = (cb) => queue.push(cb);
    window.__runFrames = (frames) => {
        for (let i = 0; i < frames; i++) {
            now += 1000 / 60;
            const callbacks = queue;
            queue = [];
            callbacks.forEach((cb) => cb(now));
        }
    };
}

export async function openGame(path = 'index.php?debug') {
    const port = await freePort();
    const server = spawn('php', ['-S', `127.0.0.1:${port}`, '-t', ROOT], { stdio: 'ignore' });
    await new Promise((resolve) => setTimeout(resolve, 500));

    const browser = await chromium.launch({ executablePath: process.env.CHROME_PATH ?? '/usr/bin/google-chrome' });
    const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
    const errors = [];
    page.on('pageerror', (e) => errors.push(`${e.message}\n${e.stack}`));
    page.on('console', (m) => {
        if (m.type() === 'error' && !m.text().includes('Failed to load resource')) errors.push(m.text());
    });
    await page.addInitScript(installVirtualClock);
    await page.goto(`http://127.0.0.1:${port}/${path}`, { timeout: 120000 });
    await page.waitForFunction(() => window.game, null, { timeout: 30000 });

    const game = {
        page,
        errors,
        /** Avance de N images (60 = une seconde de jeu). */
        async run(frames) {
            await page.evaluate((n) => window.__runFrames(n), frames);
            await page.waitForTimeout(10); // laisse arriver les fetch() des décors
        },
        async press(key, frames = 20) {
            await page.keyboard.press(key);
            await game.run(frames);
        },
        /** Maintient une touche pendant N images. */
        async hold(key, frames) {
            await page.keyboard.down(key);
            await game.run(frames);
            await page.keyboard.up(key);
        },
        /** Position et état de chaque héros en jeu. */
        players: () => page.evaluate(() => window.game.state.players.map((p) => ({
            type: p.type, slot: p.slot, x: p.x, y: p.y, state: p.state,
        }))),
        state: () => page.evaluate(() => {
            const s = window.game.state;
            return { mode: s.mode, level: s.levelIndex, selected: s.selected, lives: s.lives, score: s.score };
        }),
        /** Avance jusqu'à ce que predicate(état) soit vrai ; renvoie l'état, ou null après maxFrames. */
        async until(predicate, maxFrames) {
            for (let frames = 0; frames < maxFrames; frames += 120) {
                await game.run(120);
                const state = await game.state();
                if (predicate(state)) return state;
            }
            return null;
        },
        async close() {
            await browser.close();
            server.kill();
        },
    };
    await game.run(30);
    return game;
}

/**
 * Un petit pilote automatique : avance, tire, mord, prout de temps en temps, et suit les légumes.
 * Assez bon pour finir un niveau (les tests le rendent invincible pour aller vite).
 */
export async function autopilot(game, frames) {
    await game.page.evaluate((total) => {
        const g = window.game;
        let left = total;
        const pilot = () => {
            if (left-- <= 0 || g.state.mode !== 'playing') return false;
            const p = g.state.players[0];
            if (!p) return true;
            const target = g.state.enemies.filter((e) => !e.isDown).sort((a, b) => Math.abs(a.x - p.x) - Math.abs(b.x - p.x))[0];
            const input = g.inputOf(p);
            input.releaseAll();
            if (target) {
                if (Math.abs(target.y - p.y) > 3) input.press(target.y > p.y ? 'ArrowDown' : 'ArrowUp');
                const dx = target.x - p.x;
                if (Math.abs(dx) > 90 || Math.abs(dx) < 16) input.press(dx > 0 ? 'ArrowRight' : 'ArrowLeft');
                else p.dir = Math.sign(dx);
                if (g.state.tick % 8 === 0) input.press(Math.abs(dx) < 34 ? 'KeyV' : 'Space');
            } else {
                input.press('ArrowRight');
                if (g.state.tick % 20 === 0) input.press('Space');
            }
            return true;
        };
        window.__pilot = pilot;
    }, frames);
    for (let done = 0; done < frames; done += 30) {
        await game.page.evaluate(() => {
            for (let i = 0; i < 30; i++) {
                window.__pilot();
                window.__runFrames(1);
            }
        });
        const mode = await game.page.evaluate(() => window.game.state.mode);
        if (mode !== 'playing') return mode;
    }
    return 'playing';
}
