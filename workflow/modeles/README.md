# modeles/ — fichiers à copier dans le nouveau dépôt

| Modèle | Destination | Tâche du plan |
|---|---|---|
| `AGENTS.md` | racine du dépôt | P0.1 |
| `CLAUDE.md` | racine du dépôt (il inclut `AGENTS.md`) | P0.1 |
| `ci.yml` | `.github/workflows/ci.yml` | P0.3 |
| `deploy-web.yml` | `.github/workflows/deploy-web.yml` | P8.2 |
| `eas.json` | `apps/game/eas.json` | P0.4 |
| `app.json` | `apps/game/app.json` (à fusionner avec celui créé par `create-expo-app`) | P0.2 |

À vérifier au moment de copier :
- **Versions** : versions majeures des actions GitHub, version minimale d'`eas-cli`, options des plugins pour le SDK installé.
- **Identifiants** `fr.erwanrivet.boulette` (iOS et Android) : à choisir définitivement avant le premier build de store.
- **`experiments.baseUrl`** : nécessaire pour GitHub Pages (sous-chemin du dépôt) ; s'il gêne en développement,
  le poser seulement pendant l'export web (variable d'environnement lue par un `app.config.ts`).
- **`react-native-audio-api`** dans `plugins` : seulement si son README le demande pour la version installée.
