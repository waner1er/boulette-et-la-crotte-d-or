<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Layer;

/** SUPER BOULETTE : un nugget avalé, le pelage devient or et une cape de super-héroïne lui pousse dans le dos. */
final class SuperPug extends Pug
{
    private const CAPES = [
        [
            '......PPPPPPPP',
            '...PPPPPPPPPPPp',
            '.PPPPPPPPPPPPPp',
            'PPPPPPSPPPPPPpp',
            'PPPPPSSSPPPPpp.',
            '.pPPPPSPPPPpp..',
            '..ppPPPPPPpp...',
            '....ppppppp....',
        ],
        [
            '......PPPPPPPP',
            '..PPPPPPPPPPPPp',
            'PPPPPPPPPPPPPPp',
            '.PPPPPSPPPPPPpp',
            'PPPPPSSSPPPPpp.',
            'ppPPPPSPPPPpp..',
            '.pppPPPPPPpp...',
            '...pppppppp....',
        ],
    ];

    public function palette(): array
    {
        return [
            'F' => '#ffc82a', 'f' => '#d88a12', 'G' => '#fff4a8', 'h' => '#a85a08',
            'P' => '#ff2a4a', 'p' => '#a8102a', 'S' => '#ffe14a',
        ] + parent::palette();
    }

    public function back(int $wag): array
    {
        return [new Layer(self::CAPES[$wag % 2], 2, 9)];
    }
}
