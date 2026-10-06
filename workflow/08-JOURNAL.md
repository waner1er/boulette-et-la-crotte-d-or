# 08 — Journal

Une entrée par session, **la plus récente en haut**. C'est la mémoire du projet entre deux soirées (et entre deux sessions d'agent).

Modèle :
```
## AAAA-MM-JJ — P2.4 Sprites des chiens
**Fait** : …
**Reste** : …
**Surprises / décisions** : … (si c'est une décision durable → ADR dans 01)
**Prochaine étape** : …
```

---

## 2026-10-06 — Cadrage
**Fait** : analyse de l'ancien dépôt (PHP ~4 450 lignes, JS ~3 950 lignes ; logique déjà découplée de la plateforme) ;
choix Expo + React + TS universel, abandon du PHP, Skia, monorepo ; rédaction du dossier `workflow/`.
**Surprises / décisions** : les décors sont animés en CSS (`@keyframes` + `animation-delay` aléatoires) → ADR-006
(listes d'affichage). 19 `Math.random()` dans la logique → ADR-009.
**Prochaine étape** : P0.1 (monorepo) et, en parallèle, P1.1 (export des références dans l'ancien dépôt).
