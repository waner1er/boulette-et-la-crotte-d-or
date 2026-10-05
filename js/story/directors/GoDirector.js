import { Director } from './Director.js';

/** Intro : Boulette et Saucisse face au parking. Un prout, et c'est parti. */
export class GoDirector extends Director {
    enter() {
        this.setStage(1);
        this.cast.add('saucisse', 100, 166, { scale: 2, rest: 'happy' });
        this.cast.add('boulette', 170, 158, { scale: 2, rest: 'idle' });
        ['courgette', 'brocoli', 'carotte'].forEach((type, i) => this.cast.add(type, 270 + i * 18, 150 + i * 6, { dir: -1 }));
    }
}
