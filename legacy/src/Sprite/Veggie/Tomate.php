<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** TOMATE KAMIKAZE : elle fonce sur toi et éclate en sauce. Mieux vaut la décomposer de loin. */
final class Tomate extends VeggieDesign
{
    private const STALK = ['.V.V.V.', 'VVVVVVV', '.VvVvV.', '...v...'];

    public function palette(): array
    {
        return [
            'T' => '#ff3a2a', 't' => '#ff9a8a', 'D' => '#b01a1a', 'V' => '#4cc23a', 'v' => '#2a7a26',
            'L' => '#4cc23a', 'l' => '#2a7a26', 'k' => '#1a4a18',
        ];
    }

    public function body(): Layer
    {
        $rows = Shape::rows(Shape::disc(10), 22, fn(int $x, int $y, int $half) => Shape::shade($x, $y, $half, 20, 't', 'T', 'D'));

        return new Layer($rows, 7, 17);
    }

    public function over(): array
    {
        return [new Layer(self::STALK, 15, 15)];
    }

    public function face(): array
    {
        return [13, 22];
    }

    public function shoulder(): int
    {
        return 29;
    }

    public function shoulders(): array
    {
        return [9, 27];
    }
}
