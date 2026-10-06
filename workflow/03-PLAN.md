# 03 — Plan détaillé

Chaque tâche a un identifiant (`P2.4`), un **livrable** et un **critère de fin** vérifiable.
Estimation en **soirées** (≈ 2 à 3 h). Total visé : **40 à 55 soirées** jusqu'aux stores.

Colonne « Qui » : le sous-agent le plus adapté (voir [agents/](agents/)) et le skill à charger (voir [skills/](skills/)).

**Règles de déroulement**
- Une tâche = une branche `p2.4-sprites-chiens` = une PR. Pas de tâche de plus de 2 soirées : on la découpe.
- On ne commence une phase que si les critères de fin de la précédente sont verts, **sauf** la phase 1,
  qui peut se faire en parallèle de la phase 0.
- Après chaque tâche : cocher la case ici, ajouter une entrée dans [08-JOURNAL.md](08-JOURNAL.md).

---

## Phase 0 — Socle et spikes · 5 à 6 soirées

Objectif : un monorepo qui compile, une app vide sur 3 cibles, et les **trois risques techniques levés** avant d'investir.

| ID | Tâche | Livrable | Critère de fin | Qui |
|---|---|---|---|---|
| P0.1 | Créer le dépôt `boulette` (ou une branche `universal` de l'actuel), monorepo pnpm + Turborepo, paquets vides, `packages/config` (tsconfig strict, ESLint, Prettier, Vitest) | squelette | `pnpm install && pnpm turbo lint typecheck test` vert | architecte · `expo-monorepo` |
| P0.2 | App Expo (dernier SDK — vérifier la version et lire ses notes de migration), Expo Router, TS, `app.json` paysage | `apps/game` | `pnpm --filter game web` affiche une page ; build de dev Android installé | architecte · `expo-monorepo` |
| P0.3 | CI GitHub Actions : install, lint, typecheck, test, règles de frontières entre paquets | `.github/workflows/ci.yml` | CI verte sur une PR | publication · `publication` |
| P0.4 | Comptes : Expo, Apple Developer, Play Console ; `eas init`, profils `development`/`preview`/`production` | `eas.json` | build de dev iOS installé sur l'iPhone via EAS (sans Mac) | publication · `publication` |
| **S1** | **Spike rendu** : boucle à pas fixe sur thread JS + `SkPicture` ; dessiner 400 sprites 32×32 mis à l'échelle au plus proche voisin + 4 plans en parallaxe | branche `spike/rendu` + note | 60 i/s stables sur un Android d'entrée de gamme et sur le web ; sinon appliquer le plan B de l'ADR-005 | peintre-skia · `skia-pixel-art` |
| **S2** | **Spike audio** : `react-native-audio-api` + 1 pulse, 1 triangle, 1 bruit, filtre, délai, panoramique ; séquence de 16 pas planifiée en avance | branche `spike/audio` + note | joué sans craquement sur Android, iOS, web ; reprise après arrière-plan OK ; sinon plan B de l'ADR-008 | audio · `port-web-audio` |
| **S3** | **Spike hasard** : Mt19937 + `getInt(min, max)` en TS, comparé à PHP sur 10 000 tirages × 5 graines × 6 intervalles | `forge/src/random` | égalité stricte avec la référence (P1.2) | porteur · `parite-reference` |
| P0.5 | Mettre à jour les ADR-005 et ADR-008 (statut *Acceptée* ou *Remplacée*) selon les spikes | `01-DECISIONS.md` | statuts à jour | architecte |

## Phase 1 — Figer les références PHP · 2 soirées
À faire **dans l'ancien dépôt**, avant de porter quoi que ce soit.

| ID | Tâche | Livrable | Critère de fin | Qui |
|---|---|---|---|---|
| P1.1 | Script `tools/export-reference.php` : écrit les 20 décors (`SceneRenderer::render`), `characters.json`, `props.json`, la musique compilée, les niveaux (`LevelFactory`), l'objet `GameData` complet | `fixtures/reference/` | SHA-1 des `.svg` = `tests/snapshots/scene-N.sha1` ; idem sprites (après `json_encode` PHP) | porteur · `parite-reference` |
| P1.2 | Suites de hasard de référence : graines `[0, 1, 42, 1234, 2147483647]`, intervalles `[0,1] [0,9] [0,99] [8,36] [-50,50] [0,2147483647]`, 10 000 tirages chacun ; plus `chance`, `oneIn`, `pick` | `fixtures/reference/random.json` | fichier relu, généré par script versionné | porteur · `parite-reference` |
| P1.3 | Captures visuelles : planche des sprites (`composer preview`), planche des 20 décors, chaque niveau à 5 s (`tools/screenshot.mjs`), écran titre, choix du niveau, game over | `fixtures/reference/screenshots/` | 25 PNG présents | testeur · `parite-reference` |
| P1.4 | Trace audio : instrumenter le séquenceur JS actuel pour écrire la liste des notes planifiées (instant, canal, fréquence, durée) des 8 premières mesures de chaque morceau | `fixtures/reference/music-schedule/*.json` | un fichier par morceau | audio · `port-web-audio` |
| P1.5 | Copier `fixtures/reference/` dans le nouveau dépôt, en lecture seule (CODEOWNERS ou règle de CI : toute modification doit être justifiée dans la PR) | dossier versionné | présent dans `main` | architecte |

## Phase 2 — Contenu et forge en TypeScript · 9 à 12 soirées

Objectif : `pnpm forge` produit la même chose que le PHP. **Rien n'est supprimé tant que la parité n'est pas verte.**

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P2.1 | `packages/types` : types `GameData`, `SpriteSheet`, `Palette`, `Level`, `Song`, `DisplayList`, `Actions` (déduits des JSON de référence) | les JSON de référence se valident contre les types (test) | porteur · `port-php-vers-ts` |
| P2.2 | `packages/content` : port de `config/game.php`, `levels.php`, `story.php`, `music.php` en modules TS `as const` + `satisfies` | égalité profonde avec les tableaux PHP exportés | porteur · `port-php-vers-ts` |
| P2.3 | `forge/random` finalisé (S3) + `forge/pixel-art` (`Grid`, `Layer`, `Compositor` avec contour auto `K`, `Line`) | tests portés de `CompositorTest` + parité | porteur · `port-php-vers-ts` |
| P2.4 | Sprites des chiens (`Dog/` : `DogDesign`, `Leg`, `DogBuilder`, `Pug`, `SuperPug`, `Dachshund`) | grilles identiques à `characters.json` pour `boulette`, `boulette-super`, `saucisse` | porteur · `port-php-vers-ts` |
| P2.5 | Sprites des légumes (`Veggie/` : `Shape`, `Face`, `Limbs`, `VeggieBuilder`, les 8 légumes) | grilles identiques | porteur |
| P2.6 | Boss (`Boss/` : `Crown`, les 5 boss, `BossRoster`) + `PropCatalog` + `CharacterCatalog` | `characters.json` et `props.json` en égalité profonde | porteur |
| P2.7 | Musique : `Tracker` (mesures de 16 pas, basse déduite des accords, batterie) + `SongBook` | égalité profonde avec la musique compilée de référence | porteur |
| P2.8 | Niveaux : `LevelFactory`, `WaveGenerator`, `SceneCatalog` | égalité profonde avec `levels.json` | porteur |
| P2.9 | Décors, partie 1 : modèle `DisplayList` (formes, trames, sprites, éléments animés) + `Ink` + sérialiseur SVG + `ParkingZone` | SVG des niveaux 1 à 4 = référence, octet par octet | porteur · `parite-reference` |
| P2.10 | Décors, partie 2 : `DiningZone`, `KitchenZone` | niveaux 5 à 12 identiques | porteur |
| P2.11 | Décors, partie 3 : `FreezerZone`, `LabZone`, `Furniture`, `Sprites`, `ZonePainter` | les 20 SVG identiques | porteur |
| P2.12 | `gameData.ts` + `bin/forge.ts` + tâche Turborepo `forge` (entrées : `content`, `forge/src` ; sortie : `dist/`) | `game-data.json` = référence (hors `sceneUrl`) ; 2e exécution servie par le cache | porteur · `expo-monorepo` |
| P2.13 | Outil `tools/sprite-sheet.ts` : planche PNG de tous les sprites (remplace `composer preview`) | planche visuellement identique à la référence | testeur |

**Fin de phase** : toute la parité est verte en CI ; on peut archiver le PHP (il reste lisible dans l'ancien dépôt).

## Phase 3 — Moteur en TypeScript · 6 à 8 soirées

Objectif : le jeu complet tourne **dans Node**, sans écran ni son, et un pilote automatique finit le niveau 1.

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P3.1 | Port de `config.js`, `util/math`, `core/GameState`, `core/GameLoop` (horloge injectée) | tests unitaires de la boucle (rattrapage plafonné à 100 ms) | porteur · `port-js-vers-ts` |
| P3.2 | Port de `world/` (`Fighter`, `Camera`, `Combat`, `Projectiles`, `Pickups`, `Gifts`, `Particles`) | typecheck strict, tests des fenêtres d'attaque | porteur · `port-js-vers-ts` |
| P3.3 | Port de `actors/` (`PlayerController`, `EnemyAI`, `BossAI`) | tests de l'IA sur scénarios courts | porteur |
| P3.4 | Hasard injecté : remplacer les 19 `Math.random()` par `game.random` | règle ESLint `no-restricted-properties` sur `Math.random` dans `engine` | porteur · relecteur |
| P3.5 | Événements à la place des appels directs à l'audio, au HUD, au tremblement d'écran | le moteur n'importe plus rien de plateforme (règle de frontières verte) | architecte |
| P3.6 | Port des modes (`Campaign`, `Title`, `Select`, `Intro`, `Playing`, `Clear`, `GameOver`, `Story`) et de `story/` (`StoryPlayer`, `Typewriter`, `Cast`, réalisateurs) — logique seulement, le dessin part en phase 4 | campagne enchaînable en simulation | porteur |
| P3.7 | `createGame()` + adaptateurs d'entrées en **actions** (ADR-010), stockage injecté (ADR-014) | API documentée dans `engine/README.md` | architecte |
| P3.8 | Simulation sans écran : `simulate({ seed, level, inputs, frames })` + pilote automatique (porté de `tests/e2e/harness.mjs`) | test : le pilote finit le niveau 1 ; 2 exécutions de même graine → même état final (empreinte) | testeur · `simulation-moteur` |
| P3.9 | Tests de scénarios portés de l'e2e actuel : tir, morsure, prout, Super Boulette, duo, game over, chaque boss apparaît | tests verts | testeur · `simulation-moteur` |

## Phase 4 — Rendu Skia · 7 à 9 soirées

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P4.1 | `GameView` : `<Canvas>` plein écran, échelle 320×180 (entière si possible), bandes noires, `FilterMode.Nearest` | grille de test nette sur téléphone et web | peintre-skia · `skia-pixel-art` |
| P4.2 | `SpriteAtlas` : grilles → atlas `SkImage` (version normale + flash blanc), ancrages | planche rendue = planche de référence (comparaison de pixels sur le web) | peintre-skia |
| P4.3 | `SceneBaker` : partie statique de chaque plan → bitmap 2 écrans de large ; parallaxe 0.25 / 0.6 / 1 | capture du niveau 1 à t=0 ≈ référence (tolérance définie en 06) | peintre-skia |
| P4.4 | Animations de décor : port des `@keyframes` (`blink`, `bob`, `rise`, `steam`, `bubble`, `flicker`, `alarm`, `smoke`, `neon-flicker`, `crt-flicker`…) en fonctions `(t, delay) → transformation` | comparaison visuelle à 3 instants sur 4 niveaux | peintre-skia |
| P4.5 | Peintres : `FighterPainter`, `EffectPainter`, `PixelBrush`, ordre de `Renderer` | niveau 1 jouable en simulation, rendu ≈ référence à 5 s | peintre-skia |
| P4.6 | HUD peint (vies, score, barre de boss, meilleur score) — remplace `ui/Hud.js` (DOM) ; police pixel | captures ≈ référence | peintre-skia |
| P4.7 | Écrans : `TitlePainter` (barres copper, texte ondulant), choix du niveau, game over, `StoryPainter` + boîte de dialogue | captures ≈ référence | peintre-skia |
| P4.8 | Mode capture : `?capture=level:5,t:300` sur le web (graine fixe, pas d'audio) pour les tests visuels | utilisé par la phase 7 | testeur |

## Phase 5 — App, entrées et cycle de vie · 4 à 5 soirées

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P5.1 | Boucle dans `GameView` (thread JS, pas fixe) branchée sur `engine` + `render` | niveau 1 jouable au clavier sur le web | architecte |
| P5.2 | Clavier web : 1P (flèches, Espace, V, B, C, Entrée, M) et duo AZERTY actuel | test Playwright du duo | porteur |
| P5.3 | Commandes tactiles (`react-native-gesture-handler`) : stick analogique à zone morte + 4 boutons, multi-touch, retour haptique léger (`expo-haptics`) | jouable au pouce sur Android et iPhone | peintre-skia |
| P5.4 | Cycle de vie : pause automatique en arrière-plan (`AppState`), écran de pause, reprise | test manuel documenté dans 06 | architecte |
| P5.5 | Paysage verrouillé, barre d'état masquée, zones sûres ; web mobile : message « tourne ton téléphone » | captures sur iPhone à encoche et Android | peintre-skia |
| P5.6 | Écran réglages (React) : musique, bruitages, vibrations, taille des commandes, gaucher | réglages persistés (AsyncStorage) | porteur |

## Phase 6 — Audio · 4 à 5 soirées

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P6.1 | `AudioContextLike` + `createAudioContext.native.ts` / `.web.ts` | typecheck ; le spike S2 tourne sur l'interface | audio · `port-web-audio` |
| P6.2 | Port de `Synth` et `Instruments` (pulse 12,5/25/50 %, arpèges à 50 Hz, triangle, bruit, passe-bas, écho, stéréo) | écoute comparative + tests unitaires des fréquences | audio |
| P6.3 | Port du `Sequencer` | notes planifiées = `music-schedule` de référence (P1.4), avec un `AudioContext` factice | audio · `simulation-moteur` |
| P6.4 | Port de `sounds.js` (bruitages, prouts compris) branchés sur les événements du moteur | chaque événement sonore a son test | audio |
| P6.5 | Déblocage au premier geste, coupure (M / réglage), arrière-plan, interruption (appel entrant iOS) | testé sur les 3 cibles | audio |

## Phase 7 — Parité complète et finition · 6 à 8 soirées

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P7.1 | Les 20 niveaux et leurs boss en simulation (porté de `levels.test.mjs`) | test vert | testeur |
| P7.2 | Cinématiques : intro, fin, générique | parcours complet en simulation | testeur |
| P7.3 | Tests visuels Playwright sur le web exporté : 20 niveaux à 5 s, titre, sélection, game over | écart sous le seuil défini en 06 | testeur · `parite-reference` |
| P7.4 | Performances : profilage sur Android d'entrée de gamme ; budget 16 ms par image ; mémoire stable sur 20 niveaux | rapport dans le journal | peintre-skia |
| P7.5 | Accessibilité et confort : tailles des commandes, contraste du HUD, option « réduire les flashs » | revue manuelle | relecteur |
| P7.6 | Revue complète du code par le relecteur (conventions, frontières, tests manquants) | liste de corrections traitée | relecteur |

## Phase 8 — Publication web · 1 à 2 soirées

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P8.1 | `app.json` : `web.output: "static"`, `experiments.baseUrl` ; `setup-skia-web` dans le build | `expo export -p web` servi localement sous le sous-chemin fonctionne | publication · `publication` |
| P8.2 | `deploy-web.yml` : forge → export → `upload-pages-artifact` → `deploy-pages` ; Pages réglé sur « GitHub Actions » | déploiement automatique sur push `main` | publication |
| P8.3 | Bascule : la nouvelle version remplace l'ancienne à la même URL ; l'ancienne reste accessible en `/classique/` (option) | URL publique jouable sur ordinateur et mobile | publication |

## Phase 9 — Publication mobile · 4 à 6 soirées (+ délais des stores)

| ID | Tâche | Critère de fin | Qui |
|---|---|---|---|
| P9.1 | Icône, écran de démarrage (`expo-splash-screen`), nom, identifiants (`fr.erwanrivet.boulette` par ex.), version | build `preview` installable | publication |
| P9.2 | Fiches stores : textes FR/EN, captures (générées par le mode capture), classification d'âge, politique de confidentialité (aucune donnée collectée) | fiches complètes | publication |
| P9.3 | iOS : `eas build -p ios --profile production` + `eas submit` → TestFlight → revue Apple | app validée | publication |
| P9.4 | Android : build AAB + `eas submit` → test fermé (12 testeurs, 14 jours) → production | app publiée | publication |
| P9.5 | EAS Update configuré sur le canal `production` pour les correctifs JS | une mise à jour de test reçue sur un téléphone | publication |

## Phase 10 — Après la v1 (réserve)
Manette Bluetooth (mobile), duo sur un seul écran tactile, succès Game Center / Play Games, classement en ligne,
sauvegarde cloud, niveaux bonus, éditeur de niveaux dans le navigateur (la forge en TS le rend possible).

---

## Risques suivis

| Risque | Probabilité | Impact | Parade |
|---|---|---|---|
| Rendu Skia trop lent sur le thread JS | moyenne | fort | spike S1 ; plan B ADR-005 |
| Latence ou craquements audio sur Android | moyenne | moyen | spike S2 ; plan B : musique pré-rendue |
| Tirage `getInt` non reproductible entre PHP et TS | faible | fort (décors différents) | spike S3 contre 50 000 tirages de référence |
| Formatage des nombres SVG différent (PHP vs JS) | moyenne | faible | sérialiseur qui reproduit le format PHP ; parité octet par octet |
| Refus Apple | faible | moyen | app native complète, hors ligne, sans lien externe trompeur |
| Monorepo + modules natifs (Metro) | moyenne | moyen | guide monorepo Expo ; `node-linker=hoisted` |
| Dérive du plan (projet perso, en soirées) | forte | moyen | tâches ≤ 2 soirées, journal systématique, phase 8 publiable avant les stores |
