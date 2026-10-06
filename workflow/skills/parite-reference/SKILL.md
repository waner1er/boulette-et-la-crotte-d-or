---
name: parite-reference
description: Produire les références de l'ancien jeu Boulette (sorties PHP, suites de hasard, captures, notes de musique) dans fixtures/reference/, et écrire les tests qui comparent la nouvelle implémentation TS à ces références. À utiliser en phase 1, pour chaque port de la forge, et pour les tests visuels.
---

# Parité avec la référence

## Principe
L'ancien jeu est l'étalon. On fige ses sorties **une fois**, avant de porter, dans `fixtures/reference/`.
Le port est juste quand il reproduit ces sorties. On ne retouche jamais une référence pour faire passer un test.

## Produire les références (dans `legacy/`, PHP ≥ 8.2)
Script `tools/export-reference.php <dossier>` qui s'appuie sur `vendor/autoload.php` et écrit :
| Fichier | Source PHP |
|---|---|
| `scenes/level-N.svg` (N = 1 à 20) | `(new SceneRenderer())->render(Theme::fromArray($level['theme']))` pour chaque niveau de `config/levels.php` |
| `sprites/characters.json` | `CharacterCatalog::all()` |
| `sprites/props.json` | `new PropCatalog()` |
| `music.json` | les morceaux compilés par `Tracker` (comme dans `GameData`) |
| `levels.json` | la sortie de `LevelFactory` |
| `game-data.json` | l'objet `GameData` complet |
| `config/*.json` | `config/game.php`, `levels.php`, `story.php`, `music.php` bruts (pour le port de `content`) |
| `random.json` | `{ seed, min, max, values[10000] }` pour chaque graine et intervalle du plan (P1.2) + `chance`, `oneIn`, `pick` |
JSON écrit avec `JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION`.

**Contrôle** : `sha1_file(scenes/level-N.svg)` doit égaler `legacy/tests/snapshots/scene-N.sha1`.
S'il diffère, la référence est fausse : on corrige le script avant d'aller plus loin.

Captures (P1.3) avec les outils existants : `composer preview`, `php tools/scenes.php && node tools/capture.mjs`,
`node tools/screenshot.mjs <niveau> 5 <fichier>`.

## Écrire un test de parité (Vitest)
```ts
import { readFileSync } from 'node:fs';
const ref = (p: string) => readFileSync(new URL(`../../../../fixtures/reference/${p}`, import.meta.url), 'utf8');

it.each(range(1, 20))('le décor du niveau %i est identique au PHP', (n) => {
  const svg = toSvg(buildScene(levels[n - 1].theme));
  expectSameText(svg, ref(`scenes/level-${n}.svg`));
});
```
`expectSameText` : en cas d'échec, affiche la position du premier caractère différent avec 80 caractères avant/après,
au lieu d'un diff illisible de plusieurs centaines de Ko.

Pour les JSON : `expect(actual).toStrictEqual(JSON.parse(ref('sprites/characters.json')))`.
Si l'objet TS contient des `Map`, le convertir en objet ordinaire avant comparaison, **et** comparer l'ordre des clés
séparément si l'ordre compte.

## Tests visuels (web exporté)
- Capture via le mode capture (`?capture=level:N,t:300`) dans Playwright, à la résolution logique ×4.
- Comparaison avec `pixelmatch` ; tolérance dans `workflow/06-TESTS-QUALITE.md`.
- En échec : écrire l'image de différence dans `test-results/` pour la joindre à la PR.

## Changement voulu
`pnpm forge:reference --update <nom>` régénère une référence **depuis la forge TS** ; PR intitulée `ref: …` avec avant/après.
