<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;

/** SAUCISSE, le teckel qui bosse au fast-food : long comme un hot-dog, casquette et bandana de l'enseigne. */
final class Dachshund extends DogDesign
{
    private const HEAD = [
        '....RRRRRR........',
        '...RRRRRRRRr......',
        '..RRRJJRRRRRYYYY..',
        '...FFFFFFFFF......',
        '..AAFFFWWWFFF.....',
        '.AAAFFWWEEFFFF....',
        '.AAAFFWWEEFFFFFFNN',
        '.AAAFFFFFFFFFFFFNN',
        '.AAAAFFFFFfffffff.',
        '..AAAFFFFFFTT.....',
        '..AAA.ffffffTT....',
        '...A.......T......',
    ];

    /** Lignes du visage remplacées selon l'expression (numéro de ligne => pixels). */
    private const MOODS = [
        'idle' => [],
        'blink' => [
            4 => '..AAFFFFFFFFF.....',
            5 => '.AAAFFEEEEFFFF....',
            6 => '.AAAFFFFFFFFFFFFNN',
        ],
        'bite' => [
            4 => '..AAFFFEEEFFF.....',
            5 => '.AAAFFFEEFFFFF....',
            8 => '.AAAAFFFFFWWWWWW..',
            9 => '..AAAFFFQQQQQQQ...',
            10 => '..AAA.ffWWWWWW....',
            11 => '...A..............',
        ],
        'hurt' => [
            4 => '..AAFFEFFEFFF.....',
            5 => '.AAAFFFEEFFFFF....',
            6 => '.AAAFFEFFEFFFFFFNN',
            9 => '..AAAFFFFQQQ......',
            10 => '..AAA.fffQQf......',
            11 => '...A..............',
        ],
        'happy' => [
            4 => '..AAFFFEEFFFF.....',
            5 => '.AAAFFEFFEFFFF....',
            6 => '.AAAFFFFFFFFFFFFNN',
            9 => '..AAAFFQQQQTT.....',
        ],
    ];

    private const BODY = [
        '.....GGGGGGGGGGGGGGGGGGGGGGG......',
        '..GGGFFFFFFFFFFFFFFFFFFFFFFFFGG...',
        '.GFFFFFFFFFFFFFFFFFFFFFFFFFFFFFG..',
        'GFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF.',
        'FFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFF',
        'fFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFf',
        'ffFFFFFFFFFFFFFFFFFFFFFFFFFFFFFFff',
        '.fffffFFFFFFFFFFFFFFFFFFFFFFFffff.',
        '...ffffffffffffffffffffffffffff...',
    ];

    private const TAILS = [
        ['F....', '.F...', '.FF..', '..FFF', '...FF'],
        ['.....', 'FF...', '.FFF.', '..FFF', '...FF'],
    ];

    private const BANDANA = ['RRRRR', 'RRJRR', '.RRR.', '..R..'];

    public function palette(): array
    {
        return [
            'F' => '#b8562a', 'f' => '#7f3418', 'G' => '#e88a4a', 'h' => '#5a2410',
            'A' => '#5e2410', 'N' => '#120808', 'Q' => '#6a1020', 'W' => '#ffffff', 'E' => '#120c14',
            'T' => '#ff6f8f', 'R' => '#ff2a4a', 'r' => '#b0102a', 'J' => '#ffd23f', 'Y' => '#ffd23f',
        ];
    }

    public function width(): int
    {
        return 52;
    }

    public function height(): int
    {
        return 28;
    }

    public function head(Mood $mood): Layer
    {
        $rows = self::HEAD;
        foreach (self::MOODS[strtolower($mood->name)] as $y => $row) {
            $rows[$y] = $row;
        }

        return new Layer($rows, 33, 2);
    }

    public function body(): Layer
    {
        return new Layer(self::BODY, 6, 12);
    }

    public function tail(int $wag): Layer
    {
        return new Layer(self::TAILS[$wag % 2], 2, 8);
    }

    public function legs(): array
    {
        return ['front' => [32, 36], 'back' => [10, 14], 'top' => 20, 'length' => 8];
    }

    public function gear(bool $firing): array
    {
        return [new Layer(self::BANDANA, 35, 13)];
    }
}
