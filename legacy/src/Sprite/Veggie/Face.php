<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;

/** Les visages des légumes mutants : sourcils froncés, yeux exorbités, bouche pleine de dents. */
final class Face
{
    private const FACES = [
        'idle' => [
            'BB......BB',
            '.BBB..BBB.',
            '.WWW..WWW.',
            '.WWE..WWE.',
            '.WWW..WWW.',
            '..........',
            '..QWQWQW..',
            '...QQQQ...',
        ],
        'blink' => [
            'BB......BB',
            '.BBB..BBB.',
            '..........',
            '.BBB..BBB.',
            '..........',
            '..........',
            '..QWQWQW..',
            '...QQQQ...',
        ],
        'bite' => [
            'B........B',
            '.BBB..BBB.',
            '.WWWBBWWW.',
            '.WWE..WWE.',
            '.WWW..WWW.',
            '..WWWWWW..',
            '..QQQQQQ..',
            '..QQRRQQ..',
            '...WWWW...',
        ],
        'hurt' => [
            '..........',
            '.B.B..B.B.',
            '..B....B..',
            '.B.B..B.B.',
            '..........',
            '...QQQQ...',
            '..QQQQQQ..',
            '...QQQQ...',
        ],
        // zombie : un œil qui pend, la mâchoire de travers
        'zombie' => [
            '..........',
            '.BBB......',
            '.WWW..WW..',
            '.WEW..WWW.',
            '.WWW..WEW.',
            '.......W..',
            '.QWQWQW...',
            '..QQQQQQ..',
        ],
    ];

    public static function layer(Mood $mood, int $x, int $y, bool $zombie = false): Layer
    {
        $name = strtolower($mood->name);
        if ($zombie && in_array($mood, [Mood::Idle, Mood::Happy], true)) {
            $name = 'zombie';
        }

        return new Layer(self::FACES[$name === 'happy' ? 'idle' : $name], $x, $y);
    }
}
