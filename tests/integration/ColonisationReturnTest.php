<?php

namespace Risistar\Tests\Integration;

use Config;
use FleetFunctions;
use MissionCaseColonisation;
use PlayerUtil;

/**
 * Integration tests for colonisation rolls below 2.
 */
class ColonisationReturnTest extends GamePageTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/classes/class.MissionFunctions.php';
        require_once self::rootPath() . 'includes/classes/missions/Mission.interface.php';
        require_once self::rootPath() . 'includes/classes/missions/MissionCaseColonisation.class.php';
    }

    protected function setUp(): void
    {
        parent::setUp();
        // IntegrationTestCase::setUpBeforeClass() loads vars.php inside a function, so its resource entries are not global.
        $GLOBALS['resource'] += [901 => 'metal', 902 => 'crystal', 903 => 'deuterium', 911 => 'energy', 921 => 'darkmatter'];
        $GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
        $GLOBALS['reslist']['resstype'] = [1 => [901, 902, 903], 2 => [911], 3 => [921]];
    }

    public function testFleetIsLostOnAFreePosition(): void
    {
        $config = Config::get();
        $this->assertTrue(PlayerUtil::isPositionFree(1, $config->max_galaxy, $config->max_system, 6));
        self::$db->update('UPDATE %%USERS%% SET ' . $GLOBALS['resource'][124] . ' = 50, class_lone = 0 WHERE id = :id;',
            [':id' => $GLOBALS['USER']['id']]);
        $fleet = $this->sendColonyShip($config->max_galaxy, $config->max_system, 6);

        $this->runWithRollBelow2($fleet);

        $this->assertEmpty(self::$db->selectSingle('SELECT fleet_id FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $fleet['fleet_id']]));
        $this->assertTrue(PlayerUtil::isPositionFree(1, $config->max_galaxy, $config->max_system, 6));
        $message = self::$db->selectSingle('SELECT message_text FROM %%MESSAGES%% WHERE message_owner = :owner ORDER BY message_id DESC LIMIT 1;',
            [':owner' => $GLOBALS['USER']['id']], 'message_text');
        $this->assertSame((new MissionCaseColonisation($fleet))->getLanguage($GLOBALS['USER']['lang'])['sys_colo_reallybadluck'], $message);
    }

    public function testFleetComesHomeFromATakenPosition(): void
    {
        $taken = $this->players(2)[1];
        $planet = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $taken['id_planet']]);
        $fleet = $this->sendColonyShip($planet['galaxy'], $planet['system'], $planet['planet']);

        $this->runWithRollBelow2($fleet);

        $saved = self::$db->selectSingle('SELECT * FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $fleet['fleet_id']]);
        $this->assertEquals(FLEET_RETURN, $saved['fleet_mess']);
        $this->assertEquals($fleet['fleet_end_time'], self::$db->selectSingle('SELECT time FROM %%FLEETS_EVENT%% WHERE fleetID = :id;',
            [':id' => $fleet['fleet_id']], 'time'));
    }

    private function sendColonyShip(int $galaxy, int $system, int $planet): array
    {
        $start = $GLOBALS['PLANET'];
        FleetFunctions::sendFleet([208 => 1], 7, $GLOBALS['USER']['id'], $start['id'],
            $start['galaxy'], $start['system'], $start['planet'], $start['planet_type'],
            0, 0, $galaxy, $system, $planet, 1, [901 => 0, 902 => 0, 903 => 0],
            TIMESTAMP - 10, TIMESTAMP - 10, TIMESTAMP + 1000);
        return self::$db->selectSingle('SELECT * FROM %%FLEETS%% WHERE fleet_owner = :owner ORDER BY fleet_id DESC LIMIT 1;',
            [':owner' => $GLOBALS['USER']['id']]);
    }

    private function runWithRollBelow2(array $fleet): void
    {
        // Find a seed for which the colonisation roll is below 2
        for ($seed = 0; $seed < 10000; $seed++) {
            mt_srand($seed);
            if (mt_rand(0, 100) < 2) {
                break;
            }
        }
        mt_srand($seed);
        try {
            (new MissionCaseColonisation($fleet))->TargetEvent();
        } finally {
            mt_srand();
        }
    }
}
