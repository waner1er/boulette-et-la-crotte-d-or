<?php

declare(strict_types=1);

namespace Boulette\Scene;

use Boulette\PixelArt\Compositor;
use Boulette\PixelArt\Layer;
use Boulette\PixelArt\SvgRenderer;

/**
 * Le pinceau des décors : petits constructeurs de balises SVG, avec le tramage (« dithering »)
 * des jeux Amiga et Atari. Les motifs de trame utilisés sont collectés pour les <defs> du décor.
 */
final class Ink
{
    public const OUTLINE = '#0c0610';

    /** @var array<string, string> motifs de trame déjà utilisés */
    private array $patterns = [];

    public function rect(int $x, int $y, int $w, int $h, string $fill, string $attrs = ''): string
    {
        return sprintf(
            '<rect x="%d" y="%d" width="%d" height="%d" fill="%s"%s/>',
            $x,
            $y,
            $w,
            $h,
            $fill,
            $attrs !== '' ? ' ' . $attrs : '',
        );
    }

    /** Rectangle détouré de noir, comme tous les objets du décor. */
    public function box(int $x, int $y, int $w, int $h, string $fill, string $attrs = ''): string
    {
        return $this->rect($x - 1, $y - 1, $w + 2, $h + 2, self::OUTLINE, $attrs) . $this->rect($x, $y, $w, $h, $fill, $attrs);
    }

    /** Damier d'un pixel entre deux couleurs : le dégradé du pauvre, la signature des années 90. */
    public function dither(int $x, int $y, int $w, int $h, string $a, string $b): string
    {
        $id = 'd' . substr(md5($a . $b), 0, 8);
        $this->patterns[$id] = sprintf(
            '<pattern id="%s" width="2" height="2" patternUnits="userSpaceOnUse">'
            . '<rect width="2" height="2" fill="%s"/><rect width="1" height="1" fill="%s"/><rect x="1" y="1" width="1" height="1" fill="%s"/></pattern>',
            $id,
            $a,
            $b,
            $b,
        );

        return $this->rect($x, $y, $w, $h, "url(#$id)");
    }

    /**
     * Dégradé « copper » : des bandes de couleur unie, séparées par une rangée tramée.
     *
     * @param list<string> $colors du haut vers le bas
     */
    public function bands(int $x, int $y, int $w, int $h, array $colors): string
    {
        $svg = '';
        $count = count($colors);
        $band = $h / $count;
        foreach ($colors as $i => $color) {
            $top = $y + (int) round($i * $band);
            $bottom = $y + (int) round(($i + 1) * $band);
            $svg .= $this->rect($x, $top, $w, $bottom - $top, $color);
            if ($i + 1 < $count) {
                $svg .= $this->dither($x, $bottom - 2, $w, 2, $color, $colors[$i + 1]);
            }
        }

        return $svg;
    }

    public function text(int $x, int $y, string $label, string $color, int $size = 8, string $attrs = ''): string
    {
        return sprintf(
            '<text x="%d" y="%d" fill="%s" font-size="%d" text-anchor="middle" class="pixel-text"%s>%s</text>',
            $x,
            $y,
            $color,
            $size,
            $attrs !== '' ? ' ' . $attrs : '',
            htmlspecialchars($label),
        );
    }

    /** Texte cartoon : ombre portée noire décalée, puis le texte. */
    public function shadowText(int $x, int $y, string $label, string $color, int $size = 8, string $attrs = ''): string
    {
        return $this->text($x + 1, $y + 1, $label, self::OUTLINE, $size, $attrs) . $this->text($x, $y, $label, $color, $size, $attrs);
    }

    /**
     * Petit sprite en texte (1 caractère = 1 pixel), détouré.
     *
     * @param list<string> $rows
     * @param array<string, string> $palette
     */
    public function sprite(array $rows, array $palette, int $x, int $y, bool $outline = true): string
    {
        $grid = Compositor::compose(strlen($rows[0]), count($rows), [new Layer($rows)], $outline ? 'K' : null);

        return SvgRenderer::render($grid, $palette + ['K' => self::OUTLINE], $x - 1, $y - 1);
    }

    /** Groupe animé en CSS (.blink, .steam, .flicker...), déphasé de $delay secondes. */
    public function animated(string $class, float $delay, string $content): string
    {
        return sprintf('<g class="%s" style="animation-delay:-%.1fs">%s</g>', $class, $delay, $content);
    }

    public function defs(): string
    {
        return $this->patterns === [] ? '' : '<defs>' . implode('', $this->patterns) . '</defs>';
    }
}
