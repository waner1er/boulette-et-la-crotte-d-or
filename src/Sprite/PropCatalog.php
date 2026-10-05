<?php

declare(strict_types=1);

namespace Boulette\Sprite;

use Boulette\PixelArt\Compositor;
use Boulette\PixelArt\Layer;
use JsonSerializable;

/**
 * Les objets du jeu : projectiles (baballes, petits pois, larmes, glaçons, fioles, frites),
 * bonus (nugget, os, frites, jouet), la boîte menu enfant et la Crotte d'Or. Tous détourés de noir.
 */
final class PropCatalog implements JsonSerializable
{
    public const PALETTE = [
        'K' => '#0c0610', 'W' => '#ffffff', 'w' => '#c8d0e0',
        'Y' => '#d4ff2a', 'y' => '#8fc200', 'J' => '#ffd23f', 'j' => '#d88a12', 'g' => '#fff4a8', 'o' => '#a85a08',
        'G' => '#6ad04a', 'D' => '#2f8a2a', 'B' => '#5ad8ff', 'b' => '#2a8ac8', 'I' => '#d8f4ff', 'i' => '#8adcff',
        'R' => '#ff2a4a', 'r' => '#a8102a', 'P' => '#b4ff3a', 'p' => '#5ac81a', 'N' => '#c8a070', 'n' => '#8a6a40',
        'F' => '#f4f0e0', 'f' => '#c8c0a8', 'U' => '#2f7bff', 'u' => '#1a49b8', 'M' => '#5a3a1a', 'm' => '#8a5a2a', 'E' => '#120c14', 'Q' => '#a8102a',
    ];

    private const SPRITES = [
        'ball' => ['.YYYY.', 'YYWYYy', 'YWYYYy', 'YYYYWy', 'YYYWYy', '.yyyy.'],
        'goldball' => ['.JJJJ.', 'JJgJJj', 'JgJJJj', 'JJJJgj', 'JJJgJj', '.jjjj.'],
        'fries' => ['.J.J.J.', '.JJJJJ.', 'J.JJJ.J', 'RRRRRRR', 'RRJJJRr', 'RRJRJRr', '.RRRRr.', '.RRRRr.'],
        'pea' => ['.GG.', 'GGGD', 'GGDD', '.DD.'],
        'tear' => ['..B..', '.BB..', 'BIBB.', 'BBBb.', '.bb..'],
        'ice' => ['IIIIIii', 'IWWIIii', 'IWIIIii', 'IIIIIii', 'IIIIiii', 'iiiiiii'],
        'flask' => ['..ww..', '..ww..', '.wPPw.', 'wPPPPw', 'wPpPPw', 'wPPpPw', '.wwww.'],
        'spore' => ['.nn.', 'nNNn', 'nNNn', '.nn.'],
        'nugget' => ['...jJJJ...', '.jJJJgJJj.', 'jJJgJJJJJj', 'JJJJJJgJJj', 'jJgJJJJJjo', '.ojjjjjjo.', '...oooo...'],
        'bone' => ['FF.......FF', 'FFFFFFFFFFf', '.FFFFFFFFf.', 'FFffffffFFf', 'ff.......ff'],
        'toy' => ['..UUU..', '.UWUWU.', '.UUUUU.', 'RUUUUUR', '.uUUUu.', '.uu.uu.'],
        'gift' => [
            '....J....J....',
            '.....J..J.....',
            '......JJ......',
            'RRRRRRJJRRRRRR',
            'RWWRRRJJRRRWWR',
            'RRRRRRJJRRRRRr',
            'RRRJRRJJRRJRRr',
            'RRRRJJJJJJRRRr',
            'RRRRRRJJRRRRRr',
            'RRRRRRJJRRRRRr',
            'rrrrrrJJrrrrrr',
        ],
        'crotte' => [
            '.......jJ.......',
            '......JggJ......',
            '.....jJJJJ......',
            '....jJgJJJj.....',
            '...jJJJJJJJj....',
            '...ojjjjjjjo....',
            '..jJgJJJJJJJj...',
            '.jJgWWJJJWWJJj..',
            '.JJJWEJJJWEJJJ..',
            '.jJJJJQQQJJJJj..',
            '..ojjjjjjjjjo...',
            '.JJgJJJJJJJJJJj.',
            'jJgJJJJJJJJJJJJj',
            'jJJJJJJJJJJJJJjj',
            '.ooooooooooooo..',
        ],
    ];

    /** @return array<string, list<string>> */
    public function sprites(): array
    {
        $sprites = [];
        foreach (self::SPRITES as $name => $rows) {
            $sprites[$name] = Compositor::compose(strlen($rows[0]), count($rows), [new Layer($rows)]);
        }

        return $sprites;
    }

    /** @return array{palette: array<string, string>, sprites: array<string, list<string>>} */
    public function jsonSerialize(): array
    {
        return ['palette' => self::PALETTE, 'sprites' => $this->sprites()];
    }
}
