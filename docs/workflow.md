# Développer, tester, publier

## Installer

```bash
composer install     # PHPUnit, PHPStan, PHP_CodeSniffer
npm install          # sass, playwright-core (tests de bout en bout, utilise le Chrome du système)
npm run css          # style.scss → build/style.css
```

## Développer

```bash
composer serve       # http://localhost:8000 — tout est regénéré à chaque requête
npm run css:watch    # recompile les styles à chaque modification
```

`http://localhost:8000/?debug` expose le jeu dans la console : `game.campaign.startLevel(19)` (niveau 20),
`game.pickups.spawn('nugget', game.state.players[0].x, game.state.players[0].y)`…

Outils de vérification visuelle (sortie dans `previews/`, ignoré par git) :

| Commande | Produit |
|---|---|
| `composer preview` | planche de tous les sprites et objets, zoom x4 |
| `php tools/scenes.php && node tools/capture.mjs` | planche des 20 décors |
| `node tools/screenshot.mjs 13 6 previews/n13.png` | capture du niveau 13 après 6 s |

## Tester

| Commande | Vérifie |
|---|---|
| `composer lint` | PSR-12 |
| `composer analyse` | PHPStan niveau 6 |
| `composer test` | PHPUnit : moteur de pixel art, hasard reproductible, tracker, niveaux, empreintes des 20 décors et des sprites |
| `composer check` | les trois |
| `npm run lint:js` | syntaxe de tous les modules JS |
| `npm run test:e2e` | Chrome headless avec horloge virtuelle : solo (titre, intro, tir, morsure, prout, Super Boulette, un pilote automatique qui finit le niveau 1), duo (pavés des deux joueurs, game over), les 20 niveaux et leurs boss, la fin et le générique |

Chrome : `CHROME_PATH`, sinon `/usr/bin/google-chrome`.

## Publier sur GitHub Pages

GitHub Pages n'exécute pas PHP : on génère une version statique, qu'on commite.

```bash
npm run css && composer build    # index.html + scenes/level-0.html … level-19.html
```
