<?php
/**
 * La borne d'arcade du MÉGA MIAM.
 *
 * @var string $title
 * @var string $restaurant
 * @var int $year
 * @var int $width
 * @var int $height
 * @var string $scene décor SVG du premier niveau
 * @var string $gameJson données du jeu (voir Boulette\Game\GameData)
 * @var string $importMap
 * @var string $crotte la Crotte d'Or du fronton (SVG)
 * @var \Boulette\View\AssetVersioner $assets
 */

$buttons = [['red', 'Space', 'BABALLE'], ['yellow', 'KeyV', 'MORSURE'], ['green', 'KeyB', 'SAUT'], ['brown', 'KeyC', 'PROUT']];
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title><?= htmlspecialchars($title) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bungee+Shade&family=Bungee&family=Press+Start+2P&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $assets->url('build/style.css') ?>">
    <script type="importmap"><?= $importMap ?></script>
</head>

<body>
    <div class="cabinet">
        <header class="marquee">
            <h1 class="marquee__title">
                <span class="marquee__name">BOULETTE</span>
                <span class="marquee__subtitle"><?= $crotte ?>ET LA CROTTE D'OR<?= $crotte ?></span>
            </h1>
            <p class="marquee__copyright">© <?= $year ?> <?= $restaurant ?> GAMES · 20 NIVEAUX · 1 OU 2 TOUTOUS</p>
        </header>

        <div class="bezel">
            <div class="screen">
                <svg class="screen__scene" viewBox="0 0 <?= $width ?> <?= $height ?>" shape-rendering="crispEdges" aria-hidden="true">
                    <?= $scene ?>
                </svg>

                <!-- résolution x2 : les boss grossissent par pas de 0.5 en gardant des pixels nets -->
                <canvas class="screen__actors" width="<?= $width * 2 ?>" height="<?= $height * 2 ?>"></canvas>

                <div class="hud">
                    <div class="hud__player">
                        <div class="hud__row">
                            <span class="hud__label">BOULETTE</span>
                            <span class="hud__score" data-hud="score">000000</span>
                        </div>
                        <div class="hud__bar"><div class="hud__fill" data-hud="life"></div></div>
                        <span class="hud__lives" data-hud="lives">♥ x3</span>
                        <div class="hud__p2" data-hud="p2" hidden>
                            <span class="hud__label">SAUCISSE</span>
                            <div class="hud__bar hud__bar--p2"><div class="hud__fill" data-hud="life2"></div></div>
                            <span class="hud__lives" data-hud="lives2">♥ x3</span>
                        </div>
                    </div>
                    <div class="hud__center">
                        <span class="hud__label hud__label--accent" data-hud="level">HI-SCORE</span>
                        <span data-hud="hiscore">000000</span>
                    </div>
                    <div class="hud__enemy" data-hud="enemy" hidden>
                        <span class="hud__label" data-hud="enemy-name"></span>
                        <div class="hud__bar hud__bar--enemy"><div class="hud__fill" data-hud="enemy-life"></div></div>
                    </div>

                    <div class="hud__message" data-hud="message"></div>
                    <div class="hud__dialog" data-hud="dialog" hidden></div>
                    <div class="hud__credits" data-hud="credits" hidden></div>
                    <p class="hud__go" data-hud="go" hidden>GO ➜</p>
                    <p class="hud__super" data-hud="super" hidden>SUPER BOULETTE !</p>
                    <p class="hud__mute" data-hud="mute" hidden>♪ OFF</p>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="joystick" data-joystick aria-label="Joystick"></div>
            <div class="touch-stick" data-touch-stick aria-label="Stick">
                <span class="touch-stick__knob"></span>
            </div>
            <ul class="panel__help">
                <li><kbd>←</kbd><kbd>→</kbd><kbd>↑</kbd><kbd>↓</kbd> marcher · <kbd>ENTRÉE</kbd> start · <kbd>M</kbd> musique</li>
                <li><kbd>ESPACE</kbd> lance-baballe · <kbd>V</kbd> morsure</li>
                <li><kbd>B</kbd> saut · <kbd>C</kbd> prout turbo · <b>nugget</b> = SUPER BOULETTE</li>
            </ul>
            <div class="panel__buttons">
                <?php foreach ($buttons as [$color, $key, $label]) : ?>
                    <label class="arcade-btn-wrap">
                        <button class="arcade-btn arcade-btn--<?= $color ?>" type="button" data-key="<?= $key ?>" aria-label="<?= $label ?>"></button>
                        <span><?= $label ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            <div class="panel__system">
                <button class="system-btn" type="button" data-key="Enter">START</button>
                <button class="system-btn" type="button" data-key="KeyM">♪</button>
            </div>
        </div>
    </div>

    <div class="rotate-hint" aria-hidden="true">
        <span class="rotate-hint__icon">📱</span>
        <p>TOURNE TON TÉLÉPHONE<br>EN MODE PAYSAGE</p>
    </div>

    <script type="application/json" id="game-data"><?= $gameJson ?></script>
    <script type="module" src="<?= $assets->url('js/main.js') ?>"></script>
</body>

</html>
