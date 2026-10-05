<?php

/**
 * Planche de tous les sprites en PNG (x4), pour vérifier le pixel art à l'œil.
 *
 *   php tools/preview.php [filtre] [fichier.png]
 *   php tools/preview.php boulette previews/boulette.png
 */

declare(strict_types=1);

use Boulette\Sprite\CharacterCatalog;
use Boulette\Sprite\PropCatalog;

$app = require __DIR__ . '/../bootstrap.php';

[, $filter, $file] = $argv + [1 => '', 2 => 'previews/sprites.png'];
$zoom = 4;
$gap = 6;

$rows = [];
foreach (CharacterCatalog::all() as $name => $sheet) {
    if ($filter !== '' && !str_contains($name, $filter)) {
        continue;
    }
    $frames = [];
    foreach ($sheet->frames as $animation => $list) {
        foreach ($list as $grid) {
            $frames[] = $grid;
        }
    }
    $rows[] = [$name, $sheet->palette, $frames];
}

if ($filter === '' || str_contains('props', $filter)) {
    $rows[] = ['props', PropCatalog::PALETTE, array_values((new PropCatalog())->sprites())];
}

$width = 0;
$height = 0;
foreach ($rows as [, , $frames]) {
    $width = max($width, array_sum(array_map(fn($g) => strlen($g[0]) + $gap, $frames)));
    $height += max(array_map('count', $frames)) + $gap + 4;
}

$image = imagecreatetruecolor($width * $zoom, $height * $zoom);
imagefill($image, 0, 0, imagecolorallocate($image, 90, 110, 140));
$y = 0;
foreach ($rows as [$name, $palette, $frames]) {
    imagestring($image, 3, 4, $y * $zoom, $name, imagecolorallocate($image, 255, 255, 255));
    $y += 4;
    $x = 0;
    foreach ($frames as $grid) {
        foreach ($grid as $dy => $row) {
            for ($dx = 0; $dx < strlen($row); $dx++) {
                $color = $palette[$row[$dx]] ?? null;
                if ($row[$dx] === '.' || $color === null) {
                    continue;
                }
                [$r, $g, $b] = sscanf($color, '#%02x%02x%02x');
                $c = imagecolorallocate($image, $r, $g, $b);
                imagefilledrectangle($image, ($x + $dx) * $zoom, ($y + $dy) * $zoom, ($x + $dx + 1) * $zoom - 1, ($y + $dy + 1) * $zoom - 1, $c);
            }
        }
        $x += strlen($grid[0]) + $gap;
    }
    $y += max(array_map('count', $frames)) + $gap;
}

if (!is_dir(dirname($file))) {
    mkdir(dirname($file), 0777, true);
}
imagepng($image, $file);
echo $file, "\n";
