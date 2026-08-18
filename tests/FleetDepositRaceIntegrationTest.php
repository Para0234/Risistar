<?php

namespace Risistar\Tests;

use Database;
use MissionCaseStay;
use PHPUnit\Framework\TestCase;
use ResourceUpdate;

/**
 * Integration: planet FOR UPDATE serializes stale SavePlanetToDB vs RestoreFleet.
 *
 * Without the lock, a stale absolute economy save can wipe a relative fleet deposit
 * (ships kept, cargo gone). With request transactions + lockPlanet / SELECT FOR UPDATE,
 * the deposit cannot land between the victim's load and save — it runs after commit,
 * so cargo survives.
 *
 * Uses 2moons_test and two DB connections to observe the lock wait.
 */
class FleetDepositRaceIntegrationTest extends TestCase
{
	private const TEST_DATABASE = '2moons_test';
	private const SHIP_ID = 219; // giga_recykler
	private const SHIP_COUNT = 4;
	private const CARGO_METAL = 106023619.0;
	private const CARGO_CRYSTAL = 1010620.0;
	private const CARGO_DEUTERIUM = 30804944.0;

	/** Baseline matching the post-328564 moon leftover reported in the incident. */
	private const BASELINE_METAL = 1800000.0;
	private const BASELINE_CRYSTAL = 791000.0;
	private const BASELINE_DEUTERIUM = 165000.0;

	/** @var array{id:int,id_owner:int,metal:float,crystal:float,deuterium:float,giga_recykler:int}|null */
	private $moonSnapshot = null;

	/** @var int|null */
	private $fleetId = null;

	/** @var int|null */
	private $messageIdBefore = null;

	public static function setUpBeforeClass(): void
	{
		self::bootstrapGameOnTestDatabase();
	}

	protected function setUp(): void
	{
		$db = Database::get();
		if ($db->getTransactionDepth() > 0) {
			$db->rollBackAll();
		}
		$db->nativeQuery('USE `' . self::TEST_DATABASE . '`');

		// Bind planet_type: PDO prepared statements here drop literal ints in the
		// WHERE clause (planet_type = 3 returns 0 rows; :ptype => 3 works).
		$moon = $db->selectSingle(
			'SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND planet_type = :ptype AND destruyed = 0 ORDER BY id ASC;',
			[':owner' => 1, ':ptype' => 3]
		);
		$planet = $db->selectSingle(
			'SELECT * FROM %%PLANETS%% WHERE id_owner = :owner AND planet_type = :ptype AND destruyed = 0 ORDER BY id ASC;',
			[':owner' => 1, ':ptype' => 1]
		);

		$this->assertNotEmpty($moon, 'Need an Admin moon in 2moons_test');
		$this->assertNotEmpty($planet, 'Need an Admin planet in 2moons_test');

		$this->moonSnapshot = [
			'id' => (int) $moon['id'],
			'id_owner' => (int) $moon['id_owner'],
			'metal' => (float) $moon['metal'],
			'crystal' => (float) $moon['crystal'],
			'deuterium' => (float) $moon['deuterium'],
			'giga_recykler' => (int) $moon['giga_recykler'],
			'planet' => $planet,
			'moon' => $moon,
		];

		$db->update(
			'UPDATE %%PLANETS%% SET
				metal = :metal,
				crystal = :crystal,
				deuterium = :deuterium,
				giga_recykler = :ships
			WHERE id = :id;',
			[
				':metal' => self::BASELINE_METAL,
				':crystal' => self::BASELINE_CRYSTAL,
				':deuterium' => self::BASELINE_DEUTERIUM,
				':ships' => 10,
				':id' => $moon['id'],
			]
		);

		$maxMsg = $db->selectSingle(
			'SELECT MAX(message_id) AS m FROM %%MESSAGES%% WHERE message_owner = :owner;',
			[':owner' => $moon['id_owner']],
			'm'
		);
		$this->messageIdBefore = $maxMsg ? (int) $maxMsg : 0;

		$now = time();
		$db->insert(
			'INSERT INTO %%FLEETS%% SET
				fleet_owner = :owner,
				fleet_mission = 4,
				fleet_amount = :amount,
				fleet_array = :array,
				fleet_universe = 1,
				fleet_start_time = :startTime,
				fleet_end_stay = :startTime,
				fleet_end_time = :endTime,
				fleet_start_id = :startId,
				fleet_start_galaxy = :sg,
				fleet_start_system = :ss,
				fleet_start_planet = :sp,
				fleet_start_type = 1,
				fleet_end_id = :endId,
				fleet_end_galaxy = :eg,
				fleet_end_system = :es,
				fleet_end_planet = :ep,
				fleet_end_type = 3,
				fleet_resource_metal = :metal,
				fleet_resource_crystal = :crystal,
				fleet_resource_deuterium = :deuterium,
				fleet_resource_darkmatter = 0,
				fleet_target_owner = :owner,
				fleet_group = 0,
				fleet_target_obj = 0,
				fleet_mess = 0,
				start_time = :now,
				fleet_busy = 0,
				hasCanceled = 0;',
			[
				':owner' => (int) $moon['id_owner'],
				':amount' => self::SHIP_COUNT,
				':array' => self::SHIP_ID . ',' . self::SHIP_COUNT,
				':startTime' => $now,
				':endTime' => $now + 30,
				':startId' => (int) $planet['id'],
				':sg' => (int) $planet['galaxy'],
				':ss' => (int) $planet['system'],
				':sp' => (int) $planet['planet'],
				':endId' => (int) $moon['id'],
				':eg' => (int) $moon['galaxy'],
				':es' => (int) $moon['system'],
				':ep' => (int) $moon['planet'],
				':metal' => self::CARGO_METAL,
				':crystal' => self::CARGO_CRYSTAL,
				':deuterium' => self::CARGO_DEUTERIUM,
				':now' => $now,
			]
		);
		$this->fleetId = (int) $db->lastInsertId();

		$db->insert(
			'INSERT INTO %%FLEETS_EVENT%% SET fleetID = :fleetId, `time` = :time, `lock` = NULL;',
			[
				':fleetId' => $this->fleetId,
				':time' => $now,
			]
		);
	}

	protected function tearDown(): void
	{
		$db = Database::get();
		if ($db->getTransactionDepth() > 0) {
			$db->rollBackAll();
		}
		$db->nativeQuery('USE `' . self::TEST_DATABASE . '`');

		if ($this->fleetId) {
			$db->delete('DELETE FROM %%FLEETS_EVENT%% WHERE fleetID = :id;', [':id' => $this->fleetId]);
			$db->delete('DELETE FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $this->fleetId]);
		}

		if ($this->moonSnapshot) {
			$db->update(
				'UPDATE %%PLANETS%% SET
					metal = :metal,
					crystal = :crystal,
					deuterium = :deuterium,
					giga_recykler = :ships
				WHERE id = :id;',
				[
					':metal' => $this->moonSnapshot['metal'],
					':crystal' => $this->moonSnapshot['crystal'],
					':deuterium' => $this->moonSnapshot['deuterium'],
					':ships' => $this->moonSnapshot['giga_recykler'],
					':id' => $this->moonSnapshot['id'],
				]
			);

			if ($this->messageIdBefore !== null) {
				$db->delete(
					'DELETE FROM %%MESSAGES%% WHERE message_owner = :owner AND message_id > :id;',
					[
						':owner' => $this->moonSnapshot['id_owner'],
						':id' => $this->messageIdBefore,
					]
				);
			}
		}
	}

	public function testPlanetLockPreventsStaleSaveFromWipingFleetCargo(): void
	{
		$db = Database::get();
		$moonId = $this->moonSnapshot['id'];
		$userId = $this->moonSnapshot['id_owner'];
		$table = 'uni1_planets';

		// --- Request A: begin + FOR UPDATE (as common.php does) + stale memory ---
		$db->beginTransaction();
		$stalePlanet = $db->selectSingle(
			'SELECT * FROM %%PLANETS%% WHERE id = :id FOR UPDATE;',
			[':id' => $moonId]
		);
		$staleUser = $db->selectSingle('SELECT * FROM %%USERS%% WHERE id = :id;', [':id' => $userId]);
		$this->assertEqualsWithDelta(self::BASELINE_METAL, (float) $stalePlanet['metal'], 0.1);

		$ecoA = new ResourceUpdate(false, false);
		$ecoA->setData($staleUser, $stalePlanet);

		// --- Request B: cannot lock the moon while A holds FOR UPDATE ---
		$pdoB = self::secondaryPdo();
		$pdoB->exec('SET innodb_lock_wait_timeout = 1');
		$pdoB->beginTransaction();
		$blocked = false;
		try {
			$pdoB->query('SELECT id FROM `' . $table . '` WHERE id = ' . (int) $moonId . ' FOR UPDATE');
		} catch (\PDOException $e) {
			$blocked = true;
			$pdoB->rollBack();
		}
		$this->assertTrue($blocked, 'RestoreFleet-equivalent lock must wait on the victim FOR UPDATE');

		// --- Request A finishes: absolute save of the pre-deposit snapshot, then commit ---
		$ecoA->SavePlanetToDB($staleUser, $stalePlanet);
		$db->commit();

		$afterVictimSave = $db->selectSingle(
			'SELECT metal, crystal, deuterium, giga_recykler FROM %%PLANETS%% WHERE id = :id;',
			[':id' => $moonId]
		);
		$this->assertEqualsWithDelta(self::BASELINE_METAL, (float) $afterVictimSave['metal'], 0.1);

		// --- Request B: FleetHandler deposit now runs after A released the row ---
		$fleetRow = $db->selectSingle('SELECT * FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $this->fleetId]);
		$this->assertNotEmpty($fleetRow);

		$db->beginTransaction();
		$mission = new MissionCaseStay($fleetRow);
		$mission->RestoreFleet(false);
		$db->commit();

		$after = $db->selectSingle(
			'SELECT metal, crystal, deuterium, giga_recykler FROM %%PLANETS%% WHERE id = :id;',
			[':id' => $moonId]
		);

		$this->assertEqualsWithDelta(self::BASELINE_METAL + self::CARGO_METAL, (float) $after['metal'], 0.1, 'cargo must survive serialized deposit');
		$this->assertEqualsWithDelta(self::BASELINE_CRYSTAL + self::CARGO_CRYSTAL, (float) $after['crystal'], 0.1);
		$this->assertEqualsWithDelta(self::BASELINE_DEUTERIUM + self::CARGO_DEUTERIUM, (float) $after['deuterium'], 0.1);
		$this->assertSame(10 + self::SHIP_COUNT, (int) $after['giga_recykler']);
		$this->assertEmpty(
			$db->selectSingle('SELECT fleet_id FROM %%FLEETS%% WHERE fleet_id = :id;', [':id' => $this->fleetId])
		);
	}

	private static function secondaryPdo(): \PDO
	{
		$database = [];
		require ROOT_PATH . 'includes/config.php';
		return new \PDO(
			sprintf(
				'mysql:host=%s;port=%s;dbname=%s;charset=utf8',
				$database['host'],
				$database['port'],
				self::TEST_DATABASE
			),
			$database['user'],
			$database['userpw'],
			[\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION]
		);
	}

	private static function bootstrapGameOnTestDatabase(): void
	{
		if (!defined('ROOT_PATH')) {
			define('ROOT_PATH', str_replace('\\', '/', dirname(__DIR__)) . '/');
		}
		if (!defined('MODE')) {
			define('MODE', 'TEST');
		}
		if (!defined('TIMESTAMP')) {
			define('TIMESTAMP', time());
		}

		chdir(ROOT_PATH);
		set_include_path(ROOT_PATH);

		require_once ROOT_PATH . 'includes/constants.php';
		require_once ROOT_PATH . 'includes/GeneralFunctions.php';
		require_once ROOT_PATH . 'includes/classes/ArrayUtil.class.php';
		require_once ROOT_PATH . 'includes/classes/Cache.class.php';
		require_once ROOT_PATH . 'includes/classes/Database.class.php';
		require_once ROOT_PATH . 'includes/classes/Universe.class.php';
		require_once ROOT_PATH . 'includes/classes/Config.class.php';
		require_once ROOT_PATH . 'includes/classes/class.FleetFunctions.php';
		require_once ROOT_PATH . 'includes/classes/class.PlanetRessUpdate.php';
		require_once ROOT_PATH . 'includes/classes/class.MissionFunctions.php';
		require_once ROOT_PATH . 'includes/classes/missions/Mission.interface.php';
		require_once ROOT_PATH . 'includes/classes/missions/MissionCaseStay.class.php';

		Database::get()->nativeQuery('USE `' . self::TEST_DATABASE . '`');

		// vars.php uses extract() — requiring it from a method would leave $resource
		// local to that method. Push the cache payload into $GLOBALS instead.
		$cache = \Cache::get();
		$cache->add('vars', 'VarsBuildCache');
		foreach ($cache->getData('vars') as $key => $value) {
			$GLOBALS[$key] = $value;
		}
		$GLOBALS['resource'][901] = 'metal';
		$GLOBALS['resource'][902] = 'crystal';
		$GLOBALS['resource'][903] = 'deuterium';
		$GLOBALS['resource'][911] = 'energy';
		$GLOBALS['resource'][921] = 'darkmatter';
		$GLOBALS['reslist']['ressources'] = [901, 902, 903, 911, 921];
		$GLOBALS['reslist']['resstype'][1] = [901, 902, 903];
		$GLOBALS['reslist']['resstype'][2] = [911];
		$GLOBALS['reslist']['resstype'][3] = [921];
	}
}
