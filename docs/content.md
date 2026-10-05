# Modifier le contenu du jeu

La plupart des changements se font **sans toucher au code**, dans `config/`. Après chaque modification :
`composer check` (et `UPDATE_SNAPSHOTS=1 composer test` si un décor ou un sprite a changé volontairement), essai dans le navigateur,
puis `composer build` avant de publier.

## Un niveau

`config/levels.php`, indexé par numéro (1 à 20) ; 4 niveaux par zone, le 4e est celui du grand boss.

```php
6 => [
    'title' => 'LE COIN ANNIV',
    'theme' => ['zone' => 'dining', 'seed' => 202, 'time' => 'day', 'accent' => '#ff3ea5', 'signs' => ['JOYEUX', 'ANNIV']],
    'music' => 'dining',
    'boss' => [
        'sprite' => 'oignon', 'scale' => 2, 'name' => 'OIGNON DÉPRIMÉ',
        'hp' => 26, 'speed' => 0.6, 'damage' => 12,
        'special' => 'throw', 'projectile' => 'tear', 'every' => 100,
        'line' => 'PERSONNE NE M\'INVITE AUX ANNIVS... SNIF !',
    ],
],
```

- `theme.zone` : `parking`, `dining`, `kitchen`, `freezer`, `lab` ; `time` : `day`, `sunset`, `night` ; options `rain`, `snow`, `alarm`.
- `boss.sprite` : un légume de `config/game.php` (sous-chef géant) ou un boss de `Sprite\Boss\BossRoster`.
- `boss.special` : `summon` (appelle des légumes), `throw` (+ `projectile` : `pea`, `tear`, `ice`, `flask`), `charge`, `burrow` (creuse sous terre).
  `summons => true` : alterne avec des appels de légumes (Professeur Navet).

Les vagues ne se configurent pas : `Level\WaveGenerator` en crée trois, de plus en plus nombreuses, avec les légumes débloqués
(`config/game.php` → `unlocks`). La boîte menu enfant est placée automatiquement.

## Les légumes, les chiens, les bonus

`config/game.php` :

- `enemies` : `hp`, `speed`, `damage`, `reach`, `score`, `moves` (poids : `strike` = approcher, `lunge` = ruée, `throw` = lancer,
  `kamikaze` = foncer et exploser), `projectile` + `every` pour les tireurs ;
- `unlocks` : à partir de quel niveau chaque légume apparaît ;
- `attacks` : morsure et lance-baballe, normaux et « super » ;
- `pickups` et `gifts` : les bonus et le contenu (pondéré) des boîtes menu enfant.

## Ajouter un légume

1. Une classe dans `src/Sprite/Veggie/` qui étend `VeggieDesign` : `palette()` (dont `L`/`l`/`k` pour bras et jambes), `body()`
   (souvent avec `Shape::rows()`), `face()` (où poser les yeux), `shoulder()`, et en option `over()`, `back()`, `zombie()`.
2. L'ajouter à `Sprite\CharacterCatalog`, ses réglages dans `config/game.php` → `enemies`, son niveau d'apparition dans `unlocks`.
3. Sa voix : `js/audio/sounds.js` → `VOICES` (facultatif). Puis `UPDATE_SNAPSHOTS=1 composer test`.

## Ajouter un boss

Une classe dans `src/Sprite/Boss/` (souvent un légume décliné avec une couronne : `Crown::at()`), enregistrée dans `BossRoster`,
puis utilisée dans `config/levels.php` → `boss.sprite`.

## Dessiner en pixel art

Un sprite est une liste de chaînes : **1 caractère = 1 pixel**, `.` = transparent, chaque lettre = une couleur de la palette ;
le contour noir `K` est ajouté automatiquement. Pour voir le résultat : `composer preview` (planche PNG x4 dans `previews/`).

Lettres communes des légumes (`VeggieDesign::BASE`) : `W`/`w` gants, `E` pupilles, `B` sourcils, `Q` bouche, `O`/`o`/`X` baskets.

## La musique

`config/music.php` : un morceau = `bpm`, instrument de la mélodie (`pulse12`, `pulse25`, `square`, `saw`, `triangle`, `bell`),
accords (`arp` ou `pad`), style de basse (`bounce`, `pump`, `walk`, `gallop`, `sustain`), une mesure de batterie (`k s h o c .`),
des motifs et leur enchaînement :

```php
'A' => [
    'chords' => 'C Am F G',                      // un accord par mesure ; « F,G » = deux par mesure
    'lead' => 'E5 - G5 - C6 - B5 - G5 - - - E5 - G5 - | ...',   // 16 pas par mesure ; - tient, . silence
],
```

`loop => false` pour un jingle. Un niveau choisit son morceau dans `config/levels.php` → `music`.

## Le scénario

`config/story.php` : `intro`, `ending`, `credits`. Une étape : `['scene' => 'saucisse', 'text' => '…']`, options `shout`,
`action => 'dash'` (prout turbo de Boulette), `duration`. `[PAUSE 2]` fige la machine à écrire 2 secondes.

Plans : `legend`, `boulette`, `saucisse`, `navet`, `go` (intro) ; `victory`, `treasure`, `party`, `credits` (fin).
Un nouveau plan = une classe qui étend `Director` dans `js/story/directors/`, enregistrée dans `index.js`.
