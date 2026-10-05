import { clamp, rand } from '../util/math.js';

/**
 * La caméra avance avec les chiens (à deux, sans laisser le dernier hors champ), seulement vers la droite.
 * À chaque déclencheur, elle se bloque le temps d'une vague de légumes ; la dernière vague est le boss.
 */
export class Camera {
    /** Le héros reste à cette distance du bord gauche quand la caméra le suit. */
    static LEAD = 130;

    constructor(game) {
        this.game = game;
        this.state = game.state;
        this.width = game.data.width;
    }

    update() {
        const { game, state } = this;
        const wave = state.level.waves[state.waveIndex];

        if (!state.locked && state.players.length) {
            const xs = state.players.map((p) => p.x);
            const lead = Math.min(Math.max(...xs) - Camera.LEAD, Math.min(...xs) - 10);
            const target = clamp(lead, 0, game.data.levelLength - this.width);
            state.cam = Math.max(state.cam, target);
            if (wave) state.cam = Math.min(state.cam, wave.at);
        }

        if (!state.locked && wave && state.cam >= wave.at) {
            state.locked = true;
            game.hud.showGo(false);
            this.#spawnWave(wave);
        }

        if (state.locked && state.enemies.every((e) => e.isDown)) {
            state.locked = false;
            state.waveIndex++;
            if (state.waveIndex >= state.level.waves.length) game.campaign.levelClear();
            else game.hud.showGo(true);
        }
    }

    #spawnWave(wave) {
        const { game, state } = this;
        const { floor } = game.data;
        const W = this.width;

        wave.enemies.forEach((type, i) => {
            const fromRight = i % 2 === 0 || state.waveIndex === 0;
            const x = fromRight ? state.cam + W + 16 + i * 16 : state.cam - 16 - i * 16;
            const enemy = game.spawn(type, x, rand(floor.min, floor.max));
            enemy.dir = fromRight ? -1 : 1;
            state.enemies.push(enemy);
        });

        if (wave.boss) {
            this.#spawnBoss(wave.boss);
        } else {
            game.hud.message(`VAGUE ${state.waveIndex + 1}`, 80);
        }
    }

    #spawnBoss(cfg) {
        const { game, state } = this;
        const { floor } = game.data;
        const base = game.data.enemies[cfg.sprite] ?? {};
        const boss = game.spawn(cfg.sprite, state.cam + this.width + 20 * cfg.scale, (floor.min + floor.max) / 2, { ...base, ...cfg });
        boss.boss = true;
        boss.dir = -1;
        boss.special = 180;
        state.enemies.push(boss);
        state.boss = boss;
        state.lastEnemy = boss;
        const title = cfg.zoneBoss ? 'GRAND BOSS !' : 'SOUS-CHEF !';
        game.hud.message(`<span class="hud__warning">${title}</span><br><br>${cfg.name}`, 160);
        game.sfx('warning');
        if (!game.state.players.some((p) => game.isSuper(p))) game.audio.playMusic('boss');
    }
}
