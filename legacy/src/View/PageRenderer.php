<?php

declare(strict_types=1);

namespace Boulette\View;

use Boulette\Application;
use Boulette\PixelArt\SvgRenderer;
use Boulette\Scene\Screen;
use Boulette\Sprite\PropCatalog;

/** La page du jeu : la borne d'arcade, le décor du premier niveau et les données du jeu. */
final readonly class PageRenderer
{
    public function __construct(private Application $app)
    {
    }

    /** @param bool $static version statique pour GitHub Pages (voir Build\StaticSiteBuilder) */
    public function render(bool $static = false): string
    {
        $game = $this->app->config()->get('game');
        $assets = new AssetVersioner($this->app->root);

        return (new Template($this->app->root . '/templates'))->render('page', [
            'title' => $game['title'],
            'restaurant' => $game['restaurant'],
            'year' => $game['year'],
            'width' => Screen::WIDTH,
            'height' => Screen::HEIGHT,
            'scene' => $this->app->scenes()->level(0),
            'gameJson' => $this->app->gameData()->toJson($static),
            'assets' => $assets,
            'importMap' => $assets->importMap('js'),
            'crotte' => $this->crotte(),
        ]);
    }

    /** La Crotte d'Or du fronton, en SVG. */
    private function crotte(): string
    {
        $grid = (new PropCatalog())->sprites()['crotte'];

        return sprintf(
            '<svg class="marquee__crotte" viewBox="0 0 %d %d" shape-rendering="crispEdges" aria-hidden="true">%s</svg>',
            strlen($grid[0]),
            count($grid),
            SvgRenderer::render($grid, PropCatalog::PALETTE),
        );
    }
}
