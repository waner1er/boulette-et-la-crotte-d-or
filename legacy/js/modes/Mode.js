/** Un écran du jeu (accueil, choix du niveau, niveau...) : update() est appelé à chaque image. */
export class Mode {
    constructor(game) {
        this.game = game;
        this.state = game.state;
        this.input = game.input;
    }

    update() {}

    /** Menus : les toutous trottinent sur place pendant que le décor défile derrière eux. */
    walkInPlace() {
        const { state, game } = this;
        if (!state.players.length) {
            state.players = [game.spawn('boulette', 170, 162), game.spawn('saucisse', 120, 168)];
        }
        state.cam += 0.6;
        state.players.forEach((p, i) => {
            p.x = state.cam + 170 - i * 54;
            p.anim++;
            p.setState('walk');
        });
    }
}
