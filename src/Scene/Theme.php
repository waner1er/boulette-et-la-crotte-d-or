<?php

declare(strict_types=1);

namespace Boulette\Scene;

/** L'ambiance d'un niveau (voir config/levels.php → theme). */
final readonly class Theme
{
    /**
     * @param string $time day | sunset | night : l'heure (le ciel du parking, l'éclairage ailleurs)
     * @param list<string> $signs textes des enseignes, affiches et menus
     */
    public function __construct(
        public Zone $zone,
        public int $seed,
        public string $accent,
        public string $time = 'day',
        public array $signs = [],
        public bool $rain = false,
        public bool $snow = false,
        public bool $alarm = false,
    ) {
    }

    /** @param array<string, mixed> $config */
    public static function fromArray(array $config): self
    {
        return new self(
            Zone::from($config['zone']),
            $config['seed'],
            $config['accent'],
            $config['time'] ?? 'day',
            $config['signs'] ?? [],
            $config['rain'] ?? false,
            $config['snow'] ?? false,
            $config['alarm'] ?? false,
        );
    }
}
