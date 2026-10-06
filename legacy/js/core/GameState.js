import { LIVES } from '../config.js';
import { rand } from '../util/math.js';

/**
 * Tout l'état mutable de la partie, partagé par les systèmes du jeu.
 *
 * mode : title | select | story | loading | intro | playing | clear | gameover
 */
export class GameState {
    constructor(data, hiscore) {
        this.mode = 'title';
        this.modeTimer = 0;
        this.tick = 0;

        this.menu = 0;
        this.selected = 0;
        /** Partie à deux : Boulette (1P) et Saucisse (2P). */
        this.duo = false;

        this.levelIndex = 0;
        this.level = data.levels[0];
        this.cam = 0;
        this.locked = false;
        this.waveIndex = 0;

        // score commun aux deux joueurs, vies par joueur
        this.score = 0;
        this.hiscore = hiscore;
        this.kills = 0;
        this.lives = [LIVES.start];
        this.nextLife = LIVES.extraEvery;

        /** Les héros en jeu ; p.slot = numéro du joueur (0 = Boulette, 1 = Saucisse). */
        this.players = [];
        this.boss = null;
        this.enemies = [];
        this.projectiles = [];
        this.pickups = [];
        this.gifts = [];
        /** Saucisse en livreur (en solo), qui apporte la boîte menu enfant. */
        this.courier = null;

        // scènes animées (intro et fin)
        this.cast = [];
        this.props = [];

        // effets visuels
        this.bits = [];
        this.clouds = [];
        this.splats = [];
        this.sparks = [];
        this.texts = [];
        this.confetti = [];
        this.weather = Array.from({ length: 80 }, () => ({ x: rand(0, data.width), y: rand(0, data.height), s: rand(0.5, 1.5) }));
        this.shake = 0;
        this.flash = 0;

        this.messageUntil = 0;
        this.lastEnemy = null;
        this.lastEnemyUntil = 0;
    }

    resetScore() {
        this.score = 0;
        this.kills = 0;
        this.nextLife = LIVES.extraEvery;
        this.lives = Array(this.duo ? 2 : 1).fill(LIVES.start);
    }

    /** Le héros le plus proche, debout de préférence : celui que visent les ennemis. */
    nearestPlayer(x, y) {
        let best = null;
        let bestScore = Infinity;
        for (const p of this.players) {
            const score = Math.abs(p.x - x) + Math.abs(p.y - y) * 2 + (p.isDown ? 10000 : 0);
            if (score < bestScore) {
                best = p;
                bestScore = score;
            }
        }
        return best;
    }

    /** Vide la scène avant une séquence animée. */
    clearScene() {
        Object.assign(this, {
            players: [], cast: [], props: [], enemies: [], projectiles: [], pickups: [], gifts: [], courier: null,
            bits: [], clouds: [], splats: [], sparks: [], texts: [], confetti: [], boss: null,
        });
    }

    /** L'ennemi dont la barre de vie s'affiche (pendant 2,5 s après un coup). */
    target(enemy) {
        this.lastEnemy = enemy;
        this.lastEnemyUntil = this.tick + 150;
    }
}
