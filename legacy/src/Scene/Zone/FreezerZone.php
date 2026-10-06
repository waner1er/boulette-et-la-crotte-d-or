<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;
use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/** La chambre froide : murs givrés, étagères de surgelés, stalactites, légumes pris dans la glace. */
final readonly class FreezerZone implements ZonePainter
{
    private const FROZEN = ['courgette' => '#2f9a34', 'carotte' => '#ff8a1e', 'tomate' => '#ff3a2a', 'radis' => '#ff3a8a'];

    public function __construct(private Ink $ink, private Theme $theme)
    {
    }

    public function backdrop(): string
    {
        $ink = $this->ink;
        $colors = $this->theme->time === 'night'
            ? ['#0a1a3a', '#10264e', '#163262', '#1c3e76']
            : ['#5aa4d8', '#7ab8e4', '#9accf0', '#bce0fa'];
        $svg = $ink->bands(0, 0, Screen::WIDTH, Screen::GROUND, $colors);
        for ($x = 0; $x < Screen::WIDTH; $x += 40) {
            $svg .= $ink->rect($x, 0, 1, Screen::GROUND, '#ffffff', 'opacity="0.35"');
        }

        return $svg;
    }

    public function far(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 1);
        $svg = '';
        $signs = $this->theme->signs ?: ['NUGGETS', 'FRITES'];

        // les étagères de surgelés
        foreach ([20, 50] as $y) {
            $svg .= $ink->box(0, $y + 6, Screen::WIDTH, 2, '#c8d0e0');
            for ($x = 4; $x < Screen::WIDTH - 12; $x += $random->int(12, 18)) {
                $svg .= $ink->sprite(Sprites::FROZEN_BOX, Sprites::PALETTE, $x, $y);
            }
        }
        $svg .= $ink->shadowText(80, 16, $signs[0], '#ffffff', 6) . $ink->shadowText(240, 16, $signs[1 % count($signs)], '#ffffff', 6);

        return $svg;
    }

    public function mid(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 2);
        $svg = '';

        // stalactites
        for ($x = 2; $x < Screen::WIDTH; $x += $random->int(6, 14)) {
            $svg .= $ink->sprite(Sprites::ICICLE, Sprites::PALETTE, $x, 0, false);
        }

        // blocs de glace avec un légume prisonnier
        $x = 10;
        foreach (self::FROZEN as $color) {
            $w = $random->int(30, 40);
            $h = $random->int(26, 36);
            $svg .= $ink->box($x, Screen::GROUND - $h, $w, $h, '#bce8ff');
            $svg .= $ink->dither($x, Screen::GROUND - $h, $w, 4, '#ffffff', '#bce8ff');
            $svg .= $ink->rect($x + intdiv($w, 2) - 4, Screen::GROUND - $h + 8, 8, $h - 14, $color, 'opacity="0.55"');
            $svg .= $ink->rect($x + 3, Screen::GROUND - $h + 4, 2, $h - 10, '#ffffff', 'opacity="0.8"');
            $x += $w + $random->int(36, 46);
        }

        // le rideau à lanières de la porte
        for ($i = 0; $i < 6; $i++) {
            $svg .= $ink->rect(278 + $i * 6, 20, 5, Screen::GROUND - 20, '#d8f4ff', 'opacity="0.5"');
        }

        return $svg;
    }

    public function floor(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 3);
        $svg = $ink->rect(0, Screen::GROUND, Screen::WIDTH, 2, Ink::OUTLINE);
        $svg .= $ink->dither(0, Screen::GROUND + 2, Screen::WIDTH, Screen::HEIGHT - Screen::GROUND, '#8a9ab8', '#7a8aa8');
        // la tôle larmée
        for ($y = Screen::GROUND + 5; $y < Screen::HEIGHT; $y += 6) {
            for ($x = intdiv($y, 6) % 2 ? 0 : 4; $x < Screen::WIDTH; $x += 8) {
                $svg .= $ink->rect($x, $y, 3, 1, '#c8d4ea');
            }
        }
        for ($i = 0; $i < 4; $i++) {
            $svg .= $ink->rect($random->int(0, 280), $random->int(140, 172), $random->int(18, 36), 4, '#ffffff', 'opacity="0.8"');
        }

        return $svg;
    }
}
