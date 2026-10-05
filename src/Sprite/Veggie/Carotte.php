<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** CAROTTE MOISIE : un cône orange pointe en bas, des fanes ébouriffées et des taches de moisi. Rapide. */
class Carotte extends VeggieDesign
{
    private const LEAVES = [
        '..v...v...',
        '.vV..vV.v.',
        '..VV.V.vV.',
        'v..VVVVV..',
        '.VVvVVvVV.',
        '...VVVV...',
    ];

    public function palette(): array
    {
        return [
            'C' => '#ff8a1e', 'c' => '#ffc05a', 'D' => '#c4520a', 'M' => '#8a9a7a', 'm' => '#c8d8b0',
            'V' => '#4cc23a', 'v' => '#2a7a26',
            'L' => '#ff8a1e', 'l' => '#c4520a', 'k' => '#7a3008',
        ];
    }

    public function body(): Layer
    {
        $halves = [8, 9, 9, 9, 9, 8, 8, 8, 7, 7, 7, 6, 6, 6, 5, 5, 5, 4, 4, 3, 3, 2, 2, 1, 1];
        $height = count($halves);
        $rows = Shape::rows($halves, 20, function (int $x, int $y, int $half) use ($height): string {
            if (in_array([$x, $y], [[3, 3], [4, 3], [3, 4], [-5, 14], [-4, 14], [1, 19]], true)) {
                return 'M';
            }
            if ($y % 4 === 3 && abs($x) < $half - 1) {
                return 'D'; // les cernes de la carotte
            }

            return Shape::shade($x, $y, $half, $height + 3, 'c', 'C', 'D');
        });

        return new Layer($rows, 8, 14);
    }

    public function back(): array
    {
        return [new Layer(self::LEAVES, 13, 8)];
    }

    public function face(): array
    {
        return [13, 16];
    }

    public function shoulder(): int
    {
        return 24;
    }
}
