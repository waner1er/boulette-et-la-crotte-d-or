<?php

declare(strict_types=1);

namespace Boulette\Tests\Level;

use Boulette\Level\LevelFactory;
use Boulette\Level\WaveGenerator;
use PHPUnit\Framework\TestCase;

final class LevelFactoryTest extends TestCase
{
    private function factory(): LevelFactory
    {
        $root = dirname(__DIR__, 2);
        $game = require $root . '/config/game.php';

        return new LevelFactory(require $root . '/config/levels.php', new WaveGenerator($game['unlocks']));
    }

    public function testTwentyLevelsInFiveZones(): void
    {
        $factory = $this->factory();
        self::assertSame(range(1, 20), $factory->numbers());

        $zones = array_map(fn(int $n) => $factory->build($n)['zone'], $factory->numbers());
        self::assertSame([1, 1, 1, 1, 2, 2, 2, 2, 3, 3, 3, 3, 4, 4, 4, 4, 5, 5, 5, 5], $zones);
    }

    /** Chaque niveau finit par un boss ; le 4e niveau d'une zone par le grand boss. */
    public function testEveryLevelEndsWithABoss(): void
    {
        $factory = $this->factory();
        foreach ($factory->numbers() as $number) {
            $level = $factory->build($number);
            $last = end($level['waves']);
            self::assertArrayHasKey('boss', $last, "niveau $number");
            self::assertSame($number % 4 === 0, $level['boss']['zoneBoss'], "niveau $number");
        }
    }

    public function testWavesAreReproducibleAndUnlockNewVeggies(): void
    {
        $game = require dirname(__DIR__, 2) . '/config/game.php';
        $waves = new WaveGenerator($game['unlocks']);

        self::assertSame($waves->generate(7, []), $waves->generate(7, []));
        self::assertSame(['courgette'], $waves->pool(1));
        self::assertContains('aubergine', $waves->pool(20));
        foreach ($waves->pool(20) as $type) {
            self::assertArrayHasKey($type, $game['enemies']);
        }
    }
}
