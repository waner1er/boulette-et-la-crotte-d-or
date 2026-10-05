import { pad } from '../../util/math.js';
import { Director } from './Director.js';

/** Fin : le générique défile sur la fête qui continue. */
export class CreditsDirector extends Director {
    constructor(game, party) {
        super(game);
        this.party = party;
    }

    enter() {
        const { data } = this.game;
        const lines = data.story.credits.map(([name, job]) => (
            `<div class="hud__credit"><span class="hud__credit-name">${name}</span><span class="hud__credit-job">${job}</span></div>`
        )).join('');
        this.game.hud.showCredits(`<div class="hud__credits-roll">${lines}`
            + '<div class="hud__credit hud__credit--end"><span class="hud__track">MERCI D\'AVOIR JOUÉ !</span>'
            + `<span class="hud__small">SCORE ${pad(this.state.score)}</span>`
            + '<span class="blink-text">PRESS START</span></div></div>');
    }

    update() {
        this.party.update();
        if (this.t > 120 && this.game.input.wasPressed('Enter', 'Space')) this.game.goHome();
    }
}
