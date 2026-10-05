<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Layer;

/**
 * Une patte de chien, dessinée selon son angle : 3 px de large, un coussinet au bout.
 * Les pattes du fond sont un ton plus sombres (F devient f, f devient h).
 */
final class Leg
{
    /** Décalage du bout de la patte (px) et raccourcissement, par position. */
    private const PHASES = [
        'stand' => [0, 0],
        'forward' => [3, 0],
        'back' => [-3, 0],
        'lifted' => [1, 2],
        'tuckFront' => [3, 3],
        'tuckBack' => [-3, 3],
        'reachFront' => [6, 1],
        'reachBack' => [-6, 1],
    ];

    private const FAR = ['F' => 'f', 'f' => 'h', 'G' => 'F'];

    public static function layer(int $x, int $top, int $length, string $phase, bool $far = false): Layer
    {
        [$slant, $shorter] = self::PHASES[$phase];
        $length -= $shorter;
        $rows = [];
        $margin = 7;
        for ($i = 0; $i < $length - 2; $i++) {
            $offset = (int) round($slant * $i / max(1, $length - 3));
            $rows[] = str_repeat('.', $margin + $offset) . 'FFf' . str_repeat('.', $margin - $offset + 2);
        }
        $offset = $slant;
        $rows[] = str_repeat('.', $margin + $offset) . 'FFFf' . str_repeat('.', $margin - $offset + 1);
        $rows[] = str_repeat('.', $margin + $offset) . 'GGFf' . str_repeat('.', $margin - $offset + 1);

        $layer = new Layer($rows, $x - $margin, $top);

        return $far ? $layer->recolor(self::FAR) : $layer;
    }
}
