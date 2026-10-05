<?php

/**
 * Le scénario : l'intro avant le niveau 1, la fin après le Professeur Navet, et le générique.
 *
 * Une étape = un plan (scene) mis en scène par js/story/directors/ + un texte tapé lettre
 * par lettre. Deux étapes de suite sur le même plan : l'animation continue.
 * Options : shout (bulle au-dessus de Boulette), action => dash | happy | jump, duration (passe toute seule
 * au bout de N images). [PAUSE 2] fige la machine à écrire 2 secondes.
 *
 * Plans : legend, boulette, saucisse, navet, go (intro) ; victory, treasure, party, credits (fin).
 */

return [
    'intro' => [
        ['scene' => 'legend', 'text' => "IL ÉTAIT UNE FOIS, CACHÉE SOUS LE FAST-FOOD MÉGA MIAM... LA LÉGENDAIRE CROTTE D'OR."],
        ['scene' => 'legend', 'text' => "ON RACONTE QUE CELUI QUI LA TROUVE AURA DES NUGGETS À VOLONTÉ. [PAUSE 1] POUR TOUJOURS. [PAUSE 1] OUI, TOUJOURS."],
        ['scene' => 'boulette', 'text' => "VOICI BOULETTE. CARLIN. 8 KILOS DE MUSCLES. ENFIN... 8 KILOS."],
        ['scene' => 'boulette', 'text' => "SES PASSIONS : LES BABALLES, LES SIESTES, ET LES PROUTS SUPERSONIQUES.", 'action' => 'dash'],
        ['scene' => 'saucisse', 'text' => "SON MEILLEUR AMI, SAUCISSE LE TECKEL, TRAVAILLE AU MÉGA MIAM. IL LUI GARDE TOUJOURS LES CADEAUX DES MENUS ENFANTS."],
        ['scene' => 'saucisse', 'text' => "SAUCISSE : « TIENS BOULETTE ! LE JOUET DU JOUR : UN LANCE-BABALLE TURBO ! »", 'shout' => 'OUAF !'],
        ['scene' => 'navet', 'text' => "MAIS AU SOUS-SOL, LE TERRIBLE PROFESSEUR NAVET A DÉROBÉ LA CROTTE D'OR..."],
        ['scene' => 'navet', 'text' => "NAVET : « GRÂCE À SES RAYONS, MES LÉGUMES DEVIENNENT MUTANTS ! PLUS JAMAIS ON NE LES LAISSERA AU BORD DE L'ASSIETTE ! MOUAHAHA ! »"],
        ['scene' => 'go', 'text' => "COURGETTES MUTANTES. BROCOLIS ZOMBIES. CAROTTES MOISIES. 20 NIVEAUX DE LÉGUMES."],
        ['scene' => 'go', 'text' => "BOULETTE : « OUAF OUAF ! » (TRADUCTION : JE VAIS TOUS LES DÉCOMPOSER.)", 'shout' => 'OUAF OUAF !', 'action' => 'dash'],
    ],

    'ending' => [
        ['scene' => 'victory', 'text' => "NAVET : « NOOON ! MES LÉGUMES ! MA CROTTE D'OOOR ! »", 'duration' => 240],
        ['scene' => 'treasure', 'text' => "LA CROTTE D'OR ROULE AUX PIEDS DE BOULETTE. ELLE BRILLE. ELLE SCINTILLE. [PAUSE 1] BOULETTE LA RENIFLE..."],
        ['scene' => 'treasure', 'text' => "... CE N'ÉTAIT PAS UNE CROTTE. C'ÉTAIT LE PLUS GROS NUGGET DORÉ DU MONDE !"],
        ['scene' => 'party', 'text' => "PRIVÉS DE RAYONS, LES LÉGUMES REDEVIENNENT GENTILS. ILS OUVRENT UN BAR À SALADES. ÇA MARCHE TRÈS BIEN."],
        ['scene' => 'party', 'text' => "SAUCISSE OFFRE UN MENU ENFANT À TOUT LE MONDE. BOULETTE FAIT UN PROUT DE JOIE. FIN."],
        ['scene' => 'credits', 'text' => ''],
    ],

    // le générique : jeux de mots canins garantis
    'credits' => [
        ["BOULETTE ET LA CROTTE D'OR", 'UN JEU MÉGA MIAM'],
        ['BOULETTE', 'CARLIN, HÉROÏNE, LANCEUSE DE BABALLES'],
        ['SAUCISSE', 'TECKEL, EMPLOYÉ DU MOIS (TOUS LES MOIS)'],
        ['MÉDOR WOUAFWOUAF', 'DIRECTEUR DES PROUTS SUPERSONIQUES'],
        ['RINTINTIN NUGGETOS', 'GOÛTEUR OFFICIEL DE NUGGETS'],
        ['LASSIE CROQUETTE', 'RESPONSABLE DES BABALLES PERDUES'],
        ['BÉBERT LE BASSET', 'INGÉNIEUR EN SIESTES'],
        ['PATAPOUF', 'CHEF DE LA BRIGADE ANTI-BROCOLIS'],
        ['FIFI TRUFFE', 'RENIFLEUSE DE CROTTES (DORÉES OU PAS)'],
        ['MUSIQUE', 'UN TRACKER 16 BITS ET BEAUCOUP DE OUAF'],
        ['PROFESSEUR NAVET', 'A ÉTÉ CONDAMNÉ À FAIRE DE LA SOUPE'],
        ['', "AUCUN LÉGUME N'A ÉTÉ MALTRAITÉ. ILS SONT TOUS DEVENUS SOUPE DE LEUR PLEIN GRÉ."],
        ['', 'MANGEZ 5 FRUITS ET LÉGUMES PAR JOUR. (SAUF LES MUTANTS.)'],
    ],
];
