/** Voix (hauteur et voyelle) du cri de chaque légume quand il se décompose. */
const VOICES = {
    courgette: { pitch: 240, vowel: 'e' },
    brocoli: { pitch: 130, vowel: 'u' },
    carotte: { pitch: 300, vowel: 'a' },
    petitpois: { pitch: 520, vowel: 'e' },
    oignon: { pitch: 200, vowel: 'o' },
    tomate: { pitch: 280, vowel: 'o' },
    aubergine: { pitch: 110, vowel: 'a' },
    radis: { pitch: 440, vowel: 'a' },
};

const arpeggio = (s, notes, { duration, step, volume = 0.12, offset = 0, type = 'square' }) => notes.forEach(
    (f, i) => s.tone({ type, from: f, duration, volume, delay: offset + i * step }),
);

/** Recettes des bruitages : (synth, ...arguments) => void. */
export const SOUNDS = {
    /** Le lance-baballe : « pop » d'air comprimé et sifflement. */
    shoot: (s) => {
        s.noise({ duration: 0.06, volume: 0.5, filter: 'lowpass', from: 1800, to: 300 });
        s.tone({ type: 'sine', from: 380, to: 900, duration: 0.1, volume: 0.25 });
    },
    fries: (s) => {
        s.noise({ duration: 0.08, volume: 0.35, filter: 'bandpass', from: 2500, to: 1200, q: 2 });
        s.tone({ type: 'square', from: 600, to: 900, duration: 0.06, volume: 0.08 });
    },
    boing: (s) => s.tone({ type: 'sine', from: 260, to: 620, duration: 0.09, volume: 0.18 }),
    /** Morsure : claquement de mâchoires. */
    chomp: (s) => {
        s.noise({ duration: 0.05, volume: 0.6, filter: 'bandpass', from: 1200, to: 600, q: 3 });
        s.tone({ type: 'square', from: 300, to: 120, duration: 0.06, volume: 0.15, delay: 0.03 });
    },
    /** Super Boulette déchiquette : une rafale de mâchoires. */
    shred: (s) => [0, 0.05, 0.1].forEach((delay) => {
        s.noise({ duration: 0.05, volume: 0.5, filter: 'bandpass', from: 1500, to: 500, q: 3, delay });
    }),
    hit: (s) => {
        s.noise({ duration: 0.1, volume: 0.9, filter: 'lowpass', from: 3200, to: 350 });
        s.tone({ type: 'sine', from: 160, to: 50, duration: 0.14, volume: 0.8 });
        s.tone({ type: 'square', from: 220, to: 70, duration: 0.1, volume: 0.35 });
    },
    heavyHit: (s) => {
        s.noise({ duration: 0.24, volume: 1, filter: 'lowpass', from: 1800, to: 100 });
        s.tone({ type: 'sine', from: 110, to: 30, duration: 0.3, volume: 1 });
        s.tone({ type: 'square', from: 230, to: 55, duration: 0.14, volume: 0.4 });
    },
    /** Un légume se décompose : « splotch » mouillé. */
    splat: (s) => {
        s.noise({ duration: 0.22, volume: 0.7, filter: 'lowpass', from: 1400, to: 120 });
        s.tone({ type: 'sine', from: 500, to: 80, duration: 0.2, volume: 0.35 });
    },
    /** Le chien a mal : « KAÏ ! » */
    hurt: (s) => {
        s.noise({ duration: 0.1, volume: 0.6, filter: 'lowpass', from: 1800, to: 200 });
        s.scream({ pitch: 760, duration: 0.2, volume: 0.25, vowel: 'a', toVowel: 'e', contour: [1, 1.2, 0.8] });
    },
    heroDeath: (s) => s.scream({ pitch: 700, duration: 0.9, volume: 0.3, vowel: 'a', toVowel: 'u', contour: [1, 1.15, 0.45] }),
    bark: (s) => s.scream({ pitch: 380, duration: 0.14, volume: 0.35, vowel: 'a', toVowel: 'o', contour: [1, 1.3, 0.8] }),
    prout: (s) => s.fart({ pitch: 105 + Math.random() * 40, duration: 0.5 }),
    bigProut: (s) => s.fart({ pitch: 75, duration: 1.1, volume: 0.6, wobble: 16 }),
    jump: (s) => s.tone({ type: 'square', from: 300, to: 900, duration: 0.14, volume: 0.1 }),
    land: (s) => s.noise({ duration: 0.06, volume: 0.25, filter: 'lowpass', from: 600, to: 200 }),
    throw: (s) => s.tone({ type: 'triangle', from: 300, to: 700, duration: 0.12, volume: 0.15 }),
    pea: (s) => s.tone({ type: 'square', from: 900, to: 500, duration: 0.05, volume: 0.07 }),
    tear: (s) => s.tone({ type: 'sine', from: 900, to: 300, duration: 0.15, volume: 0.15 }),
    ice: (s) => {
        s.noise({ duration: 0.12, volume: 0.35, filter: 'highpass', from: 3000, to: 6000 });
        arpeggio(s, [2600, 3300], { duration: 0.05, step: 0.03, volume: 0.06 });
    },
    explosion: (s) => {
        s.noise({ duration: 0.6, volume: 0.8, filter: 'lowpass', from: 1200, to: 60 });
        s.tone({ type: 'sine', from: 90, to: 25, duration: 0.5, volume: 0.6 });
    },
    pickup: (s) => arpeggio(s, [660, 880, 1320], { duration: 0.07, step: 0.06 }),
    /** Un nugget : la transformation en Super Boulette. */
    nugget: (s) => arpeggio(s, [523, 659, 784, 1047, 1319, 1568, 2093], { duration: 0.09, step: 0.05, volume: 0.13 }),
    gift: (s) => arpeggio(s, [784, 988, 1175, 1568], { duration: 0.12, step: 0.08, volume: 0.12, type: 'triangle' }),
    ding: (s) => {
        s.tone({ type: 'sine', from: 1568, duration: 0.5, volume: 0.25 });
        s.tone({ type: 'sine', from: 2093, duration: 0.6, volume: 0.15, delay: 0.12 });
    },
    oneup: (s) => arpeggio(s, [660, 990, 1320, 1980], { duration: 0.1, step: 0.07 }),
    select: (s) => s.tone({ type: 'square', from: 520, to: 780, duration: 0.06, volume: 0.1 }),
    start: (s) => arpeggio(s, [440, 660, 880], { duration: 0.12, step: 0.1 }),
    warning: (s) => [0, 0.35, 0.7].forEach((delay) => {
        s.tone({ type: 'square', from: 440, to: 330, duration: 0.3, volume: 0.15, delay });
    }),
    burrow: (s) => s.noise({ duration: 0.5, volume: 0.5, filter: 'lowpass', from: 300, to: 900 }),
    type: (s) => s.tone({ type: 'square', from: 1200, duration: 0.02, volume: 0.04 }),
    /** La machine à écrire fait une pause : Boulette renifle. */
    sniff: (s) => [0, 0.14, 0.28].forEach((delay) => s.noise({ duration: 0.07, volume: 0.25, filter: 'bandpass', from: 2500, q: 2, delay })),

    /** Cri d'un légume qui se décompose : chacun sa voix, un peu aléatoire. */
    death: (s, type) => {
        const voice = VOICES[type] ?? { pitch: 260, vowel: 'a' };
        s.scream({ pitch: voice.pitch * (0.9 + Math.random() * 0.2), duration: 0.45, vowel: voice.vowel, toVowel: 'o', volume: 0.28 });
    },
    /** Cri d'un boss : d'autant plus grave qu'il est gros. */
    bossDeath: (s, scale = 2) => {
        s.scream({ pitch: 240 / scale, duration: 1.4, volume: 0.45, vowel: 'a', toVowel: 'o', contour: [1, 1.5, 0.4] });
        s.noise({ duration: 1.2, volume: 0.4, filter: 'lowpass', from: 700, to: 80 });
    },
};
