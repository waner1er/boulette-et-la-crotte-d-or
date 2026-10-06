---
name: publication
description: Publier Boulette — CI GitHub Actions, export web Expo sous un sous-chemin et déploiement GitHub Pages, builds EAS (iOS sans Mac, Android), EAS Submit vers TestFlight et Play Console, EAS Update, icônes, écran de démarrage et fiches stores. À utiliser en phases 0, 8 et 9.
---

# Publier sur le web et les stores

> Les règles des stores et les versions d'Expo changent : vérifier la doc actuelle avant chaque étape et noter
> dans le journal ce qui a différé de ce skill.

## Web — GitHub Pages
1. `app.json` :
   ```json
   { "expo": { "web": { "output": "static", "bundler": "metro" },
               "experiments": { "baseUrl": "/boulette-et-la-crotte-d-or" } } }
   ```
   `baseUrl` = nom du dépôt (site de projet). Sans lui : page blanche, scripts cherchés à la racine du domaine.
2. Script `export:web` de l'app : `setup-skia-web public && expo export -p web`.
3. Workflow : modèle `workflow/modeles/deploy-web.yml` → `.github/workflows/deploy-web.yml`.
4. Dans les réglages du dépôt : *Pages → Source : GitHub Actions*.
5. Vérifier en local avant de pousser : servir `apps/game/dist` sous le sous-chemin (`npx serve` avec une réécriture,
   ou un dossier `dist-test/boulette-et-la-crotte-d-or/`).
6. Fichier `.nojekyll` dans la sortie (GitHub Pages ignore sinon les dossiers commençant par `_`, comme `_expo/`).

## Mobile — EAS
- `eas init` (lie le projet Expo), `eas.json` : modèle `workflow/modeles/eas.json`.
- **iOS sans Mac** : `eas credentials` gère certificats et profils ; `eas device:create` pour enregistrer l'iPhone
  (build de dev ou `preview` interne) ; `eas build -p ios` compile sur les serveurs Expo.
- **Android** : `eas build -p android --profile preview` → APK installable ; `production` → AAB pour le Play Store.
- `runtimeVersion` : politique `fingerprint` (recommandée) pour qu'EAS Update ne pousse jamais du JS vers un binaire
  incompatible.
- Numéros de build : `"autoIncrement": true` sur le profil `production`, version applicative dans `app.json`.

## Envoi
- `eas submit -p ios` : clé d'API App Store Connect stockée dans EAS ; arrive dans TestFlight.
- `eas submit -p android` : compte de service Google Play ; le **premier** envoi d'un AAB se fait souvent à la main dans
  la Play Console ; puis piste « test fermé ». Compte personnel récent : exigence de test fermé (nombre de testeurs
  et durée à vérifier au moment de publier — 12 testeurs pendant 14 jours à l'écriture de ce skill).

## Fiches stores
- Icône 1024×1024 sans transparence (iOS), icône adaptative (Android, premier plan + fond), écran de démarrage.
- Captures paysage générées par le mode capture (P4.8), aux tailles demandées par chaque store.
- Politique de confidentialité : l'app ne collecte aucune donnée → le dire, l'héberger (page sur le site perso ou GitHub Pages).
- Classification d'âge : questionnaire (violence cartoon légère).
- Apple (règle 4.2) : app complète, hors ligne, sans dépendance à un site → c'est notre cas ; le préciser dans les notes de revue.

## CI
Modèle `workflow/modeles/ci.yml` : pnpm + cache, `pnpm check`, export web, e2e Playwright sur la PR.
Secrets : `EXPO_TOKEN` (pour lancer des builds EAS depuis Actions, plus tard).
