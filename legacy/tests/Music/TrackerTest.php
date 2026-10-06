<?php

declare(strict_types=1);

namespace Boulette\Tests\Music;

use Boulette\Music\Tracker;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class TrackerTest extends TestCase
{
    public function testNoteNamesBecomeMidiNumbers(): void
    {
        self::assertSame(69, Tracker::note('A4'));
        self::assertSame(60, Tracker::note('C4'));
        self::assertSame(61, Tracker::note('C#4'));
        self::assertSame(70, Tracker::note('Bb4'));
    }

    public function testChordsAreBuiltFromTheirQuality(): void
    {
        self::assertSame([57, 60, 64], Tracker::chord('Am'));
        self::assertSame([48, 52, 55, 58], Tracker::chord('C7'));
    }

    public function testHeldNotesLastSeveralSteps(): void
    {
        $song = Tracker::song([
            'bpm' => 120, 'lead' => 'square', 'chords' => 'arp', 'bass' => 'pump', 'drums' => 'k...',
            'patterns' => ['A' => ['chords' => 'C', 'lead' => 'C5 - - . E5 - . . G5 . . . . . . .']],
            'order' => 'A A',
        ]);

        self::assertSame(32, $song['length']);
        self::assertSame([[0, 72, 3], [4, 76, 2], [8, 79, 1]], array_slice($song['lead'], 0, 3));
        self::assertSame([16, 72, 3], $song['lead'][3]);
        self::assertCount(8, $song['drums']);
        self::assertCount(16, $song['bass']);
    }

    public function testABarMustHaveSixteenSteps(): void
    {
        $this->expectException(InvalidArgumentException::class);
        Tracker::song([
            'bpm' => 120, 'lead' => 'square', 'chords' => 'arp', 'bass' => 'pump', 'drums' => 'k...',
            'patterns' => ['A' => ['chords' => 'C', 'lead' => 'C5 - - .']],
            'order' => 'A',
        ]);
    }

    /** Toute la bande-son se compile, chaque mesure fait bien 16 pas. */
    public function testTheWholeSoundtrackCompiles(): void
    {
        foreach (require dirname(__DIR__, 2) . '/config/music.php' as $name => $config) {
            $song = Tracker::song($config);
            self::assertGreaterThan(0, $song['length'], $name);
            self::assertNotEmpty($song['lead'], $name);
        }
    }

    public function testEveryLevelPlaysAnExistingSong(): void
    {
        $songs = require dirname(__DIR__, 2) . '/config/music.php';
        foreach (require dirname(__DIR__, 2) . '/config/levels.php' as $number => $level) {
            self::assertArrayHasKey($level['music'], $songs, "niveau $number");
        }
    }
}
