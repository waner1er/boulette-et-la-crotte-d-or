import { Director } from './Director.js';

/** Fin : Boulette renifle la Crotte d'Or... qui était en fait un nugget géant. */
export class TreasureDirector extends Director {
    enter() {
        this.setStage(20);
        this.treasure = this.cast.prop('crotte', 190, 162, { scale: 3, glow: true });
        this.boulette = this.cast.add('boulette', 40, 162, { targetX: 130, face: 1, scale: 2, speed: 0.8 });
    }

    /** Deuxième étape : la révélation. */
    beat(step) {
        if (!step.text.includes('NUGGET')) return;
        const { game, state } = this;
        this.treasure.sprite = 'nugget';
        this.treasure.scale = 4;
        state.flash = 16;
        game.particles.confettiBurst(190, 120);
        game.sfx('nugget');
        this.boulette.setState('happy');
        this.boulette.dance = true;
    }

    update() {
        if (this.t % 60 === 0 && this.boulette.state === 'idle') this.game.sfx('sniff');
    }
}
