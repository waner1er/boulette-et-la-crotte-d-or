import assert from 'node:assert/strict';
import { after, before, test } from 'node:test';
import { openGame } from './harness.mjs';

/** À deux sur un clavier : Boulette joue avec ZQSD + V X C W, Saucisse avec OKLM + , : ; ! */
let game;
before(async () => {
    game = await openGame();
});
after(() => game?.close());

test('2 JOUEURS : Boulette et Saucisse entrent dans le niveau 1', async () => {
    await game.press('ArrowDown');
    await game.press('Enter');
    assert.equal(await game.page.evaluate(() => window.game.state.duo), true);
    for (let i = 0; i < 40 && (await game.state()).mode === 'story'; i++) await game.press('Enter', 40);
    assert.ok(await game.until((s) => s.mode === 'playing', 10 * 60), 'le niveau 1 démarre');

    assert.deepEqual((await game.players()).map((p) => p.type), ['boulette', 'saucisse']);
    assert.deepEqual((await game.state()).lives, [3, 3]);
    assert.equal(await game.page.locator('[data-hud=p2]').isHidden(), false);
});

test('chaque joueur a son pavé', async () => {
    const [boulette, saucisse] = await game.players();

    await game.hold('KeyL', 30); // « L » : 2P descend
    await game.hold('KeyA', 30); // « Q » : 1P recule
    const [boulette2, saucisse2] = await game.players();
    assert.ok(saucisse2.y > saucisse.y, 'Saucisse descend');
    assert.equal(saucisse2.x, saucisse.x, 'Saucisse n\'a pas bougé à gauche');
    assert.ok(boulette2.x < boulette.x, 'Boulette recule');

    await game.page.keyboard.press('KeyM'); // « , » : 2P lance des frites (et ne coupe pas le son)
    await game.run(4);
    assert.equal((await game.players())[1].state, 'shoot');
    assert.equal(await game.page.locator('[data-hud=mute]').isHidden(), true);
    assert.ok(await game.page.evaluate(() => window.game.state.projectiles.some((s) => s.sprite === 'fries')));

    await game.page.keyboard.press('KeyX'); // « X » : 1P mord
    await game.run(2);
    assert.equal((await game.players())[0].state, 'attack');
    assert.deepEqual(game.errors, []);
});

test('sans se défendre : chacun perd ses vies, puis GAME OVER', async () => {
    assert.ok(await game.until((s) => s.mode === 'gameover', 40 * 60 * 60), 'game over');
    assert.deepEqual((await game.state()).lives, [0, 0]);
    assert.deepEqual(game.errors, []);
});
