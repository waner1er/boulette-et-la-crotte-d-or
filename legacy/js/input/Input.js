export const ARROWS = ['ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'];
export const GAME_KEYS = [...ARROWS, 'Space', 'KeyB', 'KeyV', 'KeyC', 'Enter', 'KeyM'];

/**
 * À deux sur un clavier AZERTY, chaque joueur a son pavé, traduit en touches du mode solo
 * (ESPACE tire, V mord, C prout, B saut). Codes physiques : « Z » AZERTY = KeyW, « , » = KeyM...
 * 1P (Boulette) : ZQSD + V tir, X morsure, C prout, W saut ; 2P (Saucisse) : OKLM + , tir, : morsure, ; prout, ! saut
 */
export const DUO_KEYS = [
    {
        KeyW: 'ArrowUp', KeyA: 'ArrowLeft', KeyS: 'ArrowDown', KeyD: 'ArrowRight',
        KeyV: 'Space', KeyX: 'KeyV', KeyC: 'KeyC', KeyZ: 'KeyB',
    },
    {
        KeyO: 'ArrowUp', KeyK: 'ArrowLeft', KeyL: 'ArrowDown', Semicolon: 'ArrowRight',
        KeyM: 'Space', Period: 'KeyV', Comma: 'KeyC', Slash: 'KeyB',
    },
];

/**
 * État des commandes, quelle que soit leur source (clavier, boutons de la borne, stick tactile, démo).
 * held : touches maintenues ; pressed : touches enfoncées depuis la dernière image.
 */
export class Input {
    constructor() {
        this.held = new Set();
        this.pressed = new Set();
        /** Appelé à chaque appui ; s'il renvoie true, l'appui est ignoré. */
        this.interceptor = null;
    }

    press(code) {
        if (this.interceptor?.(code)) return;
        if (!this.held.has(code)) this.pressed.add(code);
        this.held.add(code);
    }

    release(code) {
        this.held.delete(code);
    }

    releaseAll() {
        this.held.clear();
    }

    clear() {
        this.held.clear();
        this.pressed.clear();
    }

    isHeld(code) {
        return this.held.has(code);
    }

    wasPressed(...codes) {
        return codes.some((code) => this.pressed.has(code));
    }

    /** -1, 0 ou 1 selon les flèches gauche/droite maintenues. */
    get axisX() {
        return (this.held.has('ArrowRight') ? 1 : 0) - (this.held.has('ArrowLeft') ? 1 : 0);
    }

    /** -1 (haut), 0 ou 1 (bas). */
    get axisY() {
        return (this.held.has('ArrowDown') ? 1 : 0) - (this.held.has('ArrowUp') ? 1 : 0);
    }

    /** Les appuis ne durent qu'une image. */
    endFrame() {
        this.pressed.clear();
    }
}
