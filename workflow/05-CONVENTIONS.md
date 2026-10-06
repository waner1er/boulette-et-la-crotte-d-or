# 05 — Conventions

Elles reprennent les règles de l'ancien `AGENTS.md`, transposées en TypeScript.

## Langue
- **Code** (identifiants, noms de fichiers) : anglais, comme aujourd'hui (`Fighter`, `EnemyAI`, `SpriteAtlas`).
- **Commentaires, docs, messages de commit, journal** : français, courts, pour expliquer le **pourquoi**.
- Noms du jeu gardés en français dans les données : `boulette`, `saucisse`, `courgetron`, `prout`.

## TypeScript
- `strict: true`, `noUncheckedIndexedAccess`, `exactOptionalPropertyTypes`, `noImplicitOverride`.
- Pas de `any` ; `unknown` + garde de type aux frontières (lecture d'un JSON).
- Données de contenu : `as const satisfies Type` pour garder les littéraux **et** vérifier la forme.
- **Une classe par fichier**, nommée comme le fichier. Fonctions pures pour ce qui n'a pas d'état.
- `readonly` par défaut sur les objets valeur ; état mutable seulement dans le moteur (`GameState`, `Fighter`).
- Pas d'export par défaut (sauf là où Expo Router l'exige : `app/**`).
- Imports entre paquets par leur nom (`@boulette/engine`), jamais par chemin relatif.

## Le moteur (`packages/engine`)
- **Zéro dépendance de plateforme** (voir 02-ARCHITECTURE, règle vérifiée en CI).
- Interdits : `Math.random`, `Date.now`, `performance`, `setTimeout`, `requestAnimationFrame`, `console` (hors debug).
- Un système reçoit `game` et passe par lui (principe actuel conservé).
- **Durées en images** (1/60 s), **distances en pixels d'écran**. Le nom le dit : `durationFrames`, `speedPx`.
- Le moteur émet des événements typés ; il n'appelle ni l'audio ni le rendu.

## La forge (`packages/forge`)
- **Déterministe** : même entrée → même sortie, octet par octet. Chaque plan de décor a son `SeededRandom`.
- **Ne jamais ajouter, retirer ou réordonner un tirage** sans le vouloir : cela change le décor.
  Un test de parité qui casse = régression, sauf changement visuel voulu (voir 06, « Mettre à jour une référence »).
- Pixel art : 1 caractère = 1 pixel, `.` = transparent, `K` = contour automatique.
- Musique : une mesure = 16 pas, exactement.

## Le contenu (`packages/content`)
- Le contenu va dans `content`, pas dans le code. Régler un niveau ne touche ni le moteur ni la forge.

## Le rendu (`packages/render`)
- Repère logique 320 × 180. Coordonnées entières au moment de dessiner (`Math.round` une fois, dans le peintre).
- Toujours `FilterMode.Nearest`, jamais d'anticrénelage (`antiAlias: false`).
- Aucune allocation d'objet Skia dans la boucle de dessin : `Paint`, `Rect`, images préparés au chargement.
- Un peintre = une responsabilité, une fonction `paint(canvas, state, assets, time)`.

## L'app (`apps/game`)
- Composants fonctionnels, hooks. Pas de logique de jeu dans les composants.
- Code de plateforme dans des fichiers suffixés : `.native.ts`, `.web.ts`, `.ios.ts`, `.android.ts`.
- Pas d'état global React pour l'état du jeu : il vit dans le moteur, l'écran le lit à chaque image.

## Git
- Branches : `p<phase>.<n>-<sujet-court>` (ex. `p2.4-sprites-chiens`), `spike/<sujet>`, `fix/<sujet>`.
- Commits en français, au présent, style Conventional Commits : `feat(forge): sprites des chiens`, `test(engine): pilote du niveau 1`,
  `fix(render): décalage de parallaxe au plan 0.6`, `docs(workflow): journal P2.4`.
- Une PR = une tâche du plan, avec : ce qui change, comment c'est testé, cases cochées dans 03 et 04, entrée du journal.
- `main` toujours déployable (le déploiement web est automatique).

## Dépendances
- Toute nouvelle dépendance d'exécution se justifie dans la PR (pourquoi, taille, maintenance, support web + natif).
- Versions des paquets Expo : toujours via `npx expo install` (versions compatibles avec le SDK).

## Formatage et analyse
- Prettier (largeur 120, guillemets simples, virgules finales), ESLint (TS, imports, frontières, hooks React).
- `pnpm check` avant de rendre la main : lint, typecheck, tests, parité.
