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

class Cronjob
{
	function __construct()
	{
		
	}
	
	static function execute($cronjobID)
	{
		$cronjobID	= (int) $cronjobID;
		$lockToken	= md5(uniqid(TIMESTAMP.'_'.$cronjobID, true));

		$db	= Database::get();

		// cronjob.php is reachable by every logged in player, so the schedule is
		// what decides whether a job may run, not the caller. Claiming the lock
		// and checking that the job is due must be the same statement: with a
		// SELECT followed by an UPDATE two parallel requests both pass the check
		// and the job runs twice.
		$sql = 'UPDATE %%CRONJOBS%% SET `lock` = :lock
		WHERE cronjobID = :cronjobId AND isActive = :isActive
		AND `lock` IS NULL AND nextTime < :time;';

		$db->update($sql, array(
			':lock'			=> $lockToken,
			':cronjobId'	=> $cronjobID,
			':isActive'		=> 1,
			':time'			=> TIMESTAMP
		));

		// Unknown, disabled, already running or not due yet.
		if($db->rowCount() != 1)
		{
			return false;
		}

		$sql = 'SELECT class FROM %%CRONJOBS%% WHERE cronjobID = :cronjobId;';

		$cronjobClassName	= $db->selectSingle($sql, array(
			':cronjobId'	=> $cronjobID
		), 'class');

		try
		{
			if(!preg_match('/^[A-Za-z0-9_]+$/', $cronjobClassName))
			{
				throw new Exception(sprintf("Invalid cronjob class name %s!", $cronjobClassName));
			}

			$cronjobPath	= 'includes/classes/cronjob/'.$cronjobClassName.'.class.php';

			// Deliberately not require_once on a missing file: a fatal error would
			// skip the release below and leave the job locked for good.
			if(!file_exists($cronjobPath))
			{
				throw new Exception(sprintf("Cronjob class file %s not found!", $cronjobPath));
			}

			require_once($cronjobPath);

			/** @var $cronjobObj CronjobTask */
			$cronjobObj		= new $cronjobClassName;
			$cronjobObj->run();

			$sql = 'INSERT INTO %%CRONJOBS_LOG%% SET `cronjobId` = :cronjobId,
			`executionTime` = :executionTime, `lockToken` = :lockToken';

			$db->insert($sql, array(
				':cronjobId'		=> $cronjobID,
				':executionTime'	=> Database::formatDate(TIMESTAMP),
				':lockToken'		=> $lockToken
			));
		}
		finally
		{
			// Move to the next slot and release even when run() threw, otherwise the
			// job stays locked forever and every page load retries the same failure.
			self::reCalculateCronjobs($cronjobID);

			$sql = 'UPDATE %%CRONJOBS%% SET `lock` = NULL WHERE cronjobID = :cronjobId AND `lock` = :lock;';

			$db->update($sql, array(
				':cronjobId'	=> $cronjobID,
				':lock'			=> $lockToken
			));
		}

		return true;
	}
	
	static function getNeedTodoExecutedJobs()
	{
		$sql			= 'SELECT cronjobID
		FROM %%CRONJOBS%%
		WHERE isActive = :isActive AND nextTime < :time AND `lock` IS NULL;';

		$cronjobResult	= Database::get()->select($sql, array(
			':isActive'	=> 1,
			':time'		=> TIMESTAMP
 		));

		$cronjobList	= array();

		foreach($cronjobResult as $cronjobRow)
		{
			$cronjobList[]	= $cronjobRow['cronjobID'];
		}
		
		return $cronjobList;
	}

	static function getLastExecutionTime($cronjobName)
	{
		require_once 'includes/libs/tdcron/class.tdcron.php';
		require_once 'includes/libs/tdcron/class.tdcron.entry.php';

		$sql		= 'SELECT MAX(executionTime) as executionTime FROM %%CRONJOBS_LOG%% INNER JOIN %%CRONJOBS%% USING(cronjobId) WHERE name = :cronjobName;';
		$lastTime	= Database::get()->selectSingle($sql, array(
			':cronjobName' => $cronjobName
		), 'executionTime');

		if(empty($lastTime))
		{
			return false;
		}

		return strtotime($lastTime);
	}
	
	static function reCalculateCronjobs($cronjobID = NULL)
	{
		require_once 'includes/libs/tdcron/class.tdcron.php';
		require_once 'includes/libs/tdcron/class.tdcron.entry.php';

		$db	= Database::get();

		if(!empty($cronjobID))
		{
			$sql			= 'SELECT cronjobID, min, hours, dom, month, dow FROM %%CRONJOBS%% WHERE cronjobID = :cronjobId;';
			$cronjobResult	= $db->select($sql, array(
				':cronjobId' => $cronjobID
			));
		}
		else
		{
			$sql			= 'SELECT cronjobID, min, hours, dom, month, dow FROM %%CRONJOBS%%;';
			$cronjobResult	= $db->select($sql);
		}

		$sql = 'UPDATE %%CRONJOBS%% SET nextTime = :nextTime WHERE cronjobID = :cronjobId;';

		foreach($cronjobResult as $cronjobRow)
		{
			$cronTabString	= implode(' ', array($cronjobRow['min'], $cronjobRow['hours'], $cronjobRow['dom'], $cronjobRow['month'], $cronjobRow['dow']));
			$nextTime		= tdCron::getNextOccurrence($cronTabString, TIMESTAMP + 60);

			$db->update($sql, array(
				':nextTime'		=> $nextTime,
				':cronjobId'	=> $cronjobRow['cronjobID'],
			));
		}
	}
}