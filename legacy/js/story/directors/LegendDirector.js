import { Director } from './Director.js';

/** Intro : la Crotte d'Or, seule sur son socle, qui scintille dans la pénombre du sous-sol. */
export class LegendDirector extends Director {
    enter() {
        this.setStage(20);
        this.crotte = this.cast.prop('crotte', 160, 150, { scale: 3, glow: true, pedestal: true });
    }

    update() {
        if (this.t % 50 === 0) this.game.particles.confettiBurst(160 + (Math.random() - 0.5) * 40, 100);
    }
}
