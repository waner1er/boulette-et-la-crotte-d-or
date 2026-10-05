import { PAPER } from '../config.js';
import { clamp } from '../util/math.js';

/** Morceaux de légumes, nuages, taches, confettis, étoiles d'impact et textes qui s'envolent. */
export class EffectPainter {
    constructor(ctx, brush, state, width) {
        this.ctx = ctx;
        this.brush = brush;
        this.state = state;
        this.width = width;
    }

    /** Tache de compost au sol, qui pâlit avec le temps. */
    splat(s) {
        const { ctx } = this;
        ctx.globalAlpha = Math.max(0, 0.6 - s.t / 700);
        ctx.fillStyle = s.color;
        const x = Math.round(s.x);
        const y = Math.round(s.y);
        const w = Math.round(s.w);
        ctx.fillRect(x - w, y - 1, w * 2, 3);
        ctx.fillRect(x - w + 3, y - 2, w * 2 - 6, 5);
        ctx.globalAlpha = 1;
    }

    bit(b) {
        if (b.t > 90 && b.t % 4 < 2) return;
        this.ctx.fillStyle = b.color;
        this.ctx.fillRect(Math.round(b.x), Math.round(b.y - b.z), b.size, b.size);
    }

    /** Nuage rond détouré de noir, qui se dissipe en tramé. */
    cloud(c) {
        if (c.t > 35 && c.t % 3 === 0) return;
        const r = Math.round(c.r);
        this.brush.disc(c.x, c.y, r + 1, 'rgba(12,6,16,0.6)');
        this.brush.disc(c.x, c.y, r, c.color);
        this.ctx.fillStyle = 'rgba(255,255,255,0.6)';
        this.ctx.fillRect(Math.round(c.x - r / 2), Math.round(c.y - r / 2), 1, 1);
    }

    confetti(c) {
        this.ctx.fillStyle = c.color;
        this.ctx.fillRect(Math.round(c.x), Math.round(c.y), c.t % 10 < 5 ? 2 : 1, c.t % 10 < 5 ? 1 : 2);
    }

    /** Étoile d'impact. */
    spark(s) {
        const { ctx } = this;
        const r = 2 + s.t;
        const x = Math.round(s.x);
        const y = Math.round(s.y);
        ctx.fillStyle = s.t % 2 ? '#ffffff' : this.state.level.accent;
        ctx.fillRect(x - r, y, r * 2 + 1, 1);
        ctx.fillRect(x, y - r, 1, r * 2 + 1);
        const d = Math.round(r * 0.6);
        for (const [sx, sy] of [[-1, -1], [1, -1], [-1, 1], [1, 1]]) ctx.fillRect(x + sx * d, y + sy * d, 1, 1);
    }

    /** Bosse de terre de la carotte qui creuse. */
    mound(f) {
        const { ctx } = this;
        const x = Math.round(f.x);
        const y = Math.round(f.y);
        const w = Math.round(8 * f.scale);
        ctx.fillStyle = '#0c0610';
        ctx.fillRect(x - w - 1, y - 4, w * 2 + 2, 5);
        ctx.fillStyle = f.t % 6 < 3 ? '#8a5a2a' : '#6a4018';
        ctx.fillRect(x - w, y - 3, w * 2, 3);
        ctx.fillRect(x - w + 3, y - 5, w * 2 - 6, 2);
    }

    /** Cri ou bonus qui monte, détouré de noir, sans sortir de l'écran. */
    text(t) {
        const { ctx, state } = this;
        ctx.font = '8px "Press Start 2P"';
        ctx.textAlign = 'center';
        ctx.lineWidth = 3;
        ctx.strokeStyle = '#000';
        const half = ctx.measureText(t.text).width / 2;
        const x = clamp(t.x, state.cam + half + 4, state.cam + this.width - half - 4);
        const y = Math.max(this.state.mode === 'story' ? 64 : 40, Math.round(t.y - t.t * 0.3));
        if (t.t > 70 && t.t % 4 < 2) return;
        ctx.strokeText(t.text, x, y);
        ctx.fillStyle = t.color ?? (t.t % 8 < 4 ? state.level.accent : PAPER);
        ctx.fillText(t.text, x, y);
    }
}
