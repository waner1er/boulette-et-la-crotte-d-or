import { Director } from './Director.js';

/** Fin : la fête au coin anniversaire ; les légumes, redevenus gentils, dansent avec les toutous. */
export class PartyDirector extends Director {
    enter() {
        this.setStage(6);
        const dancers = [['courgette', 40], ['carotte', 80], ['saucisse', 130], ['boulette', 190], ['brocoli', 240], ['radis', 280]];
        dancers.forEach(([type, x], i) => {
            const dog = type === 'boulette' || type === 'saucisse';
            const actor = this.cast.add(type, x, 154 + (i % 2) * 10, { dance: true, dir: x < 160 ? 1 : -1, scale: dog ? 1.5 : 1 });
            if (dog) actor.setState('happy');
        });
        this.cast.prop('nugget', 160, 120, { scale: 2, glow: true });
    }

    update() {
        if (this.t % 25 === 0) this.game.particles.confettiBurst(20 + Math.random() * 280, 20);
    }
}
