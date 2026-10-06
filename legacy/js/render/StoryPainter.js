/** Les objets des scènes animées : la Crotte d'Or sur son socle, qui rayonne ; la boîte menu enfant... */
export class StoryPainter {
    constructor(ctx, brush, sprites) {
        this.ctx = ctx;
        this.brush = brush;
        this.sprites = sprites;
    }

    prop(prop) {
        const { ctx } = this;
        const image = this.sprites.items[prop.sprite];
        if (!image) return;
        const w = image.width * prop.scale;
        const h = image.height * prop.scale;
        const y = prop.y - (prop.z ?? 0);
        if (prop.pedestal) this.#pedestal(prop.x, prop.y);
        if (prop.glow) this.#rays(prop.x, y - h / 2, prop.t, w);
        this.brush.shadow(prop.x, prop.y, Math.round(w / 2));
        ctx.drawImage(image, Math.round(prop.x - w / 2), Math.round(y - h), w, h);
    }

    /** Rayons dorés qui tournent derrière un trésor. */
    #rays(x, y, t, size) {
        const { ctx } = this;
        ctx.save();
        ctx.translate(Math.round(x), Math.round(y));
        ctx.rotate(t / 90);
        ctx.fillStyle = 'rgba(255, 230, 120, 0.25)';
        for (let i = 0; i < 8; i++) {
            ctx.rotate(Math.PI / 4);
            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.lineTo(size * 1.6, -size * 0.25);
            ctx.lineTo(size * 1.6, size * 0.25);
            ctx.fill();
        }
        ctx.restore();
    }

    #pedestal(x, y) {
        const { ctx } = this;
        ctx.fillStyle = '#0c0610';
        ctx.fillRect(x - 27, y - 1, 54, 22);
        ctx.fillStyle = '#c8b8d8';
        ctx.fillRect(x - 26, y, 52, 20);
        ctx.fillStyle = '#8a7a9a';
        ctx.fillRect(x - 26, y + 14, 52, 6);
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(x - 24, y + 2, 2, 10);
    }
}
