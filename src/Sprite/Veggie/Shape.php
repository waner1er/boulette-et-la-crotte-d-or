<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

/**
 * Dessine une forme de légume ligne par ligne à partir de son profil (demi-largeurs),
 * centrée dans une grille. Une fonction de peinture choisit la couleur de chaque pixel
 * (rayures, reflets, ombre à droite...).
 */
final class Shape
{
    /**
     * @param list<int> $halves demi-largeur de chaque ligne (0 = ligne vide)
     * @param callable(int, int, int): string $paint (x depuis le centre, ligne, demi-largeur) => caractère
     * @return list<string>
     */
    public static function rows(array $halves, int $width, callable $paint): array
    {
        $center = intdiv($width, 2);
        $rows = [];
        foreach ($halves as $y => $half) {
            $row = str_repeat('.', $width);
            for ($x = -$half; $x < $half; $x++) {
                $row[$center + $x] = $paint($x, $y, $half);
            }
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * Ombrage cartoon : reflet sur le bord gauche, ombre sur le bord droit et en bas.
     * Lettres : $light, $main, $dark.
     */
    public static function shade(int $x, int $y, int $half, int $height, string $light, string $main, string $dark): string
    {
        if ($x >= $half - 2 || $y >= $height - 2) {
            return $dark;
        }
        if ($x <= -$half + 2 && $x >= -$half + 1 && $y > 1) {
            return $light;
        }

        return $main;
    }

    /** @return list<int> profil d'un disque de rayon $radius */
    public static function disc(int $radius): array
    {
        $halves = [];
        for ($y = -$radius; $y < $radius; $y++) {
            $halves[] = (int) round(sqrt($radius * $radius - ($y + 0.5) ** 2));
        }

        return $halves;
    }
}
