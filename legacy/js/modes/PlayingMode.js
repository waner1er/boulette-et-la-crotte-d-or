import { Mode } from './Mode.js';

/** La partie : les chiens, les légumes (les boss et les plus proches d'abord), les objets, la caméra. */
export class PlayingMode extends Mode {
    /** Un légume décomposé disparaît au bout de 80 images. */
    static CORPSE_TIME = 80;

    update() {
        const { game, state } = this;
        state.players.forEach((p) => game.player.update(p));

        // chaque légume se mesure au chien le plus proche de lui
        const distance = (e) => Math.abs(e.x - (state.nearestPlayer(e.x, e.y)?.x ?? e.x));
        const queue = state.enemies
            .filter((e) => !e.isDown)
            .sort((a, b) => (b.boss - a.boss) || distance(a) - distance(b));
        for (const e of state.enemies) game.enemyAI.update(e, queue.indexOf(e));
        state.enemies = state.enemies.filter((e) => !(e.isDown && e.t > PlayingMode.CORPSE_TIME));

        game.projectiles.update();
        game.pickups.update();
        game.gifts.update();

        if (state.mode === 'playing') game.camera.update();
    }
}
