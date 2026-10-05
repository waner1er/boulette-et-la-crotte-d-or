import { pick } from '../../util/math.js';
import { Director } from './Director.js';

/** Intro : le Professeur Navet brandit la Crotte d'Or ; ses rayons transforment les légumes en mutants. */
export class NavetDirector extends Director {
    static MUTANTS = ['courgette', 'brocoli', 'carotte', 'tomate', 'petitpois', 'oignon'];

    enter() {
        this.setStage(20);
        this.navet = this.cast.add('navet', 160, 156, { dir: -1, scale: 2.5 });
        this.crotte = this.cast.prop('crotte', 160, 54, { scale: 2, glow: true });
        this.spawned = 0;
    }

    update() {
        const { game, state } = this;
        this.crotte.y = 54 + Math.round(Math.sin(this.t / 15) * 3);
        if (this.t % 70 === 35 && this.spawned < 6) {
            const side = this.spawned % 2 ? -1 : 1;
            const x = 160 + side * (50 + Math.floor(this.spawned / 2) * 34);
            const veggie = this.cast.add(NavetDirector.MUTANTS[this.spawned], x, 150 + (this.spawned % 3) * 8, { dir: -side });
            veggie.setState('attack');
            state.flash = 5;
            game.particles.splash(x, veggie.y, '#7aff3a');
            game.sfx('nugget');
            state.sparks.push({ x, y: veggie.y - 30, t: 0 });
            this.spawned++;
        }
        if (this.t % 90 === 0) {
            this.navet.setState('attack');
            this.navet.t = 0;
            game.shout(pick(['MOUAHAHA !', 'MUTEZ, MES PETITS !', 'HÉ HÉ HÉ !']), 160, 60, '#7aff3a');
        }
    }
}
