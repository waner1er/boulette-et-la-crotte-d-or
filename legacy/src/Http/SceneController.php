<?php

declare(strict_types=1);

namespace Boulette\Http;

use Boulette\Level\SceneCatalog;

/** scene.php?level=3 : le décor SVG d'un niveau (index à partir de 0). */
final readonly class SceneController
{
    public function __construct(private SceneCatalog $scenes)
    {
    }

    /** @param array<string, mixed> $query */
    public function handle(array $query): void
    {
        $index = (int) ($query['level'] ?? 0);
        if (!$this->scenes->has($index)) {
            http_response_code(404);

            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        echo $this->scenes->level($index);
    }
}
