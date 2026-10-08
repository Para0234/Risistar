<?php

namespace Risistar\Tests\Integration;

use FlyingFleetHandler;

/**
 * Integration tests for fleets whose target planet is deleted before they arrive.
 */
class FleetMissingDestinationTest extends MissionTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        require_once self::rootPath() . 'includes/classes/class.BuildFunctions.php';
        require_once self::rootPath() . 'includes/classes/class.PlanetRessUpdate.php';
        require_once self::rootPath() . 'includes/classes/class.FlyingFleetHandler.php';
    }

    public function testTransportToAnExistingPlanetDeliversItsCargo(): void
    {
        $fleet = $this->sendFleet(3, [202 => 1], TIMESTAMP - 10, [901 => 123]);

        $this->runFleets($fleet['fleet_id']);

        $target = self::$db->selectSingle('SELECT metal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]);
        $this->assertEquals($this->target['metal'] + 123, $target['metal']);
    }

    public function testTransportReturns(): void
    {
        $this->assertFleetReturns(3, [202 => 1]);
    }

    public function testStationingReturns(): void
    {
        $this->assertFleetReturns(4, [202 => 1]);
    }

    public function testSpyReturns(): void
    {
        $this->assertFleetReturns(6, [210 => 1]);
    }

    public function testDestructionReturns(): void
    {
        $this->assertFleetReturns(9, [214 => 1]);
    }

    public function testMissilesAreRemoved(): void
    {
        $fleet = $this->sendFleet(10, [503 => 2], TIMESTAMP - 10);
        self::$db->delete('DELETE FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]);

        $this->runFleets($fleet['fleet_id']);

        $this->assertFalse(self::$db->selectSingle('SELECT fleet_id FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $fleet['fleet_id']]));
        $this->assertFalse(self::$db->selectSingle('SELECT fleetID FROM %%FLEETS_EVENT%% WHERE fleetID = :id;', [':id' => $fleet['fleet_id']]));
    }

    private function assertFleetReturns(int $mission, array $ships): void
    {
        $fleet = $this->sendFleet($mission, $ships, TIMESTAMP - 10, [901 => 123]);
        self::$db->delete('DELETE FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]);

        $this->runFleets($fleet['fleet_id']);

        $saved = self::$db->selectSingle('SELECT fleet_mess, fleet_resource_metal, fleet_array FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $fleet['fleet_id']]);
        $this->assertEquals(FLEET_RETURN, $saved['fleet_mess']);
        $this->assertEquals(123, $saved['fleet_resource_metal']);
        $this->assertSame($fleet['fleet_array'], $saved['fleet_array']);

        // Back home: ships and cargo are added to the start planet
        $before = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $fleet['fleet_start_id']]);
        self::$db->update('UPDATE %%FLEETS%% SET fleet_end_time = :time WHERE fleet_id = :id;', [':time' => TIMESTAMP - 1, ':id' => $fleet['fleet_id']]);
        self::$db->update('UPDATE %%FLEETS_EVENT%% SET time = :time WHERE fleetID = :id;', [':time' => TIMESTAMP - 1, ':id' => $fleet['fleet_id']]);

        $this->runFleets($fleet['fleet_id']);

        $after = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $fleet['fleet_start_id']]);
        $this->assertEquals($before['metal'] + 123, $after['metal']);
        foreach ($ships as $ship => $count) {
            $this->assertEquals($before[$GLOBALS['resource'][$ship]] + $count, $after[$GLOBALS['resource'][$ship]]);
        }
    }

    private function runFleets(int $fleetId): void
    {
        self::$db->update('UPDATE %%FLEETS_EVENT%% SET `lock` = :token WHERE fleetID = :id;', [':token' => 'missing-destination-test', ':id' => $fleetId]);
        $handler = new FlyingFleetHandler();
        $handler->setToken('missing-destination-test');
        $handler->run();
    }
}
