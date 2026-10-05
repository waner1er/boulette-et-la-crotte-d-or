<?php

declare(strict_types=1);

namespace Boulette\Tests\Scene;

use Boulette\Scene\SceneRenderer;
use Boulette\Scene\Theme;
use Boulette\Tests\SnapshotTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

/** Les décors sont procéduraux mais reproductibles : une même graine donne toujours la même image. */
final class SceneSnapshotTest extends SnapshotTestCase
{
    /** @return iterable<string, array{int, array<string, mixed>}> */
    public static function levels(): iterable
    {
        foreach (require dirname(__DIR__, 2) . '/config/levels.php' as $number => $level) {
            yield "niveau $number" => [$number, $level['theme']];
        }
    }

    /** @param array<string, mixed> $theme */
    #[DataProvider('levels')]
    public function testSceneIsUnchanged(int $number, array $theme): void
    {
        $svg = (new SceneRenderer())->render(Theme::fromArray($theme));

        self::assertStringContainsString('class="layer"', $svg);
        self::assertMatchesSnapshot("scene-$number", $svg);
    }
}
