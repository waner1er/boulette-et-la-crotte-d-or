<?php

declare(strict_types=1);

namespace Boulette\Level;

use Boulette\Scene\Screen;
use Boulette\Scene\Theme;
use Boulette\Support\SeededRandom;

/** Assemble chaque niveau à partir de config/levels.php. */
final readonly class LevelFactory
{
    /** Longueur d'un niveau, en pixels. */
    public const LENGTH = Screen::WIDTH * 5;

    public const LEVELS_PER_ZONE = 4;

    private const ZONES = [
        'parking' => 'LE PARKING',
        'dining' => 'LA SALLE',
        'kitchen' => 'LES CUISINES',
        'freezer' => 'LA CHAMBRE FROIDE',
        'lab' => 'LE LABO SECRET',
    ];

    /** @param array<int, array<string, mixed>> $config niveaux indexés par numéro */
    public function __construct(private array $config, private WaveGenerator $waves)
    {
    }

    /** @return list<int> numéros des niveaux, dans l'ordre */
    public function numbers(): array
    {
        return array_keys($this->config);
    }

    public function theme(int $number): Theme
    {
        return Theme::fromArray($this->config[$number]['theme']);
    }

    /**
     * Le niveau tel que le lit le JavaScript.
     *
     * @return array<string, mixed>
     */
    public function build(int $number): array
    {
        $config = $this->config[$number];
        $theme = $this->theme($number);
        $zoneBoss = $number % self::LEVELS_PER_ZONE === 0;
        $boss = $config['boss'] + ['zoneBoss' => $zoneBoss];

        return [
            'number' => $number,
            'title' => $config['title'],
            'zone' => intdiv($number - 1, self::LEVELS_PER_ZONE) + 1,
            'zoneName' => self::ZONES[$theme->zone->value],
            'music' => $config['music'],
            'accent' => $theme->accent,
            'rain' => $theme->rain,
            'snow' => $theme->snow,
            'boss' => $boss,
            'waves' => $this->waves->generate($number, $boss),
            'gift' => $this->gift($number),
        ];
    }

    /**
     * Où Saucisse dépose la boîte menu enfant dans ce niveau.
     *
     * @return array{x: int, y: int} x dans le niveau, y = profondeur sous le haut du sol
     */
    private function gift(int $number): array
    {
        $random = new SeededRandom($number * 131 + 7);

        return ['x' => $random->int(200, self::LENGTH - Screen::WIDTH - 60), 'y' => $random->int(8, 34)];
    }
}
