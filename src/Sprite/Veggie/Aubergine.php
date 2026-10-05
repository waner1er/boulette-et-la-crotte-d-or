<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** AUBERGINE CATCHEUSE : une montagne violette en slip de catch. Lente, mais ses coups font mal. */
final class Aubergine extends VeggieDesign
{
    private const HAT = ['...VVVV...', '.VVVVVVVV.', 'VVvVVvVVvV', '.v..v...v.'];

    public function palette(): array
    {
        return [
            'A' => '#5a1a8a', 'a' => '#9a5ad0', 'D' => '#2e0a4e', 'V' => '#4cae3a', 'v' => '#2a6a26', 'Z' => '#ffd23f',
            'L' => '#5a1a8a', 'l' => '#2e0a4e', 'k' => '#1a0630',
        ];
    }

    public function body(): Layer
    {
        $halves = [3, 4, 5, 6, 6, 7, 7, 7, 8, 9, 10, 11, 11, 12, 12, 12, 12, 12, 11, 11, 10, 9, 7, 5];
        $height = count($halves);
        $rows = Shape::rows($halves, 26, function (int $x, int $y, int $half) use ($height): string {
            if ($y === 19 || $y === 20) {
                return 'Z'; // la ceinture de championne
            }

            return Shape::shade($x, $y, $half, $height, 'a', 'A', 'D');
        });

        return new Layer($rows, 5, 15);
    }

    public function over(): array
    {
        return [new Layer(self::HAT, 13, 13)];
    }

    public function face(): array
    {
        return [13, 19];
    }

    public function shoulder(): int
    {
        return 28;
    }

    public function shoulders(): array
    {
        return [7, 29];
    }
}
