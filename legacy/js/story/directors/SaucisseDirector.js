import { Director } from './Director.js';

/** Intro : au comptoir du MÉGA MIAM, Saucisse attend Boulette avec la boîte menu enfant. */
export class SaucisseDirector extends Director {
    enter() {
        this.setStage(7);
        this.saucisse = this.cast.add('saucisse', 230, 160, { dir: -1, scale: 2 });
        this.cast.add('boulette', -30, 162, { targetX: 110, face: 1, scale: 2, speed: 1.3 });
        this.gift = this.cast.prop('gift', 190, 162, { scale: 2 });
    }

    /** Deuxième étape : la boîte s'ouvre, le lance-baballe en sort. */
    beat(step) {
        if (!step.shout) return;
        this.gift.sprite = 'ball';
        this.gift.vz = 3;
        this.gift.z = 1;
        this.saucisse.setState('happy');
        this.game.particles.confettiBurst(190, 130);
        this.game.sfx('gift');
    }
}
