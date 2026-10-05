<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Veggie\Shape;
use Boulette\Sprite\Veggie\VeggieDesign;

/**
 * PROFESSEUR NAVET, le savant fou : c'est lui qui a volé la Crotte d'Or pour muter les légumes.
 * Bulbe violet et blanc, blouse de labo, grosses lunettes, fanes en pétard.
 */
final class ProfesseurNavet extends VeggieDesign
{
    private const LEAVES = [
        'V....V...V....V',
        '.V..VV..VV...V.',
        '.VV.Vv.vV..VV..',
        '..VVVvVVvVVV...',
        '...VVVVVVVV....',
    ];

    private const COAT = [
        'CCCCCCCC..CCCCCCCC',
        'CCCCCCCCZZCCCCCCCc',
        'CCCCCCCZZZZCCCCCcc',
        'CCCCCCCCZZCCCCCCcc',
        'CCCCCCCCCCCCCCCCcc',
        'CCCCCCCCcCCCCCCCcc',
        'CCCCCCCCcCCCCCCCcc',
        'CCCCCCCCcCCCCCCcc.',
        '.CCCCCCCcCCCCCCcc.',
    ];

    public function palette(): array
    {
        return [
            'P' => '#9a2ab8', 'p' => '#d070f0', 'U' => '#f4ecf8', 'u' => '#ffffff', 'D' => '#b8a8c8', 'd' => '#5a1470',
            'V' => '#5ad04a', 'v' => '#2a8a2a', 'C' => '#f0f4ff', 'c' => '#a8b4d0',
            'L' => '#f4ecf8', 'l' => '#b8a8c8', 'k' => '#5a4a6a',
        ] + Crown::PALETTE;
    }

    public function body(): Layer
    {
        $halves = [3, 5, 7, 9, 10, 11, 11, 12, 12, 12, 12, 12, 11, 11, 10, 9, 8, 6, 4, 2, 1];
        $rows = Shape::rows($halves, 26, function (int $x, int $y, int $half): string {
            if ($y < 9) {
                return $x >= $half - 2 ? 'd' : ($x <= -$half + 2 && $y > 1 ? 'p' : 'P');
            }

            return Shape::shade($x, $y, $half, 22, 'u', 'U', 'D');
        });

        return new Layer($rows, 5, 12);
    }

    public function back(): array
    {
        return [new Layer(self::LEAVES, 10, 7)];
    }

    public function over(): array
    {
        return [
            new Layer(['.nnnn..nnnn.', 'nzzzznnzzzzn', 'nzzzzn.nzzzzn', '.nnnn...nnnn.'], 12, 14),
            new Layer(self::COAT, 9, 27),
        ];
    }

    public function face(): array
    {
        return [13, 14];
    }

    public function shoulder(): int
    {
        return 28;
    }

    public function shoulders(): array
    {
        return [9, 27];
    }
}
