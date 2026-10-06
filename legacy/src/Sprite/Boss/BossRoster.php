<?php

declare(strict_types=1);

namespace Boulette\Sprite\Boss;

use Boulette\Sprite\Veggie\VeggieDesign;

/** Les boss, indexés par leur identifiant de sprite (config/levels.php → boss.sprite). */
final class BossRoster
{
    /** @return array<string, VeggieDesign> */
    public static function designs(): array
    {
        return [
            'courgetron' => new Courgetron(),
            'brocoking' => new BrocoKing(),
            'ladycarotte' => new LadyCarotte(),
            'choufleur' => new ChouFleur(),
            'navet' => new ProfesseurNavet(),
        ];
    }
}
