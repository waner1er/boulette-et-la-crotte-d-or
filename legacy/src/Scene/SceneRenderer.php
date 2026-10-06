<?php

declare(strict_types=1);

namespace Boulette\Scene;

use Boulette\Scene\Zone\DiningZone;
use Boulette\Scene\Zone\FreezerZone;
use Boulette\Scene\Zone\KitchenZone;
use Boulette\Scene\Zone\LabZone;
use Boulette\Scene\Zone\ParkingZone;
use Boulette\Scene\Zone\ZonePainter;

/**
 * Le décor d'un niveau, entièrement procédural : un fond fixe et trois plans en parallaxe.
 *
 * Chaque plan qui défile fait exactement un écran de large et il est rendu deux fois
 * côte à côte : le JavaScript le décale selon la caméra (data-factor = vitesse de parallaxe).
 */
final class SceneRenderer
{
    /** Halo des néons. */
    private const GLOW = '<filter id="glow" x="-20%" y="-50%" width="140%" height="200%">'
        . '<feGaussianBlur stdDeviation="1.2" result="b"/><feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge></filter>';

    public function render(Theme $theme): string
    {
        $ink = new Ink();
        $painter = self::painter($theme, $ink);

        $svg = $painter->backdrop();
        foreach ([[0.25, $painter->far()], [0.6, $painter->mid()], [1.0, $painter->floor()]] as [$factor, $content]) {
            $svg .= $this->scrolling($factor, $content);
        }

        return '<defs>' . self::GLOW . '</defs>' . $ink->defs() . $svg;
    }

    private static function painter(Theme $theme, Ink $ink): ZonePainter
    {
        return match ($theme->zone) {
            Zone::Parking => new ParkingZone($ink, $theme),
            Zone::Dining => new DiningZone($ink, $theme),
            Zone::Kitchen => new KitchenZone($ink, $theme),
            Zone::Freezer => new FreezerZone($ink, $theme),
            Zone::Lab => new LabZone($ink, $theme),
        };
    }

    private function scrolling(float $factor, string $content): string
    {
        return sprintf(
            '<g class="layer" data-factor="%s">%s<g transform="translate(%d 0)">%s</g></g>',
            $factor,
            $content,
            Screen::WIDTH,
            $content,
        );
    }
}
