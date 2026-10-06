import { Mode } from './Mode.js';

/** Écran titre façon Amiga : logo chromé, barres « copper », texte qui ondule. JOUER, 2 JOUEURS ou NIVEAUX. */
export class TitleMode extends Mode {
    static MENU = ['1 JOUEUR', '2 JOUEURS', 'NIVEAUX'];

    /** Rappel des commandes à deux, affiché quand « 2 JOUEURS » est sélectionné. */
    static DUO_HELP = '1P BOULETTE : ZQSD + V X C W<br>2P SAUCISSE : OKLM + , : ; !';

    update() {
        const { game, state, input } = this;
        const size = TitleMode.MENU.length;
        this.walkInPlace();
        if (state.modeTimer === 1) this.show();

        if (input.wasPressed('ArrowUp', 'ArrowDown')) {
            state.menu = (state.menu + (input.wasPressed('ArrowUp') ? size - 1 : 1)) % size;
            this.show();
            game.sfx('select');
        }
        if (input.wasPressed('Enter', 'Space')) this.#choose();
    }

    show() {
        const { game, state } = this;
        const items = TitleMode.MENU.map((label, i) => (i === state.menu
            ? `<span class="hud__menu-item is-active">▶ ${label}</span>`
            : `<span class="hud__menu-item">${label}</span>`)).join('');
        game.hud.setLayout('home', true);
        game.hud.message(
            '<div class="hud__logo"><span class="hud__logo-name">BOULETTE</span>'
            + '<span class="hud__logo-sub">ET LA CROTTE D\'OR</span></div>'
            + `<div class="hud__menu">${items}</div>`
            + (TitleMode.MENU[state.menu] === '2 JOUEURS' ? `<span class="hud__small">${TitleMode.DUO_HELP}</span><br>` : '')
            + '<span class="blink-text hud__small">PRESS START</span>',
        );
        game.hud.setLevelLabel('HI-SCORE');
    }

    #choose() {
        const { game, state } = this;
        state.duo = TitleMode.MENU[state.menu] === '2 JOUEURS';
        state.resetScore();
        game.sfx('start');
        game.hud.setLayout('home', false);
        if (TitleMode.MENU[state.menu] === 'NIVEAUX') {
            game.setMode('select');
            game.modes.select.show();
            game.audio.playMusic(game.data.levels[state.selected].music);
            return;
        }
        game.campaign.playIntro();
    }
}
