/** Met en scène un plan des séquences animées : enter() au début du plan, beat() à chaque étape, update() à chaque image. */
export class Director {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** Images écoulées depuis le début du plan. */
    get t() {
        return this.game.story.t;
    }

    get cast() {
        return this.game.cast;
    }

    enter() {}

    update() {}

    /** Vide la scène et place le décor d'un niveau (numéro à partir de 1). */
    setStage(levelNumber) {
        const { state, game } = this;
        state.cast = [];
        state.props = [];
        state.cam = 0;
        state.level = game.data.levels[levelNumber - 1];
        document.documentElement.style.setProperty('--accent', state.level.accent);
        game.backdrop.load(levelNumber - 1);
    }
}
