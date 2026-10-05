<?php

declare(strict_types=1);

namespace Boulette\Sprite;

/** Expression du visage d'un personnage dans une image. */
enum Mood
{
    case Idle;
    case Blink;
    case Bite;
    case Hurt;
    case Happy;
}
