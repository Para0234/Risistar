<?php

/**
 *  2Moons 
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

class FlyingFleetHandler
{	
	protected $token;
	
	public static $missionObjPattern	= array(
		1	=> 'MissionCaseAttack',
		2	=> 'MissionCaseACS',
		3	=> 'MissionCaseTransport',
		4	=> 'MissionCaseStay',
		5	=> 'MissionCaseStayAlly',
		6	=> 'MissionCaseSpy',
		7	=> 'MissionCaseColonisation',
		8	=> 'MissionCaseRecycling',
		9	=> 'MissionCaseDestruction',
		10	=> 'MissionCaseMIP',
		11	=> 'MissionCaseFoundDM',
		15	=> 'MissionCaseExpedition',
	);

	/**
	 * True for engine tokens (md5 hex from getRandomString()).
	 * Admin / custom locks (e.g. ADM_LOCK) are not auto-releasable.
	 */
	public static function isAutoReleasableLock($lock)
	{
		return is_string($lock) && (bool) preg_match('/^[a-f0-9]{32}$/', $lock);
	}

	/**
	 * Release stuck MD5 processing locks older than $lockTimeout seconds.
	 * Non-hash locks are left untouched.
	 */
	public static function clearStaleLocks($lockTimeout = 300)
	{
		$db = Database::get();
		$staleThreshold = TIMESTAMP - (int) $lockTimeout;

		// Candidates: any non-null lock past the timeout (or orphan lockedAt).
		// isAutoReleasableLock() keeps ADM_LOCK / custom locks untouched.
		$sqlSelectStale = 'SELECT fleetID, `time`, lockedAt, `lock` FROM %%FLEETS_EVENT%%
		WHERE `lock` IS NOT NULL AND (lockedAt IS NULL OR lockedAt < :staleThreshold);';

		$staleEvents = $db->select($sqlSelectStale, array(
			':staleThreshold' => $staleThreshold
		));

		$staleEvents = array_filter($staleEvents, function ($event) {
			return self::isAutoReleasableLock($event['lock']);
		});

		foreach ($staleEvents as $event)
		{
			error_log(sprintf(
				"[FLEET ALERT] Auto-released stale lock for fleet #%d, event time %s, lockedAt %s (lock: %s)",
				$event['fleetID'],
				date('Y-m-d H:i:s', $event['time']),
				empty($event['lockedAt']) ? 'NULL' : date('Y-m-d H:i:s', $event['lockedAt']),
				$event['lock']
			));

			$db->update('UPDATE %%FLEETS_EVENT%% SET `lock` = NULL, lockedAt = NULL WHERE fleetID = :fleetId AND `lock` = :lock;', array(
				':fleetId'	=> $event['fleetID'],
				':lock'		=> $event['lock'],
			));
		}
	}

	/**
	 * Run missions for fleets claimed under $token, always releasing the token.
	 * Uses late static binding so tests can subclass and override run().
	 */
	public static function processDueEvents($token)
	{
		$db = Database::get();

		try
		{
			$fleetObj = new static();
			$fleetObj->setToken($token);
			$fleetObj->run();
		}
		catch (\Throwable $e)
		{
			error_log(sprintf(
				"[FLEET ERROR] Fleet processing failed for token %s: %s in %s:%d",
				$token,
				$e->getMessage(),
				$e->getFile(),
				$e->getLine()
			));
		}
		finally
		{
			$db->update("UPDATE %%FLEETS_EVENT%% SET `lock` = NULL, lockedAt = NULL WHERE `lock` = :token;", array(
				':token' => $token
			));
		}
	}
		
	function setToken($token)
	{
		$this->token	= $token;
	}
	
	function run()
	{
		require_once 'includes/classes/class.MissionFunctions.php';
		require_once 'includes/classes/missions/Mission.interface.php';

		$db	= Database::get();

		$sql = 'SELECT %%FLEETS%%.*
		FROM %%FLEETS_EVENT%%
		INNER JOIN %%FLEETS%% ON fleetID = fleet_id
		WHERE `lock` = :token;';

		$fleetResult = $db->select($sql, array(
			':token'	=> $this->token
		));

		foreach($fleetResult as $fleetRow)
		{
			if(!isset(self::$missionObjPattern[$fleetRow['fleet_mission']])) {
				$sql = 'DELETE FROM %%FLEETS%% WHERE fleet_id = :fleetId;';

				$db->delete($sql, array(
					':fleetId'	=> $fleetRow['fleet_id']
			  	));

				continue;
			}
			
			$missionName	= self::$missionObjPattern[$fleetRow['fleet_mission']];

			$path	= 'includes/classes/missions/'.$missionName.'.class.php';
			require_once $path;
			/** @var $missionObj Mission */
			$missionObj	= new $missionName($fleetRow);
			
			switch($fleetRow['fleet_mess'])
			{
				case 0:
					$missionObj->TargetEvent();
				break;
				case 1:
					$missionObj->ReturnEvent();
				break;
				case 2:
					$missionObj->EndStayEvent();
				break;
			}
		}
	}
}
