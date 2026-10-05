<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** COURGETTE MUTANTE : un long cylindre rayé, des taches radioactives. Le gros des troupes. */
class Courgette extends VeggieDesign
{
    public function palette(): array
    {
        return [
            'G' => '#2f9a34', 'g' => '#7ed85a', 'D' => '#17602a', 'Y' => '#d4ff2a', 's' => '#9a7a3a', 'S' => '#6a5020',
            'L' => '#2f9a34', 'l' => '#17602a', 'k' => '#0c3a18',
        ];
    }

    public function body(): Layer
    {
        $halves = [1, 1, 2, 4, 5, 6, ...array_fill(0, 22, 7), 6, 6, 5, 3];
        $height = count($halves);
        $rows = Shape::rows($halves, 16, function (int $x, int $y, int $half) use ($height): string {
            if ($y < 3) {
                return $x < 0 ? 's' : 'S';
            }
            if (in_array([$x, $y], [[-3, 22], [-2, 22], [2, 8], [3, 27], [-4, 12]], true)) {
                return 'Y';
            }
            $shade = Shape::shade($x, $y, $half, $height, 'g', 'G', 'D');

            return $shade === 'G' && ($x === -2 || $x === 3) ? 'g' : $shade;
        });

        return new Layer($rows, 10, 7);
    }

    public function face(): array
    {
        return [13, 13];
    }

    public function shoulder(): int
    {
        return 24;
    }
}
