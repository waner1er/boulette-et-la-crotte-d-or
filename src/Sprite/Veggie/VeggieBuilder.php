<?php

declare(strict_types=1);

namespace Boulette\Sprite\Veggie;

use Boulette\PixelArt\Compositor;
use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;
use Boulette\Sprite\SpriteSheet;

/** Toutes les animations d'un légume mutant. */
final class VeggieBuilder
{
    /** Marche : [jambe du fond, jambe de devant, bras du fond, bras de devant, rebond]. */
    private const WALK = [
        ['back', 'forward', 'front', 'back', 1],
        ['lifted', 'stand', 'down', 'down', 0],
        ['forward', 'back', 'back', 'front', 1],
        ['stand', 'lifted', 'down', 'down', 0],
    ];

    public static function sheet(VeggieDesign $veggie): SpriteSheet
    {
        $zombie = $veggie->zombie();
        $arms = fn(string $far, string $near) => $zombie && $near !== 'punch' && $near !== 'raise' ? ['reach', 'reach'] : [$far, $near];
        $frame = function (array $legs, array $arms, Mood $mood, int $bob = 0, int $lean = 0) use ($veggie): array {
            return self::frame($veggie, $legs, $arms, $mood, $bob, $lean);
        };

        $walk = array_map(
            fn(array $w) => $frame([$w[0], $w[1]], $arms($w[2], $w[3]), Mood::Idle, $w[4]),
            self::WALK,
        );

        return new SpriteSheet(
            VeggieDesign::BASE + $veggie->palette(),
            [
                'idle' => [$frame(['stand', 'stand'], $arms('down', 'down'), Mood::Idle), $frame(['stand', 'stand'], $arms('down', 'down'), Mood::Blink, 1)],
                'walk' => $walk,
                'attack' => [
                    $frame(['back', 'forward'], ['down', 'raise'], Mood::Bite, 0, -1),
                    $frame(['back', 'forward'], ['back', 'punch'], Mood::Bite, 0, 2),
                ],
                'hurt' => [$frame(['stand', 'stand'], ['raise', 'raise'], Mood::Hurt, 0, -2)],
                'dead' => [$frame(['stand', 'stand'], ['raise', 'raise'], Mood::Hurt)],
            ],
            [intdiv($veggie->width(), 2) + 1, $veggie->height() + 1],
        );
    }

    /**
     * @param array{string, string} $legs pas [fond, devant]
     * @param array{string, string} $arms poses [fond, devant]
     * @return list<string>
     */
    private static function frame(VeggieDesign $veggie, array $legs, array $arms, Mood $mood, int $bob, int $lean): array
    {
        $bottom = $veggie->height() - 1;
        $top = $bottom - 7;
        $center = intdiv($veggie->width(), 2);
        $up = fn(Layer $l) => $l->shift($lean, $bob);
        [$farShoulder, $nearShoulder] = $veggie->shoulders();
        $shoulder = $veggie->shoulder() + $bob;
        [$faceX, $faceY] = $veggie->face();

        $layers = [
            ...Limbs::arm($farShoulder + $lean, $shoulder, $arms[0], true),
            Limbs::leg($center - 3, $top, $bottom, $legs[0], true),
            Limbs::leg($center + 2, $top, $bottom, $legs[1], false),
            ...array_map($up, $veggie->back()),
            $up($veggie->body()),
            $up(Face::layer($mood, $faceX, $faceY, $veggie->zombie())),
            ...array_map($up, $veggie->over()),
            ...Limbs::arm($nearShoulder + $lean, $shoulder, $arms[1], false),
        ];

        return Compositor::compose($veggie->width(), $veggie->height(), $layers);
    }
}
