<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;

/**
 * Un chien jouable (ou figurant) : tête, corps, queue, pattes, accessoires.
 * Il regarde à droite ; les pattes touchent le sol à la dernière ligne de la grille.
 */
abstract class DogDesign
{
    /** @return array<string, string> */
    abstract public function palette(): array;

    abstract public function head(Mood $mood): Layer;

    abstract public function body(): Layer;

    abstract public function tail(int $wag): Layer;

    /** Largeur de la grille (sans le contour). */
    abstract public function width(): int;

    /** Hauteur de la grille : les pattes touchent la dernière ligne. */
    abstract public function height(): int;

    /**
     * Position des pattes : abscisses [fond, devant] des pattes avant et arrière, haut des pattes, longueur.
     *
     * @return array{front: array{int, int}, back: array{int, int}, top: int, length: int}
     */
    abstract public function legs(): array;

    /**
     * Accessoires portés sur le dos ou le corps (lance-baballe, tablier) ; $firing : image du tir.
     *
     * @return list<Layer>
     */
    public function gear(bool $firing): array
    {
        return [];
    }

    /** @return list<Layer> derrière tout le reste (cape...) */
    public function back(int $wag): array
    {
        return [];
    }

    /** @return list<Layer> par-dessus la tête (casquette...) */
    public function hat(): array
    {
        return [];
    }
}
