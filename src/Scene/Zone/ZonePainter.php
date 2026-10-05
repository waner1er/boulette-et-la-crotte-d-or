<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

/**
 * Les plans d'une zone. Chaque plan qui défile fait exactement un écran de large
 * et doit se raccorder à lui-même (il est répété à l'infini).
 */
interface ZonePainter
{
    /** Le fond fixe : ciel ou mur du fond. */
    public function backdrop(): string;

    /** Plan lointain (défile à 0,25). */
    public function far(): string;

    /** Plan du milieu (défile à 0,6). */
    public function mid(): string;

    /** Le sol où l'on se bat (défile à 1). */
    public function floor(): string;
}
