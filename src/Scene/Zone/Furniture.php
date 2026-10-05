<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;

/** Le mobilier partagé par plusieurs zones : poubelles, plantes, panneaux, lampes... */
final readonly class Furniture
{
    public const TRASH = [
        'GGGGGGGG',
        'GgggggGG',
        '.GGGGGG.',
        '.GgGGgG.',
        '.GgGGgG.',
        '.GgGGgG.',
        '.GgGGgG.',
        '.GgGGgG.',
        '.GGGGGG.',
    ];

    public const CONE = ['...O...', '..OOO..', '..WWW..', '.OOOOO.', '.WWWWW.', 'OOOOOOO', 'ooooooo'];

    public const PLANT = [
        '.V..V..V.',
        'VVv.VvVV.',
        '.VVVVVVv.',
        'VvVVvVVVV',
        '..VVVVv..',
        '..BBBBB..',
        '..BbBBB..',
        '...BBB...',
    ];

    public const PALETTE = [
        'G' => '#3a8a5a', 'g' => '#1f5a3a', 'O' => '#ff7a1a', 'o' => '#a84a08', 'W' => '#ffffff',
        'V' => '#4cc23a', 'v' => '#2a7a26', 'B' => '#c84a2a', 'b' => '#8a2a1a',
    ];

    public function __construct(private Ink $ink)
    {
    }

    public function trash(int $x, int $y): string
    {
        return $this->ink->sprite(self::TRASH, self::PALETTE, $x, $y - count(self::TRASH));
    }

    public function cone(int $x, int $y): string
    {
        return $this->ink->sprite(self::CONE, self::PALETTE, $x, $y - count(self::CONE));
    }

    public function plant(int $x, int $y): string
    {
        return $this->ink->sprite(self::PLANT, self::PALETTE, $x, $y - count(self::PLANT));
    }

    /** Enseigne lumineuse : cadre, fond sombre, texte néon qui grésille. */
    public function neon(int $x, int $y, int $w, string $label, string $color, float $delay): string
    {
        $ink = $this->ink;

        return $ink->box($x, $y, $w, 13, '#1a0a24')
            . $ink->animated('flicker', $delay, $ink->text($x + intdiv($w, 2), $y + 10, $label, $color, 8, 'filter="url(#glow)"'));
    }

    /** Ampoule de guirlande qui clignote. */
    public function bulb(int $x, int $y, string $color, float $delay): string
    {
        return $this->ink->animated('blink', $delay, $this->ink->rect($x, $y, 2, 2, $color));
    }
}
