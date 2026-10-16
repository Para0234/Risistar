<?php

namespace Risistar\Tests\Integration;

use FlyingFleetHandler;

/**
 * Integration tests for fleets of one run that change each other.
 */
class FleetRunStateTest extends MoonDestructionTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.FlyingFleetHandler.php';
    }

    public function testTransportToTheMoonDeliversToThePlanet(): void
    {
        $destroyer = $this->sendFleet(9, [214 => 500, 233 => 100], TIMESTAMP - 20);
        $transport = $this->sendFleet(3, [202 => 1], TIMESTAMP - 10, [901 => 123]);
        $before = self::$db->selectSingle('SELECT metal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->parentPlanet['id']], 'metal');

        $this->runFleets([$destroyer, $transport]);

        $this->assertEmpty(self::$db->selectSingle('SELECT id FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]));
        $after = self::$db->selectSingle('SELECT metal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->parentPlanet['id']], 'metal');
        $this->assertEquals($before + 123, $after);
        $fleet = self::$db->selectSingle('SELECT fleet_end_id, fleet_mess, fleet_resource_metal FROM %%FLEETS%% WHERE fleet_id = :id;',
            [':id' => $transport['fleet_id']]);
        $this->assertEquals($this->parentPlanet['id'], $fleet['fleet_end_id']);
        $this->assertEquals(FLEET_RETURN, $fleet['fleet_mess']);
        $this->assertEquals(0, $fleet['fleet_resource_metal']);
    }

    public function testFleetFlyingHomeToTheMoonLandsOnThePlanet(): void
    {
        $destroyer = $this->sendFleet(9, [214 => 500, 233 => 100], TIMESTAMP - 20);
        // A fleet of the moon's owner that flies home to the moon and lands just after the destruction
        $fleet = $this->sendFleet(3, [202 => 1], TIMESTAMP - 10);
        self::$db->update('UPDATE %%FLEETS%% SET fleet_owner = :owner, fleet_start_id = :moon, fleet_start_type = 3, fleet_end_id = :end, fleet_end_type = 1, fleet_mess = :mess, fleet_end_time = :time WHERE fleet_id = :id;',
            [':owner' => $this->target['id_owner'], ':moon' => $this->target['id'], ':end' => $GLOBALS['PLANET']['id'], ':mess' => FLEET_RETURN, ':time' => TIMESTAMP - 10, ':id' => $fleet['fleet_id']]);
        $ships = $GLOBALS['resource'][202];
        $before = self::$db->selectSingle('SELECT ' . $ships . ' FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->parentPlanet['id']], $ships);

        $this->runFleets([$destroyer, $fleet]);

        $this->assertEmpty(self::$db->selectSingle('SELECT id FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]));
        $after = self::$db->selectSingle('SELECT ' . $ships . ' FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->parentPlanet['id']], $ships);
        $this->assertEquals($before + 1, $after);
    }

    private function runFleets(array $fleets): void
    {
        foreach ($fleets as $fleet) {
            self::$db->update('UPDATE %%FLEETS_EVENT%% SET `lock` = :token WHERE fleetID = :id;',
                [':token' => 'run-state-test', ':id' => $fleet['fleet_id']]);
        }
        $handler = new FlyingFleetHandler();
        $handler->setToken('run-state-test');
        $handler->run();
    }
}
