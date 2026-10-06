import assert from 'node:assert/strict';
import { after, before, test } from 'node:test';
import { openGame } from './harness.mjs';

/** Les 20 niveaux se chargent, et la fin (après le Professeur Navet) se déroule jusqu'au générique. */
let game;
before(async () => {
    game = await openGame();
});
after(() => game?.close());

test('chaque niveau démarre, chaque boss apparaît', async () => {
    const count = await game.page.evaluate(() => window.game.data.levels.length);
    assert.equal(count, 20);
    for (let i = 0; i < count; i++) {
        await game.page.evaluate((n) => window.game.campaign.startLevel(n), i);
        await game.run(240);
        assert.equal((await game.state()).level, i);
        // on saute directement au boss
        await game.page.evaluate(() => {
            const g = window.game;
            g.state.players.forEach((p) => {
                p.invuln = 1e9;
            });
            g.state.enemies = [];
            g.state.locked = false;
            g.state.waveIndex = g.state.level.waves.length - 1;
            g.state.cam = g.state.level.waves.at(-1).at;
            g.state.players.forEach((p) => {
                p.x = g.state.cam + 60;
            });
        });
        await game.run(400);
        const boss = await game.page.evaluate(() => window.game.state.boss?.cfg.name);
        assert.ok(boss, `boss du niveau ${i + 1}`);
    }
    assert.deepEqual(game.errors, []);
});

test('la fin : le Professeur Navet vaincu, la révélation, la fête et le générique', async () => {
    await game.page.evaluate(() => {
        const g = window.game;
        g.combat.killEnemy(g.state.boss, 1);
    });
    await game.run(60);
    assert.ok(await game.until((s) => s.mode === 'story', 20 * 60), 'la fin commence');
    const credits = game.page.locator('[data-hud=credits]');
    for (let i = 0; i < 12 && await credits.isHidden(); i++) await game.press('Enter', 280);
    assert.equal(await credits.isHidden(), false, 'le générique défile');
    await game.run(200);
    await game.press('Enter', 30);
    assert.equal((await game.state()).mode, 'title');
    assert.deepEqual(game.errors, []);
});
