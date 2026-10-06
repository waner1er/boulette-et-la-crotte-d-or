/**
 * Convertit les grilles de pixels envoyées par PHP (1 caractère = 1 pixel) en petits canvas prêts à dessiner.
 * Chaque image de personnage existe en version normale et en version « flash » toute blanche (coup reçu).
 */
export class SpriteBank {
    constructor(data) {
        this.fighters = SpriteBank.#animations(data.sprites);
        this.items = SpriteBank.#images(data.items.sprites, data.items.palette);
    }

    static toCanvas(grid, palette, tint = null) {
        const canvas = document.createElement('canvas');
        canvas.width = grid[0].length;
        canvas.height = grid.length;
        const g = canvas.getContext('2d');

        grid.forEach((row, y) => {
            for (let x = 0; x < row.length; x++) {
                const color = tint ?? palette[row[x]];
                if (row[x] === '.' || !color) continue;
                g.fillStyle = color;
                g.fillRect(x, y, 1, 1);
            }
        });

        return canvas;
    }

    /** { type: { anchor, anims: { animation: [{ normal, flash }] } } } */
    static #animations(sheets) {
        const result = {};
        for (const [type, sheet] of Object.entries(sheets)) {
            const anims = {};
            for (const [anim, frames] of Object.entries(sheet.frames)) {
                anims[anim] = frames.map((grid) => ({
                    normal: SpriteBank.toCanvas(grid, sheet.palette),
                    flash: SpriteBank.toCanvas(grid, sheet.palette, '#ffffff'),
                }));
            }
            result[type] = { anchor: sheet.anchor, anims, dog: Boolean(anims.shoot) };
        }
        return result;
    }

    static #images(grids, palette) {
        return Object.fromEntries(Object.entries(grids).map(([name, grid]) => [name, SpriteBank.toCanvas(grid, palette)]));
    }
}
