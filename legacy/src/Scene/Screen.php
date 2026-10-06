<?php

declare(strict_types=1);

namespace Boulette\Scene;

/** L'écran de la borne : basse résolution 16/9, comme un Amiga en mode « lowres ». */
final class Screen
{
    public const WIDTH = 320;
    public const HEIGHT = 180;

    /** Le haut du sol : au-dessus c'est le mur (ou le ciel), en dessous on se bat. */
    public const GROUND = 132;
}
