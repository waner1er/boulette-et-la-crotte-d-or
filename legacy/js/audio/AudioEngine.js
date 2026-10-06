import { Sequencer } from './Sequencer.js';
import { SOUNDS } from './sounds.js';
import { Synth } from './Synth.js';

const MUSIC_VOLUME = 0.55;
const SFX_VOLUME = 1.1;

/**
 * Tout le son du jeu. Mixage : bruitages (bitcrusher) + musique du tracker -> compresseur -> volume général.
 * Le contexte audio n'est créé qu'au premier son : les navigateurs ne l'autorisent qu'après un geste du joueur.
 */
export class AudioEngine {
    constructor(songs) {
        this.songs = songs;
        this.ctx = null;
        this.master = null;
        this.synth = null;
        this.music = null;
        this.muted = false;
        /** Morceau demandé avant que le son soit débloqué : il démarrera au premier geste. */
        this.wanted = null;
    }

    /** Débloque le son à chaque geste (iOS/Android n'autorisent le son qu'à ce moment-là). */
    unlockOnGesture(target = window) {
        for (const type of ['pointerdown', 'keydown']) {
            target.addEventListener(type, () => this.unlock(), { capture: true });
        }
    }

    unlock() {
        if (!this.#init()) return;
        this.#resume();
        if (this.wanted && !this.music.current) this.music.play(this.wanted);
    }

    play(name, ...args) {
        if (this.muted || !this.#init()) return;
        this.#resume();
        try {
            SOUNDS[name]?.(this.synth, ...args);
        } catch {
            // un son raté ne doit jamais casser le jeu
        }
    }

    /** Lance un morceau en boucle (sans effet s'il joue déjà). */
    playMusic(name) {
        if (!name) return;
        this.wanted = name;
        if (this.ctx && this.ctx.state === 'running') this.music.play(name);
    }

    /** Relance un morceau depuis le début (jingles). */
    playTrack(name) {
        this.wanted = name;
        if (this.ctx && this.ctx.state === 'running') this.music.play(name, true);
    }

    stopMusic() {
        this.wanted = null;
        this.music?.stop();
    }

    get currentMusic() {
        return this.wanted;
    }

    setMuted(muted) {
        this.muted = muted;
        if (this.master) this.master.gain.setTargetAtTime(muted ? 0 : 1, this.ctx.currentTime, 0.05);
    }

    #resume() {
        if (this.ctx.state === 'suspended') this.ctx.resume();
    }

    #init() {
        if (this.ctx) return this.ctx;
        try {
            this.ctx = new AudioContext();
        } catch {
            return null;
        }
        const { ctx } = this;

        const compressor = ctx.createDynamicsCompressor(); // évite la saturation quand tout tape en même temps
        compressor.threshold.value = -14;
        compressor.ratio.value = 6;
        compressor.attack.value = 0.003;
        compressor.release.value = 0.15;

        this.master = ctx.createGain();
        this.master.gain.value = this.muted ? 0 : 1;
        compressor.connect(this.master).connect(ctx.destination);

        const crusher = AudioEngine.#bitcrusher(ctx);
        const sfxBus = ctx.createGain();
        sfxBus.gain.value = SFX_VOLUME;
        crusher.connect(sfxBus).connect(compressor);
        this.synth = new Synth(ctx, crusher);

        const musicBus = ctx.createGain();
        musicBus.gain.value = MUSIC_VOLUME;
        musicBus.connect(compressor);
        this.music = new Sequencer(ctx, musicBus, this.songs);
        this.music.onEnd = (name) => {
            if (this.wanted === name) this.wanted = null;
        };
        ctx.addEventListener('statechange', () => {
            if (ctx.state === 'running' && this.wanted && !this.music.current) this.music.play(this.wanted);
        });

        return ctx;
    }

    /** Courbe en escalier qui réduit la résolution du signal : le grain des consoles 16 bits. */
    static #bitcrusher(ctx) {
        const crusher = ctx.createWaveShaper();
        const steps = 48;
        const curve = new Float32Array(1024);
        for (let i = 0; i < curve.length; i++) {
            const x = (i / (curve.length - 1)) * 2 - 1;
            curve[i] = Math.round(x * steps) / steps;
        }
        crusher.curve = curve;
        return crusher;
    }
}
