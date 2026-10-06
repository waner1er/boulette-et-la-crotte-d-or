<?php

declare(strict_types=1);

namespace Boulette\Level;

use Boulette\Scene\SceneRenderer;

/** Le décor SVG de chaque niveau (index à partir de 0, comme dans le JavaScript). */
final readonly class SceneCatalog
{
    public function __construct(private LevelFactory $levels, private SceneRenderer $renderer)
    {
    }

    public function has(int $index): bool
    {
        return isset($this->levels->numbers()[$index]);
    }

    public function level(int $index): string
    {
        return $this->renderer->render($this->levels->theme($this->levels->numbers()[$index]));
    }
}
