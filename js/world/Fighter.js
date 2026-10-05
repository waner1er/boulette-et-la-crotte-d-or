import { ATTACK, HEROES } from '../config.js';
import { rand } from '../util/math.js';

let nextId = 1;

/**
 * Un personnage qui se bat : un chien, un légume, un boss, un figurant des scènes animées.
 *
 * state : idle | walk | attack | hurt | dead
 *         shoot | jump | skate (le prout turbo) | happy (héros)
 *         lunge-wind | lunge | throw | kamikaze (légumes), charge-wind | charge | burrow | summon (boss)
 * t : images écoulées dans l'état courant ; anim : compteur d'animation.
 */
export class Fighter {
    constructor(type, x, y, cfg) {
        this.id = nextId++;
        this.type = type;
        this.cfg = cfg;
        this.x = x;
        this.y = y;
        this.z = 0;
        this.vx = 0;
        this.vz = 0;
        this.dir = 1;
        this.slot = 0;
        this.scale = cfg.scale ?? 1;

        this.state = 'idle';
        this.t = 0;
        this.anim = 0;

        this.hp = cfg.hp;
        this.maxHp = cfg.hp;
        this.invuln = 0;
        this.boss = false;
        this.hits = new Set();
        this.landed = false;
        this.cooldown = rand(30, 90);
        this.special = cfg.every ?? 0;

        // héros
        this.proutCooldown = 0;
        this.shotCooldown = 0;
        /** Super Boulette (nugget) et baballes d'or : jusqu'à quelle image. */
        this.superUntil = 0;
        this.goldUntil = 0;
    }

    setState(name) {
        if (this.state === name) return;
        this.state = name;
        this.t = 0;
    }

    get attackTiming() {
        if (this.isHero) return ATTACK.hero;
        return this.boss ? ATTACK.boss : ATTACK.enemy;
    }

    get isHero() {
        return HEROES.includes(this.type);
    }

    get isDown() {
        return this.state === 'dead';
    }

    /** Hors combat : au tapis, ou sous terre (la carotte qui creuse). */
    get isUntouchable() {
        return this.state === 'dead' || (this.state === 'burrow' && this.t > 20 && this.t < 70);
    }

    faceTowards(x) {
        this.dir = x > this.x ? 1 : -1;
    }
}
