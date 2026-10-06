import { Typewriter } from './Typewriter.js';

/**
 * Déroule une séquence animée (config/story.php) : à chaque étape, un « réalisateur » met le plan
 * en scène et le texte s'écrit dans la boîte de dialogue. START, ESPACE ou B pour passer.
 */
export class StoryPlayer {
    constructor(game, directors) {
        this.game = game;
        this.directors = directors;
        this.typewriter = new Typewriter((name) => game.sfx(name));
        this.steps = [];
        this.index = 0;
        this.t = 0;
        this.onEnd = null;
    }

    get step() {
        return this.steps[this.index];
    }

    /** Le plan en cours (legend, boulette, navet...). */
    get scene() {
        return this.step?.scene;
    }

    start(steps, onEnd) {
        const { game } = this;
        Object.assign(this, { steps, index: 0, onEnd });
        game.state.clearScene();
        game.hud.setLayout('home', false);
        game.hud.setLayout('story', true);
        game.hud.message('');
        game.setMode('story');
        this.#enter();
    }

    update() {
        const { game, step, typewriter } = this;
        this.t++;
        this.directors[step.scene]?.update(step);
        game.cast.update();

        const html = typewriter.update();
        if (html !== null) game.hud.setDialog(html);

        if (step.duration) {
            if (this.t >= step.duration) this.#next();
            return;
        }
        if (step.scene === 'credits' || !game.input.wasPressed('Enter', 'Space', 'KeyB')) return;
        if (typewriter.done) this.#next();
        else typewriter.finish();
    }

    #enter() {
        const { game, step } = this;
        const samePlan = this.steps[this.index - 1]?.scene === step.scene;
        this.typewriter.load(step.text ?? '');
        game.hud.showDialog(Boolean(step.text));

        // même plan que l'étape précédente : l'animation continue, on ne la rejoue pas
        if (!samePlan) {
            this.t = 0;
            this.directors[step.scene]?.enter(step);
        }
        this.directors[step.scene]?.beat?.(step);
        this.#heroReacts(step);
    }

    /** Réplique en bulle et action de Boulette au moment précis de l'étape. */
    #heroReacts(step) {
        const { game } = this;
        const hero = game.state.cast.find((a) => a.type === 'boulette');
        if (!hero) return;
        if (step.shout) {
            game.shout(step.shout, hero.x + 20, hero.y - 40 * hero.scale, '#ffd23f');
            game.sfx('bark');
        }
        if (step.action === 'dash') {
            hero.setState('skate');
            hero.t = 0;
            game.sfx('bigProut');
        }
    }

    #next() {
        this.index++;
        if (this.index < this.steps.length) {
            this.#enter();
            return;
        }
        this.game.hud.showDialog(false);
        this.game.hud.setLayout('story', false);
        this.onEnd?.();
    }
}
