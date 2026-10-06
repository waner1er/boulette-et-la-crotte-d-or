import { ATTACK, JUMP, PROUT, SHOOT } from '../config.js';
import { clamp } from '../util/math.js';

/**
 * Chaque chien obéit à ses commandes : marcher, lance-baballe (ESPACE), morsure (V), saut (B),
 * prout turbo (C). Avec un nugget dans le ventre, Boulette devient SUPER BOULETTE et déchiquette tout.
 */
export class PlayerController {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    update(p) {
        const { state } = this;
        p.anim++;
        p.invuln = Math.max(0, p.invuln - 1);
        p.proutCooldown = Math.max(0, p.proutCooldown - 1);
        p.shotCooldown = Math.max(0, p.shotCooldown - 1);
        if (p.superUntil && state.tick >= p.superUntil) this.#powerDown(p);

        if (p.z > 0 && (p.isDown || p.state === 'hurt')) p.z = Math.max(0, p.z - 2); // touché en l'air : il retombe

        switch (p.state) {
            case 'dead': return this.#dying(p);
            case 'jump': return this.#pounce(p);
            case 'shoot': return this.#shooting(p);
            case 'hurt': return this.#reeling(p);
            case 'attack': return this.#biting(p);
            case 'skate': return this.#prout(p);
            default: return this.#control(p);
        }
    }

    keepOnScreen(p) {
        const { cam } = this.state;
        const { width, floor } = this.game.data;
        p.x = clamp(p.x, cam + 14, cam + width - 14);
        p.y = clamp(p.y, floor.min, floor.max);
    }

    #control(p) {
        const { game } = this;
        const input = game.inputOf(p);

        if (input.wasPressed('Space') && p.shotCooldown === 0) {
            p.setState('shoot');
            return;
        }
        if (input.wasPressed('KeyV')) {
            p.setState('attack');
            p.hits.clear();
            return;
        }
        if (input.wasPressed('KeyB')) {
            p.setState('jump');
            p.vz = JUMP.impulse;
            p.z = 0.1;
            p.hits.clear();
            game.sfx('jump');
            return;
        }
        if (input.wasPressed('KeyC') && p.proutCooldown === 0) {
            p.setState('skate');
            p.hits.clear();
            p.invuln = Math.max(p.invuln, PROUT.duration);
            game.sfx('prout');
            return;
        }

        const dx = input.axisX;
        const dy = input.axisY;
        const speed = game.isSuper(p) ? 1.6 : 1.3;
        if (dx || dy) {
            p.setState('walk');
            p.x += dx * speed;
            p.y += dy * 0.85;
            if (dx) p.dir = dx;
        } else {
            p.setState('idle');
        }
        this.keepOnScreen(p);
        game.pickups.collect(p);
    }

    /** Lance-baballe (ou frites pour Saucisse) ; en Super Boulette, ça mitraille plus fort. */
    #shooting(p) {
        const { game, state } = this;
        p.t++;
        if (p.t === SHOOT.fireAt) {
            const attack = game.data.attacks[game.isSuper(p) ? 'superBall' : 'ball'];
            const sprite = game.data.heroes[p.type].projectile;
            game.projectiles.fire(p, sprite, attack, p.goldUntil > state.tick);
            p.shotCooldown = attack.cooldown;
            game.sfx(sprite === 'fries' ? 'fries' : 'shoot');
        }
        if (p.t >= SHOOT.duration) p.setState('idle');
    }

    /** Morsure ; Super Boulette déchiquette : plus loin, plus fort, et les légumes partent en lambeaux. */
    #biting(p) {
        const { game } = this;
        const strong = game.isSuper(p);
        p.t++;
        if (p.t === ATTACK.hero.hitFrom) game.sfx(strong ? 'shred' : 'chomp');
        if (p.t >= ATTACK.hero.hitFrom && p.t <= ATTACK.hero.hitTo) {
            const bite = game.data.attacks[strong ? 'superBite' : 'bite'];
            game.combat.heroHits(p, 2, bite.reach, bite.knockback, bite.damage);
        }
        if (p.t >= ATTACK.hero.end) p.setState('idle');
    }

    /** Saut : on bondit en avant, gueule ouverte, et on retombe sur les légumes. */
    #pounce(p) {
        p.t++;
        p.z += p.vz;
        p.vz -= JUMP.gravity;
        p.x += p.dir * JUMP.speed + this.game.inputOf(p).axisX * 0.4;
        if (p.t > 5) this.game.combat.heroHits(p, -6, 30, 4.5, this.game.isSuper(p) ? 4 : 2);
        if (p.z <= 0 && p.t > 2) {
            p.z = 0;
            p.setState('idle');
            this.game.sfx('land');
        }
        this.keepOnScreen(p);
    }

    /** Prout turbo : propulsion au gaz, invincible, renverse tout ce qu'on traverse. */
    #prout(p) {
        const { game } = this;
        p.t++;
        p.x += p.dir * PROUT.speed * (p.t < PROUT.duration - 8 ? 1 : 0.5);
        p.y += game.inputOf(p).axisY * 0.6;
        game.combat.heroHits(p, -6, 30, 3.6, 1);
        if (p.t % 3 === 0) game.particles.cloud(p.x - p.dir * 20, p.y - 10, p.t % 6 ? '#b4e86a' : '#d8ff8a', 0.1, 4);
        this.keepOnScreen(p);
        if (p.t >= PROUT.duration) {
            p.setState('idle');
            p.proutCooldown = PROUT.cooldown;
        }
    }

    #dying(p) {
        p.t++;
        p.x += p.vx;
        p.vx *= 0.9;
        if (p.t > 120) this.game.campaign.respawn(p);
    }

    #reeling(p) {
        p.t++;
        p.x += p.vx;
        p.vx *= 0.85;
        if (p.t > 16) p.setState('idle');
        this.keepOnScreen(p);
    }

    /** Fin du nugget : retour à la normale, et à la musique du niveau (ou du boss). */
    #powerDown(p) {
        const { game, state } = this;
        p.superUntil = 0;
        game.shout('PLUS DE NUGGET...', p.x, p.y - 44, '#ffffff');
        if (state.players.some((other) => game.isSuper(other))) return;
        const bossAlive = state.boss && !state.boss.isDown;
        if (state.mode === 'playing') game.audio.playMusic(bossAlive ? 'boss' : state.level.music);
    }
}
