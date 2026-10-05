import { pad } from '../util/math.js';
import { Mode } from './Mode.js';

/** Choix du niveau : ◀ ▶ pour changer (décor et musique suivent), START pour jouer, ▲ pour revenir. */
export class SelectMode extends Mode {
    update() {
        const { game, input } = this;
        this.walkInPlace();
        if (input.wasPressed('ArrowRight')) this.#select(1);
        if (input.wasPressed('ArrowLeft')) this.#select(-1);
        if (input.wasPressed('ArrowUp')) game.goHome();
        else if (input.wasPressed('Enter', 'Space')) {
            this.state.players = [];
            game.campaign.startLevel(this.state.selected);
        }
    }

    show() {
        const level = this.game.data.levels[this.state.selected];
        document.documentElement.style.setProperty('--accent', level.accent);
        this.game.hud.message(
            `<span class="hud__small">ZONE ${level.zone} · ${level.zoneName}</span><br>`
            + `<span class="hud__track">◀ NIVEAU ${pad(level.number, 2)} ▶</span><br>`
            + `<span class="hud__lyrics">${level.title}</span><br><br>`
            + `<span class="hud__small">BOSS : ${level.boss.name}</span><br><br>`
            + '<span class="blink-text">PRESS START</span><br><span class="hud__small">▲ RETOUR</span>',
        );
    }

    #select(delta) {
        const { game, state } = this;
        const { levels } = game.data;
        state.selected = (state.selected + delta + levels.length) % levels.length;
        state.level = levels[state.selected];
        this.show();
        game.backdrop.load(state.selected);
        game.audio.playMusic(levels[state.selected].music);
        game.sfx('select');
    }
}
