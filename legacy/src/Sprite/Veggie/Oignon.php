<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** OIGNON PLEUREUR : un bulbe violet qui sanglote et lance ses larmes. Ça pique les yeux. */
final class Oignon extends VeggieDesign
{
    private const SPROUT = ['..V.', '.VV.', '.Vv.', 'VVvv', '.vV.'];

    public function palette(): array
    {
        return [
            'P' => '#a8389a', 'p' => '#e07ad0', 'D' => '#6a1a62', 'U' => '#f0c8e8',
            'V' => '#7ad04a', 'v' => '#3a8a2a', 'T' => '#5ad8ff',
            'L' => '#e07ad0', 'l' => '#a8389a', 'k' => '#5a1450',
        ];
    }

    public function body(): Layer
    {
        $halves = [1, 1, 2, 2, 3, 4, 6, 8, 9, 10, 10, 11, 11, 11, 11, 10, 10, 9, 8, 6, 4];
        $height = count($halves);
        $rows = Shape::rows($halves, 24, function (int $x, int $y, int $half) use ($height): string {
            // les pelures : arcs plus clairs
            if ($y > 4 && abs($x) === intdiv($half, 2) + 1) {
                return 'U';
            }

            return Shape::shade($x, $y, $half, $height, 'p', 'P', 'D');
        });

        return new Layer($rows, 6, 17);
    }

    public function back(): array
    {
        return [new Layer(self::SPROUT, 16, 13)];
    }

    /** Deux rigoles de larmes. */
    public function over(): array
    {
        return [new Layer(['T........T', 'T........T', '.T......T.'], 13, 30)];
    }

    public function face(): array
    {
        return [13, 25];
    }

    public function shoulder(): int
    {
        return 30;
    }

    public function shoulders(): array
    {
        return [9, 27];
    }
}
