import { PICKUP_LIFETIME } from '../config.js';
import { clamp, pick } from '../util/math.js';

/**
 * Les bonus au sol (config/game.php → pickups) : nugget (Super Boulette), os et frites (soin),
 * jouet (une vie), baballes d'or (tirs perçants).
 */
export class Pickups {
    constructor(game) {
        this.game = game;
        this.state = game.state;
        this.floor = game.data.floor;
    }

    /** Pose un bonus ; avec fly, il est d'abord projeté en l'air en tournoyant. */
    spawn(kind, x, y, { fly = false, dir = pick([-1, 1]) } = {}) {
        this.state.pickups.push({
            kind, x, y: clamp(y, this.floor.min, this.floor.max), t: 0,
            z: fly ? 14 : 0, vz: fly ? 2.6 : 0, vx: fly ? dir * 1.2 : 0,
        });
    }

    update() {
        this.state.pickups = this.state.pickups.filter((item) => {
            item.t++;
            if (item.z > 0 || item.vz > 0) {
                item.x += item.vx;
                item.z += item.vz;
                item.vz -= 0.18;
                if (item.z <= 0) Object.assign(item, { z: 0, vz: 0, vx: 0 });
            }
            return item.t < PICKUP_LIFETIME;
        });
    }

    /** Le chien ramasse (et mange) ce qui est à ses pattes. */
    collect(p) {
        this.state.pickups = this.state.pickups.filter((item) => {
            if (item.z !== 0 || Math.abs(item.x - p.x) >= 14 || Math.abs(item.y - p.y) >= 7) return true;
            this.#apply(item, p);
            return false;
        });
    }

    #apply(item, p) {
        const { game, state } = this;
        const cfg = game.data.pickups[item.kind];
        const shout = (color) => game.shout(cfg.name, p.x, p.y - 44, color);
        switch (item.kind) {
            case 'nugget':
                p.superUntil = state.tick + cfg.duration;
                p.hp = p.maxHp;
                state.flash = 8;
                shout('#ffd23f');
                game.sfx('nugget');
                game.audio.playMusic('super');
                game.particles.confettiBurst(p.x, p.y - 20);
                return;
            case 'goldball':
                p.goldUntil = state.tick + cfg.duration;
                shout('#ffd23f');
                break;
            case 'toy':
                state.lives[p.slot] += cfg.life;
                shout('#5ad8ff');
                game.sfx('oneup');
                return;
            default:
                p.hp = Math.min(p.maxHp, p.hp + cfg.heal);
                shout('#7dff5a');
        }
        game.sfx('pickup');
    }
}
