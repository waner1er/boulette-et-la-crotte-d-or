<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;
use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/** Les cuisines : faïence blanche, hottes en inox, friteuses qui bouillonnent, grill en flammes, vapeur. */
final readonly class KitchenZone implements ZonePainter
{
    public function __construct(private Ink $ink, private Theme $theme)
    {
    }

    public function backdrop(): string
    {
        $ink = $this->ink;
        $svg = $ink->rect(0, 0, Screen::WIDTH, Screen::GROUND, '#dce8e8');
        for ($y = 0; $y < Screen::GROUND; $y += 6) {
            $svg .= $ink->rect(0, $y, Screen::WIDTH, 1, '#a8bcc0');
        }
        for ($x = 0; $x < Screen::WIDTH; $x += 6) {
            $svg .= $ink->rect($x, 0, 1, Screen::GROUND, '#b8c8cc');
        }
        if ($this->theme->time === 'night') {
            $svg .= $ink->rect(0, 0, Screen::WIDTH, Screen::GROUND, '#1a1040', 'opacity="0.45"');
        }

        return $svg;
    }

    public function far(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 1);
        $svg = '';

        // l'étagère aux casseroles et le rail des commandes
        $svg .= $ink->box(0, 34, Screen::WIDTH, 3, '#8a96b0');
        for ($x = 6; $x < Screen::WIDTH; $x += $random->int(22, 34)) {
            $svg .= $ink->sprite(Sprites::PAN, Sprites::PALETTE, $x, 37);
        }
        $svg .= $ink->box(0, 14, Screen::WIDTH, 2, '#c8d0e0');
        for ($x = 4; $x < Screen::WIDTH; $x += $random->int(14, 26)) {
            $svg .= $ink->sprite(Sprites::TICKET, Sprites::PALETTE, $x, 16);
        }
        $signs = $this->theme->signs ?: ['COMMANDE 42'];
        $svg .= $ink->box(120, 2, 80, 10, '#1a0a24') . $ink->text(160, 10, $signs[0], $this->theme->accent, 6, 'class="pixel-text blink"');

        return $svg;
    }

    public function mid(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 2);
        $svg = '';

        // les hottes
        foreach ([20, 180] as $x) {
            $svg .= $ink->box($x, 52, 120, 14, '#b8c4d8') . $ink->dither($x, 60, 120, 6, '#b8c4d8', '#8a96b0');
            $svg .= $ink->rect($x + 6, 54, 108, 1, '#ffffff');
        }

        // le plan de travail : friteuses, grill, inox
        $svg .= $ink->box(0, 92, Screen::WIDTH, 40, '#a8b4c8');
        $svg .= $ink->rect(0, 92, Screen::WIDTH, 3, '#e0e8f4');
        for ($x = 8; $x < Screen::WIDTH; $x += 40) {
            $svg .= $ink->rect($x, 100, 1, 30, '#6a7490') . $ink->rect($x + 30, 112, 6, 2, '#6a7490');
        }
        foreach ([30, 70] as $x) {
            $svg .= $this->fryer($x, $random);
        }
        $svg .= $this->grill(196, $random);
        foreach ([46, 230] as $x) {
            $svg .= $ink->animated('steam', $random->tenths(30), $ink->dither($x, 70, 14, 18, '#ffffff', '#dce8e8'));
        }

        return $svg;
    }

    public function floor(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 3);
        $svg = $ink->rect(0, Screen::GROUND, Screen::WIDTH, Screen::HEIGHT - Screen::GROUND, '#b83a2a');
        $svg .= $ink->rect(0, Screen::GROUND, Screen::WIDTH, 2, Ink::OUTLINE);
        $y = Screen::GROUND + 2;
        for ($row = 0; $y < Screen::HEIGHT; $row++) {
            $h = 6 + $row * 2;
            $svg .= $ink->rect(0, $y + $h - 1, Screen::WIDTH, 1, '#6a1a10');
            for ($x = ($row % 2) * 10; $x < Screen::WIDTH; $x += 20) {
                $svg .= $ink->rect($x, $y, 1, $h, '#6a1a10') . $ink->rect($x + 2, $y + 1, 6, 1, '#e86a4a');
            }
            $y += $h;
        }
        for ($i = 0; $i < 3; $i++) {
            $svg .= $ink->rect($random->int(10, 290), $random->int(146, 172), $random->int(12, 24), 3, '#ffd86a', 'opacity="0.7"');
        }

        return $svg;
    }

    private function fryer(int $x, SeededRandom $random): string
    {
        $ink = $this->ink;
        $svg = $ink->box($x, 84, 32, 10, '#6a7490') . $ink->rect($x + 2, 86, 28, 6, '#ffb83a');
        for ($i = 0; $i < 4; $i++) {
            $svg .= $ink->animated('bubble', $random->tenths(15), $ink->rect($x + 4 + $i * 7, 86, 2, 2, '#fff4a8'));
        }

        return $svg . $ink->box($x + 8, 74, 16, 8, '#c8d0e0') . $ink->rect($x + 24, 76, 10, 2, '#4a200a');
    }

    private function grill(int $x, SeededRandom $random): string
    {
        $ink = $this->ink;
        $svg = $ink->box($x, 84, 80, 8, '#2a2a3a');
        for ($i = 0; $i < 4; $i++) {
            $flame = $ink->sprite(['.O.', 'OYO', 'OYO'], ['O' => '#ff7a1a', 'Y' => '#ffd23f'], $x + 6 + $i * 18, 78, false);
            $svg .= $ink->animated('flicker', $random->tenths(10), $flame);
            $svg .= $ink->box($x + 4 + $i * 18, 82, 12, 3, '#7a3a1a');
        }

        return $svg;
    }
}
