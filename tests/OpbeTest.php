<?php

namespace Risistar\Tests;

use PHPUnit\Framework\TestCase;

class OpbeTest extends TestCase
{
    // Ship IDs
    const ID_LIGHT_HUNTER = 202;
    const ID_HEAVY_HUNTER = 203;
    const ID_CRUISER = 204;
    const ID_BATTLESHIP = 207;
    const ID_BATTLECRUISER = 215;

    public static function setUpBeforeClass(): void
    {
        if (!defined('ROOT_PATH')) {
            define('ROOT_PATH', dirname(__DIR__) . '/');
        }

        require_once ROOT_PATH . 'includes/libs/opbe/utils/includer.php';
        require_once ROOT_PATH . 'includes/libs/opbe/tests/runnable/langs/MoonsLangImplementation.php';
        
        if (!\LangManager::getInstance()->implementationExist()) {
            \LangManager::getInstance()->setImplementation(new \MoonsLangImplementation());
        }
    }

    public function testOpbeBattleExecution()
    {
        // Attacker: 50 Battleships
        $battleshipRapidFire = [
            self::ID_LIGHT_HUNTER => 3,
            self::ID_HEAVY_HUNTER => 4
        ];
        $battleship = new \Ship(
            self::ID_BATTLESHIP,
            50,                     // Amount
            $battleshipRapidFire,   // Rapid fire
            200,                    // Shield
            [45000, 15000],         // Cost (Metal, Crystal)
            1000                    // Attack power
        );
        $fleetAttacker = new \Fleet(1, [$battleship]);
        $playerAttacker = new \Player(1, [$fleetAttacker]);
        $attackers = new \PlayerGroup([$playerAttacker]);

        // Defender: 50 Battlecruisers
        $battlecruiserRapidFire = [
            self::ID_LIGHT_HUNTER => 3,
            self::ID_CRUISER => 7
        ];
        $battlecruiser = new \Ship(
            self::ID_BATTLECRUISER,
            50,                     // Amount
            $battlecruiserRapidFire,// Rapid fire
            400,                    // Shield
            [30000, 40000],         // Cost (Metal, Crystal)
            700                     // Attack power
        );
        $fleetDefender = new \Fleet(2, [$battlecruiser]);
        $playerDefender = new \Player(2, [$fleetDefender]);
        $defenders = new \PlayerGroup([$playerDefender]);

        // Execute battle engine
        $engine = new \Battle($attackers, $defenders);
        $engine->startBattle(false);

        $report = $engine->getReport();
        $this->assertNotNull($report);
        $this->assertInstanceOf(\BattleReport::class, $report);
    }
}
