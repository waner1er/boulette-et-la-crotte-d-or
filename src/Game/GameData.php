<?php

declare(strict_types=1);

namespace Boulette\Game;

use Boulette\Level\LevelFactory;
use Boulette\Music\SongBook;
use Boulette\Scene\Screen;
use Boulette\Sprite\CharacterCatalog;
use Boulette\Sprite\PropCatalog;
use Boulette\Support\ConfigRepository;

/**
 * Toutes les données envoyées au JavaScript, en JSON dans la page (#game-data).
 * Voir docs/architecture.md pour le détail de chaque clé.
 */
final readonly class GameData
{
    public function __construct(
        private ConfigRepository $config,
        private LevelFactory $levels,
        private SongBook $songs,
        private PropCatalog $props,
    ) {
    }

    /**
     * @param bool $static version statique (GitHub Pages) : les décors sont des fichiers HTML
     * @return array<string, mixed>
     */
    public function toArray(bool $static): array
    {
        $game = $this->config->get('game');

        return [
            'width' => Screen::WIDTH,
            'height' => Screen::HEIGHT,
            'levelLength' => LevelFactory::LENGTH,
            'floor' => ['min' => Screen::GROUND + 8, 'max' => Screen::HEIGHT - 5],
            'heroes' => $game['heroes'],
            'attacks' => $game['attacks'],
            'enemies' => $game['enemies'],
            'pickups' => $game['pickups'],
            'gifts' => $game['gifts'],
            'levels' => array_map($this->levels->build(...), $this->levels->numbers()),
            'story' => $this->config->get('story'),
            'music' => $this->songs->all(),
            'sceneUrl' => $static ? 'scenes/level-%d.html' : 'scene.php?level=%d',
            'sprites' => CharacterCatalog::all(),
            'items' => $this->props,
        ];
    }

    public function toJson(bool $static): string
    {
        return json_encode($this->toArray($static), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_THROW_ON_ERROR);
    }
}
