# AGENTS.md

Guide pour les agents de code (et les humains pressés) qui travaillent sur ce dépôt.

## Le projet

*Boulette et la Crotte d'Or* : beat'em up d'arcade en pixel art cartoon (années 90, Amiga/Atari), 20 niveaux, 1 ou 2 joueurs.

- **PHP** (`src/`, namespace `Boulette\`, PSR-4) **fabrique** : sprites en texte, décors SVG procéduraux, musique (tracker), données du jeu en JSON.
- **JavaScript** (`js/`, modules ES, classes) **anime** : boucle à 60 images/s, IA, rendu canvas, séquenceur et bruitages synthétisés.
- Ils ne communiquent que par les données (`#game-data` dans la page, décors chargés à la demande).

Documentation : `docs/architecture.md`, `docs/content.md`, `docs/workflow.md`.

## Commandes

```bash
composer install && npm install    # une fois
npm run css                        # style.scss → build/style.css
composer serve                     # http://localhost:8000 (?debug → window.game dans la console)
composer check                     # phpcs PSR-12 + PHPStan niveau 6 + PHPUnit
npm run lint:js                    # syntaxe des modules JS
npm run test:e2e                   # le jeu complet dans Chrome headless (~1 min)
UPDATE_SNAPSHOTS=1 composer test   # valider un changement VOULU de décor ou de sprite
composer preview                   # planche PNG de tous les sprites (previews/sprites.png)
php tools/scenes.php && node tools/capture.mjs   # planche des 20 décors (previews/scenes.png)
node tools/screenshot.mjs 4 5 out.png            # capture du niveau 4 après 5 s de jeu (title | 1-20)
composer build                     # index.html + scenes/ pour GitHub Pages
```

## Où est quoi

| Je veux… | Fichiers |
|---|---|
| régler un niveau, son boss, son décor, sa musique | `config/levels.php` |
| régler les légumes, les attaques des chiens, les bonus, les cadeaux | `config/game.php` |
| changer l'intro, la fin, le générique | `config/story.php` (+ `js/story/directors/` pour un nouveau plan) |
| composer ou modifier un morceau | `config/music.php` (format : `src/Music/Tracker.php`) |
| dessiner un chien | `src/Sprite/Dog/` (`Pug`, `SuperPug`, `Dachshund`) |
| dessiner un légume ou un boss | `src/Sprite/Veggie/`, `src/Sprite/Boss/` |
| changer les objets (baballe, nugget, Crotte d'Or…) | `src/Sprite/PropCatalog.php` |
| changer le dessin des décors | `src/Scene/Zone/` (une classe par zone), `src/Scene/Ink.php` |
| changer une mécanique de jeu | `js/actors/`, `js/world/`, `js/config.js` |
| changer un écran (titre, choix du niveau, game over) | `js/modes/` |
| changer un bruitage, un instrument | `js/audio/sounds.js`, `js/audio/Instruments.js` |
| changer la borne (HTML / styles) | `templates/page.php`, `style.scss` |

## Règles

1. **Ne jamais éditer `index.html`, `scenes/` ni `build/style.css`** : ils sont générés.
2. **Les décors sont déterministes.** Chaque plan a son `SeededRandom` ; ajouter, retirer ou réordonner un tirage change le décor.
   Un snapshot qui casse = changement visuel : voulu → `UPDATE_SNAPSHOTS=1 composer test` ; sinon, c'est une régression.
3. **Le contenu va dans `config/`**, pas dans le code.
4. **PHP** : PSR-12, `declare(strict_types=1)`, classes `final` (sauf les dessins qu'on décline : `Pug`, `Courgette`…), objets valeur `readonly`, enums, dépendances injectées (seule `Boulette\Application` assemble).
5. **JavaScript** : une classe par fichier ; un système reçoit `game` et passe par lui ; durées en images, distances en pixels d'écran ; pas de bundler, pas de dépendance d'exécution.
6. **Pixel art** : 1 caractère = 1 pixel, `.` = transparent, `K` = contour automatique (`Compositor`).
7. **Musique** : une mesure = 16 pas, exactement (le tracker refuse le reste).
8. **Commentaires en français**, courts, pour le *pourquoi*.
9. Avant de rendre la main : `composer check`, `npm run lint:js`, et `npm run test:e2e` si le JS ou les données ont changé.

## Pièges connus

- `index.php` a besoin de `vendor/` : sans `composer install`, il répond « Dépendances manquantes ».
- Générer la page prend ~2 s (sprites et musique compilés à chaque requête) : normal en développement.
- Le son ne démarre qu'après un geste du joueur (politique des navigateurs) ; la musique demandée avant démarre au premier appui.
- Les décors des autres niveaux sont téléchargés à la demande : les cinématiques les préchargent (`Backdrop.preload`).
