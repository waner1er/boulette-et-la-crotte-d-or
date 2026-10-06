# workflow/ — le dossier de pilotage

Ce dossier contient tout ce qu'il faut pour construire **Boulette et la Crotte d'Or — édition universelle** :
une seule base de code **Expo + React + TypeScript** qui sort sur **iOS, Android et le web (GitHub Pages)**,
à partir du jeu actuel ([waner1er/boulette-et-la-crotte-d-or](https://github.com/waner1er/boulette-et-la-crotte-d-or), PHP + JS).

À poser à la racine du nouveau dépôt, puis à faire lire à l'agent de code au début de chaque session.

## Ordre de lecture

| Fichier | Quand le lire |
|---|---|
| [00-CONTEXTE.md](00-CONTEXTE.md) | toujours, en premier : le jeu, l'existant, les objectifs, le vocabulaire |
| [01-DECISIONS.md](01-DECISIONS.md) | avant de remettre un choix en cause (ADR numérotées) |
| [02-ARCHITECTURE.md](02-ARCHITECTURE.md) | avant de créer un fichier : où il va, de qui il dépend |
| [03-PLAN.md](03-PLAN.md) | pour savoir quoi faire maintenant : phases, tâches, critères de fin |
| [04-CARTE-MIGRATION.md](04-CARTE-MIGRATION.md) | pour porter un fichier de l'ancien jeu (`legacy/`) : sa destination et la façon de le porter |
| [05-CONVENTIONS.md](05-CONVENTIONS.md) | avant d'écrire du code |
| [06-TESTS-QUALITE.md](06-TESTS-QUALITE.md) | avant de dire « fini » |
| [07-COMMANDES.md](07-COMMANDES.md) | référence des commandes (à tenir à jour) |
| [08-JOURNAL.md](08-JOURNAL.md) | à la fin de chaque session : ce qui a été fait, ce qui reste, les surprises |

## Outillage pour l'agent de code

| Dossier | Contenu | Installation |
|---|---|---|
| [agents/](agents/) | sous-agents spécialisés (architecte, porteur, peintre Skia, audio, testeur, relecteur, publication) | copier ou lier dans `.claude/agents/` |
| [skills/](skills/) | savoir-faire réutilisables (monorepo Expo, pixel art Skia, port Web Audio, parité, publication…) | copier ou lier dans `.claude/skills/` |
| [modeles/](modeles/) | `CLAUDE.md` et `AGENTS.md` du nouveau dépôt, workflows GitHub Actions, `eas.json`, `app.json` | copier aux emplacements indiqués en tête de chaque fichier |

```bash
# depuis la racine du nouveau dépôt, une fois workflow/ en place
mkdir -p .claude
ln -s ../workflow/agents .claude/agents
ln -s ../workflow/skills .claude/skills
cp workflow/modeles/CLAUDE.md workflow/modeles/AGENTS.md .
```

## Règle d'or

**Une tâche du plan = une branche = une PR**, avec ses tests verts et le journal mis à jour.
Le plan est vivant : quand la réalité le contredit, on corrige le plan dans la même PR.
