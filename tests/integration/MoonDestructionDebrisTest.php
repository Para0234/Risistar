<?php

namespace Risistar\Tests\Integration;

use MissionCaseDestruction;

/**
 * Integration tests for the debris of a moon destruction combat.
 */
class MoonDestructionDebrisTest extends MoonDestructionTestCase
{
    public function testDebrisIsAddedWhenTheMoonSurvives(): void
    {
        $this->assertDebrisAdded(10000, false);
    }

    public function testDebrisIsAddedWhenTheMoonIsDestroyed(): void
    {
        $this->assertDebrisAdded(4000, true);
    }

    private function assertDebrisAdded(int $diameter, bool $destroyed): void
    {
        self::$db->update('UPDATE %%PLANETS%% SET der_metal = 12345, der_crystal = 6789 WHERE id = :id;',
            [':id' => $this->parentPlanet['id']]);
        self::$db->update('UPDATE %%PLANETS%% SET diameter = :diameter, ' . $GLOBALS['resource'][202] . ' = 1, der_metal = 0, der_crystal = 0 WHERE id = :id;',
            [':diameter' => $diameter, ':id' => $this->target['id']]);
        $fleet = $this->sendFleet(9, [214 => 500, 233 => 100], TIMESTAMP - 10);
        (new MissionCaseDestruction($fleet))->TargetEvent();
        $moon = self::$db->selectSingle('SELECT id FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->target['id']]);
        $this->assertSame($destroyed, empty($moon));
        $reports = self::$db->select('SELECT raport FROM %%RW%% WHERE time = :time AND attacker = :owner;',
            [':time' => $fleet['fleet_start_time'], ':owner' => (string) $fleet['fleet_owner']]);
        $this->assertCount(1, $reports);
        $report = unserialize($reports[0]['raport']);
        $this->assertGreaterThan(0, array_sum($report['debris']));
        $planet = self::$db->selectSingle('SELECT der_metal, der_crystal FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->parentPlanet['id']]);
        $this->assertEquals(12345 + $report['debris'][901], $planet['der_metal']);
        $this->assertEquals(6789 + $report['debris'][902], $planet['der_crystal']);
    }
}
