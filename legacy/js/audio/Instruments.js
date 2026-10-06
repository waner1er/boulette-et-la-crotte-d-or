/** Fréquence d'une note MIDI (69 = La 440 Hz). */
export const frequency = (midi) => 440 * 2 ** ((midi - 69) / 12);

/**
 * Les instruments du tracker, tous synthétisés : ondes « pulse » de largeurs différentes (le grain Amiga/C64),
 * arpèges ultra-rapides pour faire des accords avec une seule voix, basse ronde, batterie au bruit blanc.
 */
export class Instruments {
    /** Une note d'arpège dure 1/50 s, comme un « tick » de tracker sur un Amiga PAL. */
    static ARP_TICK = 1 / 50;

    constructor(ctx) {
        this.ctx = ctx;
        this.waves = {
            pulse12: Instruments.#pulse(ctx, 0.125),
            pulse25: Instruments.#pulse(ctx, 0.25),
            pulse50: Instruments.#pulse(ctx, 0.5),
        };
        this.noise = Instruments.#noise(ctx);
    }

    /** La mélodie : attaque nette, vibrato sur les notes tenues. */
    lead(out, kind, midi, time, duration) {
        const { ctx } = this;
        if (kind === 'bell') {
            this.#voice(out, 'sine', frequency(midi), time, duration, 0.32, 0.6);
            this.#voice(out, 'sine', frequency(midi) * 2.01, time, Math.min(duration, 0.3), 0.12, 0.2);
            return;
        }
        const osc = this.#oscillator(kind, frequency(midi));
        if (duration > 0.25) {
            const lfo = ctx.createOscillator();
            const depth = ctx.createGain();
            lfo.frequency.value = 6;
            depth.gain.setValueAtTime(0, time);
            depth.gain.linearRampToValueAtTime(frequency(midi) * 0.012, time + 0.2);
            lfo.connect(depth).connect(osc.frequency);
            lfo.start(time);
            lfo.stop(time + duration + 0.1);
        }
        const level = kind === 'saw' || kind === 'square' ? 0.13 : 0.18;
        this.#play(osc, out, time, duration, level, 0.75);
    }

    /** Accord en arpège : une seule voix qui saute de note en note 50 fois par seconde. */
    arp(out, notes, time, duration) {
        const osc = this.#oscillator('pulse12', frequency(notes[0] + 12));
        const tick = Instruments.ARP_TICK;
        for (let i = 0, t = time; t < time + duration; i++, t += tick) {
            osc.frequency.setValueAtTime(frequency(notes[i % notes.length] + 12), t);
        }
        this.#play(osc, out, time, duration, 0.07, 0.6);
    }

    /** Accord tenu, doux (cinématiques). */
    pad(out, notes, time, duration) {
        for (const midi of notes) this.#voice(out, 'triangle', frequency(midi + 12), time, duration, 0.07, 0.9, 0.12);
    }

    bass(out, midi, time, duration) {
        this.#voice(out, 'triangle', frequency(midi), time, duration, 0.42, 0.8);
        this.#play(this.#oscillator('pulse50', frequency(midi)), out, time, Math.min(duration, 0.09), 0.05, 0.5);
    }

    drum(out, hit, time) {
        switch (hit) {
            case 'k':
                this.#sweep(out, 'sine', 160, 42, time, 0.16, 0.9);
                this.#hiss(out, 'lowpass', 2500, time, 0.015, 0.25);
                break;
            case 's':
                this.#hiss(out, 'bandpass', 1900, time, 0.14, 0.45);
                this.#sweep(out, 'triangle', 220, 140, time, 0.08, 0.3);
                break;
            case 'h':
                this.#hiss(out, 'highpass', 7500, time, 0.03, 0.18);
                break;
            case 'o':
                this.#hiss(out, 'highpass', 6500, time, 0.16, 0.16);
                break;
            case 'c':
                this.#hiss(out, 'highpass', 4000, time, 0.7, 0.22);
                break;
        }
    }

    #oscillator(kind, freq) {
        const osc = this.ctx.createOscillator();
        if (this.waves[kind]) osc.setPeriodicWave(this.waves[kind]);
        else osc.type = { saw: 'sawtooth', square: 'square', triangle: 'triangle' }[kind] ?? 'square';
        osc.frequency.value = freq;
        return osc;
    }

    #voice(out, type, freq, time, duration, volume, sustain, attack = 0.005) {
        const osc = this.ctx.createOscillator();
        osc.type = type;
        osc.frequency.value = freq;
        this.#play(osc, out, time, duration, volume, sustain, attack);
    }

    /** Enveloppe : attaque, petite chute vers le maintien, relâche à la fin de la note. */
    #play(source, out, time, duration, volume, sustain, attack = 0.005) {
        const gain = this.ctx.createGain();
        const end = time + Math.max(0.03, duration);
        gain.gain.setValueAtTime(0.0001, time);
        gain.gain.exponentialRampToValueAtTime(volume, time + attack);
        gain.gain.exponentialRampToValueAtTime(volume * sustain, time + attack + 0.08);
        gain.gain.setValueAtTime(volume * sustain, end - 0.02);
        gain.gain.exponentialRampToValueAtTime(0.0001, end + 0.04);
        source.connect(gain).connect(out);
        source.start(time);
        source.stop(end + 0.06);
    }

    #sweep(out, type, from, to, time, duration, volume) {
        const osc = this.ctx.createOscillator();
        osc.type = type;
        osc.frequency.setValueAtTime(from, time);
        osc.frequency.exponentialRampToValueAtTime(to, time + duration);
        const gain = this.ctx.createGain();
        gain.gain.setValueAtTime(volume, time);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + duration);
        osc.connect(gain).connect(out);
        osc.start(time);
        osc.stop(time + duration + 0.02);
    }

    #hiss(out, filterType, cutoff, time, duration, volume) {
        const source = this.ctx.createBufferSource();
        source.buffer = this.noise;
        const filter = this.ctx.createBiquadFilter();
        filter.type = filterType;
        filter.frequency.value = cutoff;
        const gain = this.ctx.createGain();
        gain.gain.setValueAtTime(volume, time);
        gain.gain.exponentialRampToValueAtTime(0.0001, time + duration);
        source.connect(filter).connect(gain).connect(out);
        source.start(time, Math.random() * 0.5);
        source.stop(time + duration + 0.02);
    }

    /** Onde « pulse » de rapport cyclique duty, par sa série de Fourier. */
    static #pulse(ctx, duty) {
        const size = 32;
        const real = new Float32Array(size);
        const imag = new Float32Array(size);
        for (let n = 1; n < size; n++) real[n] = (2 / (n * Math.PI)) * Math.sin(n * Math.PI * duty);
        return ctx.createPeriodicWave(real, imag);
    }

    static #noise(ctx) {
        const buffer = ctx.createBuffer(1, ctx.sampleRate, ctx.sampleRate);
        const data = buffer.getChannelData(0);
        for (let i = 0; i < data.length; i++) data[i] = Math.random() * 2 - 1;
        return buffer;
    }
}
