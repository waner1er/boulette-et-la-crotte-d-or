<?php

/**
 * Version statique pour GitHub Pages : php tools/build.php (ou composer build).
 */

declare(strict_types=1);

use Boulette\Build\StaticSiteBuilder;

$app = require __DIR__ . '/../bootstrap.php';

foreach ((new StaticSiteBuilder($app))->build() as $file) {
    echo $file, "\n";
}
