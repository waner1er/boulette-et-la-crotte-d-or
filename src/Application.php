<?php

declare(strict_types=1);

namespace Boulette;

use Boulette\Game\GameData;
use Boulette\Level\LevelFactory;
use Boulette\Level\SceneCatalog;
use Boulette\Level\WaveGenerator;
use Boulette\Music\SongBook;
use Boulette\Scene\SceneRenderer;
use Boulette\Sprite\PropCatalog;
use Boulette\Support\ConfigRepository;
use Boulette\View\PageRenderer;

/**
 * Point d'assemblage de l'application : crée chaque service une seule fois, à la demande.
 * C'est le seul endroit qui connaît les dépendances concrètes (injection de dépendances « à la main »).
 */
final class Application
{
    private ?ConfigRepository $config = null;
    private ?LevelFactory $levels = null;

    public function __construct(public readonly string $root)
    {
    }

    public function config(): ConfigRepository
    {
        return $this->config ??= new ConfigRepository($this->root . '/config');
    }

    public function levels(): LevelFactory
    {
        return $this->levels ??= new LevelFactory(
            $this->config()->get('levels'),
            new WaveGenerator($this->config()->get('game')['unlocks']),
        );
    }

    public function scenes(): SceneCatalog
    {
        return new SceneCatalog($this->levels(), new SceneRenderer());
    }

    public function gameData(): GameData
    {
        return new GameData($this->config(), $this->levels(), new SongBook($this->config()->get('music')), new PropCatalog());
    }

    public function page(): PageRenderer
    {
        return new PageRenderer($this);
    }
}
