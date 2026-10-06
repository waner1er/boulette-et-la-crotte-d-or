---
name: skia-pixel-art
description: Dessiner du pixel art net et rapide avec @shopify/react-native-skia sur iOS, Android et web pour Boulette — boucle d'affichage par Picture, mise à l'échelle au plus proche voisin, atlas de sprites créés depuis des grilles de pixels, cuisson et parallaxe des décors, animations de décor, chargement de CanvasKit sur le web. À utiliser pour toute tâche de rendu.
---

# Pixel art avec React Native Skia

> L'API de Skia évolue. Avant d'utiliser une fonction, vérifier sa signature dans la version installée
> (`node_modules/@shopify/react-native-skia/lib/typescript`). Les noms ci-dessous sont un guide, pas une garantie.

## La boucle d'affichage (ADR-005)
```tsx
const picture = useSharedValue<SkPicture | null>(null);

useEffect(() => {
  const loop = new GameLoop({ clock, update: () => game.update(), draw: () => {
    const recorder = Skia.PictureRecorder();
    const canvas = recorder.beginRecording(Skia.XYWHRect(0, 0, screenW, screenH));
    canvas.save(); canvas.translate(offsetX, offsetY); canvas.scale(scale, scale);
    paint(canvas, game.state, assets, game.frame);
    canvas.restore();
    picture.value = recorder.finishRecordingAsPicture();
  }});
  loop.start(); return () => loop.stop();
}, []);

return <Canvas style={StyleSheet.absoluteFill}><Picture picture={picture} /></Canvas>;
```
- `scale` : la plus grande valeur entière telle que `320 × scale ≤ largeur` et `180 × scale ≤ hauteur` ; si elle vaut 0
  ou laisse trop de bandes, autoriser une échelle fractionnaire (le pixel art reste net grâce au plus proche voisin,
  au prix de pixels de tailles inégales — à valider à l'œil).
- Mesurer au spike S1 ; si c'est trop lent, réduire le dessin (décors cuits, atlas) avant de changer d'approche.

## Des grilles de pixels aux images
```ts
function gridToImage(grid: readonly string[], palette: Palette, tint?: Rgba): SkImage {
  const w = grid[0]!.length, h = grid.length, bytes = new Uint8Array(w * h * 4);
  // pour chaque pixel ≠ '.', écrire R, G, B, 255 (ou la teinte « flash »)
  return Skia.Image.MakeImage(
    { width: w, height: h, colorType: ColorType.RGBA_8888, alphaType: AlphaType.Unpremul },
    Skia.Data.fromBytes(bytes), w * 4)!;
}
```
- Créer **un atlas** par famille : coller toutes les images sur une surface (`Skia.Surface.MakeOffscreen` ou une grande
  grille de pixels assemblée avant `MakeImage`), garder une table `{ animation, frame } → rect source`.
- Version « flash » (tout blanc, coup reçu) précalculée dans le même atlas.

## Dessiner net
- Images : `canvas.drawImageRectOptions(atlas, src, dst, FilterMode.Nearest, MipmapMode.None, paint)`.
- Rectangles : `canvas.drawRect(rect, paint)` avec `paint.setAntiAlias(false)`.
- Coordonnées arrondies avant de dessiner. Retournement horizontal : `save(); translate(x + w, y); scale(-1, 1); … restore()`.
- **Préparer** `Paint` et `Rect` hors de la boucle ; dans la boucle, les modifier (`setColor`, `setXYWH`) au lieu d'en créer.

## Décors (ADR-006)
- Au chargement d'un niveau, pour chacun des 4 plans : dessiner les formes **statiques** de la liste d'affichage dans
  une surface de 2 écrans de large (le motif est répété), en garder l'image.
- À chaque image : pour chaque plan, `offset = round(camera × facteur) mod 320`, dessiner l'image à `-offset`,
  puis dessiner ses éléments **animés** avec la même translation.
- Animations : une fonction par `@keyframes` de l'ancien `style.scss`, de la forme
  `(timeSec: number, delaySec: number) => { dx, dy, opacity, visible, scale }`. Le délai CSS est **négatif**
  (`animation-delay:-1.2s`) : l'animation est déjà avancée de 1,2 s au départ. Reproduire la durée, le nombre d'étapes
  (`steps(n)`), la courbe et `infinite` de chaque règle. Utiliser le temps du **moteur** (images / 60), pas l'horloge murale.

## Web : CanvasKit
- `canvaskit.wasm` doit être servi : `npx setup-skia-web public` (dans le script de build, fichier non versionné).
- Charger CanvasKit avant le premier rendu (`LoadSkiaWeb` / `WithSkiaWeb`, selon la doc de la version) ;
  avec `experiments.baseUrl`, passer le bon chemin du `.wasm` (`locateFile`).
- Afficher un écran de chargement pendant ce temps (quelques Mo au premier lancement).

## Contrôle qualité
Capture ×4 comparée à la référence ; profil sur Android d'entrée de gamme ; aucune allocation visible au profileur mémoire en jeu.
