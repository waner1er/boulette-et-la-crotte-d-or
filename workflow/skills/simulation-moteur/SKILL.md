---
name: simulation-moteur
description: Faire tourner le moteur de Boulette dans Node sans écran ni son — simulate(), pilote automatique, scénarios d'entrées scriptées, empreinte d'état, déterminisme. À utiliser pour écrire les tests de gameplay (phase 3 et 7), reproduire un bug, ou équilibrer un niveau.
---

# Simulation sans écran

## L'API
```ts
simulate({
  seed: number,                 // graine du RandomSource
  level: number,                // 1 à 20 (ou 'campaign' pour enchaîner)
  players: 1 | 2,
  autopilot?: boolean | AutopilotOptions,
  inputs?: InputScript,         // [{ frame: 120, player: 0, press: ['shoot'] }, { frame: 180, release: ['shoot'] }]
  maxFrames: number,
  onFrame?: (game, frame) => void,  // sondes pour les assertions intermédiaires
}): { outcome: 'clear' | 'gameover' | 'timeout', frames: number, fingerprint: string, events: GameEvent[], state: GameState }
```
- Construit le jeu avec `createGame({ data: forgeOutput, random: new SeededRandom(seed), storage: memoryStorage() })`.
- Avance image par image (`game.update(actions)`), sans boucle temps réel.
- `fingerprint` : SHA-1 d'une sérialisation stable de l'état (positions arrondies, vies, score, mode, image).

## Le pilote automatique
Porté de `tests/e2e/harness.mjs` de l'ancien dépôt. Règle de base : avancer, se tourner vers le légume le plus proche,
tirer à distance, mordre au contact, prout quand ≥ 3 légumes sont proches et que la recharge est prête, ramasser les bonus.
Il doit finir le niveau 1 ; pour les autres niveaux, on accepte un pilote « invincible » (option) pour tester l'enchaînement.

## Écrire un scénario
```ts
it('le prout renverse tous les légumes touchés', () => {
  const run = simulate({ seed: 7, level: 1, players: 1, maxFrames: 600,
    inputs: [{ frame: 200, player: 0, press: ['prout'] }],
    onFrame: (g, f) => { if (f === 199) before = g.enemiesNear(0, 40); } });
  expect(run.state.enemies.filter((e) => before.includes(e.id)).every((e) => e.knockedDown)).toBe(true);
});
```

## Déterminisme
- Deux `simulate` identiques → même `fingerprint`. Test permanent sur 3 niveaux.
- Si le test casse : chercher une source cachée de non-déterminisme (`Math.random`, `Date`, ordre d'un `Set`/`Map`
  modifié pendant un parcours, tri instable sur égalité). La règle ESLint du moteur doit l'avoir empêché : sinon, la renforcer.

## Rejouer un bug
L'app peut enregistrer `(seed, niveau, entrées)` d'une partie (option de debug) ; le fichier se rejoue avec
`pnpm simulate --replay partie.json`, puis devient un test de non-régression.
