<?php

namespace Risistar\Tests\Unit;

class OpbeTest extends UnitTestCase
{
    const ID_LIGHT_HUNTER = 202;
    const ID_HEAVY_HUNTER = 203;
    const ID_CRUISER = 204;
    const ID_BATTLESHIP = 207;
    const ID_BATTLECRUISER = 215;

    public static function setUpBeforeClass(): void
    {
        require_once self::rootPath() . 'includes/libs/opbe/utils/includer.php';
        require_once self::rootPath() . 'includes/libs/opbe/tests/runnable/langs/MoonsLangImplementation.php';

        if (!\LangManager::getInstance()->implementationExist()) {
            \LangManager::getInstance()->setImplementation(new \MoonsLangImplementation());
        }
    }

    public function testOpbeBattleExecution()
    {
        $battleshipRapidFire = [
            self::ID_LIGHT_HUNTER => 3,
            self::ID_HEAVY_HUNTER => 4,
        ];
        $battleship = new \Ship(
            self::ID_BATTLESHIP,
            50,
            $battleshipRapidFire,
            200,
            [45000, 15000],
            1000
        );
        $fleetAttacker = new \Fleet(1, [$battleship]);
        $playerAttacker = new \Player(1, [$fleetAttacker]);
        $attackers = new \PlayerGroup([$playerAttacker]);

        $battlecruiserRapidFire = [
            self::ID_LIGHT_HUNTER => 3,
            self::ID_CRUISER => 7,
        ];
        $battlecruiser = new \Ship(
            self::ID_BATTLECRUISER,
            50,
            $battlecruiserRapidFire,
            400,
            [30000, 40000],
            700
        );
        $fleetDefender = new \Fleet(2, [$battlecruiser]);
        $playerDefender = new \Player(2, [$fleetDefender]);
        $defenders = new \PlayerGroup([$playerDefender]);

        $engine = new \Battle($attackers, $defenders);
        $engine->startBattle(false);

        $report = $engine->getReport();
        $this->assertNotNull($report);
        $this->assertInstanceOf(\BattleReport::class, $report);
    }
}
