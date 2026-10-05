import { clamp, pick, rand } from '../util/math.js';

/**
 * Les attaques spéciales des boss (config/levels.php) : charge, lancer, appel de sbires,
 * et « burrow » : la carotte s'enfonce sous terre et ressort sous les pattes du chien.
 */
export class BossAI {
    static START = { charge: 'charge-wind', throw: 'throw', summon: 'summon', burrow: 'burrow' };

    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** Renvoie true si le boss est occupé par son attaque spéciale. */
    update(e) {
        switch (e.state) {
            case 'charge-wind': return this.#windUp(e);
            case 'charge': return this.#charge(e);
            case 'summon': return this.#summon(e);
            case 'burrow': return this.#burrow(e);
        }
        return this.#trigger(e);
    }

    #trigger(e) {
        const p = this.state.nearestPlayer(e.x, e.y);
        e.special--;
        if (e.special > 0 || !p || p.isDown) return false;
        e.special = e.cfg.every;

        // le Professeur Navet alterne fioles et appels de légumes
        const special = e.cfg.summons && Math.random() < 0.35 ? 'summon' : e.cfg.special;
        e.setState(BossAI.START[special]);
        e.landed = false;
        if (special === 'summon' || Math.random() < 0.3) this.game.shout(e.cfg.line, e.x, e.y - 50 * e.scale, '#ffffff');
        return true;
    }

    #windUp(e) {
        e.t++;
        e.faceTowards(this.state.nearestPlayer(e.x, e.y)?.x ?? e.x);
        if (e.t > 34) {
            e.setState('charge');
            this.game.sfx('prout');
        }
        return true;
    }

    /** Fonce à travers l'écran jusqu'au bord. */
    #charge(e) {
        const { state } = this;
        const W = this.game.data.width;
        e.t++;
        e.x += e.dir * 3.4;
        for (const p of state.players) {
            if (Math.abs(p.x - e.x) < 14 * e.scale && Math.abs(p.y - e.y) < 6 + e.scale * 3) {
                this.game.combat.damagePlayer(p, Math.round(e.cfg.damage * 1.4), e.x - e.dir * 10);
            }
        }
        const atEdge = e.x < state.cam + 14 || e.x > state.cam + W - 14;
        if (e.t > 80 || atEdge) {
            e.x = clamp(e.x, state.cam + 14, state.cam + W - 14);
            e.setState('idle');
        }
        return true;
    }

    /** Sous terre : une bosse de terre file vers le chien, puis le boss jaillit dessous. */
    #burrow(e) {
        const { game, state } = this;
        const p = state.nearestPlayer(e.x, e.y);
        e.t++;
        if (e.t === 1) game.sfx('burrow');
        if (e.t > 20 && e.t < 70 && p) {
            e.x += Math.sign(p.x - e.x) * Math.min(3, Math.abs(p.x - e.x));
            e.y += Math.sign(p.y - e.y) * Math.min(1.5, Math.abs(p.y - e.y));
            if (e.t % 4 === 0) game.particles.splash(e.x, e.y, '#8a5a2a');
        }
        if (e.t === 70) {
            game.sfx('heavyHit');
            state.shake = 5;
            for (const dog of state.players) {
                if (Math.abs(dog.x - e.x) < 16 * e.scale && Math.abs(dog.y - e.y) < 10) {
                    game.combat.damagePlayer(dog, Math.round(e.cfg.damage * 1.3), e.x);
                }
            }
        }
        if (e.t > 90) e.setState('idle');
        return true;
    }

    /** Appelle jusqu'à deux sbires, sans dépasser quatre à l'écran. */
    #summon(e) {
        const { game, state } = this;
        const { width: W, floor } = game.data;
        e.t++;
        if (e.t === 20) {
            const minions = state.enemies.filter((m) => !m.boss && !m.isDown).length;
            const pool = state.level.waves.flatMap((w) => w.enemies);
            for (let i = 0; i < Math.min(2, 4 - minions); i++) {
                const fromRight = i === 0;
                const x = fromRight ? state.cam + W + 16 : state.cam - 16;
                const minion = game.spawn(pick(pool.length ? pool : ['courgette']), x, rand(floor.min, floor.max));
                minion.dir = fromRight ? -1 : 1;
                state.enemies.push(minion);
            }
        }
        if (e.t > 40) e.setState('idle');
        return true;
    }
}
