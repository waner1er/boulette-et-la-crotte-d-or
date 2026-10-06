import { Mode } from './Mode.js';

/** NIVEAU TERMINÉ : les toutous font la fête, puis niveau suivant. */
export class ClearMode extends Mode {
    static DURATION = 240;

    update() {
        const { game, state } = this;
        for (const p of state.players) {
            p.anim++;
            if (!p.isDown) p.setState('happy');
            p.z = Math.abs(Math.sin(state.modeTimer / 8)) * 6;
        }
        for (const e of state.enemies) e.t++;
        if (state.modeTimer % 30 === 0) game.particles.confettiBurst(state.cam + 60 + Math.random() * 200, 60);
        if (state.modeTimer > ClearMode.DURATION) game.campaign.nextLevel();
    }
}
