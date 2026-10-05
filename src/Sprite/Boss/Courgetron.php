<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Veggie\Courgette;

/** COURGETRON, chef de la bande du parking : une courgette géante couronnée, lunettes de frimeur. */
final class Courgetron extends Courgette
{
    public function palette(): array
    {
        return ['G' => '#1f7a2a', 'g' => '#5ac84a', 'D' => '#0c4a1a'] + Crown::PALETTE + parent::palette();
    }

    public function over(): array
    {
        return [
            Crown::at(13, 4),
            new Layer(['nnnnnnnnnnn', 'nzznnnnzznn', '.nnn...nnn.'], 13, 15),
        ];
    }
}
