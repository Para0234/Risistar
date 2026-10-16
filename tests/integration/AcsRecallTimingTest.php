<?php

namespace Risistar\Tests\Integration;

use FleetFunctions;

/**
 * Integration tests for the return time of recalled ACS fleets.
 */
class AcsRecallTimingTest extends MissionTestCase
{
    private int $group = 0;
    private array $leader = [];
    private array $member = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::$db->insert('INSERT INTO %%AKS%% (name, target, ankunft) VALUES (:name, :target, :time);',
            [':name' => 'Recall timing', ':target' => $this->target['id'], ':time' => TIMESTAMP + 1000]);
        $this->group = (int) self::$db->lastInsertId();
        self::$db->insert('INSERT INTO %%USERS_ACS%% (userID, acsID) VALUES (:user, :group);',
            [':user' => $GLOBALS['USER']['id'], ':group' => $this->group]);
        $this->leader = $this->sendFleet(1, [202 => 1], TIMESTAMP + 1000);
        $this->member = $this->sendFleet(2, [202 => 1], TIMESTAMP + 1000);
        foreach ([[$this->leader, 100], [$this->member, 30]] as [$fleet, $elapsed]) {
            foreach (['%%FLEETS%%', '%%LOG_FLEETS%%'] as $table) {
                self::$db->update('UPDATE ' . $table . ' SET fleet_group = :group, start_time = :time WHERE fleet_id = :id;',
                    [':group' => $this->group, ':time' => TIMESTAMP - $elapsed, ':id' => $fleet['fleet_id']]);
            }
        }
    }

    public function testGroupRecallUsesEachFleetsFlightTime(): void
    {
        $this->assertTrue(FleetFunctions::SendFleetBack($GLOBALS['USER'], $this->leader['fleet_id']));
        $this->assertReturnTime($this->leader['fleet_id'], TIMESTAMP + 100);
        $this->assertReturnTime($this->member['fleet_id'], TIMESTAMP + 30);
    }

    public function testMemberRecallKeepsTheGroup(): void
    {
        $this->assertTrue(FleetFunctions::SendFleetBack($GLOBALS['USER'], $this->member['fleet_id']));
        $this->assertReturnTime($this->member['fleet_id'], TIMESTAMP + 30);
        $leader = self::$db->selectSingle('SELECT fleet_mess, fleet_group FROM %%FLEETS%% WHERE fleet_id = :id;',
            [':id' => $this->leader['fleet_id']]);
        $this->assertEquals(FLEET_OUTWARD, $leader['fleet_mess']);
        $this->assertEquals($this->group, $leader['fleet_group']);
        $this->assertNotFalse(self::$db->selectSingle('SELECT id FROM %%AKS%% WHERE id = :id;', [':id' => $this->group]));
    }

    private function assertReturnTime(int $id, int $time): void
    {
        foreach (['%%FLEETS%%', '%%LOG_FLEETS%%'] as $table) {
            $fleet = self::$db->selectSingle('SELECT fleet_end_time, fleet_end_stay, fleet_mess, hasCanceled FROM ' . $table . ' WHERE fleet_id = :id;',
                [':id' => $id]);
            $this->assertEquals($time, $fleet['fleet_end_time']);
            $this->assertEquals(TIMESTAMP, $fleet['fleet_end_stay']);
            $this->assertEquals(FLEET_RETURN, $fleet['fleet_mess']);
            $this->assertEquals(1, $fleet['hasCanceled']);
        }
        $event = self::$db->selectSingle('SELECT time FROM %%FLEETS_EVENT%% WHERE fleetID = :id;', [':id' => $id]);
        $this->assertEquals($time, $event['time']);
    }
}
