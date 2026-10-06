<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Layer;

/**
 * Un légume mutant : son corps (qui est aussi sa tête), où placer le visage, ses épaules
 * et ses accessoires. Bras, gants, jambes et baskets sont communs (Limbs).
 */
abstract class VeggieDesign
{
    /** Couleurs communes : contour, gants, bouche, baskets. */
    public const BASE = [
        'K' => '#0c0610', 'B' => '#120a14', 'W' => '#ffffff', 'w' => '#aeb8d0', 'v' => '#6a7490',
        'E' => '#120a14', 'Q' => '#3a0618', 'R' => '#ff4a6a',
        'O' => '#ff2a4a', 'o' => '#a8102a', 'X' => '#ffffff',
    ];

    /** @return array<string, string> couleurs du légume, dont L/l/k (bras et jambes) */
    abstract public function palette(): array;

    abstract public function body(): Layer;

    /** @return array{int, int} coin haut-gauche du visage */
    abstract public function face(): array;

    /** Hauteur des épaules (y d'accroche des bras). */
    abstract public function shoulder(): int;

    /** @return array{int, int} x des épaules [fond, devant] */
    public function shoulders(): array
    {
        return [12, 24];
    }

    /** Un zombie marche les bras tendus et a l'œil qui pend. */
    public function zombie(): bool
    {
        return false;
    }

    /** @return list<Layer> par-dessus le corps (couronne, casque, lunettes...) */
    public function over(): array
    {
        return [];
    }

    /** @return list<Layer> derrière le corps (cape, fanes...) */
    public function back(): array
    {
        return [];
    }

    public function width(): int
    {
        return 36;
    }

    public function height(): int
    {
        return 46;
    }
}
