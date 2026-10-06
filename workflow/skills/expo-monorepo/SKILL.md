---
name: expo-monorepo
description: Mettre en place et entretenir le monorepo pnpm + Turborepo de Boulette avec une app Expo universelle (iOS, Android, web) — création des paquets, tsconfig, Metro, tâches Turborepo, ajout de dépendances Expo. À utiliser en phase 0 et dès qu'on touche à la structure, aux dépendances ou à la config de build.
---

# Monorepo Expo — pnpm + Turborepo

## Squelette
```
package.json            "packageManager": "pnpm@<version>", scripts: check, lint, typecheck, test, forge
pnpm-workspace.yaml     packages: ["apps/*", "packages/*"]
turbo.json              tâches : forge, lint, typecheck, test, export:web
.nvmrc                  version de Node (lue par la CI)
.npmrc                  (si besoin) node-linker=hoisted
packages/config/        tsconfig.base.json, eslint.config.js, vitest.shared.ts
```
Chaque paquet : `package.json` avec `"name": "@boulette/<nom>"`, `"type": "module"`, `"exports": { ".": "./src/index.ts" }`
(on consomme les sources TS directement : Metro et Vitest savent les transpiler ; pas de build intermédiaire des paquets).

## tsconfig de base (strict)
`strict`, `noUncheckedIndexedAccess`, `exactOptionalPropertyTypes`, `noImplicitOverride`, `verbatimModuleSyntax`,
`moduleResolution: "bundler"`, `target: "ES2022"`. L'app étend `expo/tsconfig.base` **et** nos règles strictes.

## turbo.json (principe)
```jsonc
{
  "tasks": {
    "forge":      { "inputs": ["src/**", "bin/**", "../content/src/**"], "outputs": ["dist/**"] },
    "typecheck":  { "dependsOn": ["^typecheck"] },
    "test":       { "dependsOn": ["forge"] },
    "lint":       {},
    "export:web": { "dependsOn": ["forge"], "outputs": ["dist/**"] }
  }
}
```

## Créer l'app
1. `pnpm create expo-app apps/game --template` (modèle TS avec Expo Router), puis supprimer les écrans d'exemple.
2. **Vérifier la version du SDK** créée et lire ses notes de version (changements d'Expo Router, version de Node requise).
3. `app.json` : `orientation: "landscape"`, `userInterfaceStyle: "dark"`, `web.output: "static"`, `web.bundler: "metro"`,
   `experiments.baseUrl` (seulement pour l'export GitHub Pages — voir skill `publication`).
4. Ajouter les modules natifs **avec `npx expo install`** (versions alignées sur le SDK) :
   `@shopify/react-native-skia react-native-reanimated react-native-gesture-handler react-native-audio-api
   @react-native-async-storage/async-storage expo-haptics expo-screen-orientation react-native-safe-area-context`.
5. Plugins de config dans `app.json` pour les modules qui en demandent (lire leur README).
6. Ces modules natifs imposent un **build de dev** : `npx expo install expo-dev-client`, puis `eas build --profile development`.

## Metro dans un monorepo
Depuis le SDK 52, `expo/metro-config` détecte le monorepo tout seul : partir de
`const config = getDefaultConfig(__dirname)` sans réglage manuel de `watchFolders`. N'ajouter de réglage que sur
un problème constaté, en le commentant. Symptôme classique (module natif introuvable, double React) → `node-linker=hoisted`.

## Frontières entre paquets
`eslint-plugin-boundaries` (ou `import/no-restricted-paths`) avec les règles du tableau de `workflow/02-ARCHITECTURE.md`.
Ajouter aussi, pour `packages/engine` : `no-restricted-globals` (`performance`, `requestAnimationFrame`, `localStorage`,
`document`, `window`) et `no-restricted-properties` (`Math.random`, `Date.now`).

## Pièges
- Ne jamais `npm install` dans un paquet : toujours `pnpm --filter <paquet> add`.
- Une dépendance native ajoutée = nouveau build de dev obligatoire.
- `expo-doctor` après chaque changement de dépendances.
