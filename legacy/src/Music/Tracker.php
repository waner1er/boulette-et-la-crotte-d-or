<?php

declare(strict_types=1);

namespace Boulette\Music;

use InvalidArgumentException;

/**
 * Le « tracker » : compile un morceau écrit en texte (config/music.php) en événements que joue le JavaScript.
 *
 * Une mesure = 16 pas (doubles croches). Mélodie : une note par pas (« C5 », « F#4 », « Bb5 »),
 * « - » prolonge la note, « . » est un silence, « | » sépare les mesures (pour la lecture).
 * Accords : un par mesure (« Am »), ou deux par mesure séparés d'une virgule (« F,G »).
 * La basse est déduite des accords selon un style ; la batterie est une mesure de caractères
 * (k = grosse caisse, s = caisse claire, h = charleston, o = charleston ouvert, c = cymbale).
 */
final class Tracker
{
    public const STEPS_PER_BAR = 16;

    private const SEMITONES = ['C' => 0, 'D' => 2, 'E' => 4, 'F' => 5, 'G' => 7, 'A' => 9, 'B' => 11];

    /** Intervalles de chaque sorte d'accord, en demi-tons depuis la fondamentale. */
    private const QUALITIES = [
        '' => [0, 4, 7], 'm' => [0, 3, 7], '7' => [0, 4, 7, 10], 'm7' => [0, 3, 7, 10], 'maj7' => [0, 4, 7, 11],
        'dim' => [0, 3, 6], 'aug' => [0, 4, 8], 'sus4' => [0, 5, 7], '5' => [0, 7, 12],
    ];

    /** Rythmes de basse sur une mesure : décalage d'octave à chaque pas joué (null = rien). */
    private const BASS = [
        'bounce' => [0, null, 12, null, 0, null, 12, null, 0, null, 12, null, 0, null, 12, null],
        'pump' => [0, null, 0, null, 0, null, 0, null, 0, null, 0, null, 0, null, 0, null],
        'walk' => [0, null, null, null, 7, null, null, null, 12, null, null, null, 7, null, null, null],
        'gallop' => [0, null, 0, 0, 0, null, 0, 0, 0, null, 0, 0, 0, null, 12, 12],
        'sustain' => [0, null, null, null, null, null, null, null, 0, null, null, null, null, null, null, null],
    ];

    /** Note MIDI d'un nom de note : « A4 » = 69. */
    public static function note(string $token): int
    {
        if (!preg_match('/^([A-G])(#|b)?(-?\d)$/', $token, $m)) {
            throw new InvalidArgumentException("Note inconnue : « $token »");
        }
        $accidental = match ($m[2]) {
            '#' => 1,
            'b' => -1,
            default => 0,
        };

        return 12 * ((int) $m[3] + 1) + self::SEMITONES[$m[1]] + $accidental;
    }

    /**
     * Les notes d'un accord, la fondamentale à l'octave 4 : « Am » = [57, 60, 64].
     *
     * @return list<int>
     */
    public static function chord(string $name): array
    {
        if (!preg_match('/^([A-G](?:#|b)?)(.*)$/', $name, $m) || !isset(self::QUALITIES[$m[2]])) {
            throw new InvalidArgumentException("Accord inconnu : « $name »");
        }
        $root = self::note($m[1] . '3');

        return array_map(fn(int $interval) => $root + $interval, self::QUALITIES[$m[2]]);
    }

    /**
     * Un morceau complet, prêt pour le JavaScript.
     *
     * @param array<string, mixed> $song
     * @return array<string, mixed>
     */
    public static function song(array $song): array
    {
        $lead = [];
        $chords = [];
        $bass = [];
        $drums = [];
        $offset = 0;

        foreach (preg_split('/\s+/', trim($song['order'])) ?: [] as $name) {
            $pattern = $song['patterns'][$name] ?? throw new InvalidArgumentException("Motif inconnu : « $name »");
            $bars = self::bars($pattern['chords']);
            $length = count($bars) * self::STEPS_PER_BAR;

            array_push($lead, ...self::melody($pattern['lead'] ?? '', $offset, $length));
            foreach ($bars as $bar => $names) {
                $slice = intdiv(self::STEPS_PER_BAR, count($names));
                foreach ($names as $i => $chordName) {
                    $at = $offset + $bar * self::STEPS_PER_BAR + $i * $slice;
                    $notes = self::chord($chordName);
                    $chords[] = [$at, $notes, $slice];
                    array_push($bass, ...self::bassLine($song['bass'], $notes[0] - 12, $at, $slice, $i * $slice));
                }
            }
            array_push($drums, ...self::beat($pattern['drums'] ?? $song['drums'], $offset, $length));
            $offset += $length;
        }

        return [
            'bpm' => $song['bpm'],
            'loop' => $song['loop'] ?? true,
            'length' => $offset,
            'instruments' => ['lead' => $song['lead'], 'chords' => $song['chords']],
            'lead' => $lead,
            'chords' => $chords,
            'bass' => $bass,
            'drums' => $drums,
        ];
    }

    /** @return list<list<string>> accords de chaque mesure */
    private static function bars(string $chords): array
    {
        return array_map(fn(string $bar) => explode(',', $bar), preg_split('/\s+/', trim($chords)) ?: []);
    }

    /** @return list<array{int, int, int}> [pas, note, durée] */
    private static function melody(string $line, int $offset, int $length): array
    {
        if (trim($line) === '') {
            return [];
        }
        $tokens = self::tokens($line);
        if (count($tokens) !== $length) {
            throw new InvalidArgumentException(sprintf('Mélodie de %d pas au lieu de %d : « %s »', count($tokens), $length, $line));
        }

        $events = [];
        $current = null;
        foreach ($tokens as $step => $token) {
            if ($token === '-') {
                if ($current !== null) {
                    $events[$current][2]++;
                }
                continue;
            }
            $current = null;
            if ($token !== '.') {
                $events[] = [$offset + $step, self::note($token), 1];
                $current = count($events) - 1;
            }
        }

        return $events;
    }

    /**
     * @return list<string> les pas, mesure par mesure (chaque mesure doit en compter 16)
     */
    private static function tokens(string $line): array
    {
        $tokens = [];
        foreach (explode('|', $line) as $bar) {
            $steps = preg_split('/\s+/', trim($bar)) ?: [];
            if (count($steps) !== self::STEPS_PER_BAR) {
                throw new InvalidArgumentException(sprintf('Mesure de %d pas au lieu de 16 : « %s »', count($steps), trim($bar)));
            }
            array_push($tokens, ...$steps);
        }

        return $tokens;
    }

    /** @return list<array{int, int, int}> */
    private static function bassLine(string $style, int $root, int $at, int $steps, int $from): array
    {
        $events = [];
        $rhythm = self::BASS[$style] ?? throw new InvalidArgumentException("Basse inconnue : « $style »");
        for ($i = 0; $i < $steps; $i++) {
            $octave = $rhythm[($from + $i) % self::STEPS_PER_BAR];
            if ($octave !== null) {
                $events[] = [$at + $i, $root + $octave, $style === 'sustain' ? 8 : 2];
            }
        }

        return $events;
    }

    /** @return list<array{int, string}> */
    private static function beat(string $pattern, int $offset, int $length): array
    {
        $events = [];
        $pattern = str_replace([' ', '|'], '', $pattern);
        for ($step = 0; $step < $length; $step++) {
            $hit = $pattern[$step % strlen($pattern)];
            if ($hit !== '.') {
                $events[] = [$offset + $step, $hit];
            }
        }

        return $events;
    }
}
