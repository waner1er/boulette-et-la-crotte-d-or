<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;
use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/**
 * Le labo secret du Professeur Navet, sous le fast-food : tuyaux, écrans radar,
 * cuves vertes où flottent des légumes mutants, sol à rayures de danger.
 */
final readonly class LabZone implements ZonePainter
{
    public function __construct(private Ink $ink, private Theme $theme)
    {
    }

    public function backdrop(): string
    {
        $ink = $this->ink;
        $svg = $ink->bands(0, 0, Screen::WIDTH, Screen::GROUND, ['#050a08', '#081410', '#0c1e18', '#102a20', '#14362a']);
        if ($this->theme->alarm) {
            $svg .= $ink->rect(0, 0, Screen::WIDTH, Screen::GROUND, '#ff0030', 'class="alarm" opacity="0.12"');
        }

        return $svg;
    }

    public function far(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 1);
        $svg = '';

        // tuyauterie
        foreach ([12, 30] as $i => $y) {
            $svg .= $ink->box(0, $y, Screen::WIDTH, 5, $i ? '#4a5a6a' : '#6a4a2a');
            $svg .= $ink->rect(0, $y + 1, Screen::WIDTH, 1, $i ? '#8a9aaa' : '#a8803a');
            for ($x = $random->int(0, 40); $x < Screen::WIDTH; $x += $random->int(50, 90)) {
                $svg .= $ink->box($x, $y - 2, 4, 9, '#2a3038');
            }
        }

        // écrans radar à légumes
        foreach ([40, 200] as $x) {
            $svg .= $ink->box($x, 46, 48, 32, '#2a3038') . $ink->rect($x + 3, 49, 42, 26, '#042a10');
            for ($y = 51; $y < 75; $y += 4) {
                $svg .= $ink->rect($x + 3, $y, 42, 1, '#0a4a1a');
            }
            $svg .= $ink->animated('blink', $random->tenths(20), $ink->rect($x + $random->int(8, 36), $random->int(52, 70), 3, 3, '#5aff5a'));
            $svg .= $ink->text($x + 24, 60, 'RADAR', '#5aff5a', 6, 'opacity="0.8"');
        }
        $signs = $this->theme->signs ?: ['DANGER'];
        $svg .= $ink->box(124, 50, 60, 12, '#ffd23f') . $ink->text(154, 59, $signs[0], Ink::OUTLINE, 6);

        return $svg;
    }

    public function mid(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 2);
        $svg = '';

        foreach ([16, 120, 230] as $x) {
            $svg .= $this->tank($x, $random);
        }
        // pupitre de commande et ses voyants
        $svg .= $ink->box(70, 104, 40, 28, '#3a4450') . $ink->rect(70, 104, 40, 4, '#5a6a7a');
        for ($i = 0; $i < 6; $i++) {
            $color = ['#ff2a4a', '#5aff5a', '#ffd23f'][$i % 3];
            $svg .= $ink->animated('blink', $random->tenths(12), $ink->rect(74 + $i * 6, 112, 3, 3, $color));
        }
        $svg .= $ink->rect(180, 104, 40, 28, '#3a4450') . $ink->text(200, 120, '☢', '#ffd23f', 12);

        return $svg;
    }

    public function floor(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 3);
        $svg = $ink->rect(0, Screen::GROUND, Screen::WIDTH, 6, '#ffd23f');
        for ($x = 0; $x < Screen::WIDTH; $x += 8) {
            $svg .= $ink->rect($x, Screen::GROUND, 4, 6, Ink::OUTLINE);
        }
        $svg .= $ink->dither(0, Screen::GROUND + 6, Screen::WIDTH, Screen::HEIGHT - Screen::GROUND, '#2a3440', '#222a34');
        for ($y = Screen::GROUND + 10; $y < Screen::HEIGHT; $y += 8) {
            $svg .= $ink->rect(0, $y, Screen::WIDTH, 1, '#3a4656');
        }
        for ($i = 0; $i < 3; $i++) {
            $svg .= $ink->animated('bob', $random->tenths(20), $ink->rect($random->int(0, 290), $random->int(146, 172), $random->int(14, 28), 3, '#7aff3a'));
        }

        return $svg;
    }

    /** Une cuve de verre pleine de jus vert, un légume mutant qui flotte dedans, des bulles. */
    private function tank(int $x, SeededRandom $random): string
    {
        $ink = $this->ink;
        $svg = $ink->box($x, 44, 40, 8, '#5a6a7a') . $ink->box($x, 116, 40, 16, '#5a6a7a');
        $svg .= $ink->box($x + 2, 52, 36, 64, '#1aa84a');
        $svg .= $ink->dither($x + 2, 52, 36, 10, '#5ae87a', '#1aa84a');
        $svg .= $ink->animated('bob', $random->tenths(30), $ink->sprite(Sprites::VEGGIE_IN_TANK, Sprites::PALETTE, $x + 17, 76));
        for ($i = 0; $i < 3; $i++) {
            $svg .= $ink->animated('rise', $random->tenths(30), $ink->rect($x + 6 + $i * 11, 108, 2, 2, '#c8ffd8'));
        }

        return $svg . $ink->rect($x + 5, 54, 2, 58, '#ffffff', 'opacity="0.45"');
    }
}
