<?php

declare(strict_types=1);

namespace Boulette\Sprite;

use JsonSerializable;

/**
 * Toutes les animations d'un personnage : une palette, des grilles de pixels par animation,
 * et le point d'ancrage (milieu des pattes, au ras du sol) commun à toutes ses images.
 */
final readonly class SpriteSheet implements JsonSerializable
{
    /**
     * @param array<array-key, string> $palette caractère => couleur CSS
     * @param array<string, list<list<string>>> $frames animation => images
     * @param array{int, int} $anchor
     */
    public function __construct(public array $palette, public array $frames, public array $anchor)
    {
    }

    /** @return array{palette: array<array-key, string>, frames: array<string, list<list<string>>>, anchor: array{x: int, y: int}} */
    public function jsonSerialize(): array
    {
        return [
            'palette' => $this->palette,
            'frames' => $this->frames,
            'anchor' => ['x' => $this->anchor[0], 'y' => $this->anchor[1]],
        ];
    }
}
