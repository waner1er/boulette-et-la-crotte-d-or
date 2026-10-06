<?php

declare(strict_types=1);

namespace Boulette\Level;

use Boulette\Scene\Screen;
use Boulette\Support\SeededRandom;

/**
 * Vagues d'ennemis d'un niveau : de plus en plus nombreuses, avec de nouveaux légumes
 * débloqués au fil des niveaux, puis le boss au bout du couloir.
 */
final readonly class WaveGenerator
{
    /** Position de la caméra qui déclenche chaque vague. */
    public const TRIGGERS = [0, 360, 760];

    private const MAX_PER_WAVE = 7;

    /** @param array<int, list<string>> $unlocks niveau => ennemis débloqués */
    public function __construct(private array $unlocks)
    {
    }

    /**
     * @param array<string, mixed> $boss
     * @return list<array{at: int, enemies: list<string>, boss?: array<string, mixed>}>
     */
    public function generate(int $level, array $boss): array
    {
        $pool = $this->pool($level);
        $random = new SeededRandom($level * 97);
        $waves = [];

        foreach (self::TRIGGERS as $i => $at) {
            $enemies = [];
            $count = min(self::MAX_PER_WAVE, 2 + intdiv($level, 4) + $i);
            for ($e = 0; $e < $count; $e++) {
                // le dernier débloqué est plus fréquent : on le découvre
                $enemies[] = $random->chance(30) ? end($pool) : $random->pick($pool);
            }
            $waves[] = ['at' => $at, 'enemies' => $enemies];
        }
        $waves[] = ['at' => LevelFactory::LENGTH - Screen::WIDTH, 'enemies' => [], 'boss' => $boss];

        return $waves;
    }

    /** @return non-empty-list<string> */
    public function pool(int $level): array
    {
        $pool = ['courgette'];
        foreach ($this->unlocks as $unlockedAt => $types) {
            if ($unlockedAt <= $level) {
                array_push($pool, ...$types);
            }
        }

        return array_values(array_unique($pool));
    }
}
