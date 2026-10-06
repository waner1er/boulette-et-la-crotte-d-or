<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/** BROCOLI ZOMBIE : une touffe de fleurettes moisies sur un tronc pâle, les bras tendus. Lent mais costaud. */
class Brocoli extends VeggieDesign
{
    public function palette(): array
    {
        return [
            'G' => '#4c8f3a', 'g' => '#8ccf5a', 'D' => '#2a5a26', 'T' => '#b8d88a', 't' => '#7fa05a', 'P' => '#a06ad8',
            'L' => '#9cc070', 'l' => '#6a8a48', 'k' => '#3a5028',
        ];
    }

    public function zombie(): bool
    {
        return true;
    }

    public function body(): Layer
    {
        $top = [4, 6, 8, 9, 10, 10, 11, 11, 11, 11, 10, 10, 9];
        $stem = array_fill(0, 18, 6);
        $halves = [...$top, ...$stem];
        $crown = count($top);
        $rows = Shape::rows($halves, 24, function (int $x, int $y, int $half) use ($crown): string {
            if ($y >= $crown) {
                return $x >= $half - 2 ? 't' : 'T';
            }
            // fleurettes : des bosses claires et sombres en damier irrégulier
            $bump = (($x + 20) * 7 + $y * 5) % 9;
            if ($x >= $half - 2 || $y === $crown - 1) {
                return 'D';
            }
            if ($bump === 0 && $y > 2) {
                return 'P'; // la moisissure
            }

            return $bump < 3 ? 'g' : ($bump < 6 ? 'G' : 'D');
        });

        return new Layer($rows, 6, 8);
    }

    public function face(): array
    {
        return [13, 19];
    }

    public function shoulder(): int
    {
        return 26;
    }
}
