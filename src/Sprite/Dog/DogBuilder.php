<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Compositor;
use Boulette\PixelArt\Grid;
use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;
use Boulette\Sprite\SpriteSheet;

/** Toutes les animations d'un chien, à partir de son dessin (DogDesign). */
final class DogBuilder
{
    /** Pattes [avant fond, avant devant, arrière fond, arrière devant] des 4 images de la marche. */
    private const WALK = [
        ['back', 'forward', 'forward', 'back'],
        ['lifted', 'stand', 'stand', 'lifted'],
        ['forward', 'back', 'back', 'forward'],
        ['stand', 'lifted', 'lifted', 'stand'],
    ];

    private const STAND = ['stand', 'stand', 'stand', 'stand'];
    private const TUCK = ['tuckFront', 'tuckFront', 'tuckBack', 'tuckBack'];
    private const STRETCH = ['reachFront', 'reachFront', 'reachBack', 'reachBack'];

    public static function sheet(DogDesign $dog): SpriteSheet
    {
        $frame = fn(array $legs, Mood $mood, int $wag = 0, int $lean = 0, bool $firing = false, int $bob = 0)
            => self::frame($dog, $legs, $mood, $wag, $lean, $firing, $bob);
        $standing = $frame(self::STAND, Mood::Hurt);

        $frames = [
            'idle' => [$frame(self::STAND, Mood::Idle), $frame(self::STAND, Mood::Blink, 1, 0, false, 1)],
            'walk' => array_map(fn(int $i) => $frame(self::WALK[$i], Mood::Idle, $i % 2, 0, false, $i % 2), range(0, 3)),
            'attack' => [$frame(self::STAND, Mood::Idle, 0, -2), $frame(self::WALK[0], Mood::Bite, 1, 3)],
            'shoot' => [$frame(self::STAND, Mood::Idle), $frame(self::STAND, Mood::Idle, 1, -1, true)],
            'jump' => [$frame(self::TUCK, Mood::Happy, 1)],
            'kick' => [$frame(self::STRETCH, Mood::Bite, 1, 2)],
            'skate' => [$frame(self::STRETCH, Mood::Happy, 0, 0, false, 1), $frame(self::STRETCH, Mood::Happy, 1, 1)],
            'hurt' => [$frame(self::STAND, Mood::Hurt, 0, -2)],
            'dead' => [Grid::dropToFloor(Grid::flipVertical($standing))],
            'happy' => [$frame(self::STAND, Mood::Happy, 0), $frame(self::STAND, Mood::Happy, 1, 0, false, 1)],
        ];

        return new SpriteSheet($dog->palette() + ['K' => '#0c0610'], $frames, [intdiv($dog->width(), 2) + 1, $dog->height() + 1]);
    }

    /**
     * @param list<string> $legs positions des 4 pattes
     * @param int $lean la tête avance (morsure) ou recule
     * @param int $bob le corps descend d'un pixel (marche, respiration)
     * @return list<string>
     */
    private static function frame(DogDesign $dog, array $legs, Mood $mood, int $wag, int $lean, bool $firing, int $bob): array
    {
        $spec = $dog->legs();
        $leg = fn(int $x, string $phase, bool $far) => Leg::layer($x, $spec['top'], $spec['length'], $phase, $far);
        $down = fn(Layer $layer) => $layer->shift(0, $bob);

        $layers = [
            ...array_map($down, $dog->back($wag)),
            $down($dog->tail($wag)),
            $leg($spec['back'][0], $legs[2], true),
            $leg($spec['front'][0], $legs[0], true),
            $down($dog->body()),
            $leg($spec['back'][1], $legs[3], false),
            $leg($spec['front'][1], $legs[1], false),
            ...array_map($down, $dog->gear($firing)),
            $down($dog->head($mood)->shift($lean)),
            ...array_map(fn(Layer $l) => $down($l->shift($lean)), $dog->hat()),
        ];

        return Compositor::compose($dog->width(), $dog->height(), $layers);
    }
}
