---
name: port-php-vers-ts
description: Traduire fidèlement le PHP de Boulette (src/ et config/ — pixel art, sprites, décors, tracker, niveaux, hasard) en TypeScript pour packages/content et packages/forge, avec les pièges de traduction PHP → JS. À utiliser pour toute tâche de la phase 2.
---

# Porter du PHP en TypeScript sans rien changer

## Méthode, fichier par fichier
1. Lire le fichier PHP **en entier**, et ses dépendances directes.
2. Créer le fichier TS au chemin donné par `workflow/04-CARTE-MIGRATION.md`. Même nom de classe, mêmes méthodes, même ordre.
3. Écrire le test de parité correspondant (skill `parite-reference`).
4. Traduire ligne à ligne, en relisant la liste de pièges ci-dessous.
5. Lancer la parité ; en cas d'écart, chercher le premier écart et revenir au PHP.

## Correspondances
| PHP | TypeScript |
|---|---|
| `final class X` | `export class X` (pas d'héritage prévu) |
| `readonly` (propriété / classe) | `readonly` ; objets valeur figés si utile |
| `enum Mood: string` | `export const Mood = {…} as const` + `type Mood = …` (ou `enum` si les méthodes d'enum sont utilisées) |
| `match` | `switch` exhaustif avec `satisfies never` dans `default` |
| tableau liste `[1, 2]` | `number[]` |
| tableau associatif `['a' => 1]` | `Record<string, number>` **ou** `Map` si l'**ordre d'insertion de clés numériques** compte (voir piège 6) |
| `array_map`, `array_filter` | `.map`, `.filter` — mais `array_filter` **garde les clés** : réindexer avec `array_values` = `.filter` en JS |
| `str_repeat($c, $n)` | `c.repeat(n)` |
| `strlen` / `str_split` / `substr` | `.length` / `[...s]` / `.slice` (attention : `substr($s, $start, $length)` ≠ `slice(start, end)`) |
| `implode('', $a)` | `a.join('')` |
| `count_chars($s, 3)` | `[...new Set(s)].sort().join('')` |
| `array_reverse` | `[...a].reverse()` (ne pas muter) |
| `max`/`min` sur tableau | `Math.max(...a)` |

## Pièges numériques (les vrais pièges)
1. **`round()` PHP arrondit « loin de zéro »** : `round(-2.5) = -3`, `Math.round(-2.5) = -2`.
   Utiliser `phpRound(x) = Math.sign(x) * Math.round(Math.abs(x))` partout où l'ancien code a `round`.
2. **`intdiv($a, $b)`** tronque vers zéro → `Math.trunc(a / b)` (pas `Math.floor`).
3. **`(int) $x`** tronque vers zéro → `Math.trunc(x)`.
4. **`/` PHP** renvoie un float si la division n'est pas exacte, comme JS ; mais `$a % $b` PHP est **entier**
   (convertit les floats en int avant) : `phpMod(a, b) = Math.trunc(a) % Math.trunc(b)`.
5. **`sprintf`** : `%d` tronque un float vers zéro ; `%.1f` arrondit au plus proche → `x.toFixed(1)` convient pour des
   dixièmes exacts (`tenths()`), sinon écrire un formateur dédié et le tester contre la référence.
   La conversion implicite d'un float en chaîne en PHP (`"$x"`) utilise 14 chiffres significatifs (`precision`), JS en utilise 17 :
   ne jamais concaténer un float « brut » dans une sortie, passer par un formateur.
6. **Ordre des clés** : un tableau PHP garde l'ordre d'insertion même pour les clés numériques ; un objet JS trie les clés
   entières en premier. Si l'ordre de parcours compte (palettes, catalogues), utiliser `Map` ou un tableau de paires.
7. **`json_encode`** échappe `/` en `\/` et l'unicode en `\uXXXX` : ne jamais comparer des empreintes de JSON entre
   PHP et JS, comparer en **égalité profonde**.
8. Entiers > 2^53 : n'existent pas ici, sauf dans Mt19937 (voir ci-dessous) → travailler en 32 bits non signés (`>>> 0`, `Math.imul`).

## Hasard : Mt19937 et `Randomizer::getInt`
`SeededRandom` utilise `new Randomizer(new Mt19937($seed))`. Le port doit reproduire **exactement** :
- l'initialisation et le rechargement de Mt19937 tels qu'implémentés par PHP (mode `MT_RAND_MT19937`) ;
- le tirage dans un intervalle de PHP ≥ 8.2 pour `getInt($min, $max)` : sortie 32 bits **complète** du générateur
  (pas décalée d'un bit comme `mt_rand()` sans argument), puis réduction à l'intervalle `umax = max - min` :
  si `umax + 1` est une puissance de 2 → masque ; sinon rejet des valeurs au-delà de la plus grande multiple, puis modulo ;
  au-delà de 32 bits, combinaison de deux tirages.
- Ces détails sont **à vérifier dans le code source PHP de la version utilisée** (`ext/random/`), puis contre
  `fixtures/reference/random.json` : c'est la seule source de vérité. Toute la forge en dépend.

## Ce qu'on ne fait pas pendant un port
Renommer, factoriser, « corriger » un comportement étrange, changer un algorithme, réordonner des appels.
On le note dans le journal, et on le fera dans une tâche à part, **après** la parité.
