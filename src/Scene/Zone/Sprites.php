<?php

declare(strict_types=1);

namespace Boulette\Scene\Zone;

/** Petits dessins en pixels des décors (1 caractère = 1 pixel) et leur palette. */
final class Sprites
{
    public const PALETTE = [
        'B' => '#ffb84a', 'b' => '#c87a1a', 'G' => '#6ad04a', 'R' => '#ff3a2a', 'M' => '#7a3a1a', 'm' => '#4a200a',
        'W' => '#ffffff', 'w' => '#c8d0e0', 'Y' => '#ffd23f', 'y' => '#c08a1a', 'S' => '#b8c4d8', 's' => '#6a7490',
        'P' => '#ff3a8a', 'p' => '#a8105a', 'C' => '#5ad8ff', 'c' => '#2a8ac8', 'F' => '#f8d878', 'f' => '#c89a3a',
        'V' => '#4cc23a', 'v' => '#2a7a26', 'K' => '#0c0610', 'O' => '#ff7a1a', 'L' => '#8adcff', 'l' => '#d8f4ff',
    ];

    public const BURGER = [
        '...BBBBBBB...',
        '.BBbBBBbBBBB.',
        'BBBBBBbBBBBBB',
        'GGGGGGGGGGGGG',
        'RRRRRRRRRRRRR',
        'YYYYYYYYYYYYY',
        'MMMMMMMMMMMMM',
        'MmMMMmMMMMmMM',
        'BBBBBBBBBBBBB',
        '.bbbbbbbbbbb.',
    ];

    public const CLOUD = [
        '.....WWWW.........',
        '...WWWWWWWW..WWW..',
        '.WWWWWWWWWWWWWWWW.',
        'WWWWWWWWWWWWWWWWWW',
        '.wwwwwwwwwwwwwwww.',
    ];

    public const FRIES = [
        '.F.F.F.',
        'FFFFFFF',
        'F.FFF.F',
        'RRRRRRR',
        'RRYYYRR',
        'RRYRYRR',
        '.RRRRR.',
    ];

    public const SODA = ['.ss..', '..s..', 'RRRRR', 'RWWRR', 'RRWRR', 'RRRRR', '.RRR.'];

    public const PAN = ['....SSSSS..', '...SSSSSSS.', 'mmmSSSSSSSS', '...SSSSSSS.', '....sssss..'];

    public const TICKET = ['WWWWW', 'WsssW', 'WWWWW', 'WssWW', 'WWWWW', 'WsssW'];

    public const BALLS = ['R', 'Y', 'C', 'G', 'P'];

    public const FROZEN_BOX = [
        'CCCCCCCCCC',
        'CWWWWWWWWC',
        'CWYYYYYYWC',
        'CWYFFFFYWC',
        'CWWWWWWWWC',
        'cccccccccc',
    ];

    public const ICICLE = ['lll', 'll.', 'l..', 'l..'];

    public const VEGGIE_IN_TANK = [
        '..vV..',
        '.VVVV.',
        'GGGGGG',
        'GWGGWG',
        'GGGGGG',
        '.GGGG.',
    ];
}
