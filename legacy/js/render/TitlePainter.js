/** Couleurs des barres « copper » : chaque barre est un dégradé en bandes de 1 pixel. */
const BARS = [
    ['#3a0a5a', '#7a1a9a', '#c83ad8', '#ff8af0', '#ffffff', '#ff8af0', '#c83ad8', '#7a1a9a', '#3a0a5a'],
    ['#5a2a00', '#a85a00', '#ff9a1a', '#ffd23f', '#ffffff', '#ffd23f', '#ff9a1a', '#a85a00', '#5a2a00'],
    ['#002a5a', '#004aa8', '#1a8aff', '#8adcff', '#ffffff', '#8adcff', '#1a8aff', '#004aa8', '#002a5a'],
];

/** Le texte qui défile en ondulant, comme dans les démos Amiga. */
const SCROLLER = "*** BOULETTE ET LA CROTTE D'OR *** 20 NIVEAUX DE LÉGUMES MUTANTS *** "
    + 'ESPACE : LANCE-BABALLE · V : MORSURE · B : SAUT · C : PROUT TURBO *** '
    + 'MANGE UN NUGGET ET DEVIENS SUPER BOULETTE !!! *** '
    + 'GREETINGS TO SAUCISSE, AU MÉGA MIAM, ET À TOUS LES TOUTOUS DU MONDE *** ';

/** L'écran titre façon démo Amiga : barres « copper » qui ondulent et texte défilant en vague. */
export class TitlePainter {
    constructor(ctx, width) {
        this.ctx = ctx;
        this.width = width;
    }

    draw(tick) {
        this.#bars(tick);
        this.#scroller(tick);
    }

    #bars(tick) {
        const { ctx } = this;
        ctx.globalAlpha = 0.55;
        BARS.forEach((bar, i) => {
            const y = Math.round(52 + Math.sin(tick / 40 + i * 2.1) * 40);
            bar.forEach((color, row) => {
                ctx.fillStyle = color;
                ctx.fillRect(0, y + row, this.width, 1);
            });
        });
        ctx.globalAlpha = 1;
    }

    #scroller(tick) {
        const { ctx } = this;
        ctx.font = '8px "Press Start 2P"';
        ctx.textAlign = 'left';
        const charWidth = 8;
        const total = SCROLLER.length * charWidth;
        const offset = (tick * 1.5) % total;
        const first = Math.floor(offset / charWidth);
        for (let i = 0; i < this.width / charWidth + 2; i++) {
            const char = SCROLLER[(first + i) % SCROLLER.length];
            const x = i * charWidth - (offset % charWidth);
            const y = 174 + Math.round(Math.sin((x + tick * 2) / 22) * 3);
            ctx.fillStyle = '#000';
            ctx.fillText(char, x + 1, y + 1);
            ctx.fillStyle = `hsl(${(x + tick * 3) % 360}, 100%, 65%)`;
            ctx.fillText(char, x, y);
        }
    }
}
