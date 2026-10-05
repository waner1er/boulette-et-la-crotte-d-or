import { Director } from './Director.js';

/** Intro : Boulette arrive sur le parking, la langue pendante. */
export class BouletteDirector extends Director {
    enter() {
        this.setStage(1);
        this.cast.add('boulette', -40, 160, { targetX: 150, face: 1, scale: 2, speed: 1.2, rest: 'happy' });
        this.cast.prop('ball', 230, 164, { scale: 2 });
    }

    update() {
        const [boulette] = this.state.cast;
        if (this.t === 150) this.game.shout('OUAF !', boulette.x, boulette.y - 80, '#ffffff');
        if (this.t === 150) this.game.sfx('bark');
    }
}
