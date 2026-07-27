<?php

/**
 * Risistar - Functional Scenario: All Scenarios Sequence
 */

require_once __DIR__ . '/../ScenarioInterface.php';

class AllScenarios implements ScenarioInterface
{
    public function getKey(): string
    {
        return 'all';
    }

    public function getName(): string
    {
        return 'Suite Complète de Tous les Scénarios (Échelonnés par intervalle)';
    }

    public function getDescription(): string
    {
        return 'Exécute l\'ensemble des scénarios fonctionnels à la suite en échelonnant leurs événements. Options: --interval=SECONDS (défaut: 5s).';
    }

    public function execute(array $options, bool $isCli): void
    {
        $interval = isset($options['interval']) ? max(1, (int)$options['interval']) : 5;
        $currentEta = isset($options['eta']) ? max(1, (int)$options['eta']) : 5;
        $userName = $options['user'] ?? 'Admin';

        echo "🎬 🔥 Lancement de la SUITE COMPLÈTE de tous les scénarios fonctionnels pour '{$userName}' (Intervalle: {$interval}s)...\n\n";

        // 1. Attaques entrantes (Attack incoming)
        echo "=== [1/10] SCÉNARIO : ATTAQUES ENTRANTES ===\n";
        $opt1 = array_merge($options, ['direction' => 'incoming', 'eta' => $currentEta, 'count' => 2, 'interval' => $interval]);
        (new AttackScenario())->execute($opt1, $isCli);
        $currentEta += ($interval * 2);
        echo "\n";

        // 2. Raids sortants (Attack outgoing)
        echo "=== [2/10] SCÉNARIO : RAIDS SORTANTS ===\n";
        $opt2 = array_merge($options, ['direction' => 'outgoing', 'eta' => $currentEta, 'count' => 2, 'interval' => $interval]);
        (new AttackScenario())->execute($opt2, $isCli);
        $currentEta += ($interval * 2);
        echo "\n";

        // 3. Missions d'espionnage (Spy)
        echo "=== [3/10] SCÉNARIO : ESPIONNAGE ===\n";
        $opt3 = array_merge($options, ['direction' => 'incoming', 'eta' => $currentEta, 'count' => 3, 'interval' => $interval]);
        (new SpyScenario())->execute($opt3, $isCli);
        $currentEta += ($interval * 3);
        echo "\n";

        // 4. Salves de Missiles Interplanétaires (MIP)
        echo "=== [4/10] SCÉNARIO : MISSILES INTERPLANÉTAIRES (MIP) ===\n";
        $opt4 = array_merge($options, ['direction' => 'incoming', 'eta' => $currentEta, 'count' => 2, 'interval' => $interval]);
        (new MipScenario())->execute($opt4, $isCli);
        $currentEta += ($interval * 2);
        echo "\n";

        // 5. Expéditions Spatiales (Expedition - Pos 16 : 4 Tailles)
        echo "=== [5/10] SCÉNARIO : EXPÉDITIONS SPATIALES (Small, Medium, Large, Massive) ===\n";
        $expeditionSizes = ['small', 'medium', 'large', 'massive'];
        foreach ($expeditionSizes as $size) {
            $optExp = array_merge($options, ['eta' => $currentEta, 'count' => 1, 'fleet_size' => $size, 'interval' => $interval]);
            (new ExpeditionScenario())->execute($optExp, $isCli);
            $currentEta += $interval;
        }
        echo "\n";

        // 6. Attaque Groupée ACS
        echo "=== [6/10] SCÉNARIO : ATTAQUE GROUPÉE ACS ===\n";
        $opt6 = array_merge($options, ['eta' => $currentEta, 'count' => 2, 'interval' => $interval]);
        (new AcsAttackScenario())->execute($opt6, $isCli);
        $currentEta += ($interval * 2);
        echo "\n";

        // 7. Défense d'alliance ACS
        echo "=== [7/10] SCÉNARIO : DÉFENSE ALLIÉE ACS ===\n";
        $opt7 = array_merge($options, ['eta' => $currentEta]);
        (new AcsDefenseScenario())->execute($opt7, $isCli);
        $currentEta += $interval;
        echo "\n";

        // 8. Destruction de lune
        echo "=== [8/10] SCÉNARIO : DESTRUCTION DE LUNE ===\n";
        $opt8 = array_merge($options, ['eta' => $currentEta]);
        (new MoonDestructionScenario())->execute($opt8, $isCli);
        $currentEta += $interval;
        echo "\n";

        // 9. Matrice de combinaisons de vol
        echo "=== [9/10] SCÉNARIO : MATRICE DE COMBINAISONS DE VOL ===\n";
        $opt9 = array_merge($options, ['eta' => $currentEta, 'interval' => $interval]);
        (new FlightCombinationsScenario())->execute($opt9, $isCli);
        $currentEta += ($interval * 5);
        echo "\n";

        // 10. Cas limites de destruction de lune
        echo "=== [10/10] SCÉNARIO : CAS LIMITES LUNE ===\n";
        $opt10 = array_merge($options, ['eta' => $currentEta, 'interval' => $interval]);
        (new MoonDestructionEdgeCasesScenario())->execute($opt10, $isCli);

        echo "\n✅ SUITE COMPLÈTE INJECTÉE AVEC SUCCÈS (Intervalle: {$interval}s) !\n";
    }
}
