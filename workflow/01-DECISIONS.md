# 01 — Décisions (ADR)

Chaque décision est numérotée et datée. On ne la change pas en silence : on ajoute une ADR qui la **remplace**
(statut « Remplacée par ADR-0XX »). Statuts : *Acceptée*, *À valider* (en attente d'un spike), *Remplacée*.

---

## ADR-001 — Expo + React Native + TypeScript, une seule app universelle
**Statut** : Acceptée — 2026-10-06

**Contexte** : on veut iOS, Android et web, solides et maintenables, sans Mac.
**Décision** : une app **Expo** (dernier SDK stable — 56 au moment d'écrire, à vérifier en phase 0) avec **Expo Router**,
**React** et **TypeScript strict**. Le web sort de la même app via React Native Web (`expo export -p web`).
**Alternatives écartées** :
- *Capacitor / WebView* : rapide, mais le jeu reste un site emballé (risque de refus Apple 4.2, pas d'accès natif propre).
- *Flutter* : soit une réécriture complète en Dart (deux jeux à maintenir), soit une WebView (aucun gain).
- *Vue.js* : Expo ne supporte que React ; NativeScript-Vue a une communauté trop réduite.
**Conséquences** : toute la base est en TS ; builds natifs via EAS (cloud), donc pas de Mac.

## ADR-002 — Abandon du PHP : la forge est portée en TypeScript, sous contrôle de parité
**Statut** : Acceptée — 2026-10-06

**Contexte** : dans la nouvelle architecture, le PHP ne servirait plus qu'à fabriquer les assets au build.
**Décision** : porter `src/` et `config/` en TS (paquets `content` et `forge`). Avant de supprimer quoi que ce soit,
**figer les sorties PHP** dans `fixtures/reference/` (décors SVG, JSON des sprites, musique compilée, niveaux, suites de hasard).
Le port est terminé quand chaque sortie TS est identique à sa référence.
**Conséquences** : un seul langage, une seule chaîne d'outils ; les données deviennent des modules typés importés par le moteur.
`src/Http`, `src/View`, `src/Build`, `templates/`, `style.scss` disparaissent.

## ADR-003 — Monorepo pnpm + Turborepo
**Statut** : Acceptée — 2026-10-06

**Décision** : `apps/` + `packages/` gérés par **pnpm workspaces**, tâches orchestrées et mises en cache par **Turborepo**.
Suivre le guide monorepo d'Expo (Metro les détecte automatiquement depuis SDK 52). Si un module natif pose problème
avec les liens symboliques : `node-linker=hoisted` dans `.npmrc`.
**Conséquences** : les frontières entre paquets sont des **frontières de dépendances vérifiables** (voir 02-ARCHITECTURE).

## ADR-004 — Rendu avec React Native Skia, en 320 × 180 mis à l'échelle
**Statut** : Acceptée — 2026-10-06

**Décision** : `@shopify/react-native-skia` sur les trois cibles (CanvasKit/WASM sur le web).
Le jeu dessine dans un repère logique **320 × 180** ; l'écran applique une **échelle** (entière si possible, sinon
la plus grande qui tienne, avec bandes noires) et un échantillonnage **au plus proche voisin** (`FilterMode.Nearest`)
pour garder des pixels nets.
**Alternatives écartées** : `expo-gl` + moteur maison (trop bas niveau), PixiJS (pas natif), un canvas par plateforme (deux rendus).
**Conséquences** : le web télécharge CanvasKit (quelques Mo, mis en cache) ; `canvaskit.wasm` doit être servi par le site.

## ADR-005 — Boucle de jeu sur le thread JS, rendu par Picture Skia
**Statut** : À valider (spike S1, phase 0)

**Contexte** : le moteur est fait de classes JS ; il ne peut pas tourner dans des worklets Reanimated.
**Décision** : la boucle à pas fixe (`GameLoop`, inchangée dans son principe) tourne sur le **thread JS** via
`requestAnimationFrame`. À chaque affichage, les peintres **enregistrent** une `SkPicture` (`Skia.PictureRecorder`),
publiée dans une `SharedValue` lue par `<Canvas><Picture/></Canvas>`.
**Critère de validation** : 60 i/s stables au niveau le plus chargé, sur un Android d'entrée de gamme de 2021 et sur le web.
**Plan B** : réduire le travail par image (décors cuits en bitmaps, atlas unique), puis seulement envisager de
déplacer le dessin dans un worklet avec un état sérialisé.

## ADR-006 — Les décors deviennent des listes d'affichage, plus du SVG
**Statut** : Acceptée — 2026-10-06

**Contexte** : les décors PHP sont du SVG animé par des `@keyframes` CSS (`blink`, `bob`, `rise`, `steam`, `bubble`,
`flicker`…) avec des `animation-delay` tirés au hasard. Le moteur SVG de Skia ne joue pas les animations CSS.
**Décision** : la forge produit pour chaque niveau une **liste d'affichage** : fond + 3 plans, chacun avec ses
**formes statiques** (rectangles, trames, sprites, lignes) et ses **éléments animés** (`{ animation, delay, forme }`).
Le rendu **cuit** la partie statique de chaque plan en bitmap au chargement du niveau, puis dessine les éléments
animés à chaque image avec des fonctions d'animation TS qui reproduisent les `@keyframes`.
La forge garde un **sérialiseur SVG** de la liste d'affichage : il doit produire exactement le SVG de référence
(même empreinte SHA-1 que les snapshots PHP), ce qui prouve que la liste est complète.
**Conséquences** : décors portables, animations sous contrôle du moteur (pause, ralenti, capture déterministe).

## ADR-007 — Les assets sont fabriqués au build, pas au lancement
**Statut** : Acceptée — 2026-10-06

**Décision** : `pnpm forge` génère `packages/forge/dist/` (données du jeu en JSON + listes d'affichage), consommé par l'app.
Au lancement, l'app ne fait que convertir les grilles de pixels en bitmaps Skia (rapide, mis en cache).
**Conséquences** : démarrage rapide ; la forge est testable en Node sans app ; Turborepo met la sortie en cache.

## ADR-008 — Audio : une interface Web Audio minimale, deux implémentations
**Statut** : À valider (spike S2, phase 0)

**Décision** : le synthé et le séquenceur dépendent d'une interface `AudioContextLike` (oscillateurs, gains, filtres
biquad, délai, panoramique stéréo, buffers de bruit, `PeriodicWave`, `currentTime`, `suspend/resume`).
- **Natif** : `react-native-audio-api` (Software Mansion), qui implémente ces nœuds.
- **Web** : l'`AudioContext` du navigateur.
Choix par fichiers de plateforme : `createAudioContext.native.ts` / `createAudioContext.web.ts`.
**Critère de validation** : le morceau du niveau 1 et 5 bruitages joués sans craquement ni dérive sur Android, iOS et web.
**Plan B** : pré-rendre la musique en fichiers audio au build (forge + rendu hors ligne) et ne synthétiser que les bruitages.
**Conséquences** : impose un **build de dev** (module natif), Expo Go ne suffit plus.

## ADR-009 — Le hasard est injecté et reproductible
**Statut** : Acceptée — 2026-10-06

**Décision** : un `SeededRandom` TS (Mt19937 + tirage d'intervalle **identique à PHP `Random\Randomizer::getInt`**,
vérifié contre des suites de référence) sert à la forge. Le moteur reçoit une source de hasard injectée
(`game.random`) à la place des 19 `Math.random()` actuels.
**Conséquences** : une partie = (graine, suite d'entrées) ; on peut la rejouer à l'identique dans un test.

## ADR-010 — Les entrées passent par des actions, jamais par des touches
**Statut** : Acceptée — 2026-10-06

**Décision** : le moteur ne connaît que des **actions** par joueur (`left`, `right`, `up`, `down`, `shoot`, `bite`,
`jump`, `prout`, `start`). Des adaptateurs les produisent : clavier (web), stick et boutons tactiles
(`react-native-gesture-handler`), et plus tard manette.
**Conséquences** : le duo au clavier du web reste possible ; ajouter la manette ne touchera pas au moteur.

## ADR-011 — HUD, menus et cinématiques peints en Skia ; React pour les commandes tactiles
**Statut** : Acceptée — 2026-10-06

**Décision** : tout ce qui fait partie de l'image du jeu (HUD, titre, choix du niveau, game over, dialogues) est
peint dans le canvas en pixel art, à l'identique sur les trois cibles. React Native ne sert qu'à l'habillage :
commandes tactiles, écran de réglages, gestion du cycle de vie de l'app.
**Conséquences** : rendu pixel-perfect partout ; le HUD DOM et les animations CSS de la borne sont réécrits en peintres.

## ADR-012 — Publication : GitHub Pages par GitHub Actions, mobiles par EAS
**Statut** : Acceptée — 2026-10-06

**Décision** :
- **Web** : `expo export -p web` (`web.output: "static"`, `experiments.baseUrl: "/boulette-et-la-crotte-d-or"`),
  déployé par `actions/deploy-pages` à chaque push sur `main`. Rien de généré n'est commité.
- **Mobile** : profils EAS `development`, `preview`, `production` ; `eas submit` vers TestFlight et Play Console.
- **Correctifs JS** : EAS Update sur le canal `production` (jamais pour un changement de module natif).

## ADR-013 — Stratégie de test en quatre étages
**Statut** : Acceptée — 2026-10-06

**Décision** : (1) **Vitest** pour la forge et le moteur ; (2) **parité** contre `fixtures/reference/` ;
(3) **simulation sans écran** : le moteur tourne en Node, avec une graine et des entrées scriptées (pilote automatique) ;
(4) **Playwright** sur la version web exportée (capture d'écran et parcours) ; Maestro sur mobile en option, plus tard.
Détails dans [06-TESTS-QUALITE.md](06-TESTS-QUALITE.md).

## ADR-014 — Stockage local : AsyncStorage
**Statut** : Acceptée — 2026-10-06

**Décision** : `@react-native-async-storage/async-storage` (fonctionne aussi sur le web) derrière une interface
`Storage` du moteur (meilleur score, réglages audio, niveaux débloqués).

## ADR-015 — Branche `expo-refacto` dans le dépôt existant, ancien jeu rangé dans `legacy/`
**Statut** : Acceptée — 2026-10-06

**Contexte** : le site en ligne est servi par GitHub Pages depuis la racine de `main` (`index.html` commité, aucun workflow).
**Décision** : la migration se fait sur la branche `expo-refacto` du dépôt actuel. L'ancien jeu y est déplacé tel quel
dans `legacy/` (aucun fichier modifié) ; la racine accueille le monorepo. `main` et le site en ligne ne bougent pas
jusqu'à la phase 8, où la publication passe par GitHub Actions (ADR-012).
**Vérification** : `php tools/build.php` lancé depuis `legacy/` régénère des fichiers identiques à ceux commités
(seuls changent les paramètres de cache `?v=`).
**Conséquences** : chemins de la carte de migration relatifs à `legacy/` ; `legacy/` est supprimé une fois la parité atteinte.
