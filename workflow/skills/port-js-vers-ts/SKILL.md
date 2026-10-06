---
name: port-js-vers-ts
description: Porter les modules JS de l'ancien jeu Boulette (js/core, world, actors, modes, story, config) en TypeScript dans packages/engine, en retirant toute dépendance de plateforme (hasard, horloge, DOM, audio) par injection et événements. À utiliser pour la phase 3.
---

# Porter le moteur JS en TypeScript

## Ce qui ne bouge pas
- Le découpage en classes, les noms, l'ordre des mises à jour dans `update()`, les constantes de `config.js`.
- Les unités : durées en images (1/60 s), distances en pixels d'écran.
- Le principe « un système reçoit `game` et passe par lui ».

## Ce qui change (et seulement ça)
| Ancien | Nouveau |
|---|---|
| `Math.random()` | `this.game.random.next()` (flottant [0, 1)), `.int(min, max)`, `.chance(p)` — `RandomSource` injecté |
| `performance.now()`, `requestAnimationFrame` | `Clock` injecté dans `GameLoop` ; le moteur n'a pas de boucle à lui |
| `localStorage` | `Storage` injecté (asynchrone : charger avant de démarrer la campagne) |
| `game.audio.play('bite')` | `game.events.emit({ type: 'sfx', name: 'bite' })` |
| `game.audio.music('zone1')` | `game.events.emit({ type: 'music', song: 'zone1' })` |
| manipulation du HUD (DOM) | rien : le HUD est peint d'après l'état |
| lecture du clavier / `Input` | `game.actions.player(n)` → `{ held, pressed }` par action |
| `fetch` de décor | rien : les décors sont des assets déjà chargés ; le moteur ne connaît que le numéro du niveau |

## Typage
- Commencer par les types de données (`GameData` vient de `@boulette/types`) puis l'état (`GameState`, `Fighter`).
- Les champs ajoutés dynamiquement dans l'ancien JS (`fighter.stunned = …`) deviennent des champs déclarés,
  initialisés dans le constructeur. Les lister tous avant de traduire la classe (`grep "this\.\w\+ ="`).
- Les chaînes magiques (`'idle'`, `'walk'`, `'shoot'`…) deviennent des unions de littéraux.
- Pas de `!` (assertion non nulle) pour faire taire le compilateur : traiter le cas absent.

## Événements
Un seul type union `GameEvent` dans `@boulette/types`, émis par `game.events` (file vidée par l'app après chaque `update`).
L'app distribue : `sfx` et `music` → audio ; `shake`, `flash` → rendu ; `haptic` → `expo-haptics` ; `save` → stockage.

## Méthode
1. Porter dans l'ordre des dépendances : `config` → `util` → `core` → `world` → `actors` → `story` → `modes` → `createGame`.
2. Après chaque dossier : typecheck strict, puis un test minimal (création + quelques `update()` sans erreur).
3. Dès que `createGame` existe : brancher la simulation (skill `simulation-moteur`) et faire tourner le niveau 1.
4. Comparer le comportement à l'ancien jeu en simulation **avec la même suite d'entrées** ; les écarts de hasard sont attendus
   (on ne reproduit pas `Math.random`), les écarts de logique ne le sont pas.
