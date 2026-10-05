<?php

/**
 * La bande-son 16 bits, écrite comme dans un tracker Amiga (voir Boulette\Music\Tracker).
 *
 * bpm : tempo ; lead : instrument de la mélodie (pulse12 | pulse25 | square | saw | triangle | bell) ;
 * chords : arp (arpège ultra-rapide, le son Amiga) | pad (accords tenus) ;
 * bass : bounce | pump | walk | gallop | sustain ; drums : une mesure de batterie (k s h o c .) ;
 * patterns : motifs (accords une mesure par mot, mélodie 16 pas par mesure) ; order : l'enchaînement.
 * loop => false : un jingle qui ne joue qu'une fois.
 */

return [
    // l'écran titre : le thème de Boulette, fromage Amiga garanti
    'title' => [
        'bpm' => 132, 'lead' => 'pulse25', 'chords' => 'arp', 'bass' => 'bounce', 'drums' => 'k.h.s.h.k.khs.h.',
        'patterns' => [
            'A' => [
                'chords' => 'C G Am F',
                'lead' => 'C5 - E5 - G5 - C6 - - - G5 - E5 - G5 - | B4 - - - D5 - B4 - G4 - - - - - D5 - | C5 - - - A4 - E4 - A4 - C5 - E5 - - - | D5 - C5 - A4 - F4 - G4 - - - - - . .',
            ],
            'B' => [
                'chords' => 'F G C C',
                'lead' => 'A4 - - - C5 - - - A4 - G4 - F4 - - - | G4 - - - B4 - - - D5 - - - B4 - - - | C5 - G4 - E4 - G4 - C5 - D5 - E5 - - - | - - - - - - - - . . . . G4 - B4 -',
            ],
        ],
        'order' => 'A A B A',
    ],

    // l'intro et les cinématiques : mystère et nuggets
    'story' => [
        'bpm' => 96, 'lead' => 'triangle', 'chords' => 'pad', 'bass' => 'sustain', 'drums' => 'k...h...s...h...',
        'patterns' => [
            'A' => [
                'chords' => 'Am F C G',
                'lead' => 'A4 - - - C5 - E5 - - - D5 - C5 - - - | F5 - - - E5 - - - C5 - - - A4 - - - | G4 - - - C5 - E5 - G5 - - - E5 - - - | D5 - - - - - - - B4 - - - - - - -',
            ],
            'B' => [
                'chords' => 'Dm Am E Am',
                'lead' => 'F5 - - - E5 - D5 - - - A4 - - - - - | C5 - - - B4 - A4 - - - - - E5 - - - | G#5 - - - F5 - E5 - - - D5 - B4 - - - | A4 - - - - - - - - - - - . . . .',
            ],
        ],
        'order' => 'A B A B',
    ],

    // zone 1 : le parking, funky et ensoleillé
    'parking' => [
        'bpm' => 140, 'lead' => 'pulse25', 'chords' => 'arp', 'bass' => 'bounce', 'drums' => 'k.h.s.h.k.k.s.h.',
        'patterns' => [
            'A' => [
                'chords' => 'C Am F G',
                'lead' => 'E5 - G5 - C6 - B5 - G5 - - - E5 - G5 - | A5 - - - G5 - E5 - C5 - - - D5 - E5 - | F5 - A5 - C6 - A5 - F5 - - - G5 - A5 - | B5 - - - A5 - G5 - D5 - - - - - . .',
            ],
            'B' => [
                'chords' => 'F G Em Am',
                'lead' => 'A5 - A5 - G5 - F5 - G5 - - - A5 - C6 - | D6 - - - C6 - B5 - G5 - - - - - - - | G5 - G5 - F5 - E5 - F5 - - - G5 - B5 - | C6 - - - B5 - A5 - E5 - - - - - . .',
            ],
        ],
        'order' => 'A A B A',
    ],

    // zone 2 : la salle, sautillante comme un goûter d'anniversaire
    'dining' => [
        'bpm' => 150, 'lead' => 'pulse12', 'chords' => 'arp', 'bass' => 'walk', 'drums' => 'k.hhs.hhk.hhs.ho',
        'patterns' => [
            'A' => [
                'chords' => 'F Dm Bb C',
                'lead' => 'F5 - A5 - C6 - A5 - F5 - A5 - C6 - D6 - | C6 - - - A5 - - - F5 - - - D5 - F5 - | D6 - - - C6 - Bb5 - A5 - G5 - F5 - G5 - | A5 - - - G5 - - - - - - - . . . .',
            ],
            'B' => [
                'chords' => 'Bb C F F',
                'lead' => 'Bb5 - Bb5 - A5 - G5 - F5 - - - D5 - F5 - | G5 - - - E5 - C5 - E5 - G5 - Bb5 - - - | A5 - F5 - C6 - F5 - A5 - C6 - F6 - - - | - - - - - - - - . . C6 - D6 - E6 -',
            ],
        ],
        'order' => 'A A B A B',
    ],

    // zone 3 : les cuisines, ça frit, ça speede
    'kitchen' => [
        'bpm' => 164, 'lead' => 'square', 'chords' => 'arp', 'bass' => 'pump', 'drums' => 'kkh.s.h.kkh.s.hh',
        'patterns' => [
            'A' => [
                'chords' => 'Em C D B',
                'lead' => 'E5 E5 . E5 G5 - E5 - B5 - A5 - G5 - F#5 - | E5 - - - C5 - E5 - G5 - - - E5 - - - | F#5 F#5 . F#5 A5 - F#5 - D6 - C6 - A5 - F#5 - | D#5 - - - F#5 - - - B5 - - - A5 - F#5 -',
            ],
            'B' => [
                'chords' => 'Am Em Am B',
                'lead' => 'A5 - C6 - A5 - E5 - A5 - C6 - E6 - C6 - | B5 - G5 - E5 - G5 - B5 - - - E6 - - - | C6 - B5 - A5 - G5 - F#5 - E5 - F#5 - G5 - | F#5 - - - D#5 - - - B4 - - - . . . .',
            ],
        ],
        'order' => 'A A B A',
    ],

    // zone 4 : la chambre froide, des clochettes qui grelottent
    'freezer' => [
        'bpm' => 112, 'lead' => 'bell', 'chords' => 'arp', 'bass' => 'sustain', 'drums' => 'k...h.h.s...h..h',
        'patterns' => [
            'A' => [
                'chords' => 'Dm Bb Gm A',
                'lead' => 'D6 - - - A5 - - - F5 - - - A5 - - - | Bb5 - - - F5 - - - D5 - - - F5 - - - | G5 - - - Bb5 - - - D6 - - - G6 - - - | E6 - - - C#6 - - - A5 - - - - - - -',
            ],
            'B' => [
                'chords' => 'Dm C Bb A',
                'lead' => 'F6 - - - E6 - D6 - - - A5 - - - - - | E6 - - - D6 - C6 - - - G5 - - - - - | D6 - - - C6 - Bb5 - - - F5 - G5 - A5 - | A5 - - - - - - - C#6 - - - E6 - - -',
            ],
        ],
        'order' => 'A B A B',
    ],

    // zone 5 : le labo secret, savant fou et éprouvettes
    'lab' => [
        'bpm' => 138, 'lead' => 'saw', 'chords' => 'arp', 'bass' => 'gallop', 'drums' => 'k..kh.s.k..kh.so',
        'patterns' => [
            'A' => [
                'chords' => 'Cm Ab Fm G',
                'lead' => 'C5 - - Eb5 - - G5 - F#5 - G5 - Ab5 - G5 - | C6 - - - B5 - Ab5 - G5 - - - Eb5 - - - | F5 - - Ab5 - - C6 - B5 - C6 - Db6 - C6 - | B5 - - - D6 - - - G5 - - - - - . .',
            ],
            'B' => [
                'chords' => 'Cm Cm Ab G',
                'lead' => 'G5 G5 . G5 Ab5 - G5 - F5 - Eb5 - D5 - Eb5 - | C5 - - - - - - - G4 - Bb4 - C5 - D5 - | Eb5 - - - C5 - Ab4 - Eb5 - F5 - G5 - Ab5 - | B5 - - - G5 - - - D5 - - - B4 - - -',
            ],
        ],
        'order' => 'A A B A',
    ],

    // les boss : ça presse !
    'boss' => [
        'bpm' => 170, 'lead' => 'square', 'chords' => 'arp', 'bass' => 'pump', 'drums' => 'kkhsk.hskkhsk.hs',
        'patterns' => [
            'A' => [
                'chords' => 'Am F G E',
                'lead' => 'A5 - A5 - C6 - A5 - E6 - D6 - C6 - B5 - | A5 - - - F5 - A5 - C6 - - - A5 - - - | B5 - B5 - D6 - B5 - G6 - F6 - D6 - B5 - | G#5 - - - E5 - G#5 - B5 - - - E6 - - -',
            ],
            'B' => [
                'chords' => 'Dm Am E E',
                'lead' => 'F6 - E6 - D6 - A5 - F5 - A5 - D6 - E6 - | E6 - C6 - A5 - E5 - A5 - C6 - E6 - - - | D6 - B5 - G#5 - E5 - G#5 - B5 - D6 - E6 - | E6 - - - - - - - D6 - C6 - B5 - G#5 -',
            ],
        ],
        'order' => 'A A B A B',
    ],

    // SUPER BOULETTE : un nugget dans le ventre, la musique s'emballe
    'super' => [
        'bpm' => 176, 'lead' => 'pulse25', 'chords' => 'arp', 'bass' => 'bounce', 'drums' => 'k.hsk.hsk.hsk.hs',
        'patterns' => [
            'A' => [
                'chords' => 'C F G C',
                'lead' => 'C6 - G5 - C6 - E6 - G6 - E6 - C6 - G5 - | A5 - C6 - F6 - C6 - A5 - C6 - F6 - A6 - | G6 - F6 - D6 - B5 - G5 - B5 - D6 - F6 - | E6 - - - C6 - - - G6 - - - C7 - - -',
            ],
        ],
        'order' => 'A',
    ],

    // fin de niveau
    'clear' => [
        'bpm' => 150, 'lead' => 'pulse25', 'chords' => 'arp', 'bass' => 'pump', 'drums' => 'k...s...k.k.c...', 'loop' => false,
        'patterns' => [
            'A' => [
                'chords' => 'C G,C',
                'lead' => 'C5 - E5 - G5 - C6 - E6 - - - C6 - G5 - | C6 - - - - - - - . . . . . . . .',
            ],
        ],
        'order' => 'A',
    ],

    'gameover' => [
        'bpm' => 90, 'lead' => 'triangle', 'chords' => 'pad', 'bass' => 'sustain', 'drums' => '................', 'loop' => false,
        'patterns' => [
            'A' => [
                'chords' => 'Am E,Am',
                'lead' => 'E5 - - - D5 - - - C5 - - - B4 - - - | G#4 - - - - - - - A4 - - - - - - -',
            ],
        ],
        'order' => 'A',
    ],

    // la fin et le générique : victoire, nuggets pour tout le monde
    'ending' => [
        'bpm' => 124, 'lead' => 'pulse25', 'chords' => 'arp', 'bass' => 'walk', 'drums' => 'k.h.s.h.k.h.s.hh',
        'patterns' => [
            'A' => [
                'chords' => 'C Em F G',
                'lead' => 'G5 - - - E5 - G5 - C6 - - - B5 - C6 - | B5 - - - G5 - - - E5 - - - - - D5 - | C5 - F5 - A5 - C6 - F6 - - - E6 - D6 - | D6 - - - B5 - - - G5 - - - A5 - B5 -',
            ],
            'B' => [
                'chords' => 'Am Em F G',
                'lead' => 'C6 - - - E6 - - - A6 - G6 - E6 - - - | G6 - - - E6 - - - B5 - - - - - - - | A5 - C6 - F6 - E6 - D6 - C6 - A5 - C6 - | D6 - - - - - - - G6 - - - F6 - D6 -',
            ],
        ],
        'order' => 'A B A B',
    ],
];
