<?php

/**
 * Réglages généraux du jeu, envoyés au JavaScript.
 *
 * Ennemis : reach = portée du coup, projectile = attaque à distance (toutes les « every » images),
 * moves = attaques possibles et leur poids (strike = coup normal, lunge = ruée, throw = lancer,
 * kamikaze = fonce et explose).
 * Héros : bite = morsure (au corps à corps), ball = lance-baballe, et leurs versions « super ».
 */

return [
    'title' => "Boulette et la Crotte d'Or",
    'restaurant' => 'MÉGA MIAM',
    'year' => 2026,

    'heroes' => [
        'boulette' => ['name' => 'BOULETTE', 'hp' => 100, 'projectile' => 'ball', 'super' => 'boulette-super'],
        'saucisse' => ['name' => 'SAUCISSE', 'hp' => 100, 'projectile' => 'fries', 'super' => null],
    ],

    'attacks' => [
        'bite' => ['damage' => 2, 'reach' => 30, 'knockback' => 2.6],
        'superBite' => ['damage' => 6, 'reach' => 40, 'knockback' => 4.5],
        'ball' => ['damage' => 1, 'speed' => 4.2, 'cooldown' => 16],
        'superBall' => ['damage' => 3, 'speed' => 5.2, 'cooldown' => 9],
    ],

    'enemies' => [
        'courgette' => [
            'name' => 'COURGETTE MUTANTE', 'hp' => 3, 'speed' => 0.7, 'damage' => 8, 'reach' => 26, 'score' => 100,
            'moves' => ['strike' => 3, 'lunge' => 1],
        ],
        'brocoli' => [
            'name' => 'BROCOLI ZOMBIE', 'hp' => 6, 'speed' => 0.45, 'damage' => 11, 'reach' => 32, 'score' => 150,
            'moves' => ['strike' => 3, 'lunge' => 1],
        ],
        'carotte' => [
            'name' => 'CAROTTE MOISIE', 'hp' => 3, 'speed' => 1.05, 'damage' => 9, 'reach' => 26, 'score' => 150,
            'moves' => ['strike' => 2, 'lunge' => 3],
        ],
        'petitpois' => [
            'name' => 'PETIT POIS COMMANDO', 'hp' => 2, 'speed' => 0.7, 'damage' => 6, 'reach' => 22, 'score' => 150,
            'projectile' => 'pea', 'every' => 90, 'moves' => ['throw' => 4],
        ],
        'oignon' => [
            'name' => 'OIGNON PLEUREUR', 'hp' => 5, 'speed' => 0.55, 'damage' => 9, 'reach' => 26, 'score' => 200,
            'projectile' => 'tear', 'every' => 130, 'moves' => ['strike' => 2, 'throw' => 2],
        ],
        'tomate' => [
            'name' => 'TOMATE KAMIKAZE', 'hp' => 2, 'speed' => 1.2, 'damage' => 16, 'reach' => 22, 'score' => 150,
            'moves' => ['strike' => 1, 'kamikaze' => 4],
        ],
        'aubergine' => [
            'name' => 'AUBERGINE CATCHEUSE', 'hp' => 10, 'speed' => 0.45, 'damage' => 17, 'reach' => 30, 'score' => 300,
            'moves' => ['strike' => 3, 'lunge' => 1],
        ],
        'radis' => [
            'name' => 'RADIS FURIEUX', 'hp' => 2, 'speed' => 1.45, 'damage' => 7, 'reach' => 22, 'score' => 120,
            'moves' => ['strike' => 1, 'lunge' => 3],
        ],
    ],

    /** Ennemis débloqués au fil des niveaux. */
    'unlocks' => [
        1 => ['courgette'],
        2 => ['radis'],
        3 => ['petitpois'],
        5 => ['tomate'],
        6 => ['oignon'],
        8 => ['brocoli'],
        10 => ['carotte'],
        13 => ['aubergine'],
    ],

    /** Bonus : soin, super-pouvoir, vie, baballes d'or. Durées en images (60 par seconde). */
    'pickups' => [
        'nugget' => ['name' => 'NUGGET !', 'duration' => 600],
        'bone' => ['name' => 'OS À MOELLE', 'heal' => 40],
        'fries' => ['name' => 'FRITES', 'heal' => 20],
        'toy' => ['name' => 'JOUET : 1UP', 'life' => 1],
        'goldball' => ['name' => "BABALLES D'OR", 'duration' => 900],
    ],

    /** Ce que contient une boîte menu enfant, et le poids de chaque surprise. */
    'gifts' => ['nugget' => 3, 'bone' => 3, 'toy' => 1, 'goldball' => 2],
];
