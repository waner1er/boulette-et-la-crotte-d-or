# 07 — Commandes

Référence **cible** : chaque commande est créée par la tâche indiquée. Tant qu'elle n'existe pas, elle est marquée *(à venir)*.
Mettre ce fichier à jour dans la PR qui ajoute ou change une commande.

## Prérequis
- Node ≥ 20.19.4 (exigence de React Native 0.85 / Expo SDK 56 — revérifier au SDK choisi), pnpm (via `corepack enable`)
- Compte Expo + `npm i -g eas-cli` (ou `pnpm dlx eas-cli`)
- PHP ≥ 8.2 et Composer, pour la phase 1 seulement (ancien jeu dans `legacy/`)

## Racine du monorepo
| Commande | Effet | Créée par |
|---|---|---|
| `pnpm install` | installe tout le monorepo | P0.1 |
| `pnpm check` | lint + typecheck + test + parité (tous les paquets, via Turborepo) | P0.1 |
| `pnpm lint` · `pnpm typecheck` · `pnpm test` | séparément | P0.1 |
| `pnpm forge` | génère `packages/forge/dist/` (données + listes d'affichage) | P2.12 |
| `pnpm forge:reference --update <nom>` | met à jour une référence (changement **voulu**) | P2.9 |
| `pnpm sprites` | planche de tous les sprites → `previews/sprites.png` | P2.13 |
| `pnpm simulate --level 5 --seed 42 --autopilot` | joue un niveau sans écran, affiche l'issue et l'empreinte | P3.8 |

## App (`apps/game`)
| Commande | Effet | Créée par |
|---|---|---|
| `pnpm --filter game start` | serveur Metro (build de dev sur le téléphone) | P0.2 |
| `pnpm --filter game web` | version web locale | P0.2 |
| `pnpm --filter game export:web` | `expo export -p web` → `apps/game/dist/` | P8.1 |
| `pnpm --filter game e2e` | Playwright sur la version web exportée | P4.8 / P7.3 |
| `pnpm --filter game capture -- --level 5 --t 300` | capture PNG d'un niveau à l'image 300 | P4.8 |

## EAS
| Commande | Effet |
|---|---|
| `eas build -p android --profile development` | build de dev Android (APK) |
| `eas build -p ios --profile development` | build de dev iOS (iPhone enregistré via `eas device:create`) |
| `eas build -p all --profile preview` | builds de test internes |
| `eas build -p all --profile production` | builds de store |
| `eas submit -p ios` · `eas submit -p android` | envoi à TestFlight / Play Console |
| `eas update --channel production -m "message"` | correctif JS sans passer par les stores |

## Ancien jeu dans `legacy/` (phase 1 uniquement — commandes lancées depuis `legacy/`)
| Commande | Effet |
|---|---|
| `php tools/export-reference.php ../fixtures/reference` | exporte les références (P1.1, P1.2) |
| `composer preview` | planche des sprites de référence |
| `node tools/screenshot.mjs <niveau> 5 <sortie.png>` | capture d'un niveau à 5 s |
