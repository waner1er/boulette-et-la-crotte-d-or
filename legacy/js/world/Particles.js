import { pick, rand } from '../util/math.js';

/** Couleurs de la palette d'un légume qui partent en morceaux (pas le contour ni les gants). */
const FLESH = ['G', 'g', 'D', 'C', 'c', 'T', 't', 'P', 'p', 'A', 'a', 'U', 'u', 'V', 'S'];
const CONFETTI = ['#ff3ea5', '#ffd23f', '#5ad8ff', '#7dff5a', '#ff8a1e', '#ffffff'];

/**
 * Les effets qui vivent un moment puis disparaissent : morceaux de légumes, nuages de prout,
 * taches au sol, confettis, étoiles d'impact, textes qui s'envolent, flocons et gouttes.
 */
export class Particles {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** Un légume se décompose : il éclate en morceaux de sa couleur, qui retombent en compost. */
    decompose(e) {
        const sheet = this.game.data.sprites[e.type];
        const colors = FLESH.map((key) => sheet?.palette[key]).filter(Boolean);
        const count = e.boss ? 40 : 16;
        for (let i = 0; i < count; i++) {
            this.state.bits.push({
                x: e.x + rand(-6, 6) * e.scale, y: e.y + rand(-3, 3), z: rand(8, 30) * e.scale,
                vx: rand(-1.6, 1.6), vz: rand(0.5, 3), color: pick(colors.length ? colors : ['#4cc23a']),
                size: Math.random() < 0.3 ? 2 : 1, t: 0,
            });
        }
        this.state.splats.push({ x: e.x, y: e.y, w: 10 * e.scale, color: pick(colors.length ? colors : ['#4cc23a']), t: 0 });
        // et ça sent pas bon
        for (let i = 0; i < 3; i++) this.cloud(e.x + rand(-8, 8), e.y - rand(10, 30) * e.scale, '#a8c86a', 0.2);
    }

    /** Petit nuage (prout vert, odeur de compost...). */
    cloud(x, y, color = '#a8e86a', vy = 0.15, size = 3) {
        this.state.clouds.push({ x, y, color, vy, r: size, t: 0 });
    }

    /** Explosion de tomate ou de fiole : nuage rouge et morceaux. */
    boom(x, y) {
        for (let i = 0; i < 10; i++) this.cloud(x + rand(-14, 14), y - rand(4, 24), pick(['#ff3a2a', '#ff8a1e', '#ffd23f']), 0.3, 5);
        this.state.flash = Math.max(this.state.flash, 4);
    }

    splash(x, y, color) {
        for (let i = 0; i < 8; i++) {
            this.state.bits.push({ x, y, z: 2, vx: rand(-1.4, 1.4), vz: rand(0.8, 2), color, size: 1, t: 0 });
        }
    }

    confettiBurst(x, y) {
        for (let i = 0; i < 30; i++) {
            this.state.confetti.push({ x, y, vx: rand(-2, 2), vy: rand(-3, -0.5), color: pick(CONFETTI), t: 0 });
        }
    }

    update() {
        const { state } = this;
        const W = this.game.data.width;
        for (const b of state.bits) {
            b.t++;
            if (b.z > 0 || b.vz > 0) {
                b.x += b.vx;
                b.z += b.vz;
                b.vz -= 0.18;
                if (b.z < 0) Object.assign(b, { z: 0, vz: 0 });
            }
        }
        state.bits = state.bits.filter((b) => b.t < 110);
        for (const c of state.clouds) {
            c.t++;
            c.y -= c.vy;
            c.r += 0.04;
        }
        state.clouds = state.clouds.filter((c) => c.t < 50);
        state.splats.forEach((s) => s.t++);
        state.splats = state.splats.filter((s) => s.t < 400);
        for (const c of state.confetti) {
            c.t++;
            c.x += c.vx;
            c.y += c.vy;
            c.vy += 0.08;
            c.vx *= 0.98;
        }
        state.confetti = state.confetti.filter((c) => c.t < 120);
        state.sparks.forEach((s) => s.t++);
        state.sparks = state.sparks.filter((s) => s.t < 8);
        state.texts.forEach((t) => t.t++);
        state.texts = state.texts.filter((t) => t.t < 90);

        // météo : neige qui tombe en zigzag, pluie en diagonale
        const { level } = state;
        for (const w of state.weather) {
            if (level.snow) {
                w.y += 0.4 * w.s;
                w.x += Math.sin((state.tick + w.s * 100) / 30) * 0.3;
            } else if (level.rain) {
                w.y += 4 * w.s;
                w.x -= 1;
            }
            if (w.y > this.game.data.height) w.y -= this.game.data.height;
            if (w.x < 0) w.x += W;
        }
    }
}
