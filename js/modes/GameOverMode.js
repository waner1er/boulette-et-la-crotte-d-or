import { Mode } from './Mode.js';

/** GAME OVER : START pour recommencer le niveau (le score repart à zéro), ▲ pour l'accueil. */
export class GameOverMode extends Mode {
    update() {
        const { game, state } = this;
        for (const e of state.enemies) if (e.isDown) e.t = Math.min(e.t + 1, 60);
        if (this.input.wasPressed('Enter')) {
            state.resetScore();
            game.campaign.startLevel(state.levelIndex);
        } else if (this.input.wasPressed('ArrowUp')) {
            game.goHome();
        }
    }
}
