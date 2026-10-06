# 02 — Architecture cible

## Arborescence

```
boulette/
├── apps/
│   └── game/                    Expo (iOS, Android, web) — l'habillage et la colle de plateforme
│       ├── app/                 Expo Router : _layout.tsx, index.tsx (le jeu), settings.tsx
│       ├── src/
│       │   ├── GameView.tsx         <Canvas> Skia + boucle + cycle de vie (pause en arrière-plan)
│       │   ├── platform/            adaptateurs : audio, stockage, horloge, orientation, plein écran
│       │   │   ├── createAudioContext.native.ts / .web.ts
│       │   │   └── storage.ts
│       │   ├── input/               adaptateurs d'entrées → actions : keyboard.web.ts, TouchControls.tsx, gamepad (plus tard)
│       │   └── assets.ts            charge la sortie de la forge (JSON + listes d'affichage)
│       ├── public/canvaskit.wasm    copié par `setup-skia-web` (non versionné)
│       ├── app.json · eas.json · metro.config.js
│       └── e2e/                     Playwright (web exporté)
├── packages/
│   ├── content/                 LE CONTENU : niveaux, réglages, histoire, partitions (ex config/*.php) — données typées, zéro logique
│   ├── forge/                   LA FABRIQUE (ex src/ PHP) — s'exécute au build, en Node
│   │   ├── src/random/              SeededRandom (Mt19937 + getInt compatible PHP)
│   │   ├── src/pixel-art/           Grid, Layer, Compositor, Line
│   │   ├── src/sprites/             Dog/, Veggie/, Boss/, CharacterCatalog, PropCatalog
│   │   ├── src/scenes/              Theme, Zone/*, Ink → listes d'affichage ; svg/ = sérialiseur de parité
│   │   ├── src/music/               Tracker, SongBook
│   │   ├── src/levels/              LevelFactory, WaveGenerator, SceneCatalog
│   │   ├── src/gameData.ts          assemble l'objet GameData (ex #game-data)
│   │   ├── bin/forge.ts             écrit dist/
│   │   └── dist/                    généré (non versionné)
│   ├── engine/                  LE MOTEUR : logique pure (ex js/core, world, actors, modes, story, config.js)
│   ├── render/                  LES PEINTRES Skia (ex js/render + HUD + titre + parallaxe + animations de décor)
│   ├── audio/                   synthé, instruments, séquenceur, bruitages (ex js/audio) sur AudioContextLike
│   ├── types/                   types partagés : GameData, DisplayList, Actions, interfaces de plateforme
│   └── config/                  tsconfig, eslint, vitest partagés
├── fixtures/
│   └── reference/               sorties PHP figées (étalon de parité) — versionné, en lecture seule
├── tools/                       scripts de dev (planche de sprites, captures)
├── workflow/                    CE DOSSIER
├── .github/workflows/           ci.yml, deploy-web.yml
├── CLAUDE.md · AGENTS.md
├── package.json · pnpm-workspace.yaml · turbo.json · .npmrc
```

## Qui a le droit de dépendre de qui

```
                       types
            ┌────────────┼──────────────┬──────────┐
         content ──► forge          engine ◄──── render
                       │               ▲  ▲        │
                       │ (dist/ JSON)  │  └─ audio │
                       ▼               │           │
                 apps/game ────────────┴───────────┘
```

| Paquet | Peut importer | Ne doit **jamais** importer |
|---|---|---|
| `types` | rien | — |
| `content` | `types` | tout le reste |
| `forge` | `types`, `content`, Node (`fs`, `crypto`) | `engine`, `render`, `audio`, React, Skia |
| `engine` | `types` | React, React Native, Skia, audio, DOM, `Math.random`, `Date.now`, `performance` |
| `audio` | `types` | React, Skia, `react-native-audio-api` (reçoit un `AudioContextLike`) |
| `render` | `types`, `engine` (lecture de l'état), `@shopify/react-native-skia` | React (sauf hooks Skia), DOM |
| `apps/game` | tout | — |

Ces règles sont vérifiées par ESLint (`import/no-restricted-paths` ou `eslint-plugin-boundaries`) dans la CI.
**Le moteur doit pouvoir tourner dans Node sans aucun mock de plateforme** : c'est ce qui rend les simulations possibles.

## Flux de données

```
 build (Node)                                     exécution (app)
 ─────────────                                    ───────────────
 content ─► forge ─► dist/game-data.json ──────► assets.ts ─► SpriteAtlas (grilles → SkImage, version normale + flash)
                  └► dist/scenes/level-N.json ─► SceneBaker (statique → 4 SkImage par niveau) + animations
                                                       │
            entrées (tactile, clavier) ─► actions ─► engine.update()  ×N par image (pas fixe 1/60 s)
                                                       │
                                               engine.state (lecture seule)
                                                       │
                                     render.paint(state) ─► SkPicture ─► <Canvas>
                                                       │
                                    événements sonores ─► audio (séquenceur + bruitages)
```

## Le moteur

- Point d'entrée : `createGame({ data, random, storage, clock? })` → `{ update(actions), state, events, campaign }`.
- `update()` avance d'**une image** (1/60 s). La boucle de l'app l'appelle autant de fois que nécessaire (pas fixe,
  rattrapage plafonné à 100 ms comme aujourd'hui).
- Le moteur **n'appelle jamais** l'audio ni le rendu : il émet des **événements** (`sound:bite`, `music:play`, `shake`,
  `level:clear`…) que l'app distribue. C'est le remplaçant des appels directs actuels à `game.audio`.
- Les modes (`TitleMode`, `PlayingMode`…) gardent leur logique ; ce qu'ils dessinaient passe dans des peintres.

## Le rendu

- `paint(canvas, state, assets, time)` : pile de peintres dans l'ordre actuel de `Renderer.js` :
  fond → plans de parallaxe (bitmaps cuits + éléments animés) → ombres → personnages triés par y → projectiles →
  effets → HUD → surimpression du mode (titre, dialogues, transitions).
- Repère logique 320 × 180 ; l'échelle et le centrage sont appliqués une fois, en tête d'image.
- Toutes les bitmaps sont dessinées en `FilterMode.Nearest`, `MipmapMode.None`.
- Les sprites sont regroupés dans **un atlas** par famille (chiens, légumes, boss, objets) pour limiter les changements de texture.

## L'audio

- `createAudio(ctx: AudioContextLike, songs)` → `{ play(song), stop(), sfx(name), setMuted(), suspend(), resume() }`.
- Le séquenceur planifie en avance sur `ctx.currentTime` (principe actuel conservé).
- Déblocage : le contexte est créé suspendu et repris au premier geste (exigence iOS et navigateurs).
- Arrière-plan : `AppState` → `suspend()` ; retour → `resume()` si le jeu n'est pas en pause.

## Spécificités par plateforme

| Sujet | iOS / Android | Web |
|---|---|---|
| Orientation | paysage verrouillé (`app.json`) | page libre ; message « tourne ton téléphone » sur mobile en portrait |
| Plein écran | natif, barre d'état masquée | `requestFullscreen` au premier appui si disponible |
| Zones sûres | `react-native-safe-area-context` (encoche) | `env(safe-area-inset-*)` |
| Entrées | tactile (stick + 4 boutons) | clavier (1P et 2P AZERTY actuels), tactile sur mobile |
| Audio | `react-native-audio-api` | `AudioContext` du navigateur |
| Skia | natif | CanvasKit (WASM), chargé avant le premier rendu (`LoadSkiaWeb`) |
| Distribution | EAS Build / Submit / Update | GitHub Pages via Actions |
