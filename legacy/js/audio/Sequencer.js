import { Instruments } from './Instruments.js';

/**
 * Le lecteur de tracker : joue les morceaux compilés par PHP (config/music.php → Boulette\Music\Tracker).
 *
 * Programmation « en avance » : toutes les 25 ms, on planifie les notes des 150 prochaines millisecondes
 * sur l'horloge audio, qui est précise à l'échantillon près (contrairement aux minuteries JS).
 * Stéréo tranchée comme sur un Amiga : la mélodie à gauche, les arpèges à droite, basse et batterie au centre.
 */
export class Sequencer {
    static LOOKAHEAD = 0.15;
    static INTERVAL = 25;

    constructor(ctx, output, songs) {
        this.ctx = ctx;
        this.songs = Object.fromEntries(Object.entries(songs).map(([name, song]) => [name, Sequencer.#index(song)]));
        this.instruments = new Instruments(ctx);
        this.current = null;
        this.timer = null;
        /** Appelé quand un jingle (morceau sans boucle) est fini. */
        this.onEnd = null;
        this.#buildMixer(output);
    }

    /** Joue un morceau depuis le début (sans effet s'il joue déjà, sauf restart). */
    play(name, restart = false) {
        if (!this.songs[name] || (this.current === name && !restart)) return;
        this.stop();
        this.current = name;
        this.song = this.songs[name];
        this.step = 0;
        this.nextTime = this.ctx.currentTime + 0.05;
        this.timer = setInterval(() => this.#schedule(), Sequencer.INTERVAL);
        this.#schedule();
    }

    /** Coupe le morceau : les notes déjà planifiées s'éteignent en douceur sur l'ancien bus. */
    stop() {
        clearInterval(this.timer);
        this.timer = null;
        this.current = null;
        const old = this.bus;
        const now = this.ctx.currentTime;
        old.gain.setValueAtTime(old.gain.value, now);
        old.gain.linearRampToValueAtTime(0, now + 0.08);
        setTimeout(() => old.disconnect(), 400);
        this.bus = this.ctx.createGain();
        this.bus.connect(this.filter);
        this.#connectChannels();
    }

    #schedule() {
        const { song, ctx } = this;
        const stepTime = 60 / song.bpm / 4;
        while (this.nextTime < ctx.currentTime + Sequencer.LOOKAHEAD) {
            for (const event of song.steps[this.step] ?? []) this.#trigger(event, this.nextTime, stepTime);
            this.nextTime += stepTime;
            this.step++;
            if (this.step >= song.length) {
                if (!song.loop) {
                    const name = this.current;
                    setTimeout(() => {
                        if (this.current !== name) return;
                        this.stop();
                        this.onEnd?.(name);
                    }, (this.nextTime - ctx.currentTime) * 1000 + 600);
                    clearInterval(this.timer);
                    return;
                }
                this.step = 0;
            }
        }
    }

    #trigger([channel, a, b], time, stepTime) {
        const { instruments, channels, song } = this;
        switch (channel) {
            case 'lead':
                return instruments.lead(channels.lead, song.instruments.lead, a, time, b * stepTime);
            case 'chords':
                return song.instruments.chords === 'pad'
                    ? instruments.pad(channels.chords, a, time, b * stepTime)
                    : instruments.arp(channels.chords, a, time, b * stepTime);
            case 'bass':
                return instruments.bass(channels.bass, a, time, b * stepTime);
            case 'drums':
                return instruments.drum(channels.drums, a, time);
        }
    }

    /** Filtre passe-bas (le son étouffé de la puce Paula) puis un écho discret. */
    #buildMixer(output) {
        const { ctx } = this;
        this.filter = ctx.createBiquadFilter();
        this.filter.type = 'lowpass';
        this.filter.frequency.value = 7500;
        this.filter.connect(output);

        this.echo = ctx.createDelay(1);
        this.echo.delayTime.value = 0.19;
        const feedback = ctx.createGain();
        feedback.gain.value = 0.28;
        const wet = ctx.createGain();
        wet.gain.value = 0.22;
        this.echo.connect(feedback).connect(this.echo);
        this.echo.connect(wet).connect(this.filter);

        this.bus = ctx.createGain();
        this.bus.connect(this.filter);
        this.#connectChannels();
    }

    #connectChannels() {
        const pan = (value, sendEcho) => {
            const panner = this.ctx.createStereoPanner();
            panner.pan.value = value;
            panner.connect(this.bus);
            if (sendEcho) panner.connect(this.echo);
            return panner;
        };
        this.channels = { lead: pan(-0.45, true), chords: pan(0.5, true), bass: pan(0, false), drums: pan(0.1, false) };
    }

    /** Range les événements par pas : steps[pas] = [[canal, valeur, durée en pas]...]. */
    static #index(song) {
        const steps = [];
        const add = (channel, [step, a, b]) => (steps[step] ??= []).push([channel, a, b]);
        song.lead.forEach((e) => add('lead', e));
        song.chords.forEach((e) => add('chords', e));
        song.bass.forEach((e) => add('bass', e));
        song.drums.forEach((e) => add('drums', e));
        return { ...song, steps };
    }
}
