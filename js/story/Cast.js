/**
 * Les figurants des scènes animées : ils marchent vers une cible (targetX), sautent de joie,
 * filent en prout turbo. Plus les objets du décor (state.props : la Crotte d'Or, la boîte menu enfant...).
 */
export class Cast {
    constructor(game) {
        this.game = game;
        this.state = game.state;
    }

    /**
     * Ajoute un personnage à la scène.
     * options : scale, dir, targetX (où il marche), face (sens une fois arrivé), speed, dance (saute en rythme)...
     */
    add(type, x, y, options = {}) {
        const { data } = this.game;
        const cfg = data.heroes[type] ?? data.enemies[type] ?? data.levels.find((l) => l.boss.sprite === type)?.boss ?? { hp: 1 };
        const actor = this.game.spawn(type, x, y, cfg);
        Object.assign(actor, { z: 0, vz: 0 }, options);
        actor.dir = options.dir ?? 1;
        this.state.cast.push(actor);
        return actor;
    }

    /** Un objet posé dans la scène (sprite de PropCatalog). */
    prop(sprite, x, y, options = {}) {
        const prop = { sprite, x, y, z: 0, scale: 1, glow: false, t: 0, ...options };
        this.state.props.push(prop);
        return prop;
    }

    update() {
        for (const actor of this.state.cast) this.#act(actor);
        for (const prop of this.state.props) {
            prop.t++;
            if (prop.vz !== undefined) {
                prop.x += prop.vx ?? 0;
                prop.z += prop.vz;
                prop.vz -= 0.2;
                if (prop.z <= 0) {
                    prop.z = 0;
                    prop.vz = prop.vz < -1.5 ? -prop.vz * 0.4 : undefined;
                }
            }
        }
    }

    #act(actor) {
        actor.anim++;
        actor.t++;
        if (actor.dance && actor.z === 0 && (actor.anim + actor.id * 13) % 40 === 0) actor.vz = 2.4;
        if (actor.vz || actor.z > 0) {
            actor.z += actor.vz;
            actor.vz -= 0.2;
            if (actor.z <= 0) Object.assign(actor, { z: 0, vz: 0 });
        }
        switch (actor.state) {
            case 'attack':
            case 'shoot':
                if (actor.t >= 20) actor.setState('idle');
                return;
            case 'skate':
                actor.x += actor.dir * 3;
                if (actor.t % 3 === 0) this.game.particles.cloud(actor.x - actor.dir * 22 * actor.scale, actor.y - 10, '#b4e86a', 0.1, 5);
                if (actor.t > 50) actor.setState('happy');
                return;
            case 'dead':
            case 'hurt':
                return;
        }
        if (actor.targetX !== undefined && Math.abs(actor.targetX - actor.x) > 1) {
            actor.dir = Math.sign(actor.targetX - actor.x);
            actor.x += actor.dir * (actor.speed ?? 1);
            actor.setState('walk');
        } else if (actor.state === 'walk') {
            actor.dir = actor.face ?? actor.dir;
            actor.setState(actor.rest ?? 'idle');
        }
    }
}
