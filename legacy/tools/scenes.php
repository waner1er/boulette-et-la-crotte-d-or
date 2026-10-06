<?php

/**
 * Planche des 20 décors (previews/scenes.html), à ouvrir dans un navigateur ou à capturer :
 *   php tools/scenes.php && node tools/capture.mjs previews/scenes.html previews/scenes.png
 */

declare(strict_types=1);

use Boulette\Scene\SceneRenderer;
use Boulette\Scene\Theme;

require __DIR__ . '/../vendor/autoload.php';

$levels = require __DIR__ . '/../config/levels.php';
$renderer = new SceneRenderer();
$html = '<!doctype html><meta charset="utf-8"><link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">'
    . '<style>body{margin:0;background:#222;display:grid;grid-template-columns:repeat(4,640px);gap:4px}'
    . 'svg{width:640px;height:360px}.pixel-text{font-family:"Press Start 2P"}p{color:#fff;margin:0;font:12px monospace}</style>';
foreach ($levels as $n => $level) {
    $html .= '<div><p>' . $n . ' ' . $level['title'] . '</p><svg viewBox="0 0 320 180" shape-rendering="crispEdges">'
        . $renderer->render(Theme::fromArray($level['theme'])) . '</svg></div>';
}
if (!is_dir(__DIR__ . '/../previews')) {
    mkdir(__DIR__ . '/../previews');
}
file_put_contents(__DIR__ . '/../previews/scenes.html', $html);
echo "previews/scenes.html\n";
