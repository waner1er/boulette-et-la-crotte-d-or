<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Veggie\Carotte;

/** LADY CAROTTE : la reine des cuisines, rongée de moisi jusqu'au trognon, collier de perles. */
final class LadyCarotte extends Carotte
{
    public function palette(): array
    {
        return ['C' => '#e8701a', 'D' => '#9a3a08', 'M' => '#7a9a6a', 'm' => '#d8e8c0'] + Crown::PALETTE + parent::palette();
    }

    public function over(): array
    {
        return [
            Crown::at(13, 9),
            new Layer(['MM.....mM', 'Mm......M', '.........', '.........', '.........', '.........', '.........', 'mMM......'], 10, 26),
            new Layer(['z.z.z.z.z.z', '.z.z.z.z.z.'], 12, 25),
        ];
    }
}
