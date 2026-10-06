<!-- À copier à la racine du nouveau dépôt. -->
# AGENTS.md

Guide pour les agents de code (et les humains pressés) qui travaillent sur ce dépôt.

## Le projet
*Boulette et la Crotte d'Or — édition universelle* : beat'em up pixel art, une seule base **Expo + React + TypeScript**
pour **iOS, Android et web**. Monorepo pnpm + Turborepo. Tout le pilotage est dans **`workflow/`**.

## Au début de chaque session
1. Lire `workflow/README.md`, puis `00-CONTEXTE.md`.
2. Lire la dernière entrée de `workflow/08-JOURNAL.md` et la tâche en cours dans `workflow/03-PLAN.md`.
3. Charger le skill de la tâche (colonne « Qui » du plan) ; déléguer au sous-agent indiqué si la tâche le justifie.

## À la fin de chaque session
`pnpm check` vert → cases cochées dans `03-PLAN.md` (et `04-CARTE-MIGRATION.md`) → entrée dans `08-JOURNAL.md`
→ commit en français (`feat(forge): …`).

## Où est quoi
| Je veux… | Où |
|---|---|
| régler un niveau, un légume, un bonus, l'histoire, la musique | `packages/content` |
| changer un sprite, un décor, le tracker | `packages/forge` |
| changer une mécanique, l'IA, un mode de jeu | `packages/engine` |
| changer un dessin à l'écran, le HUD, le titre | `packages/render` |
| changer un son, un instrument | `packages/audio` |
| commandes tactiles, réglages, cycle de vie, plateforme | `apps/game` |
| l'étalon de parité (ne pas modifier) | `fixtures/reference` |

## Règles (détail dans `workflow/05-CONVENTIONS.md`)
1. Respecter les **frontières entre paquets** (`workflow/02-ARCHITECTURE.md`) ; le moteur n'a **aucune** dépendance de plateforme.
2. La forge est **déterministe** : ne jamais ajouter, retirer ou réordonner un tirage de hasard sans le vouloir.
3. **Ne jamais modifier `fixtures/reference/`** pour faire passer un test.
4. Le contenu va dans `packages/content`, pas dans le code.
5. Durées en images (1/60 s), distances en pixels d'écran ; rendu en 320 × 180, `FilterMode.Nearest`.
6. Un port **traduit**, il n'améliore pas.
7. Toute décision durable → une ADR dans `workflow/01-DECISIONS.md`.
8. Dépendances Expo : `npx expo install` ; nouvelle dépendance native → nouveau build de dev.
9. Commentaires et commits en français ; code en anglais.

## Commandes
Voir `workflow/07-COMMANDES.md`. Les essentielles : `pnpm install`, `pnpm check`, `pnpm forge`,
`pnpm --filter game web`, `pnpm --filter game start`.
