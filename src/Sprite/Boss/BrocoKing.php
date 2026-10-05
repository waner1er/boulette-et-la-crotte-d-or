<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Veggie\Brocoli;

/** LE ROI BROCOLI : zombie en chef, couronne de travers et cape mitée. */
final class BrocoKing extends Brocoli
{
    public function palette(): array
    {
        return ['C' => '#6a1a8a', 'c' => '#3a0a52'] + Crown::PALETTE + parent::palette();
    }

    public function back(): array
    {
        return [new Layer([
            'CCCCCCCCCCCCCCCCCCCC',
            'CCCCCCCCCCCCCCCCCCCc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCCcc',
            'CCCCCCCCCCCCCCCCCcc.',
            'CC.CCCCCC.CCCCC.cc..',
            'C...CCC....CC....c..',
        ], 8, 22)];
    }

    public function over(): array
    {
        return [Crown::at(15, 5)];
    }
}
