<?php

declare(strict_types=1);

namespace Boulette\Tests\Sprite;

use Boulette\Sprite\CharacterCatalog;
use Boulette\Sprite\PropCatalog;
use Boulette\Sprite\SpriteSheet;
use Boulette\Tests\SnapshotTestCase;

final class SpriteSnapshotTest extends SnapshotTestCase
{
    public function testCharacterSpritesAreUnchanged(): void
    {
        self::assertMatchesSnapshot('characters', json_encode(CharacterCatalog::all(), JSON_THROW_ON_ERROR));
    }

    public function testPropSpritesAreUnchanged(): void
    {
        self::assertMatchesSnapshot('props', json_encode(new PropCatalog(), JSON_THROW_ON_ERROR));
    }

    public function testEveryBossOfTheLevelsHasASprite(): void
    {
        $sprites = CharacterCatalog::all();
        $levels = require dirname(__DIR__, 2) . '/config/levels.php';

        foreach ($levels as $level) {
            self::assertInstanceOf(SpriteSheet::class, $sprites[$level['boss']['sprite']] ?? null, $level['boss']['sprite']);
        }
    }

    /** Toutes les images d'un personnage ont la même taille, et chaque pixel a une couleur. */
    public function testFramesAreRectangularAndFullyColored(): void
    {
        foreach (CharacterCatalog::all() as $name => $sheet) {
            $width = null;
            foreach ($sheet->frames as $animation => $frames) {
                foreach ($frames as $grid) {
                    $width ??= strlen($grid[0]);
                    foreach ($grid as $row) {
                        self::assertSame($width, strlen($row), "$name/$animation");
                        foreach (count_chars($row, 3) === '' ? [] : str_split(count_chars($row, 3)) as $char) {
                            self::assertTrue($char === '.' || isset($sheet->palette[$char]), "$name/$animation : couleur « $char » manquante");
                        }
                    }
                }
            }
        }
    }
}
