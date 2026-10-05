<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Veggie\Shape;
use Boulette\Sprite\Veggie\VeggieDesign;

/** CHOU-FLEUR DES GLACES : il règne sur la chambre froide, givré de partout, et lance des glaçons. */
final class ChouFleur extends VeggieDesign
{
    public function palette(): array
    {
        return [
            'U' => '#f4f0e0', 'u' => '#ffffff', 'D' => '#b8b8c8', 'A' => '#8adcff', 'V' => '#4cae4a', 'v' => '#2a6a2a',
            'L' => '#8adcff', 'l' => '#4a9ac8', 'k' => '#1a4a6a',
        ];
    }

    public function body(): Layer
    {
        $halves = [5, 8, 10, 11, 12, 13, 13, 14, 14, 14, 14, 14, 13, 13, 12, 11, 10, 9, 9, 8, 8, 7, 7, 6];
        $rows = Shape::rows($halves, 30, function (int $x, int $y, int $half): string {
            if ($y >= 13) {
                return $x >= $half - 2 ? 'v' : 'V'; // les feuilles qui enveloppent le chou
            }
            $bump = (($x + 30) * 5 + $y * 3) % 7;
            if ($x >= $half - 2 || $y === 12) {
                return 'D';
            }

            return $bump === 0 ? 'A' : ($bump < 3 ? 'u' : 'U');
        });

        return new Layer($rows, 3, 9);
    }

    public function over(): array
    {
        return [new Layer(['A.A..A.A..A.A..A', 'A.A..A.A..A.A..A', '..A....A....A...'], 10, 22)];
    }

    public function face(): array
    {
        return [13, 12];
    }

    public function shoulder(): int
    {
        return 26;
    }

    public function shoulders(): array
    {
        return [7, 29];
    }
}
