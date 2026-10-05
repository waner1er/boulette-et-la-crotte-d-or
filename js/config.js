/** Réglages du gameplay. Durées en images (60 par seconde), distances en pixels. */

export const FPS = 60;

export const PAPER = '#fff6e0';
export const INK = '#120a1c';

/** Fenêtres d'une attaque : le coup touche entre hitFrom et hitTo, l'image de frappe dure jusqu'à strikeUntil. */
export const ATTACK = {
    hero: { hitFrom: 5, hitTo: 9, strikeUntil: 14, end: 18 },
    enemy: { hitFrom: 26, hitTo: 29, strikeUntil: 38, end: 46 },
    boss: { hitFrom: 18, hitTo: 22, strikeUntil: 30, end: 38 },
};

/** Le prout turbo : une glissade invincible qui renverse tout, propulsée au gaz. */
export const PROUT = { duration: 32, speed: 3.4, cooldown: 75 };
export const JUMP = { impulse: 3.6, gravity: 0.22, speed: 1.6 };
export const SHOOT = { duration: 12, fireAt: 3 };

export const LIVES = { start: 3, extraEvery: 15000 };

/** Les héros jouables, par numéro de joueur : Boulette (1P) et Saucisse (2P). */
export const HEROES = ['boulette', 'saucisse'];

/** Un os tombe tous les N légumes décomposés. */
export const BONE_EVERY_KILLS = 12;

/** Chance qu'un légume lâche un nugget en se décomposant. */
export const NUGGET_DROP = 0.04;

/** Durée de vie d'un bonus au sol. */
export const PICKUP_LIFETIME = 720;

export const HISCORE_KEY = 'boulette-hiscore';
