<?php

declare(strict_types=1);

namespace Boulette\Sprite\Dog;

use Boulette\PixelArt\Layer;
use Boulette\Sprite\Mood;

/**
 * BOULETTE, le carlin : grosse tête ronde, masque noir, yeux globuleux, queue en tire-bouchon
 * et le lance-baballe sanglé sur le dos.
 */
class Pug extends DogDesign
{
    /** Le haut de la tête : commun à toutes les expressions. */
    private const SKULL = [
        '..mM........Mm..',
        '.mMMF.FFFF.FMMm.',
        '.MMFFFGGGGFFFMM.',
        '.MMFFGGGGGGFFMM.',
        '..FFFFfFFfFFFF..',
    ];

    /** Yeux et museau, selon l'expression. */
    private const FACES = [
        'idle' => [
            '.FFWWWWFFWWWWFF.',
            '.FFWWEEFfWWEEFF.',
            '.FFWWEEMMWWEEFF.',
            '.FfFFMMNNMMFFfF.',
            '.FffMMMNNMMMffF.',
            '..FfMMmMMmMMfF..',
            '..FFfMMMMMMfFF..',
            '...FFfMTTMfFF...',
            '.....FFTTFF.....',
        ],
        'blink' => [
            '.FFFFFFFFFFFFFF.',
            '.FFEEEEFfEEEEFF.',
            '.FFFFFFMMFFFFFF.',
            '.FfFFMMNNMMFFfF.',
            '.FffMMMNNMMMffF.',
            '..FfMMmMMmMMfF..',
            '..FFfMMMMMMfFF..',
            '...FFfMTTMfFF...',
            '.....FFTTFF.....',
        ],
        'bite' => [
            '.FFWWWWFFWWWWFF.',
            '.FFEEEEFfEEEEFF.',
            '.FFFFFFMMFFFFFF.',
            '.FfFMMMNNMMMFfF.',
            '.FfMWMWMMWMWMfF.',
            '.FFMQQQQQQQQMFF.',
            '.FFMQQQTTQQQMFF.',
            '..FFMWMWWMWMFF..',
            '....FFFFFFFF....',
        ],
        'hurt' => [
            '.FFEFFEFFEFFEFF.',
            '.FFFEEFFfFEEFFF.',
            '.FFEFFEMMEFFEFF.',
            '.FfFFMMNNMMFFfF.',
            '.FffMMMNNMMMffF.',
            '..FfMMQQQQMMfF..',
            '..FFfMQQQQMfFF..',
            '...FFfMMMMfFF...',
            '.....FFFFFF.....',
        ],
        'happy' => [
            '.FFFEEFFFFEEFFF.',
            '.FFEFFEFfEFFEFF.',
            '.FFFFFFMMFFFFFF.',
            '.FfFFMMNNMMFFfF.',
            '.FffMMMNNMMMffF.',
            '..FfMQQQQQQMfF..',
            '..FFfMQTTQMfFF..',
            '...FFfMTTMfFF...',
            '.....FFTTFF.....',
        ],
    ];

    private const BODY = [
        '......GGGGGGGGGGGGG.......',
        '...GGGFFFFFFFFFFFFFFGG....',
        '.GGFFFFFFFFFFFFFFFFFFFFG..',
        'GFFFFFFFFFFFFFFFFFFFFFFFF.',
        'FFFFFFFFFFFFFFFFFFFFFFFFFF',
        'FFFFFFFFFFFFFFFFFFFFFFFFFF',
        'fFFFFFFFFFFFFFFFFFFFFFFFFf',
        'ffFFFFFFFFFFFFFFFFFFFFFFff',
        '.fffFFFFFFFFFFFFFFFFFFffff',
        '...ffffffffffffffffffff...',
    ];

    private const TAILS = [
        [
            '.FFF.',
            'FGGfF',
            'F...F',
            'FF.fF',
            '.Fff.',
        ],
        [
            '..FFF',
            '.FGGf',
            '.F..F',
            '.FFfF',
            '..ff.',
        ],
    ];

    /** Le lance-baballe : réservoir à balles, canon bleu, sangles. */
    private const LAUNCHER = [
        '...YYY..........',
        '..YyYYY.........',
        '..YYYYY.........',
        'BBBBBBBBBBBBBBcc',
        'BbbbbbbbbbbbbbcC',
        'BBBBBBBBBBBBBBcc',
        '...LL......LL...',
        '...LL......LL...',
    ];

    private const COLLAR = ['RR', 'RR', 'RJ', 'RR', 'RR'];

    private const FLASH = [
        '.W.W',
        '..WW',
        'WWWW',
        '..WW',
        '.W.W',
    ];

    public function palette(): array
    {
        return [
            'F' => '#e8b06a', 'f' => '#b77a3e', 'G' => '#ffd99a', 'h' => '#8a5428',
            'M' => '#2a1e22', 'm' => '#584048', 'N' => '#0c0608', 'Q' => '#7a1830',
            'W' => '#ffffff', 'E' => '#120c14', 'T' => '#ff6f8f',
            'B' => '#2f7bff', 'b' => '#1a49b8', 'c' => '#d8e4ff', 'C' => '#7d8db8', 'L' => '#ff7a1a',
            'Y' => '#d4ff2a', 'y' => '#8fc200', 'R' => '#ff2a4a', 'J' => '#ffd23f',
        ];
    }

    public function width(): int
    {
        return 44;
    }

    public function height(): int
    {
        return 32;
    }

    public function head(Mood $mood): Layer
    {
        $face = self::FACES[strtolower($mood->name)];

        return new Layer([...self::SKULL, ...$face], 27, 4);
    }

    public function body(): Layer
    {
        return new Layer(self::BODY, 8, 15);
    }

    public function tail(int $wag): Layer
    {
        return new Layer(self::TAILS[$wag % 2], 5, 12);
    }

    public function legs(): array
    {
        return ['front' => [25, 29], 'back' => [10, 14], 'top' => 23, 'length' => 9];
    }

    public function gear(bool $firing): array
    {
        $layers = [new Layer(self::COLLAR, 27, 16), new Layer(self::LAUNCHER, $firing ? 10 : 11, 8)];
        if ($firing) {
            $layers[] = new Layer(self::FLASH, 27, 9);
        }

        return $layers;
    }
}
