import assert from 'node:assert/strict';
import { after, before, test } from 'node:test';
import { autopilot, openGame } from './harness.mjs';

/** Une partie en solo : écran titre, intro, niveau 1 jusqu'au boss, Super Boulette. */
let game;
before(async () => {
    game = await openGame();
});
after(() => game?.close());

test('écran titre puis intro, puis le niveau 1', async () => {
    assert.equal((await game.state()).mode, 'title');
    await game.press('Enter', 30);
    assert.equal((await game.state()).mode, 'story', 'l\'intro démarre');
    // START fait avancer l'intro étape par étape
    for (let i = 0; i < 40 && (await game.state()).mode === 'story'; i++) await game.press('Enter', 40);
    assert.ok(await game.until((s) => s.mode === 'playing', 10 * 60), 'le niveau 1 démarre');
    assert.deepEqual((await game.players()).map((p) => p.type), ['boulette']);
    assert.deepEqual(game.errors, []);
});

test('ESPACE lance une baballe, V mord, C fait un prout turbo', async () => {
    await game.page.keyboard.press('Space');
    await game.run(4);
    assert.equal((await game.players())[0].state, 'shoot');
    assert.equal(await game.page.evaluate(() => window.game.state.projectiles.filter((s) => s.owner === 'hero').length), 1);
    await game.run(20);
    await game.page.keyboard.press('KeyV');
    await game.run(2);
    assert.equal((await game.players())[0].state, 'attack');
    await game.run(30);
    const before = (await game.players())[0].x;
    await game.page.keyboard.press('KeyC');
    await game.run(20);
    const dog = (await game.players())[0];
    assert.equal(dog.state, 'skate');
    assert.ok(dog.x > before + 30, 'le prout propulse Boulette');
    assert.ok(await game.page.evaluate(() => window.game.state.clouds.length > 0), 'nuages de prout');
});

test('un nugget transforme Boulette en Super Boulette', async () => {
    await game.page.evaluate(() => {
        const [p] = window.game.state.players;
        p.invuln = 1e9;
        p.setState('idle');
        window.game.pickups.spawn('nugget', p.x, p.y);
    });
    await game.run(70);
    assert.ok(await game.page.evaluate(() => window.game.isSuper(window.game.state.players[0])));
    assert.equal(await game.page.locator('[data-hud=super]').isHidden(), false);
});

test('le pilote automatique termine le niveau 1 (boss compris)', async () => {
    await game.page.evaluate(() => {
        window.game.state.players[0].invuln = 1e9;
    });
    const mode = await autopilot(game, 90 * 60);
    assert.equal(mode, 'clear', 'niveau terminé');
    assert.ok((await game.state()).score > 3000);
    assert.ok(await game.until((s) => s.mode === 'playing' && s.level === 1, 20 * 60), 'le niveau 2 démarre');
    assert.deepEqual(game.errors, []);
});
