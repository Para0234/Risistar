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

require_once 'includes/classes/class.FlyingFleetHandler.php';

FlyingFleetHandler::clearStaleLocks();

$token	= getRandomString();
$db		= Database::get();

$db->update("UPDATE %%FLEETS_EVENT%% SET `lock` = :token, lockedAt = :lockedAt WHERE `lock` IS NULL AND `time` <= :time;", array(
	':time'		=> TIMESTAMP,
	':token'	=> $token,
	':lockedAt'	=> TIMESTAMP,
));

if($db->rowCount() !== 0) {
	FlyingFleetHandler::processDueEvents($token);
}
