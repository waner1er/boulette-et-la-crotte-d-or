# Architecture

*Boulette et la Crotte d'Or* est découpé en deux moitiés qui ne se parlent que par des **données** :

- **PHP fabrique** : il compose tout le pixel art (sprites en texte, décors procéduraux en SVG), compile la musique
  et assemble la configuration du jeu.
- **JavaScript anime** : il reçoit ces données en JSON, transforme les grilles de pixels en images,
  joue la musique et fait tourner le jeu à 60 images par seconde.

```
 config/game.php ───┐                         ┌─► <svg> décor du niveau 1 (dans la page)
 config/levels.php ─┤                         ├─► <script id="game-data"> JSON : niveaux, sprites, musique, scénario…
 config/music.php ──┼─► PHP (src/) ───────────┤
 config/story.php ──┘   Boulette\…            └─► scene.php?level=N  /  scenes/level-N.html (autres décors)
                                                              │
                                                              ▼
                              JavaScript (js/) : Game → modes, systèmes, rendu canvas + parallaxe SVG + séquenceur
```

## Deux façons de servir le jeu

| | Développement | Production (GitHub Pages) |
|---|---|---|
| Page | `index.php`, générée à chaque requête | `index.html`, générée par `php tools/build.php` |
| Décors | `scene.php?level=N` | `scenes/level-N.html` |

La seule différence est la clé `sceneUrl` des données du jeu (`Boulette\Game\GameData`).

## Ce que PHP envoie au JavaScript

| Clé | Contenu | Source |
|---|---|---|
| `width`, `height`, `floor` | écran 320 × 180, bande du sol où l'on se bat | `Scene\Screen` |
| `levelLength` | longueur d'un niveau (5 écrans) | `Level\LevelFactory::LENGTH` |
| `heroes`, `attacks`, `enemies`, `pickups`, `gifts` | réglages des chiens, des légumes, des bonus | `config/game.php` |
| `levels[]` | 20 niveaux : titre, zone, musique, couleur, vagues, boss, boîte menu enfant | `Level\LevelFactory` + `config/levels.php` |
| `music` | les morceaux compilés : tempo, instruments, notes par canal | `Music\Tracker` + `config/music.php` |
| `story` | intro, fin et générique | `config/story.php` |
| `sprites` | toutes les animations (`boulette`, `boulette-super`, `saucisse`, légumes, boss), avec leur ancrage | `Sprite\CharacterCatalog` |
| `items` | objets : baballes, projectiles, bonus, boîte, Crotte d'Or | `Sprite\PropCatalog` |

## Les sprites : des pièces en texte

Un sprite est une liste de chaînes, **1 caractère = 1 pixel**. Les personnages sont assemblés à partir de pièces :

- **Chiens** (`Sprite\Dog\`) : `DogDesign` (tête selon l'humeur, corps, queue, accessoires) + `Leg` (pattes selon l'angle) → `DogBuilder`.
  Le chien K.O. est le même dessin retourné, les quatre pattes en l'air.
- **Légumes** (`Sprite\Veggie\`) : `Shape` dessine le corps à partir d'un profil (demi-largeur de chaque ligne) et d'une fonction de peinture
  (rayures, fleurettes, reflets), `Face` les expressions, `Limbs` les bras à gants blancs et les jambes à baskets → `VeggieBuilder`.
- **Boss** (`Sprite\Boss\`) : des légumes déclinés (couronne, cape, blouse de labo).

`PixelArt\Compositor` empile les calques et détoure chacun d'un contour noir : le trait des dessins animés.

## Les décors : parallaxe SVG tramée

Chaque décor est un fond fixe et trois plans qui défilent à des vitesses différentes (`data-factor` 0.25, 0.6 et 1).
Chaque plan fait un écran de large et il est dessiné **deux fois côte à côte** : `world/Backdrop.js` le décale selon la caméra.
Une classe par zone (`Scene\Zone\`) ; `Scene\Ink` fournit le tramage en damier et les dégradés « copper » en bandes.
Les décors sont reproductibles (`Support\SeededRandom`) et vérifiés par des tests d'empreinte.

## La musique : un tracker

`config/music.php` décrit chaque morceau comme dans un tracker Amiga : motifs de mesures de 16 pas, mélodie note à note, accords par mesure.
`Music\Tracker` le compile (la basse est déduite des accords, la batterie répète une mesure). En JS, `audio/Sequencer` planifie les notes
en avance sur l'horloge audio, et `audio/Instruments` les synthétise : ondes pulse 12,5/25/50 %, arpèges à 50 Hz, basse triangle,
batterie au bruit blanc, filtre passe-bas et écho, mélodie à gauche et arpèges à droite.

## Le JavaScript

| Dossier | Rôle |
|---|---|
| `Game.js`, `config.js` | assemblage des systèmes ; réglages (durées en images, distances en pixels) |
| `core/` | boucle à pas fixe, état de la partie |
| `input/` | clavier, pavés des deux joueurs, stick tactile |
| `modes/` | titre, choix du niveau, cinématique, intro de niveau, partie, fin de niveau, game over ; `Campaign` enchaîne |
| `world/` | personnage (`Fighter`), caméra et vagues, combats, projectiles, bonus, boîtes menu enfant, effets |
| `actors/` | commandes des chiens, IA des légumes, attaques spéciales des boss |
| `story/` | lecteur de cinématiques, machine à écrire, figurants, un réalisateur par plan |
| `render/` | ordre de dessin, banque de sprites, personnages, effets, écran titre (barres copper, texte ondulant) |
| `audio/` | moteur, séquenceur, instruments, bruitages (prouts compris) |
| `ui/` | HUD (DOM), panneau de commandes |
