<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** PETIT POIS COMMANDO : une boule verte casquée, qui mitraille des petits pois de loin. */
final class PetitPois extends VeggieDesign
{
    private const HELMET = [
        '....HHHHHHHH....',
        '..HHHHhHHHHHHH..',
        '.HHHhHHHHHhHHHH.',
        'HHHHHHHHHHHHHHHH',
        'JJJJJJJJJJJJJJJJ',
    ];

    public function palette(): array
    {
        return [
            'G' => '#6ad04a', 'g' => '#b4f48a', 'D' => '#2f8a2a', 'H' => '#5a6a2a', 'h' => '#8a9a4a', 'J' => '#3a4418',
            'L' => '#6ad04a', 'l' => '#2f8a2a', 'k' => '#1a4a18',
        ];
    }

    public function body(): Layer
    {
        $halves = Shape::disc(9);
        $rows = Shape::rows($halves, 20, fn(int $x, int $y, int $half) => Shape::shade($x, $y, $half, 18, 'g', 'G', 'D'));

        return new Layer($rows, 8, 20);
    }

    public function over(): array
    {
        return [new Layer(self::HELMET, 10, 17)];
    }

    public function face(): array
    {
        return [13, 23];
    }

    public function shoulder(): int
    {
        return 31;
    }

    public function shoulders(): array
    {
        return [10, 26];
    }
}
