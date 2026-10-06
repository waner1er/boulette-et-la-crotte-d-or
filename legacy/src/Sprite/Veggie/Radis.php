<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** RADIS FURIEUX : petit, rose, nerveux. Il se rue sur toi sans prévenir. */
final class Radis extends VeggieDesign
{
    private const LEAVES = ['V..V..V', '.VvVvV.', '..VVV..'];

    public function palette(): array
    {
        return [
            'P' => '#ff3a8a', 'p' => '#ff9ac8', 'D' => '#b01a5a', 'U' => '#ffffff', 'V' => '#5ad04a', 'v' => '#2a8a2a',
            'L' => '#ff9ac8', 'l' => '#b01a5a', 'k' => '#5a0a2a',
        ];
    }

    public function body(): Layer
    {
        $halves = [...Shape::disc(8), 3, 2, 1, 1];
        $rows = Shape::rows($halves, 18, function (int $x, int $y, int $half): string {
            if ($y >= 12) {
                return 'U';
            }

            return Shape::shade($x, $y, $half, 13, 'p', 'P', 'D');
        });

        return new Layer($rows, 9, 22);
    }

    public function back(): array
    {
        return [new Layer(self::LEAVES, 15, 19)];
    }

    public function face(): array
    {
        return [13, 25];
    }

    public function shoulder(): int
    {
        return 31;
    }

    public function shoulders(): array
    {
        return [11, 25];
    }
}
