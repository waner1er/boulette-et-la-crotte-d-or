<?php

declare(strict_types=1);

namespace Boulette\Music;

/** Tous les morceaux de config/music.php, compilés par le tracker. */
final readonly class SongBook
{
    /** @param array<string, array<string, mixed>> $songs */
    public function __construct(private array $songs)
    {
    }

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        return array_map(Tracker::song(...), $this->songs);
    }
}
