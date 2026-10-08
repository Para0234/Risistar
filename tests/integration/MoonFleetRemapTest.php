<?php

namespace Risistar\Tests\Integration;

/**
 * Integration tests for fleets flying to a moon when it is destroyed.
 */
class MoonFleetRemapTest extends MoonDestructionTestCase
{
    public function testFleetsToTheMoonAndItsPlanetEndAtThePlanet(): void
    {
        $toMoon = $this->sendFleet(3, [202 => 1], TIMESTAMP + 1000);
        $nextDestruction = $this->sendFleet(9, [214 => 1], TIMESTAMP + 1000);
        $moon = $this->target;
        $this->target = $this->parentPlanet;
        $toPlanet = $this->sendFleet(3, [202 => 1], TIMESTAMP + 1000);
        $this->target = $moon;

        $this->destroyMoon();

        foreach ([$toMoon, $nextDestruction, $toPlanet] as $fleet) {
            $saved = self::$db->selectSingle('SELECT fleet_end_id, fleet_end_type, fleet_mission FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $fleet['fleet_id']]);
            $this->assertEquals($this->parentPlanet['id'], $saved['fleet_end_id']);
            $this->assertEquals(1, $saved['fleet_end_type']);
            // A destruction mission cannot target a planet, so it becomes an attack
            $this->assertEquals($fleet === $nextDestruction ? 1 : 3, $saved['fleet_mission']);
        }
    }
}
