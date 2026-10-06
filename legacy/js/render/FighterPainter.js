import { SHOOT } from '../config.js';

/** Dessine les personnages : choisit l'image de l'animation selon leur état. */
export class FighterPainter {
    constructor(ctx, sprites, game) {
        this.ctx = ctx;
        this.sprites = sprites;
        this.game = game;
    }

    draw(f) {
        if (!this.#visible(f)) return;

        const sheet = this.#sheetOf(f);
        const frame = this.#frameOf(f, sheet.anims);
        const winding = ['charge-wind', 'lunge-wind', 'kamikaze'].includes(f.state);
        const golden = this.game.isSuper(f) && !this.sprites.fighters[`${f.type}-super`];
        const flashing = (f.state === 'hurt' && f.t < 5) || (winding && Math.floor(f.t / 4) % 2) || (golden && f.anim % 8 < 2);
        const image = flashing ? frame.flash : frame.normal;
        const { ctx } = this;

        let sink = 0;
        if (f.state === 'burrow') sink = f.t < 20 ? f.t * 2 : Math.max(0, (90 - f.t) * 2);

        ctx.save();
        ctx.translate(Math.round(f.x), Math.round(f.y - (f.z ?? 0)));
        if (sink) {
            // la carotte s'enfonce : on ne dessine que ce qui dépasse du sol
            ctx.beginPath();
            ctx.rect(-80, -200, 160, 200);
            ctx.clip();
            ctx.translate(0, sink * f.scale);
        }
        ctx.scale(f.dir < 0 ? -f.scale : f.scale, f.scale);
        if (f.state === 'dead' && !sheet.dog) {
            const fall = Math.min(1, f.t / 16); // tombe à la renverse
            ctx.translate(0, -6 * fall);
            ctx.rotate(-fall * Math.PI / 2);
        }
        ctx.drawImage(image, -sheet.anchor.x, -sheet.anchor.y);
        ctx.restore();

        if (this.game.isSuper(f)) this.#aura(f);
        this.#speedLines(f);
    }

    /** Super Boulette : sa propre planche dorée et sa cape. */
    #sheetOf(f) {
        const fighters = this.sprites.fighters;
        if (this.game.isSuper(f) && fighters[`${f.type}-super`]) return fighters[`${f.type}-super`];
        return fighters[f.type];
    }

    /** Clignotements : légume qui disparaît, invulnérabilité, sous terre. */
    #visible(f) {
        if (f.state === 'dead' && f.t > 50 && Math.floor(f.t / 4) % 2) return false;
        if (f.state === 'burrow' && f.t >= 20 && f.t <= 70) return false;
        return !(f.invuln && !['hurt', 'dead', 'skate'].includes(f.state) && Math.floor(f.invuln / 3) % 2);
    }

    #frameOf(f, anims) {
        const timing = f.attackTiming;
        const pick = (list, i) => list[Math.floor(i) % list.length];

        switch (f.state) {
            case 'walk':
                return pick(anims.walk, f.anim / 7);
            case 'attack':
                return anims.attack[f.t >= timing.hitFrom && f.t < timing.strikeUntil ? 1 : 0];
            case 'shoot':
                return (anims.shoot ?? anims.attack)[f.t >= SHOOT.fireAt && f.t < SHOOT.fireAt + 6 ? 1 : 0];
            case 'skate':
                return anims.skate ? pick(anims.skate, f.anim / 4) : anims.attack[1];
            case 'jump':
                return f.t > 5 ? anims.kick[0] : anims.jump[0];
            case 'happy':
                return pick(anims.happy ?? anims.idle, f.anim / 12);
            case 'lunge':
            case 'charge':
            case 'kamikaze':
                return pick(anims.walk, f.anim / 3);
            case 'lunge-wind':
            case 'charge-wind':
            case 'summon':
            case 'burrow':
                return anims.attack[0];
            case 'throw':
                return anims.attack[f.t >= 18 ? 1 : 0];
            case 'dead':
                return anims.dead[0];
            case 'hurt':
                return (anims.hurt ?? anims.idle)[0];
            default:
                return pick(anims.idle, f.anim / 30);
        }
    }

    /** Étincelles dorées autour de Super Boulette. */
    #aura(f) {
        const { ctx } = this;
        for (let i = 0; i < 3; i++) {
            const angle = f.anim / 8 + (i * Math.PI * 2) / 3;
            const x = Math.round(f.x + Math.cos(angle) * 20 * f.scale);
            const y = Math.round(f.y - f.z - 14 * f.scale + Math.sin(angle) * 10);
            ctx.fillStyle = f.anim % 6 < 3 ? '#fff4a8' : '#ffd23f';
            ctx.fillRect(x, y, 2, 2);
            ctx.fillRect(x - 1, y + 1, 4, 0.5);
        }
    }

    /** Traînées de la morsure, du prout turbo et des ruées. */
    #speedLines(f) {
        const timing = f.attackTiming;
        const biting = f.isHero && f.state === 'attack' && f.t >= timing.hitFrom && f.t <= timing.hitTo + 2;
        const rushing = ['skate', 'charge', 'lunge', 'kamikaze'].includes(f.state);
        if (!biting && !rushing) return;

        this.ctx.fillStyle = 'rgba(255,255,255,0.8)';
        const x = Math.round(f.x);
        const y = Math.round(f.y - f.z);
        for (const [offset, length] of [[-18, 14], [-13, 20], [-8, 10]]) {
            const start = biting
                ? (f.dir > 0 ? x + 26 : x - 26 - length)
                : (f.dir > 0 ? x - 22 * f.scale - length : x + 22 * f.scale);
            this.ctx.fillRect(start, y + offset * (biting ? 1 : f.scale * 0.8), length, 1);
        }
    }
}
