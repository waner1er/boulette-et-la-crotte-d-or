import { PICKUP_LIFETIME } from '../config.js';
import { rand } from '../util/math.js';
import { EffectPainter } from './EffectPainter.js';
import { FighterPainter } from './FighterPainter.js';
import { PixelBrush } from './PixelBrush.js';
import { StoryPainter } from './StoryPainter.js';
import { TitlePainter } from './TitlePainter.js';

/**
 * Dessine une image du jeu : fait défiler le décor SVG, puis peint les acteurs sur le canvas,
 * triés par profondeur (y), et les effets par-dessus.
 */
export class Renderer {
    /** Le canvas est en résolution x2 : on dessine en coordonnées d'écran (320 x 180). */
    static SCALE = 2;

    constructor(game, canvas) {
        const { state, data, sprites } = game;
        this.game = game;
        this.state = state;
        this.sprites = sprites;
        this.width = data.width;
        this.height = data.height;
        this.ctx = canvas.getContext('2d');
        this.ctx.imageSmoothingEnabled = false;
        this.brush = new PixelBrush(this.ctx);
        this.fighters = new FighterPainter(this.ctx, sprites, game);
        this.effects = new EffectPainter(this.ctx, this.brush, state, this.width);
        this.title = new TitlePainter(this.ctx, this.width);
        this.story = new StoryPainter(this.ctx, this.brush, sprites);
    }

    draw() {
        const { ctx, state } = this;
        const shake = state.shake > 0 ? Math.round(rand(-state.shake, state.shake)) : 0;
        state.shake = Math.max(0, state.shake - 0.4);
        this.game.backdrop.scroll(state.cam, shake);

        ctx.setTransform(Renderer.SCALE, 0, 0, Renderer.SCALE, 0, 0);
        ctx.imageSmoothingEnabled = false;
        ctx.clearRect(0, 0, this.width, this.height);
        if (state.mode === 'title') this.title.draw(state.tick);

        ctx.save();
        ctx.translate(-Math.round(state.cam) + shake, 0);
        state.splats.forEach((s) => this.effects.splat(s));
        this.#gifts();
        this.#pickups();
        state.props.forEach((prop) => this.story.prop(prop));
        this.#actors();
        this.#projectiles();
        state.bits.forEach((b) => this.effects.bit(b));
        state.clouds.forEach((c) => this.effects.cloud(c));
        state.confetti.forEach((c) => this.effects.confetti(c));
        state.sparks.forEach((s) => this.effects.spark(s));
        state.texts.forEach((t) => this.effects.text(t));
        ctx.restore();

        this.#weather();
        this.#whiteFlash();
        this.game.hud.update();
    }

    #gifts() {
        const image = this.sprites.items.gift;
        for (const box of this.state.gifts) {
            if (box.open && box.t % 4 < 2) continue;
            this.brush.shadow(box.x, box.y, 8);
            const hop = !box.open && box.z === 0 && box.t % 60 < 6 ? 2 : 0; // la boîte sautille : ouvre-moi !
            this.ctx.drawImage(image, Math.round(box.x - image.width / 2), Math.round(box.y - image.height + 1 - box.z - hop));
        }
    }

    #pickups() {
        for (const item of this.state.pickups) {
            if (item.t > PICKUP_LIFETIME - 120 && item.t % 6 < 3) continue; // clignote avant de disparaître
            const image = this.sprites.items[item.kind];
            const bob = item.z > 0 ? 0 : Math.floor(item.t / 20) % 2;
            this.brush.shadow(item.x, item.y, Math.max(4, image.width / 2));
            this.brush.spinning(image, item.x, item.y - item.z - image.height / 2 - 1 - bob, item.z > 0 ? item.t * 0.35 : 0);
        }
    }

    #actors() {
        const { state } = this;
        const actors = [...state.enemies, ...state.cast, ...state.players, state.courier]
            .filter(Boolean)
            .sort((a, b) => a.y - b.y);
        for (const a of actors) {
            if (a.state === 'burrow' && a.t >= 20 && a.t <= 70) this.effects.mound(a);
            else this.brush.shadow(a.x, a.y, Math.round(12 * a.scale));
        }
        actors.forEach((a) => this.fighters.draw(a));
    }

    #projectiles() {
        for (const shot of this.state.projectiles) {
            const image = this.sprites.items[shot.sprite];
            this.brush.shadow(shot.x, shot.y, 3);
            this.brush.spinning(image, shot.x, shot.y - shot.z - image.height / 2, shot.spin ? shot.t * 0.4 : 0);
        }
    }

    /** Par-dessus tout, sans suivre la caméra : neige ou pluie. */
    #weather() {
        const { ctx, state } = this;
        const { level } = state;
        if (!level.snow && !level.rain) return;
        ctx.fillStyle = level.snow ? 'rgba(255,255,255,0.85)' : 'rgba(200,215,240,0.5)';
        for (const w of state.weather) {
            if (level.snow) ctx.fillRect(Math.round(w.x), Math.round(w.y), w.s > 1 ? 2 : 1, w.s > 1 ? 2 : 1);
            else ctx.fillRect(Math.round(w.x), Math.round(w.y), 1, 4);
        }
    }

    #whiteFlash() {
        const { state } = this;
        if (state.flash <= 0) return;
        this.ctx.fillStyle = `rgba(255,255,255,${state.flash / 16})`;
        this.ctx.fillRect(0, 0, this.width, this.height);
        state.flash--;
    }
}
