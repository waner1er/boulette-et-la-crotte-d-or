import { Director } from './Director.js';

/** Fin : le Professeur Navet se décompose, la Crotte d'Or lui échappe et rebondit. */
export class VictoryDirector extends Director {
    enter() {
        this.setStage(20);
        this.navet = this.cast.add('navet', 200, 156, { dir: -1, scale: 2.5 });
        this.navet.setState('hurt');
        this.cast.add('boulette', 80, 160, { scale: 2, rest: 'happy' });
    }

    update() {
        const { game, state } = this;
        this.navet.x = 200 + (this.t % 4 < 2 ? -1 : 1) * Math.min(3, this.t / 30);
        if (this.t % 20 === 0) game.particles.boom(170 + Math.random() * 60, 120 + Math.random() * 30);
        if (this.t === 150) {
            game.particles.decompose(this.navet);
            this.navet.setState('dead');
            state.flash = 14;
            game.sfx('bossDeath', 2.5);
            this.cast.prop('crotte', 200, 160, { scale: 2, glow: true, z: 60, vz: 2, vx: -0.9 });
        }
    }
}
