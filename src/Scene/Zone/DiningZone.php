<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

use Boulette\Scene\Ink;
use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/** La salle du MÉGA MIAM : murs jaunes, frise de carrelage, menus lumineux, banquettes rouges, coin anniversaire. */
final readonly class DiningZone implements ZonePainter
{
    private const WALLS = [
        'day' => ['#ffd86a', '#ffc84a'],
        'sunset' => ['#ffb86a', '#ff9a4a'],
        'night' => ['#c8a04a', '#a8803a'],
    ];

    private Furniture $furniture;

    public function __construct(private Ink $ink, private Theme $theme)
    {
        $this->furniture = new Furniture($ink);
    }

    public function backdrop(): string
    {
        $ink = $this->ink;
        [$wall, $shade] = self::WALLS[$this->theme->time];
        $svg = $ink->rect(0, 0, Screen::WIDTH, Screen::GROUND, $wall);
        $svg .= $ink->dither(0, 0, Screen::WIDTH, 10, $shade, $wall);
        $svg .= $ink->rect(0, 10, Screen::WIDTH, 2, $shade);

        return $svg;
    }

    public function far(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 1);
        $signs = $this->theme->signs ?: ['MENU MIAM', 'NUGGETS', 'SUNDAE'];
        $svg = '';

        // les menus lumineux au-dessus du comptoir
        foreach ([8, 116, 224] as $i => $x) {
            $svg .= $ink->box($x, 18, 88, 36, '#2a1a3a');
            $svg .= $ink->rect($x, 18, 88, 9, $this->theme->accent);
            $svg .= $ink->shadowText($x + 44, 26, $signs[$i % count($signs)], '#ffffff', 8);
            $svg .= $ink->sprite(Sprites::BURGER, Sprites::PALETTE, $x + 6, 31);
            $svg .= $ink->sprite(Sprites::FRIES, Sprites::PALETTE, $x + 26, 33);
            $svg .= $ink->sprite(Sprites::SODA, Sprites::PALETTE, $x + 40, 33);
            $svg .= $ink->text($x + 70, 42, $random->int(2, 9) . '€' . $random->int(10, 99), '#ffd23f', 8);
        }

        return $svg;
    }

    public function mid(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 2);
        $svg = '';

        // carrelage rouge et blanc à mi-hauteur
        for ($x = 0; $x < Screen::WIDTH; $x += 8) {
            for ($y = 92; $y < Screen::GROUND; $y += 8) {
                $svg .= $ink->rect($x, $y, 8, 8, intdiv($x + $y, 8) % 2 ? '#ff2a4a' : '#ffffff');
            }
        }
        $svg .= $ink->rect(0, 89, Screen::WIDTH, 3, $this->theme->accent) . $ink->rect(0, 88, Screen::WIDTH, 1, Ink::OUTLINE);

        // fenêtres sur le parking, posters
        $svg .= $ink->box(14, 60, 46, 26, '#86c4ff') . $ink->dither(14, 74, 46, 12, '#86c4ff', '#5aa4ff');
        $svg .= $ink->rect(36, 60, 2, 26, Ink::OUTLINE) . $ink->rect(18, 63, 2, 18, '#ffffff', 'opacity="0.7"');
        $svg .= $this->poster(214, 58, 'MENU', 'ENFANT');
        $svg .= $this->poster(258, 58, 'JOUET', 'OFFERT');

        // banquettes et tables
        foreach ([70, 150] as $x) {
            $svg .= $this->booth($x, $random);
        }

        // le coin anniversaire : ballons et piscine à balles
        $svg .= $this->balloons(232, 74, $random);
        $svg .= $ink->box(226, 112, 64, 20, '#3a86f0');
        for ($i = 0; $i < 70; $i++) {
            $color = Sprites::PALETTE[$random->pick(Sprites::BALLS)];
            $svg .= $ink->rect(228 + $random->int(0, 58), 106 + $random->int(0, 12), 3, 3, $color);
        }
        $svg .= $ink->rect(226, 118, 64, 2, '#ffd23f');
        $svg .= $this->furniture->plant(196, Screen::GROUND);

        return $svg;
    }

    public function floor(): string
    {
        $ink = $this->ink;
        $random = new SeededRandom($this->theme->seed + 3);
        $svg = $ink->rect(0, Screen::GROUND, Screen::WIDTH, 2, Ink::OUTLINE);

        // damier noir et blanc : les rangées grandissent vers le bas (perspective)
        $y = Screen::GROUND + 2;
        for ($row = 0; $y < Screen::HEIGHT; $row++) {
            $h = 5 + $row * 2;
            for ($x = -16; $x < Screen::WIDTH; $x += 16) {
                $shift = $row % 2 ? 8 : 0;
                $svg .= $ink->rect($x + $shift, $y, 8, $h, '#f4f0e8') . $ink->rect($x + $shift + 8, $y, 8, $h, '#26222e');
            }
            $svg .= $ink->rect(0, $y, Screen::WIDTH, 1, '#ffffff', 'opacity="0.35"');
            $y += $h;
        }
        // une frite écrasée, une serviette, un soda renversé
        $svg .= $ink->rect($random->int(20, 140), $random->int(150, 170), 6, 2, '#ffd23f');
        $svg .= $ink->box($random->int(160, 280), $random->int(146, 168), 7, 5, '#ffffff');

        return $svg;
    }

    private function poster(int $x, int $y, string $top, string $bottom): string
    {
        $ink = $this->ink;

        return $ink->box($x, $y, 40, 28, '#ffffff') . $ink->rect($x + 2, $y + 2, 36, 24, '#ff2a4a')
            . $ink->shadowText($x + 20, $y + 11, $top, '#ffd23f', 5) . $ink->shadowText($x + 20, $y + 22, $bottom, '#ffffff', 5);
    }

    private function booth(int $x, SeededRandom $random): string
    {
        $ink = $this->ink;
        $svg = $ink->box($x, 96, 10, 36, '#c81a3a') . $ink->rect($x + 2, 98, 3, 30, '#ff5a6a');
        $svg .= $ink->box($x + 58, 96, 10, 36, '#c81a3a') . $ink->rect($x + 60, 98, 3, 30, '#ff5a6a');
        $svg .= $ink->box($x + 14, 108, 40, 4, '#f4f0e8') . $ink->box($x + 32, 112, 4, 20, '#8a8a9a');
        $svg .= $ink->sprite(Sprites::SODA, Sprites::PALETTE, $x + 18 + $random->int(0, 4), 101);
        $svg .= $ink->sprite(Sprites::FRIES, Sprites::PALETTE, $x + 38, 101);

        return $svg;
    }

    private function balloons(int $x, int $y, SeededRandom $random): string
    {
        $svg = '';
        foreach (['R', 'Y', 'C', 'P', 'G'] as $i => $color) {
            $bx = $x + $i * 12;
            $by = $y - ($i % 2) * 8;
            $svg .= $this->ink->rect($bx + 3, $by + 8, 1, 22 + ($i % 2) * 8, '#5a5a6a');
            $svg .= $this->ink->animated('bob', $random->tenths(20), $this->ink->sprite(
                ['.XXX.', 'XWXXX', 'XXXXX', 'XXXXX', '.XXX.', '..X..'],
                ['X' => Sprites::PALETTE[$color], 'W' => '#ffffff'],
                $bx,
                $by,
            ));
        }

        return $svg;
    }
}
