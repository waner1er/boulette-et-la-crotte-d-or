<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;

/** Accessoires partagés par les boss : couronne, lunettes de savant. */
final class Crown
{
    /** Couleurs des accessoires (or, rubis, verre). */
    public const PALETTE = ['J' => '#ffd23f', 'j' => '#c08a1a', 'Z' => '#ff2a4a', 'z' => '#d8f4ff', 'n' => '#4a5a8a'];

    public static function at(int $x, int $y): Layer
    {
        return new Layer([
            'J..J..J..J',
            'JJ.JJ.JJ.J',
            'JJJJJJJJJJ',
            'JZJjJZJjJZ',
            'jjjjjjjjjj',
        ], $x, $y);
    }
}
