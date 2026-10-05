import { BossAI } from './actors/BossAI.js';
import { EnemyAI } from './actors/EnemyAI.js';
import { PlayerController } from './actors/PlayerController.js';
import { AudioEngine } from './audio/AudioEngine.js';
import { HEROES, HISCORE_KEY } from './config.js';
import { GameState } from './core/GameState.js';
import { Input } from './input/Input.js';
import { Campaign } from './modes/Campaign.js';
import { ClearMode } from './modes/ClearMode.js';
import { GameOverMode } from './modes/GameOverMode.js';
import { IntroMode } from './modes/IntroMode.js';
import { Mode } from './modes/Mode.js';
import { PlayingMode } from './modes/PlayingMode.js';
import { SelectMode } from './modes/SelectMode.js';
import { StoryMode } from './modes/StoryMode.js';
import { TitleMode } from './modes/TitleMode.js';
import { Renderer } from './render/Renderer.js';
import { SpriteBank } from './render/SpriteBank.js';
import { Cast } from './story/Cast.js';
import { createDirectors } from './story/directors/index.js';
import { StoryPlayer } from './story/StoryPlayer.js';
import { ControlPanel } from './ui/ControlPanel.js';
import { Hud } from './ui/Hud.js';
import { HiscoreStore } from './util/HiscoreStore.js';
import { Backdrop } from './world/Backdrop.js';
import { Camera } from './world/Camera.js';
import { Combat } from './world/Combat.js';
import { Fighter } from './world/Fighter.js';
import { Gifts } from './world/Gifts.js';
import { Particles } from './world/Particles.js';
import { Pickups } from './world/Pickups.js';
import { Projectiles } from './world/Projectiles.js';

/**
 * Le jeu : assemble les systèmes et les fait tourner à chaque image.
 * Chaque système reçoit le jeu et y retrouve ce dont il a besoin (état, entrées, son, autres systèmes).
 */
export class Game {
    constructor(data, root = document) {
        this.data = data;
        this.hiscores = new HiscoreStore(HISCORE_KEY);
        this.state = new GameState(data, this.hiscores.load());
        this.input = new Input();
        /** Commandes de chaque joueur en partie à deux (en solo, Boulette obéit à input). */
        this.pads = [new Input(), new Input()];
        this.audio = new AudioEngine(data.music);
        this.sprites = new SpriteBank(data);
        this.hud = new Hud(root, this);
        this.panel = new ControlPanel(root, this.input);
        this.backdrop = new Backdrop(root.querySelector('.screen__scene'), data.sceneUrl, data.width);

        this.combat = new Combat(this);
        this.pickups = new Pickups(this);
        this.gifts = new Gifts(this);
        this.projectiles = new Projectiles(this);
        this.particles = new Particles(this);
        this.camera = new Camera(this);

        this.player = new PlayerController(this);
        this.enemyAI = new EnemyAI(this);
        this.bossAI = new BossAI(this);

        this.cast = new Cast(this);
        this.story = new StoryPlayer(this, createDirectors(this));
        this.campaign = new Campaign(this);
        this.modes = {
            title: new TitleMode(this),
            select: new SelectMode(this),
            story: new StoryMode(this),
            loading: new Mode(this),
            intro: new IntroMode(this),
            playing: new PlayingMode(this),
            clear: new ClearMode(this),
            gameover: new GameOverMode(this),
        };

        this.renderer = new Renderer(this, root.querySelector('.screen__actors'));
    }

    /** Crée un personnage ; sans cfg, celle du héros ou du légume de ce type (config/game.php). */
    spawn(type, x, y, cfg = undefined) {
        cfg ??= this.data.heroes[type] ?? this.data.enemies[type];
        return new Fighter(type, x, y, cfg);
    }

    /** Fait entrer les héros (un en solo, deux en duo), côte à côte. */
    spawnPlayers(x, y) {
        const count = this.state.duo ? 2 : 1;
        return HEROES.slice(0, count).map((type, slot) => {
            const p = this.spawn(type, x - slot * 26, y + (count > 1 ? (slot ? 8 : -8) : 0));
            p.slot = slot;
            return p;
        });
    }

    /** Les commandes d'un héros : son pavé en duo, sinon les commandes communes. */
    inputOf(p) {
        return this.state.duo ? this.pads[p.slot] : this.input;
    }

    /** Super Boulette : un nugget avalé il y a moins de 10 secondes. */
    isSuper(p) {
        return p.superUntil > this.state.tick;
    }

    sfx(name, ...args) {
        this.audio.play(name, ...args);
    }

    /** Texte qui s'envole (cri, bonus...) ; sans couleur, il clignote. */
    shout(text, x, y, color = null) {
        this.state.texts.push({ text, x, y, t: 0, color });
    }

    setMode(mode) {
        this.state.mode = mode;
        this.state.modeTimer = 0;
    }

    goHome() {
        const { state, hud } = this;
        state.clearScene();
        Object.assign(state, { duo: false, menu: 0, cam: 0 });
        hud.showDialog(false);
        hud.showCredits(null);
        hud.setLayout('story', false);
        this.backdrop.load(0);
        state.level = this.data.levels[0];
        document.documentElement.style.setProperty('--accent', state.level.accent);
        this.setMode('title');
        this.audio.playMusic('title');
    }

    toggleMute() {
        this.audio.setMuted(!this.audio.muted);
        this.hud.setMuted(this.audio.muted);
    }

    /** Une image de logique (1/60 s). */
    update() {
        const { state, input } = this;
        state.tick++;
        state.modeTimer++;
        if (input.wasPressed('KeyM')) this.toggleMute();

        this.modes[state.mode].update();

        this.particles.update();
        this.campaign.updateScore();
        this.hud.expireMessage();
        this.panel.update();
        input.endFrame();
        this.pads.forEach((pad) => pad.endFrame());
    }

    draw() {
        this.renderer.draw();
    }
}
