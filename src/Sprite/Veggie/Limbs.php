<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;
use Boulette\PixelArt\Line;

/**
 * Bras et jambes des légumes mutants, comme dans les dessins animés des années 90 :
 * bras en tige, gros gants blancs, baskets. Les membres du fond sont un ton plus sombres.
 */
final class Limbs
{
    /** Position de la main par rapport à l'épaule, pour chaque pose. */
    public const HANDS = [
        'down' => [1, 9],
        'front' => [4, 8],
        'back' => [-3, 8],
        'raise' => [3, -9],
        'punch' => [12, 0],
        'reach' => [10, 2],
    ];

    private const GLOVE = [
        '.WW.',
        'WWWw',
        'WWww',
        '.ww.',
    ];

    /** Décalage du pied et raccourcissement de la jambe. */
    private const STEPS = ['stand' => [0, 0], 'forward' => [3, 0], 'back' => [-3, 0], 'lifted' => [1, 2]];

    private const FAR = ['L' => 'l', 'l' => 'k', 'W' => 'w', 'w' => 'v', 'O' => 'o', 'o' => 'k'];

    /** @return list<Layer> le bras puis le gant */
    public static function arm(int $sx, int $sy, string $pose, bool $far): array
    {
        [$dx, $dy] = self::HANDS[$pose];
        $layers = [
            Line::between($sx, $sy, $sx + $dx, $sy + $dy, 'L', 'l'),
            new Layer(self::GLOVE, $sx + $dx - 1, $sy + $dy - 1),
        ];

        return $far ? array_map(fn(Layer $l) => $l->recolor(self::FAR), $layers) : $layers;
    }

    public static function leg(int $x, int $top, int $bottom, string $step, bool $far): Layer
    {
        [$slant, $shorter] = self::STEPS[$step];
        $length = $bottom - $top + 1 - $shorter;
        $rows = [];
        $margin = 5;
        for ($i = 0; $i < $length - 2; $i++) {
            $offset = (int) round($slant * $i / max(1, $length - 3));
            $rows[] = str_repeat('.', $margin + $offset) . 'LLl' . str_repeat('.', $margin - $offset + 3);
        }
        $rows[] = str_repeat('.', $margin + $slant - 1) . 'OOOOOo' . str_repeat('.', $margin - $slant);
        $rows[] = str_repeat('.', $margin + $slant - 1) . 'XXXXXX' . str_repeat('.', $margin - $slant);
        $layer = new Layer($rows, $x - $margin, $top);

        return $far ? $layer->recolor(self::FAR) : $layer;
    }
}
