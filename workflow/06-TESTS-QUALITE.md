# 06 — Tests et qualité

## Les quatre étages

| Étage | Outil | Où | Ce qu'il prouve | Quand |
|---|---|---|---|---|
| 1. Unitaires | Vitest | `packages/*/test` | une fonction, une classe | chaque PR |
| 2. Parité | Vitest | `packages/forge/test/parity` | la forge TS = le PHP de référence | chaque PR |
| 3. Simulation | Vitest + `simulate()` | `packages/engine/test/scenarios` | le jeu se joue : pilote automatique, boss, game over, duo | chaque PR |
| 4. Bout en bout | Playwright | `apps/game/e2e` | la version web exportée démarre, se joue au clavier, ressemble à la référence | chaque PR sur `main` + nuit |
| (5. Mobile) | Maestro (plus tard) | `apps/game/.maestro` | démarrage, tactile, arrière-plan | avant chaque version store |

## Parité avec la référence

`fixtures/reference/` contient les sorties du PHP figées (phase 1). Comparaisons :

| Sortie | Comparaison |
|---|---|
| suites de hasard | égalité stricte, tirage par tirage |
| décors (sérialiseur SVG) | égalité **octet par octet** ; en cas d'échec, le test affiche le premier caractère différent avec 80 caractères de contexte |
| sprites, objets, musique, niveaux, `GameData` | égalité **profonde** (pas d'empreinte : `JSON.stringify` et `json_encode` n'échappent pas pareil) |
| notes planifiées par le séquenceur | égalité à 1 µs près sur l'instant, stricte sur canal et fréquence |
| captures d'écran (web) | `pixelmatch`, seuil 0, **tolérance ≤ 0,5 % de pixels différents** (les dégradés copper et les polices peuvent bouger d'un pixel) |

### Mettre à jour une référence
On ne modifie `fixtures/reference/` **que** pour un changement visuel ou sonore **voulu**, jamais pour faire passer un test.
```bash
pnpm forge:reference --update scene-7   # régénère la référence depuis la forge TS
```
La PR l'indique en titre (`ref:`), avec avant/après en image.

## Simulation sans écran
```ts
const run = simulate({ seed: 42, level: 1, players: 1, autopilot: true, maxFrames: 60 * 180 });
expect(run.outcome).toBe('clear');
expect(run.fingerprint).toBe(simulate({ ...même config }).fingerprint); // déterminisme
```
- `fingerprint` : empreinte de l'état final (positions, vies, score, image courante).
- Les scénarios reprennent ceux de l'ancien e2e : tir, morsure, saut, prout, Super Boulette, boîte menu enfant, duo,
  game over, chaque boss apparaît et meurt, intro, fin, générique.

## Budget de performance
- **16,6 ms par image**, dont ≤ 4 ms de logique et ≤ 8 ms de dessin sur l'Android de référence.
- Pas d'allocation dans la boucle (vérifier au profileur : pas de dents de scie mémoire).
- Démarrage à froid ≤ 2 s jusqu'à l'écran titre.
- Appareils de référence à noter ici dès la phase 0 : *Android d'entrée de gamme : …* · *iPhone : …* · *navigateur : Chrome + Safari iOS*.

## Tests manuels avant chaque version
- [ ] Premier lancement : le son démarre au premier appui (iOS, Android, Safari, Chrome)
- [ ] Appel entrant / passage en arrière-plan : pause, reprise propre, pas de son fantôme
- [ ] Rotation : reste en paysage ; web mobile en portrait : message
- [ ] iPhone à encoche : commandes et HUD hors de l'encoche
- [ ] Meilleur score conservé après fermeture
- [ ] Niveau 20 et générique jusqu'au bout
- [ ] Mode avion : tout fonctionne hors ligne

## Définition de « fini » pour une tâche
1. Critère de fin du plan atteint.
2. `pnpm check` vert (lint, typecheck, unitaires, parité, simulation).
3. Pas de nouveau `any`, `@ts-ignore` ou `eslint-disable` sans commentaire qui explique pourquoi.
4. Cases cochées dans 03 (et 04 si un fichier a été porté), entrée dans le journal.
5. Si visuel : capture avant/après dans la PR.
