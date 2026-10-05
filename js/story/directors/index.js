import { BouletteDirector } from './BouletteDirector.js';
import { CreditsDirector } from './CreditsDirector.js';
import { GoDirector } from './GoDirector.js';
import { LegendDirector } from './LegendDirector.js';
import { NavetDirector } from './NavetDirector.js';
import { PartyDirector } from './PartyDirector.js';
import { SaucisseDirector } from './SaucisseDirector.js';
import { TreasureDirector } from './TreasureDirector.js';
import { VictoryDirector } from './VictoryDirector.js';

/** Un réalisateur par plan, indexé par le nom de plan utilisé dans config/story.php. */
export function createDirectors(game) {
    const party = new PartyDirector(game);
    return {
        legend: new LegendDirector(game),
        boulette: new BouletteDirector(game),
        saucisse: new SaucisseDirector(game),
        navet: new NavetDirector(game),
        go: new GoDirector(game),
        victory: new VictoryDirector(game),
        treasure: new TreasureDirector(game),
        party,
        credits: new CreditsDirector(game, party),
    };
}
