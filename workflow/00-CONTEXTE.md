# 00 — Contexte

## Le jeu

*Boulette et la Crotte d'Or* : beat'em up d'arcade en pixel art cartoon, façon Amiga/Atari des années 90.
Boulette, une carline, cherche la légendaire **Crotte d'Or** cachée sous le fast-food **MÉGA MIAM**.

- **20 niveaux** = 5 zones × 4 (parking, salle, cuisines, chambre froide, labo secret).
- Chaque niveau : 3 vagues de légumes mutants, puis un boss (sous-chef aux niveaux 1-3 d'une zone, grand boss au 4e).
- **1 ou 2 joueurs** : Boulette (carline) et Saucisse (teckel).
- Attaques : lance-baballe, morsure, saut (retombée gueule ouverte), prout turbo (glissade invincible).
- Bonus : nugget (Super Boulette 10 s), os, boîtes menu enfant (nugget, os, jouet = 1 vie, baballes d'or).
- Cinématiques (intro, fin, générique), musique de tracker synthétisée, bruitages synthétisés.
- Écran logique **320 × 180**, 60 images/s à pas fixe.

## L'existant (dépôt d'origine)

`github.com/waner1er/boulette-et-la-crotte-d-or` — en ligne sur GitHub Pages.

| Moitié | Rôle | Taille |
|---|---|---|
| **PHP** `src/` + `config/` | **fabrique** : sprites en texte (1 caractère = 1 pixel), décors SVG procéduraux tramés et animés en CSS, musique compilée par un tracker, données du jeu | ~4 450 lignes |
| **JS** `js/` (modules ES, sans dépendance) | **anime** : boucle à pas fixe, IA, combats, rendu canvas 2D, parallaxe SVG (DOM), séquenceur Web Audio, HUD en DOM | ~3 950 lignes |
| **SCSS** `style.scss` | la borne d'arcade, les animations CSS des décors (`blink`, `bob`, `rise`, `steam`, `bubble`, `flicker`…) | — |
| **Tests** | PHPUnit (empreintes SHA-1 des 20 décors et des sprites, tracker, niveaux, hasard), e2e Playwright avec horloge virtuelle et pilote automatique | — |

Points forts à préserver :
- **La logique est déjà découplée de la plateforme** : `actors/`, `world/`, `core/GameState`, `ui/` (hors DOM) et `util/math`
  n'utilisent ni DOM ni canvas. Seuls 14 fichiers touchent la plateforme (voir [04-CARTE-MIGRATION.md](04-CARTE-MIGRATION.md)).
- **Le contenu est dans `config/`**, pas dans le code.
- **Les décors sont déterministes** (`SeededRandom` sur Mt19937) et vérifiés par empreinte.
- Unités : durées en **images** (1/60 s), distances en **pixels d'écran**.

Points faibles à corriger :
- Deux langages, deux chaînes d'outils (Composer + npm), PHP nécessaire pour publier.
- `index.html` et `scenes/` générés **et commités**.
- Le HUD et la parallaxe passent par le DOM et le CSS : non portables en natif.
- 19 appels à `Math.random()` dans la logique : impossible de rejouer une partie à l'identique.
- Sur iOS, ni plein écran ni verrouillage paysage depuis le navigateur.

## L'objectif

**Une app solide, maintenable, sur trois cibles, depuis un seul code TypeScript :**

1. **iOS** et **Android** natifs (pas de WebView), publiés via EAS, **sans Mac**.
2. **Web** statique, déployé automatiquement sur GitHub Pages à chaque push sur `main`.
3. **Parité** avec le jeu actuel : mêmes sprites (au pixel), mêmes décors, même musique, même gameplay.
4. **Plus de PHP** : la fabrique est portée en TypeScript, vérifiée contre les sorties PHP avant suppression.

Hors périmètre de la v1 (à garder en tête pour ne pas fermer de portes) : manette Bluetooth sur mobile,
mode 2 joueurs sur un seul écran tactile, sauvegarde cloud, achats intégrés, classement en ligne.

## Contraintes

- Développeur seul, en soirées : des tâches courtes, livrables indépendamment.
- Pas de Mac : tout ce qui est iOS passe par EAS Build / EAS Submit / build de développement sur iPhone.
- Comptes nécessaires : Expo (gratuit), Apple Developer (99 $/an), Google Play Console (25 $ une fois ;
  test fermé de 14 jours avec 12 testeurs pour un compte personnel récent).
- La version web actuelle reste en ligne jusqu'à ce que la nouvelle soit à parité (phase 8).

## Vocabulaire

| Terme | Sens |
|---|---|
| **forge** | le paquet qui fabrique les assets (ex-PHP) : sprites, décors, musique, données des niveaux |
| **moteur** (`engine`) | la logique du jeu, sans aucune dépendance de plateforme |
| **peintre** | un module de rendu Skia qui dessine une partie de l'état (personnages, effets, HUD…) |
| **plan** (de décor) | une couche de parallaxe : fond fixe + 3 plans aux facteurs 0.25, 0.6 et 1 |
| **liste d'affichage** | description structurée d'un décor (formes statiques + éléments animés), produite par la forge |
| **parité** | sortie TS identique à la sortie PHP de référence (octet par octet ou en égalité profonde) |
| **référence** (`fixtures/reference/`) | sorties du PHP figées avant sa suppression, qui servent d'étalon |
| **image** | 1/60 s de logique (unité de durée), à ne pas confondre avec une image bitmap (on dit alors *bitmap* ou *sprite*) |
| **build de dev** | app Expo compilée avec nos modules natifs (Skia, audio), installée sur le téléphone ; Expo Go ne suffit pas |
