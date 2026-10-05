<?php

declare(strict_types=1);

namespace Boulette\PixelArt;

/** Opérations sur une grille de pixels (liste de chaînes de même longueur). */
final class Grid
{
    public const EMPTY = '.';

    /** @return list<string> */
    public static function blank(int $width, int $height): array
    {
        return array_fill(0, $height, str_repeat(self::EMPTY, $width));
    }

    /**
     * Supprime les colonnes vides à gauche et à droite.
     *
     * @param list<string> $grid
     * @return list<string>
     */
    public static function trimColumns(array $grid): array
    {
        $used = array_filter(
            range(0, strlen($grid[0]) - 1),
            fn(int $x) => array_filter($grid, fn(string $row) => $row[$x] !== self::EMPTY) !== [],
        );
        $from = min($used);
        $length = max($used) - $from + 1;

        return array_map(fn(string $row) => substr($row, $from, $length), $grid);
    }

    /**
     * Retournement vertical (le chien K.O. tombe les quatre pattes en l'air).
     *
     * @param list<string> $grid
     * @return list<string>
     */
    public static function flipVertical(array $grid): array
    {
        return array_reverse($grid);
    }

    /**
     * Retournement horizontal.
     *
     * @param list<string> $grid
     * @return list<string>
     */
    public static function flipHorizontal(array $grid): array
    {
        return array_map(strrev(...), $grid);
    }

    /**
     * Fait descendre le dessin jusqu'en bas de la grille (supprime les lignes vides du bas).
     *
     * @param list<string> $grid
     * @return list<string>
     */
    public static function dropToFloor(array $grid): array
    {
        $width = strlen($grid[0]);
        $blank = str_repeat(self::EMPTY, $width);
        $height = count($grid);
        while ($grid !== [] && end($grid) === $blank) {
            array_pop($grid);
        }

        return array_merge(array_fill(0, $height - count($grid), $blank), $grid);
    }
}
