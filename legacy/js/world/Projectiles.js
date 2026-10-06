/**
 * Tout ce qui vole : baballes et frites des chiens, petits pois, larmes, glaçons et fioles des légumes.
 * Les baballes rebondissent au sol ; une baballe d'or traverse les légumes au lieu de rebondir dessus.
 */
export class Projectiles {
    /** Hauteur (z) jusqu'à laquelle un projectile touche un personnage de taille 1. */
    static BODY = 34;

    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** Un chien tire : baballe (Boulette) ou frites (Saucisse). */
    fire(p, sprite, { damage, speed }, gold) {
        this.state.projectiles.push({
            sprite: gold && sprite === 'ball' ? 'goldball' : sprite, owner: 'hero', from: p.id,
            x: p.x + p.dir * 18, y: p.y, z: 20, vx: p.dir * speed, vy: 0, vz: 0.5, gravity: 0.1,
            damage, pierce: gold, bounces: sprite === 'ball' ? 2 : 0, live: true, spin: true, t: 0, hits: new Set(),
        });
    }

    update() {
        const { state } = this;
        state.projectiles = state.projectiles.filter((shot) => {
            shot.x += shot.vx;
            shot.y += shot.vy;
            shot.z += shot.vz;
            shot.vz -= shot.gravity ?? 0.08;
            shot.t++;

            if (shot.owner === 'hero') {
                if (shot.live) this.#hitVeggies(shot);
            } else if (this.#hitDogs(shot)) {
                return false;
            }

            if (shot.z > 0) return shot.t < 240 && Math.abs(shot.x - state.cam - 160) < 260;
            return this.#land(shot);
        });
    }

    #hitVeggies(shot) {
        const { game, state } = this;
        for (const e of state.enemies) {
            if (e.isUntouchable || shot.hits.has(e.id)) continue;
            const size = Math.max(1, e.scale);
            if (Math.abs(e.x - shot.x) > 8 * size || Math.abs(e.y - shot.y) > 7 + size * 2 || shot.z > Projectiles.BODY * size) continue;

            shot.hits.add(e.id);
            const dir = Math.sign(shot.vx) || 1;
            game.combat.damageEnemy(e, shot.damage, dir, 1.8, shot.x, shot.y - shot.z);
            if (!shot.pierce) {
                // la baballe ricoche et retombe, elle ne fait plus mal
                Object.assign(shot, { live: false, vx: -shot.vx * 0.35, vz: 1.8 });
                game.sfx('boing');
                return;
            }
        }
        for (const box of state.gifts) {
            if (!box.open && box.z === 0 && Math.abs(box.x - shot.x) < 9 && Math.abs(box.y - shot.y) < 8) {
                game.gifts.open(box);
                shot.live = false;
            }
        }
    }

    #hitDogs(shot) {
        const { game, state } = this;
        const hits = (p) => Math.abs(shot.z - p.z) < 26 && Math.abs(p.x - shot.x) < 12 && Math.abs(p.y - shot.y) < 7;
        return state.players.some((p) => hits(p) && game.combat.damagePlayer(p, shot.damage, shot.x - shot.vx * 10));
    }

    /** Au sol : la baballe rebondit, la fiole explose, le reste s'écrase. */
    #land(shot) {
        const { game, state } = this;
        if (shot.bounces > 0) {
            shot.bounces--;
            shot.z = 0.1;
            shot.vz = Math.abs(shot.vz) * 0.6 + 0.6;
            shot.vx *= 0.85;
            if (shot.live) game.sfx('boing');
            return true;
        }
        if (shot.sprite === 'flask') {
            game.combat.blast(shot.x, shot.y, 34, shot.damage);
            game.particles.splash(shot.x, shot.y, '#7aff3a');
        } else if (shot.sprite === 'ice' || shot.sprite === 'tear') {
            game.particles.splash(shot.x, shot.y, shot.sprite === 'ice' ? '#d8f4ff' : '#5ad8ff');
            game.sfx(shot.sprite);
        } else if (shot.owner !== 'hero') {
            state.sparks.push({ x: shot.x, y: shot.y - 4, t: 0 });
        }
        return false;
    }
}
