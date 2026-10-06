# 04 — Carte de migration

Pour chaque fichier de l'ancien dépôt : sa destination et la façon de le porter.

| Action | Sens |
|---|---|
| **Traduire** | même logique, même structure ; on ajoute les types. On ne « l'améliore » pas pendant le port. |
| **Adapter** | même logique, mais une dépendance de plateforme est remplacée (hasard, horloge, audio, événements). |
| **Réécrire** | le rôle est conservé, l'implémentation change (DOM/CSS/SVG → Skia ou React). |
| **Supprimer** | n'a plus de raison d'exister. |

Une ligne portée = une case cochée ici, dans la PR qui la porte.

## PHP → `packages/content` et `packages/forge`

| Ancien | Nouveau | Action | Notes |
|---|---|---|---|
| `config/game.php` | `content/src/game.ts` | Traduire | `as const satisfies GameSettings` |
| `config/levels.php` | `content/src/levels.ts` | Traduire | les clés numériques PHP (1-20) deviennent un tableau ; garder le numéro dans l'objet |
| `config/story.php` | `content/src/story.ts` | Traduire | |
| `config/music.php` | `content/src/music.ts` | Traduire | garder la notation tracker telle quelle (chaînes) |
| `src/Support/SeededRandom.php` | `forge/src/random/SeededRandom.ts` + `Mt19937.ts` | Traduire | **réimplémenter l'algorithme d'intervalle de `Randomizer::getInt`** (PHP ≥ 8.2) ; parité sur `random.json` |
| `src/Support/ConfigRepository.php` | — | Supprimer | les modules TS s'importent directement |
| `src/PixelArt/Grid.php`, `Layer.php`, `Line.php`, `Compositor.php` | `forge/src/pixel-art/` | Traduire | contour `K` automatique du `Compositor` : cœur du style, tests à porter en premier |
| `src/PixelArt/SvgRenderer.php` | `forge/src/scenes/svg/` | Adapter | devient le sérialiseur de parité des listes d'affichage |
| `src/Sprite/SpriteSheet.php`, `Mood.php` | `forge/src/sprites/` | Traduire | enum PHP → union de chaînes ou `enum` TS `const` |
| `src/Sprite/Dog/*` (6) | `forge/src/sprites/dog/` | Traduire | héritage `Pug` → `SuperPug` conservé (classes non `final` en PHP) |
| `src/Sprite/Veggie/*` (12) | `forge/src/sprites/veggie/` | Traduire | `Shape` (profil + fonction de peinture) : attention aux arrondis (`intdiv`, `round` PHP ≠ `Math.round` pour les négatifs) |
| `src/Sprite/Boss/*` (7) | `forge/src/sprites/boss/` | Traduire | |
| `src/Sprite/CharacterCatalog.php`, `PropCatalog.php` | `forge/src/sprites/` | Traduire | |
| `src/Music/Tracker.php`, `SongBook.php` | `forge/src/music/` | Traduire | le tracker refuse une mesure ≠ 16 pas : garder l'erreur explicite |
| `src/Level/LevelFactory.php`, `WaveGenerator.php`, `SceneCatalog.php` | `forge/src/levels/` | Traduire | |
| `src/Scene/Theme.php`, `Screen.php` | `forge/src/scenes/` | Traduire | `Screen` (320×180, bande du sol) → aussi exporté dans `types` |
| `src/Scene/Ink.php` | `forge/src/scenes/Ink.ts` | Adapter | produit des nœuds de liste d'affichage au lieu de chaînes SVG ; `animated()` → nœud `{ animation, delay, child }` |
| `src/Scene/SceneRenderer.php` | `forge/src/scenes/SceneBuilder.ts` | Adapter | renvoie une `DisplayList` ; le SVG vient du sérialiseur |
| `src/Scene/Zone/*` (8) | `forge/src/scenes/zones/` | Adapter | **ordre des tirages de hasard sacré** : ne pas réordonner |
| `src/Game/GameData.php` | `forge/src/gameData.ts` | Traduire | `sceneUrl` disparaît (les décors sont dans `dist/scenes/`) |
| `src/Application.php` | `forge/bin/forge.ts` | Réécrire | assemble et écrit `dist/` |
| `src/Http/SceneController.php`, `src/View/*`, `src/Build/StaticSiteBuilder.php` | — | Supprimer | plus de serveur PHP ni de page générée |
| `templates/page.php`, `index.php`, `scene.php`, `bootstrap.php` | — | Supprimer | |
| `style.scss` | `render/src/scene/animations.ts`, `render/src/title/*` | Réécrire | les `@keyframes` deviennent des fonctions TS (P4.4) ; la borne disparaît |
| `tools/preview.php`, `tools/scenes.php` | `tools/sprite-sheet.ts`, `tools/scene-sheet.ts` | Réécrire | |
| `tools/build.php` | — | Supprimer | remplacé par `expo export` |
| `tests/**/*.php` | `forge/test/**` | Traduire | les empreintes SHA-1 deviennent des comparaisons à `fixtures/reference/` |

## JavaScript → `packages/engine`, `render`, `audio` et `apps/game`

| Ancien | Nouveau | Action | Notes |
|---|---|---|---|
| `js/config.js` | `engine/src/config.ts` | Traduire | `HISCORE_KEY` → clé de `Storage` |
| `js/util/math.js` | `engine/src/util/math.ts` | Traduire | |
| `js/util/HiscoreStore.js` | `engine/src/util/HiscoreStore.ts` | Adapter | `localStorage` → `Storage` injecté (asynchrone : charger au démarrage) |
| `js/core/GameState.js` | `engine/src/core/GameState.ts` | Traduire | |
| `js/core/GameLoop.js` | `engine/src/core/GameLoop.ts` | Adapter | `performance.now` et `requestAnimationFrame` injectés |
| `js/world/Fighter.js`, `Camera.js`, `Combat.js`, `Projectiles.js`, `Pickups.js`, `Gifts.js`, `Particles.js` | `engine/src/world/` | Adapter | `Math.random` → `game.random` ; tremblement → événement |
| `js/world/Backdrop.js` | `render/src/scene/Parallax.ts` | Réécrire | plus de `fetch` ni de DOM : bitmaps cuites + décalage selon la caméra |
| `js/actors/PlayerController.js`, `EnemyAI.js`, `BossAI.js` | `engine/src/actors/` | Adapter | lit des **actions**, plus `Input` |
| `js/modes/Mode.js`, `Campaign.js`, `TitleMode.js`, `SelectMode.js`, `IntroMode.js`, `PlayingMode.js`, `ClearMode.js`, `GameOverMode.js`, `StoryMode.js` | `engine/src/modes/` | Adapter | `Campaign` et `SelectMode` touchent le DOM : extraire en événements |
| `js/story/StoryPlayer.js`, `Typewriter.js`, `Cast.js` | `engine/src/story/` | Traduire | |
| `js/story/directors/*` (11) | `engine/src/story/directors/` | Adapter | `Director.js` touche la plateforme : à isoler |
| `js/input/Input.js` | `engine/src/input/Actions.ts` | Réécrire | état des actions par joueur (pressé, vient d'être pressé) |
| `js/input/KeyboardControls.js` | `apps/game/src/input/keyboard.web.ts` | Adapter | mêmes touches |
| `js/input/TouchStick.js`, `capturePointer.js` | `apps/game/src/input/TouchControls.tsx` | Réécrire | `react-native-gesture-handler` |
| `js/input/MobileGuard.js` | — | Supprimer | le natif gère l'orientation ; le web garde un petit garde dans `apps/game` |
| `js/render/Renderer.js` | `render/src/paint.ts` | Réécrire | même ordre de dessin, API Skia |
| `js/render/SpriteBank.js` | `render/src/SpriteAtlas.ts` | Réécrire | grilles → atlas `SkImage`, flash blanc |
| `js/render/FighterPainter.js`, `EffectPainter.js`, `PixelBrush.js`, `StoryPainter.js`, `TitlePainter.js` | `render/src/painters/` | Réécrire | `fillRect` → `drawRect`, `drawImage` → `drawImageRect` (Nearest) |
| `js/ui/Hud.js` | `render/src/painters/HudPainter.ts` | Réécrire | était en DOM |
| `js/ui/ControlPanel.js` | `apps/game/src/input/TouchControls.tsx` | Réécrire | |
| `js/audio/AudioEngine.js` | `audio/src/AudioEngine.ts` | Adapter | reçoit un `AudioContextLike` |
| `js/audio/Synth.js`, `Instruments.js`, `Sequencer.js`, `sounds.js` | `audio/src/` | Traduire | vérifier chaque nœud contre la couverture de `react-native-audio-api` |
| `js/Game.js` | `engine/src/createGame.ts` + `apps/game/src/GameView.tsx` | Réécrire | l'assemblage se coupe en deux : logique (moteur) et colle de plateforme (app) |
| `js/main.js` | `apps/game/app/index.tsx` | Réécrire | |
| `tests/e2e/harness.mjs` | `engine/test/simulate.ts` + `apps/game/e2e/` | Adapter | le pilote automatique passe en simulation Node |
| `tests/e2e/*.test.mjs` | `engine/test/scenarios/` + `apps/game/e2e/` | Adapter | |
| `tools/screenshot.mjs`, `capture.mjs` | `apps/game/e2e/capture.ts` | Adapter | sur le web exporté, mode capture (P4.8) |
