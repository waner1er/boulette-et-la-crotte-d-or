<?php

declare(strict_types=1);

namespace Boulette\Sprite;

use Boulette\Sprite\Dog\DogBuilder;
use Boulette\Sprite\Boss\BossRoster;
use Boulette\Sprite\Dog\Dachshund;
use Boulette\Sprite\Dog\Pug;
use Boulette\Sprite\Dog\SuperPug;
use Boulette\Sprite\Veggie\Aubergine;
use Boulette\Sprite\Veggie\Brocoli;
use Boulette\Sprite\Veggie\Carotte;
use Boulette\Sprite\Veggie\Courgette;
use Boulette\Sprite\Veggie\Oignon;
use Boulette\Sprite\Veggie\PetitPois;
use Boulette\Sprite\Veggie\Radis;
use Boulette\Sprite\Veggie\Tomate;
use Boulette\Sprite\Veggie\VeggieBuilder;

final class CharacterCatalog
{
    /** @return array<string, SpriteSheet> */
    public static function all(): array
    {
        $sheets = [
            'boulette' => DogBuilder::sheet(new Pug()),
            'boulette-super' => DogBuilder::sheet(new SuperPug()),
            'saucisse' => DogBuilder::sheet(new Dachshund()),
            'courgette' => VeggieBuilder::sheet(new Courgette()),
            'brocoli' => VeggieBuilder::sheet(new Brocoli()),
            'carotte' => VeggieBuilder::sheet(new Carotte()),
            'petitpois' => VeggieBuilder::sheet(new PetitPois()),
            'oignon' => VeggieBuilder::sheet(new Oignon()),
            'tomate' => VeggieBuilder::sheet(new Tomate()),
            'aubergine' => VeggieBuilder::sheet(new Aubergine()),
            'radis' => VeggieBuilder::sheet(new Radis()),
        ];
        foreach (BossRoster::designs() as $name => $design) {
            $sheets[$name] = VeggieBuilder::sheet($design);
        }

        return $sheets;
    }
}
