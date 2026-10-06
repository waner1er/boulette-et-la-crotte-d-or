<?php

/**
 * Les 20 niveaux : 5 zones du fast-food MÉGA MIAM, 4 niveaux par zone.
 * Chaque niveau finit par un boss : un sous-chef (légume géant) aux niveaux 1 à 3 d'une zone,
 * le grand boss de la zone au niveau 4.
 *
 * theme : ambiance du décor (voir Boulette\Scene\Theme).
 *         zone = parking | dining | kitchen | freezer | lab ; time = day | sunset | night ;
 *         seed = graine du décor (change-la pour un autre agencement) ; accent = couleur des néons et du HUD.
 * music : morceau joué pendant le niveau (voir config/music.php).
 * boss  : sprite (un ennemi de config/game.php ou un boss de Boulette\Sprite\Boss\BossRoster) et comportement.
 *         scale = taille, multiple de 0.5 ; special = summon | throw (+ projectile) | charge | burrow,
 *         déclenché toutes les « every » images.
 */

return [
    // ---- ZONE 1 : LE PARKING ----
    1 => [
        'title' => 'LE DRIVE',
        'theme' => ['zone' => 'parking', 'seed' => 101, 'time' => 'day', 'accent' => '#ff2a4a', 'signs' => ['MÉGA MIAM']],
        'music' => 'parking',
        'boss' => ['sprite' => 'courgette', 'scale' => 1.5, 'name' => 'COURGETTE BALÈZE', 'hp' => 18, 'speed' => 0.6, 'damage' => 10, 'special' => 'charge', 'every' => 300, 'line' => 'TU VAS MANGER TES LÉGUMES !'],
    ],
    2 => [
        'title' => 'LES POUBELLES',
        'theme' => ['zone' => 'parking', 'seed' => 102, 'time' => 'sunset', 'accent' => '#ff8a1e', 'signs' => ['MÉGA MIAM']],
        'music' => 'parking',
        'boss' => ['sprite' => 'radis', 'scale' => 2, 'name' => 'RADIS RAGEUR', 'hp' => 20, 'speed' => 1.1, 'damage' => 10, 'special' => 'charge', 'every' => 220, 'line' => 'GRRR... RADIS-CALEMENT FÂCHÉ !'],
    ],
    3 => [
        'title' => 'PARKING DE NUIT',
        'theme' => ['zone' => 'parking', 'seed' => 103, 'time' => 'night', 'accent' => '#3ef0ff', 'signs' => ['OUVERT 24H']],
        'music' => 'parking',
        'boss' => ['sprite' => 'petitpois', 'scale' => 2, 'name' => 'SERGENT PETIT POIS', 'hp' => 22, 'speed' => 0.7, 'damage' => 10, 'special' => 'throw', 'projectile' => 'pea', 'every' => 110, 'line' => 'FEU À VOLONTÉ, SOLDATS !'],
    ],
    4 => [
        'title' => 'LA DRIVE-PARTY',
        'theme' => ['zone' => 'parking', 'seed' => 104, 'time' => 'night', 'accent' => '#ff3ea5', 'signs' => ['MÉGA MIAM'], 'rain' => true],
        'music' => 'parking',
        'boss' => ['sprite' => 'courgetron', 'scale' => 2, 'name' => 'COURGETRON', 'hp' => 36, 'speed' => 0.7, 'damage' => 13, 'special' => 'summon', 'every' => 330, 'line' => 'CE PARKING EST À MOI, LE CARLIN !'],
    ],

    // ---- ZONE 2 : LA SALLE ----
    5 => [
        'title' => 'LA SALLE',
        'theme' => ['zone' => 'dining', 'seed' => 201, 'time' => 'day', 'accent' => '#ff2a4a', 'signs' => ['MENU MIAM', 'NUGGETS', 'SUNDAE']],
        'music' => 'dining',
        'boss' => ['sprite' => 'tomate', 'scale' => 2, 'name' => 'TOMATE CERISE GÉANTE', 'hp' => 24, 'speed' => 0.9, 'damage' => 12, 'special' => 'charge', 'every' => 240, 'line' => 'JE VAIS TE METTRE EN SAUCE !'],
    ],
    6 => [
        'title' => 'LE COIN ANNIV',
        'theme' => ['zone' => 'dining', 'seed' => 202, 'time' => 'day', 'accent' => '#ff3ea5', 'signs' => ['JOYEUX', 'ANNIV', 'GÂTEAU']],
        'music' => 'dining',
        'boss' => ['sprite' => 'oignon', 'scale' => 2, 'name' => 'OIGNON DÉPRIMÉ', 'hp' => 26, 'speed' => 0.6, 'damage' => 12, 'special' => 'throw', 'projectile' => 'tear', 'every' => 100, 'line' => 'PERSONNE NE M\'INVITE AUX ANNIVS... SNIF !'],
    ],
    7 => [
        'title' => 'LE COMPTOIR',
        'theme' => ['zone' => 'dining', 'seed' => 203, 'time' => 'sunset', 'accent' => '#ffd23f', 'signs' => ['COMMANDEZ', 'ICI', 'MERCI']],
        'music' => 'dining',
        'boss' => ['sprite' => 'aubergine', 'scale' => 1.5, 'name' => 'AUBERGINE CATCHEUSE', 'hp' => 30, 'speed' => 0.6, 'damage' => 15, 'special' => 'charge', 'every' => 260, 'line' => 'PRISE DE L\'AUBERGINE VOLANTE !'],
    ],
    8 => [
        'title' => 'LA FERMETURE',
        'theme' => ['zone' => 'dining', 'seed' => 204, 'time' => 'night', 'accent' => '#9b4dff', 'signs' => ['FERMÉ', 'À DEMAIN', 'BONNE NUIT']],
        'music' => 'dining',
        'boss' => ['sprite' => 'brocoking', 'scale' => 2, 'name' => 'LE ROI BROCOLI', 'hp' => 42, 'speed' => 0.55, 'damage' => 14, 'special' => 'summon', 'every' => 320, 'line' => 'MANGEZ... VOS... BROCOLIIIS...'],
    ],

    // ---- ZONE 3 : LES CUISINES ----
    9 => [
        'title' => 'LES FRITEUSES',
        'theme' => ['zone' => 'kitchen', 'seed' => 301, 'time' => 'day', 'accent' => '#ff8a1e', 'signs' => ['COMMANDE 42']],
        'music' => 'kitchen',
        'boss' => ['sprite' => 'carotte', 'scale' => 2, 'name' => 'CAROTTE RÂPÉE', 'hp' => 28, 'speed' => 1, 'damage' => 13, 'special' => 'burrow', 'every' => 260, 'line' => 'JE VAIS TE RÂPER LA TRUFFE !'],
    ],
    10 => [
        'title' => 'LE GRILL',
        'theme' => ['zone' => 'kitchen', 'seed' => 302, 'time' => 'day', 'accent' => '#ff2a4a', 'signs' => ['GRILL CHAUD !']],
        'music' => 'kitchen',
        'boss' => ['sprite' => 'courgette', 'scale' => 2.5, 'name' => 'COURGETTE FARCIE', 'hp' => 32, 'speed' => 0.55, 'damage' => 15, 'special' => 'summon', 'every' => 300, 'line' => 'JE SUIS FARCIE DE COLÈRE !'],
    ],
    11 => [
        'title' => 'LA PLONGE',
        'theme' => ['zone' => 'kitchen', 'seed' => 303, 'time' => 'night', 'accent' => '#3ef0ff', 'signs' => ['LAVEZ-VOUS', 'LES PATTES']],
        'music' => 'kitchen',
        'boss' => ['sprite' => 'petitpois', 'scale' => 2.5, 'name' => 'GÉNÉRAL PETIT POIS', 'hp' => 32, 'speed' => 0.7, 'damage' => 12, 'special' => 'throw', 'projectile' => 'pea', 'every' => 70, 'line' => 'MITRAILLEZ-MOI CE CARLIN !'],
    ],
    12 => [
        'title' => 'LE PASSE-PLAT',
        'theme' => ['zone' => 'kitchen', 'seed' => 304, 'time' => 'night', 'accent' => '#ff3ea5', 'signs' => ['SERVICE !']],
        'music' => 'kitchen',
        'boss' => ['sprite' => 'ladycarotte', 'scale' => 2, 'name' => 'LADY CAROTTE', 'hp' => 48, 'speed' => 0.9, 'damage' => 15, 'special' => 'burrow', 'every' => 240, 'line' => 'MA MOISISSURE EST UN PARFUM, CHÉRIE !'],
    ],

    // ---- ZONE 4 : LA CHAMBRE FROIDE ----
    13 => [
        'title' => 'LA CHAMBRE FROIDE',
        'theme' => ['zone' => 'freezer', 'seed' => 401, 'time' => 'day', 'accent' => '#3ef0ff', 'signs' => ['NUGGETS', 'FRITES'], 'snow' => true],
        'music' => 'freezer',
        'boss' => ['sprite' => 'radis', 'scale' => 2.5, 'name' => 'RADIS GIVRÉ', 'hp' => 32, 'speed' => 1.2, 'damage' => 13, 'special' => 'charge', 'every' => 200, 'line' => 'BRRR... ÇA CAILLE, ET TOI TU VAS GELER !'],
    ],
    14 => [
        'title' => 'LES SURGELÉS',
        'theme' => ['zone' => 'freezer', 'seed' => 402, 'time' => 'day', 'accent' => '#8adcff', 'signs' => ['GLACES', 'POTAGE'], 'snow' => true],
        'music' => 'freezer',
        'boss' => ['sprite' => 'oignon', 'scale' => 2.5, 'name' => 'OIGNON GLACÉ', 'hp' => 36, 'speed' => 0.6, 'damage' => 14, 'special' => 'throw', 'projectile' => 'ice', 'every' => 90, 'line' => 'MES LARMES ONT GELÉ... BOUHOUHOU !'],
    ],
    15 => [
        'title' => 'LE BLIZZARD',
        'theme' => ['zone' => 'freezer', 'seed' => 403, 'time' => 'night', 'accent' => '#ffffff', 'signs' => ['-30°C', 'BRRR'], 'snow' => true],
        'music' => 'freezer',
        'boss' => ['sprite' => 'aubergine', 'scale' => 2, 'name' => 'AUBERGINE DES NEIGES', 'hp' => 40, 'speed' => 0.65, 'damage' => 17, 'special' => 'charge', 'every' => 220, 'line' => 'CHAMPIONNE DU MONDE DE CATCH SUR GLACE !'],
    ],
    16 => [
        'title' => 'LE CŒUR DE GLACE',
        'theme' => ['zone' => 'freezer', 'seed' => 404, 'time' => 'night', 'accent' => '#9b4dff', 'signs' => ['DANGER', 'GLACE'], 'snow' => true],
        'music' => 'freezer',
        'boss' => ['sprite' => 'choufleur', 'scale' => 2, 'name' => 'CHOU-FLEUR DES GLACES', 'hp' => 54, 'speed' => 0.6, 'damage' => 16, 'special' => 'throw', 'projectile' => 'ice', 'every' => 80, 'line' => 'TU VAS FINIR EN GRATIN SURGELÉ !'],
    ],

    // ---- ZONE 5 : LE LABO SECRET ----
    17 => [
        'title' => 'LE SOUS-SOL',
        'theme' => ['zone' => 'lab', 'seed' => 501, 'time' => 'night', 'accent' => '#5aff5a', 'signs' => ['DANGER']],
        'music' => 'lab',
        'boss' => ['sprite' => 'brocoli', 'scale' => 2.5, 'name' => 'BROCOLI CYBORG', 'hp' => 40, 'speed' => 0.6, 'damage' => 16, 'special' => 'summon', 'every' => 300, 'line' => 'ZOMBIE... ET MAINTENANT ROBOT !'],
    ],
    18 => [
        'title' => 'LES CUVES',
        'theme' => ['zone' => 'lab', 'seed' => 502, 'time' => 'night', 'accent' => '#b4ff3a', 'signs' => ['MUTATION']],
        'music' => 'lab',
        'boss' => ['sprite' => 'tomate', 'scale' => 3, 'name' => 'TOMATE ATOMIQUE', 'hp' => 44, 'speed' => 0.8, 'damage' => 17, 'special' => 'charge', 'every' => 200, 'line' => 'JE SUIS PRÊTE À EXPLOSER !'],
    ],
    19 => [
        'title' => 'LE POTAGER INTERDIT',
        'theme' => ['zone' => 'lab', 'seed' => 503, 'time' => 'night', 'accent' => '#ff3ea5', 'signs' => ['INTERDIT'], 'alarm' => true],
        'music' => 'lab',
        'boss' => ['sprite' => 'carotte', 'scale' => 3, 'name' => 'CAROTTE MUTANTE XXL', 'hp' => 48, 'speed' => 1, 'damage' => 18, 'special' => 'burrow', 'every' => 200, 'line' => 'MOI AUSSI J\'AI MANGÉ DE LA CROTTE D\'OR !'],
    ],
    20 => [
        'title' => 'LA CROTTE D\'OR',
        'theme' => ['zone' => 'lab', 'seed' => 504, 'time' => 'night', 'accent' => '#ffd23f', 'signs' => ['CROTTE D\'OR'], 'alarm' => true],
        'music' => 'lab',
        'boss' => ['sprite' => 'navet', 'scale' => 2.5, 'name' => 'PROFESSEUR NAVET', 'hp' => 70, 'speed' => 0.7, 'damage' => 18, 'special' => 'throw', 'projectile' => 'flask', 'summons' => true, 'every' => 110, 'line' => 'LA CROTTE D\'OR EST À MOI ! MOUAHAHAHA !'],
    ],
];
