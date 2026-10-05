<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;
use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/** Le parking et le drive du MÉGA MIAM : ciel « copper », immeubles au loin, façade du resto, bitume. */
final readonly class ParkingZone implements ZonePainter
{
    private const SKIES = [
        'day' => ['#2a5ad0', '#3a7aec', '#5a9cff', '#86bcff', '#b8dcff', '#e0f0ff'],
        'sunset' => ['#2a1a5a', '#5a2a8a', '#a8388a', '#e85a6a', '#ff8a4a', '#ffc85a'],
        'night' => ['#05051a', '#0a0c28', '#10163a', '#18224e', '#223062', '#2c3c76'],
    ];

    private const CITY = ['day' => '#7a9ad8', 'sunset' => '#6a3a7a', 'night' => '#141a3a'];

    private Furniture $furniture;

    public function __construct(private Ink $ink, private Theme $theme)
    {
        $this->furniture = new Furniture($ink);
    }

    public function backdrop(): string
    {
        $ink = $this->ink;
        $time = $this->theme->time;
        $random = new SeededRandom($this->theme->seed);
        $svg = $ink->bands(0, 0, Screen::WIDTH, Screen::GROUND, self::SKIES[$time]);

        if ($time === 'night') {
            for ($i = 0; $i < 50; $i++) {
                $svg .= $ink->animated('twinkle', $random->tenths(40), $ink->rect($random->int(0, 319), $random->int(2, 90), 1, 1, '#ffffff'));
            }
            $svg .= $this->disc(250, 30, 12, '#f4f0d8', '#c8c4a8');
        } else {
            $svg .= $this->disc(70, $time === 'day' ? 28 : 84, $time === 'day' ? 14 : 20, '#fff8a0', '#ffd23f');
        }

        return $svg;
    }

    public function far(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 1);
        $city = self::CITY[$this->theme->time];
        $svg = '';

        if ($this->theme->time !== 'night') {
            for ($i = 0; $i < 3; $i++) {
                $svg .= $ink->sprite(Sprites::CLOUD, Sprites::PALETTE, $i * 110 + $random->int(0, 50), $random->int(8, 40), false);
            }
        }
        for ($x = 0; $x < Screen::WIDTH;) {
            $w = $random->int(18, 34);
            $h = $random->int(24, 62);
            $svg .= $ink->rect($x, Screen::GROUND - 40 - $h, $w, $h + 40, $city);
            for ($wy = Screen::GROUND - 36 - $h; $wy < Screen::GROUND - 44; $wy += 6) {
                for ($wx = $x + 3; $wx < $x + $w - 3; $wx += 5) {
                    $lit = $this->theme->time === 'night' ? $random->oneIn(3) : $random->oneIn(9);
                    if ($lit) {
                        $svg .= $ink->rect($wx, $wy, 2, 3, $this->theme->time === 'night' ? '#ffd86a' : '#d8e8ff');
                    }
                }
            }
            $x += $w + $random->int(0, 4);
        }

        return $svg;
    }

    public function mid(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 2);
        $top = 64;
        $night = $this->theme->time === 'night';

        // la façade : mur crème, frise rouge et blanche, grandes vitres
        $svg = $ink->rect(0, $top, Screen::WIDTH, Screen::GROUND - $top, '#f4d8a8');
        $svg .= $ink->dither(0, Screen::GROUND - 22, Screen::WIDTH, 22, '#f4d8a8', '#d8a878');
        $svg .= $ink->rect(0, $top - 12, Screen::WIDTH, 12, '#ff2a4a');
        for ($x = 0; $x < Screen::WIDTH; $x += 16) {
            $svg .= $ink->rect($x, $top - 12, 8, 12, '#ffffff');
        }
        $svg .= $ink->rect(0, $top - 14, Screen::WIDTH, 2, Ink::OUTLINE) . $ink->rect(0, $top, Screen::WIDTH, 2, Ink::OUTLINE);
        $svg .= $ink->rect(0, $top + 2, Screen::WIDTH, 3, '#ffd23f');

        foreach ([[16, 70], [104, 70], [234, 70]] as [$x, $w]) {
            $svg .= $ink->box($x, $top + 14, $w, 40, $night ? '#ffe9a8' : '#5ab8e8');
            $svg .= $ink->dither($x, $top + 14, $w, 40, $night ? '#ffe9a8' : '#5ab8e8', $night ? '#ffd86a' : '#86d4ff');
            $svg .= $ink->rect($x + 6, $top + 18, 3, 30, '#ffffff', 'opacity="0.6"') . $ink->rect($x + 12, $top + 18, 2, 30, '#ffffff', 'opacity="0.4"');
            $svg .= $ink->rect($x + intdiv($w, 2), $top + 14, 2, 40, Ink::OUTLINE);
        }

        // le logo géant et l'enseigne
        $svg .= $ink->sprite(Sprites::BURGER, Sprites::PALETTE, 186, $top - 36);
        $svg .= $ink->sprite(Sprites::BURGER, Sprites::PALETTE, 199, $top - 36);
        $signs = $this->theme->signs ?: ['MÉGA MIAM'];
        $svg .= $this->furniture->neon(194, $top + 18, 34, 'DRIVE', $this->theme->accent, $random->tenths(30));
        $svg .= $ink->shadowText(196, $top - 18, $signs[0], '#ffd23f', 8, 'class="pixel-text flicker"');

        // la haie au pied de la façade
        for ($x = 0; $x < Screen::WIDTH; $x += 8) {
            $svg .= $ink->rect($x, Screen::GROUND - 8, 9, 8, Ink::OUTLINE) . $ink->rect($x + 1, Screen::GROUND - 7, 7, 7, '#3aa04a');
            $svg .= $ink->rect($x + 2, Screen::GROUND - 6, 2, 2, '#7ad86a');
        }

        return $svg;
    }

    public function floor(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 3);
        $ground = Screen::GROUND;

        $svg = $ink->rect(0, $ground, Screen::WIDTH, 7, '#b8b0a8') . $ink->dither(0, $ground + 4, Screen::WIDTH, 3, '#b8b0a8', '#8a8078');
        $svg .= $ink->rect(0, $ground + 7, Screen::WIDTH, 2, Ink::OUTLINE);
        $svg .= $ink->dither(0, $ground + 9, Screen::WIDTH, Screen::HEIGHT - $ground - 9, '#3a3a4a', '#30303e');
        for ($x = 10; $x < Screen::WIDTH; $x += 64) {
            for ($y = $ground + 12; $y < Screen::HEIGHT; $y++) {
                $svg .= $ink->rect($x + intdiv($y - $ground - 12, 3), $y, 2, 1, '#f4f0d8');
            }
        }
        if ($this->theme->rain) {
            for ($i = 0; $i < 6; $i++) {
                $svg .= $ink->rect($random->int(0, 290), $random->int($ground + 14, 172), $random->int(16, 30), 2, '#6a7aa8');
            }
        }
        $svg .= $this->furniture->cone($random->int(20, 120), $ground + 6);
        $svg .= $this->furniture->trash($random->int(180, 290), $ground + 6);

        return $svg;
    }

    /** Soleil ou lune : un disque au bord tramé. */
    private function disc(int $cx, int $cy, int $radius, string $color, string $edge): string
    {
        $svg = '';
        for ($dy = -$radius; $dy <= $radius; $dy++) {
            $half = (int) round(sqrt($radius * $radius - $dy * $dy));
            $svg .= $this->ink->rect($cx - $half, $cy + $dy, $half * 2, 1, abs($dy) > $radius * 0.7 ? $edge : $color);
        }

        return $svg;
    }
}
