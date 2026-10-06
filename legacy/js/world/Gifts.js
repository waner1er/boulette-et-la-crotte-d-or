import { pick, rand } from '../util/math.js';

/**
 * Les boîtes menu enfant : une par niveau. En solo, Saucisse arrive en courant de la cuisine et la dépose ;
 * à deux (Saucisse joue), elle tombe du passe-plat. Une morsure ou une baballe l'ouvre : surprise !
 */
export class Gifts {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** Remise à zéro au début d'un niveau. */
    reset() {
        this.delivered = false;
        this.state.gifts = [];
        this.state.courier = null;
    }

    update() {
        const { game, state } = this;
        const { gift } = state.level;
        const { width, floor } = game.data;

        if (!this.delivered && state.cam + width > gift.x + 30) {
            this.delivered = true;
            const y = floor.min + gift.y;
            if (state.duo) {
                state.gifts.push({ x: gift.x, y, z: 120, vz: 0, open: false, t: 0 });
                game.shout('DING ! COMMANDE PRÊTE !', gift.x, y - 60, '#ffd23f');
                game.sfx('ding');
            } else {
                const courier = game.spawn('saucisse', state.cam + width + 30, y);
                Object.assign(courier, { dir: -1, targetX: gift.x + 16, carrying: true, scale: 1 });
                state.courier = courier;
            }
        }

        this.#courier();
        for (const box of state.gifts) {
            box.t++;
            if (box.z > 0) {
                box.vz -= 0.25;
                box.z = Math.max(0, box.z + box.vz);
                if (box.z === 0) game.sfx('land');
            }
        }
        state.gifts = state.gifts.filter((box) => !box.open || box.t < 40);
    }

    /** Ouvre la boîte : un cadeau tiré au sort jaillit, avec des confettis. */
    open(box) {
        const { game } = this;
        box.open = true;
        box.t = 0;
        game.sfx('gift');
        game.particles.confettiBurst(box.x, box.y - 10);
        game.pickups.spawn(Gifts.#draw(game.data.gifts), box.x, box.y + 2, { fly: true });
        game.shout('SURPRISE !', box.x, box.y - 30, '#ff3ea5');
    }

    /** Saucisse livre la boîte, aboie, et repart en cuisine. */
    #courier() {
        const { game, state } = this;
        const c = state.courier;
        if (!c) return;
        c.anim++;
        c.t++;
        if (c.carrying && Math.abs(c.x - c.targetX) > 2) {
            c.x += Math.sign(c.targetX - c.x) * 2.2;
            c.setState('walk');
            return;
        }
        if (c.carrying) {
            c.carrying = false;
            c.setState('happy');
            state.gifts.push({ x: c.x - 18, y: c.y, z: 0, vz: 0, open: false, t: 0 });
            game.shout(pick(['TIENS BOULETTE !', 'UN MENU ENFANT !', 'CADEAU !']), c.x, c.y - 40, '#ff9a5a');
            game.sfx('bark');
            return;
        }
        if (c.t > 50) {
            c.dir = 1;
            c.setState('walk');
            c.x += 2.6;
            if (c.x > state.cam + game.data.width + 40) state.courier = null;
        }
    }

    static #draw(weights) {
        const total = Object.values(weights).reduce((a, b) => a + b, 0);
        let roll = rand(0, total);
        for (const [kind, weight] of Object.entries(weights)) {
            roll -= weight;
            if (roll < 0) return kind;
        }
        return 'bone';
    }
}
