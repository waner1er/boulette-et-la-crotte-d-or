import { BONE_EVERY_KILLS, NUGGET_DROP } from '../config.js';
import { pick } from '../util/math.js';

/** Les coups : ceux des chiens, ceux qu'ils encaissent, et les légumes qui se décomposent. */
export class Combat {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /**
     * Le héros mord devant lui entre minReach et maxReach : légumes et boîtes menu enfant.
     * Chaque cible n'est touchée qu'une fois par attaque (p.hits).
     */
    heroHits(p, minReach, maxReach, knockback, damage) {
        const { state } = this;
        for (const e of state.enemies) {
            if (e.isUntouchable || p.hits.has(e.id)) continue;
            const bulk = (e.scale - 1) * 12; // les gros boss sont plus faciles à toucher
            const reach = (e.x - p.x) * p.dir;
            if (reach < minReach - bulk || reach > maxReach + bulk || Math.abs(e.y - p.y) > 8 + bulk / 4) continue;
            p.hits.add(e.id);
            this.damageEnemy(e, damage, p.dir, knockback, e.x - p.dir * 6 * e.scale, e.y - 20 * e.scale);
        }
        for (const box of state.gifts) {
            const reach = (box.x - p.x) * p.dir;
            if (!box.open && box.z === 0 && reach >= minReach - 4 && reach <= maxReach && Math.abs(box.y - p.y) < 12) {
                this.game.gifts.open(box);
            }
        }
    }

    /** Un légume encaisse un coup (morsure, baballe, frites...). */
    damageEnemy(e, damage, dir, knockback, sparkX, sparkY) {
        const { state, game } = this;
        e.hp -= damage;
        state.score += 10 * damage;
        state.shake = Math.max(state.shake, 2);
        state.target(e);
        state.sparks.push({ x: sparkX, y: sparkY, t: 0 });

        if (e.hp <= 0) {
            this.killEnemy(e, dir);
        } else if (e.boss && ['charge', 'kamikaze', 'burrow'].includes(e.state)) {
            game.sfx('heavyHit'); // le boss encaisse sans s'arrêter
        } else {
            e.dir = -dir;
            e.setState('hurt');
            e.vx = dir * (e.boss ? knockback * 0.4 : knockback);
            game.sfx(e.boss ? 'heavyHit' : 'hit');
        }
    }

    /** Le légume se décompose en morceaux. Un boss vaincu emporte tous ses sbires. */
    killEnemy(e, fromDir) {
        const { game, state } = this;
        e.dir = -fromDir;
        e.setState('dead');
        e.vx = fromDir * 2.4;
        state.score += e.boss ? 5000 * (e.cfg.zoneBoss ? 2 : 1) : e.cfg.score;
        game.particles.decompose(e);
        game.sfx('splat');

        if (e.boss) {
            game.sfx('bossDeath', e.scale);
            state.flash = 12;
            state.shake = 8;
            game.shout('DÉCOMPOSÉ !', e.x, e.y - 50 * e.scale, '#ffd23f');
            for (const other of state.enemies) {
                if (other !== e && !other.isDown) this.killEnemy(other, other.x < e.x ? -1 : 1);
            }
            state.projectiles = state.projectiles.filter((shot) => shot.owner === 'hero');
            return;
        }

        game.sfx('death', e.type);
        game.shout(pick(['SPLOTCH !', 'BEURK !', 'COMPOST !', 'POUAH !', 'SCRONCH !']), e.x, e.y - 46);
        this.#loot(e, fromDir);
    }

    /** Le héros p encaisse un coup venu de fromX. Renvoie false s'il l'esquive (invulnérable, en l'air, super...). */
    damagePlayer(p, amount, fromX) {
        const { game, state } = this;
        if (p.invuln || p.isDown || p.z > 10 || game.isSuper(p)) return false;

        const dir = p.x > fromX ? 1 : -1;
        p.hp -= amount;
        p.invuln = 75;
        p.dir = -dir;
        state.shake = 4;
        state.sparks.push({ x: p.x - dir * 4, y: p.y - 18, t: 0 });

        if (p.hp <= 0) {
            p.hp = 0;
            p.goldUntil = 0;
            state.lives[p.slot]--;
            p.setState('dead');
            p.vx = dir * 2;
            game.sfx('heavyHit');
            game.sfx('heroDeath');
        } else {
            p.setState('hurt');
            p.vx = dir * 2.5;
            game.sfx('hurt');
        }
        return true;
    }

    /** Une explosion (tomate kamikaze, fiole) blesse les chiens à portée. */
    blast(x, y, radius, damage) {
        const { game, state } = this;
        game.particles.boom(x, y);
        game.sfx('explosion');
        state.shake = 6;
        for (const p of state.players) {
            if (Math.abs(p.x - x) < radius && Math.abs(p.y - y) < radius / 2) this.damagePlayer(p, damage, x);
        }
    }

    /** Parfois un nugget, parfois des frites ; un os tous les 12 légumes. */
    #loot(e, fromDir) {
        const { pickups } = this.game;
        if (Math.random() < NUGGET_DROP) pickups.spawn('nugget', e.x, e.y, { fly: true, dir: fromDir });
        else if (Math.random() < 0.1) pickups.spawn('fries', e.x, e.y, { fly: true, dir: fromDir });

        this.state.kills++;
        if (this.state.kills % BONE_EVERY_KILLS === 0) pickups.spawn('bone', e.x, e.y, { fly: true });
    }
}
