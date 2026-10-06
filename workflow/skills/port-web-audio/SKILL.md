---
name: port-web-audio
description: Porter le synthé, les instruments, le séquenceur et les bruitages Web Audio de Boulette vers une interface AudioContextLike, implémentée par react-native-audio-api en natif et par l'AudioContext du navigateur sur le web ; déblocage, arrière-plan, interruptions, tests avec un contexte factice. À utiliser pour le spike S2 et la phase 6.
---

# Web Audio sur iOS, Android et web

## L'interface
`packages/types` déclare `AudioContextLike` : **seulement** ce que l'ancien code utilise. Pour la dresser :
`grep -ohE "ctx\.\w+|\.(frequency|gain|detune|Q|delayTime|pan|type)\b|\b(set|linearRampTo|exponentialRampTo|setTarget)\w*" js/audio`.
Attendu (à confirmer) : `currentTime`, `sampleRate`, `state`, `destination`, `createOscillator`, `createGain`,
`createBiquadFilter`, `createDelay`, `createStereoPanner`, `createBuffer`, `createBufferSource`, `createPeriodicWave`,
`suspend`, `resume`, et sur les `AudioParam` : `setValueAtTime`, `linearRampToValueAtTime`, `exponentialRampToValueAtTime`,
`setTargetAtTime`, `cancelScheduledValues`.

## Les deux implémentations
```ts
// apps/game/src/platform/createAudioContext.native.ts
import { AudioContext } from 'react-native-audio-api';
export const createAudioContext = (): AudioContextLike => new AudioContext() as unknown as AudioContextLike;

// apps/game/src/platform/createAudioContext.web.ts
export const createAudioContext = (): AudioContextLike => new globalThis.AudioContext();
```
Le cast natif se justifie par un test de conformité (`audioConformance.test.ts`) qui vérifie la présence de chaque
méthode de l'interface sur l'objet natif, exécuté dans le build de dev (écran de diagnostic caché dans les réglages).

Vérifier la page « Web Audio API coverage » de `react-native-audio-api` pour la version installée : les nœuds
utilisés (oscillateur, gain, biquad, délai, panoramique stéréo, buffer, PeriodicWave) y sont annoncés implémentés ;
noter dans le journal tout comportement différent constaté à l'écoute (enveloppes, filtre).

## Pulse 12,5 / 25 / 50 %
Les ondes pulse passent par `PeriodicWave` (coefficients de Fourier calculés une fois par rapport cyclique) ou par
deux dents de scie déphasées, selon ce que fait l'ancien `Synth.js` : **reprendre la même méthode**, ne pas la changer.

## Déblocage et cycle de vie
- Créer le contexte au démarrage, **suspendu** ; `resume()` au premier geste (appui sur l'écran titre, touche Entrée).
- Musique demandée avant le déblocage : mémorisée, lancée au `resume` (comportement actuel).
- `AppState` `background` → `suspend()` ; `active` → `resume()` si le jeu n'est pas en pause.
- iOS : régler la catégorie de session audio (jeu : mixable avec la musique du téléphone, ou non — décision à prendre,
  la noter en ADR) via l'API de session de `react-native-audio-api`.
- Interruption (appel) : traitée comme l'arrière-plan.

## Tester sans son
Un `FakeAudioContext` en TS qui enregistre les appels (`createOscillator` → nœud factice dont `start(t)`/`stop(t)` et
les `AudioParam` notent instant et valeur). Avec lui :
- le séquenceur se compare à `fixtures/reference/music-schedule/*.json` ;
- chaque bruitage a un test « produit au moins un nœud, démarre maintenant, s'arrête avant N ms ».

## Plan B (si S2 échoue)
Pré-rendre chaque morceau en fichier (rendu hors ligne `OfflineAudioContext` dans un navigateur headless au build),
le lire en boucle avec un lecteur simple ; garder la synthèse temps réel pour les bruitages seulement. Nouvelle ADR.
