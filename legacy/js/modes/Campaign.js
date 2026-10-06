import { LIVES } from '../config.js';
import { pad } from '../util/math.js';

/** L'enchaînement de la partie : intro, 20 niveaux, vies, game over, fin. */
export class Campaign {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /** JOUER : l'intro, puis le niveau 1. */
    playIntro() {
        const { game } = this;
        game.audio.playMusic('story');
        [19, 6, 5].forEach((index) => game.backdrop.preload(index));
        game.story.start(game.data.story.intro, () => this.startLevel(0));
    }

    async startLevel(index) {
        if (index === this.game.data.levels.length - 1) [5].forEach((scene) => this.game.backdrop.preload(scene));
        const { game, state } = this;
        const { data } = game;
        game.setMode('loading');
        game.hud.setLayout('home', false);
        game.hud.message('CHARGEMENT...');
        await game.backdrop.load(index);

        const level = data.levels[index];
        state.clearScene();
        Object.assign(state, { levelIndex: index, level, cam: 0, locked: false, waveIndex: 0, lastEnemy: null });
        game.gifts.reset();
        // un joueur à court de vies revient au niveau suivant
        state.lives = state.lives.map((lives) => (lives > 0 ? lives : LIVES.start));
        state.players = game.spawnPlayers(70, 158);

        game.hud.showGo(false);
        game.hud.setLevelLabel(`NIVEAU ${level.number}`);
        document.documentElement.style.setProperty('--accent', level.accent);
        game.hud.message(
            `<span class="hud__small">ZONE ${level.zone} · ${level.zoneName}</span><br>`
            + `<span class="hud__track">NIVEAU ${level.number}</span><br>`
            + `<span class="hud__lyrics">${level.title}</span><br><br>`
            + '<span class="blink-text">PRÊTE, BOULETTE ?</span>',
        );
        game.audio.playMusic(level.music);
        game.sfx('start');
        game.setMode('intro');
    }

    nextLevel() {
        this.startLevel(this.state.levelIndex + 1);
    }

    /** Bonus de fin de niveau ; après le Professeur Navet, place à la fin. */
    levelClear() {
        const { game, state } = this;
        const bonus = state.players.reduce((sum, p) => sum + p.hp * 10, 1000);
        state.score += bonus;
        state.players.forEach((p) => {
            p.superUntil = 0;
        });
        if (state.levelIndex === game.data.levels.length - 1) {
            game.audio.playMusic('ending');
            game.story.start(game.data.story.ending, () => game.goHome());
            return;
        }
        game.setMode('clear');
        game.hud.showGo(false);
        const next = game.data.levels[state.levelIndex + 1];
        const zoneDone = next.zone !== state.level.zone;
        game.hud.message((zoneDone ? `ZONE ${state.level.zone} NETTOYÉE !` : 'NIVEAU TERMINÉ !')
            + `<br><br><span class="hud__small">BONUS ${pad(bonus)}</span>`);
        game.audio.playTrack('clear');
    }

    /** Après un K.O. d'un chien : il se relève, ou il quitte la partie ; plus personne, c'est le game over. */
    respawn(p) {
        const { game, state } = this;
        if (state.lives[p.slot] > 0) {
            Object.assign(p, { hp: p.maxHp, invuln: 120, vx: 0 });
            p.setState('idle');
            return;
        }
        if (state.players.length > 1) {
            state.players = state.players.filter((other) => other !== p);
            game.shout(`${p.slot + 1}P GAME OVER`, p.x, p.y - 40, '#ffffff');
            return;
        }
        game.setMode('gameover');
        game.audio.playTrack('gameover');
        game.hud.message('LES LÉGUMES ONT GAGNÉ...<br>(POUR CETTE FOIS)<br><br>'
            + '<span class="blink-text">GAME OVER · START POUR REJOUER</span><br><span class="hud__small">▲ ACCUEIL</span>');
    }

    /** Une vie de plus pour chaque joueur tous les 15 000 points (score commun), et le meilleur score. */
    updateScore() {
        const { game, state } = this;
        const playing = state.players.length && ['playing', 'clear'].includes(state.mode);
        if (playing && state.score >= state.nextLife) {
            state.nextLife += LIVES.extraEvery;
            state.lives = state.lives.map((lives) => lives + 1);
            for (const p of state.players) game.shout('1UP !', p.x, p.y - 60, '#7dff5a');
            game.sfx('oneup');
        }
        if (state.score > state.hiscore) {
            state.hiscore = state.score;
            game.hiscores.save(state.hiscore);
        }
    }
}
