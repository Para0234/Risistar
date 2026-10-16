<?php

namespace Risistar\Tests\Integration;

use PDO;

/**
 * Integration tests for joining an ACS group from fleet step 3.
 */
class AcsJoinTest extends FleetDispatchTestCase
{
    private int $group = 0;
    private array $players = [];

    protected function setUp(): void
    {
        parent::setUp();
        $this->players = $this->players(3);
        $this->target = self::$db->selectSingle('SELECT * FROM %%PLANETS%% WHERE id = :id;', [':id' => $this->players[1]['id_planet']]);
        $GLOBALS['USER']['total_points'] = 100;
        self::$db->update('UPDATE %%STATPOINTS%% SET total_points = 100 WHERE id_owner = :id AND stat_type = 1;', [':id' => $this->players[1]['id']]);
        self::$db->insert('INSERT INTO %%AKS%% (name, target, ankunft) VALUES (:name, :target, :time);',
            [':name' => 'Test group', ':target' => $this->target['id'], ':time' => TIMESTAMP + 60000]);
        $this->group = (int) self::$db->lastInsertId();
        self::$db->insert('INSERT INTO %%USERS_ACS%% (userID, acsID) VALUES (:user, :group);', [':user' => $this->players[2]['id'], ':group' => $this->group]);
        $form = &$GLOBALS['_SESSION']['fleet']['dispatch-token'];
        $form['fleetGroup'] = $this->group;
        $form['targetGalaxy'] = $this->target['galaxy'];
        $form['targetSystem'] = $this->target['system'];
        $form['targetPlanet'] = $this->target['planet'];
        $form['targetType'] = (int) $this->target['planet_type'];
        $GLOBALS['_REQUEST']['mission'] = 2;
    }

    public function testInvitedPlayerJoinsTheGroup(): void
    {
        $this->invite();

        $this->sendFleet();

        $fleet = $this->latestFleet();
        $this->assertEquals(2, $fleet['fleet_mission']);
        $this->assertEquals($this->group, $fleet['fleet_group']);
    }

    public function testPlayerWhoIsNotInvitedDoesNotJoinTheGroup(): void
    {
        $this->sendFleet();

        $fleet = $this->latestFleet();
        $this->assertEquals(1, $fleet['fleet_mission']);
        $this->assertEquals(0, $fleet['fleet_group']);
    }

    public function testFleetToAnotherTargetDoesNotJoinTheGroup(): void
    {
        $this->invite();
        self::$db->update('UPDATE %%AKS%% SET target = :target WHERE id = :id;', [':target' => $this->players[2]['id_planet'], ':id' => $this->group]);

        $this->sendFleet();

        $fleet = $this->latestFleet();
        $this->assertEquals(1, $fleet['fleet_mission']);
        $this->assertEquals(0, $fleet['fleet_group']);
    }

    public function testJoinWaitsForTheGroupLock(): void
    {
        // Another connection holds the group lock, like a second join in progress.
        // The group lands in 10 days, so setACSTime() does not update it and wait on the lock too.
        $other = $this->connection();
        $other->exec('INSERT INTO ' . $this->table('aks') . " (name, target, ankunft) VALUES ('Test group', " . (int) $this->target['id'] . ', ' . (TIMESTAMP + 10 * 86400) . ')');
        $group = (int) $other->lastInsertId();
        $other->exec('INSERT INTO ' . $this->table('users_to_acs') . ' (userID, acsID) VALUES (' . (int) $GLOBALS['USER']['id'] . ', ' . $group . ')');
        $GLOBALS['_SESSION']['fleet']['dispatch-token']['fleetGroup'] = $group;
        $other->beginTransaction();
        $other->query('SELECT id FROM ' . $this->table('aks') . ' WHERE id = ' . $group . ' FOR UPDATE');
        self::$db->getHandle()->exec('SET innodb_lock_wait_timeout = 1');
        $error = '';
        try {
            $this->sendFleet();
        } catch (\Exception $e) {
            $error = $e->getMessage();
        } finally {
            self::$db->getHandle()->exec('SET innodb_lock_wait_timeout = DEFAULT');
            $other->rollBack();
            $other->exec('DELETE FROM ' . $this->table('users_to_acs') . ' WHERE acsID = ' . $group);
            $other->exec('DELETE FROM ' . $this->table('aks') . ' WHERE id = ' . $group);
        }
        $this->assertStringContainsString('Lock wait timeout', $error, 'The join did not wait for the group lock');
    }

    private function invite(): void
    {
        self::$db->insert('INSERT INTO %%USERS_ACS%% (userID, acsID) VALUES (:user, :group);', [':user' => $GLOBALS['USER']['id'], ':group' => $this->group]);
    }

    private function connection(): PDO
    {
        $database = [];
        require self::rootPath() . 'includes/config.test.php';
        return new PDO('mysql:host=' . $database['host'] . ';port=' . $database['port'] . ';dbname=' . $database['databasename'],
            $database['user'], $database['userpw'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    }

    private function table(string $name): string
    {
        $database = [];
        require self::rootPath() . 'includes/config.test.php';
        return $database['tableprefix'] . $name;
    }
}
