# Boulette et la Crotte d'Or

Beat'em up d'arcade en pixel art cartoon, façon jeux Amiga et Atari des années 90.
Boulette, une carline, cherche la légendaire **Crotte d'Or** cachée sous le fast-food **MÉGA MIAM**.
Elle traverse **20 niveaux** (5 zones × 4) remplis de légumes mutants, avec son lance-baballe, ses morsures
et ses prouts supersoniques. Son ami Saucisse, le teckel qui travaille au fast-food, lui apporte les cadeaux des menus enfants.
À deux, Saucisse rejoint la bagarre.

**▶ Jouer : https://waner1er.github.io/boulette-et-la-crotte-d-or/**

## Commandes

Au clavier, ou sur mobile avec le stick et les boutons dessinés sur la borne (en paysage).

| Touche | Action |
|---|---|
| ← → ↑ ↓ | marcher |
| Espace | lance-baballe (la baballe décompose les légumes et rebondit) |
| V | morsure |
| B | saut (on retombe gueule ouverte sur les légumes) |
| C | prout turbo : glissade invincible qui renverse tout |
| Entrée | start / passer les dialogues |
| M | couper la musique |

**Nugget** : Super Boulette pendant 10 secondes (dorée, cape, invincible, elle déchiquette les légumes).
**Boîte menu enfant** : mords-la ou tire dessus pour l'ouvrir (nugget, os, jouet = 1 vie, baballes d'or perçantes).

**À deux** (menu « 2 JOUEURS », clavier AZERTY) :

| Action | 1P · Boulette | 2P · Saucisse |
|---|---|---|
| marcher | Z Q S D | O K L M |
| tirer (baballe / frites) | V | , |
| morsure | X | : |
| prout turbo | C | ; |
| saut | W | ! |

## Le jeu

| Zone | Niveaux | Grand boss |
|---|---|---|
| Le parking | 1 à 4 | Courgetron |
| La salle | 5 à 8 | Le Roi Brocoli |
| Les cuisines | 9 à 12 | Lady Carotte |
| La chambre froide | 13 à 16 | Chou-Fleur des Glaces |
| Le labo secret | 17 à 20 | Professeur Navet |

Chaque niveau : trois vagues de légumes, puis un boss (un sous-chef aux niveaux 1 à 3 de chaque zone, le grand boss au 4e).
Les ennemis : courgette mutante, radis furieux, petit pois commando, tomate kamikaze, oignon pleureur, brocoli zombie,
carotte moisie, aubergine catcheuse.

## Comment c'est fait

Même architecture que *Vigilante – Behind the Mask* :

- **PHP** (`src/`, namespace `Boulette\`) **fabrique** tout : les sprites sont décrits en texte (1 caractère = 1 pixel)
  et assemblés en pièces (têtes, corps, pattes, gants, baskets), les décors sont des SVG procéduraux tramés,
  et la **musique** est écrite note à note dans `config/music.php` et compilée par un petit tracker.
- **JavaScript** (`js/`, modules ES, sans dépendance) **anime** : boucle à 60 images/s, IA, rendu canvas,
  séquenceur 16 bits (ondes pulse, arpèges à 50 Hz, stéréo Amiga) et bruitages synthétisés.
- **SCSS** (`style.scss` → `build/style.css`) dessine la borne.

Documentation : [architecture](docs/architecture.md) · [modifier le contenu](docs/content.md) · [développer, tester, publier](docs/workflow.md).

## Lancer en local

```bash
composer install && npm install
npm run css                    # styles de la borne
composer serve                 # http://localhost:8000
```

## Vérifier

```bash
composer check                 # PSR-12, PHPStan, PHPUnit (dont empreintes des décors et sprites)
npm run lint:js                # syntaxe des modules JS
npm run test:e2e               # le jeu dans Chrome headless (dont un pilote automatique qui finit le niveau 1)
```

## Publier (GitHub Pages)

```bash
composer build                 # écrit index.html et scenes/level-N.html (version statique, sans PHP)
```
