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
//Ce cronjob est le cronjob qui permet de donner la MN X fois par jour.


require_once 'includes/classes/cronjob/CronjobTask.interface.php';


class DmMailCronjob implements CronjobTask
{
	function run()
	{
		$db				= Database::get();
		$sql	= 'UPDATE %%USERS%% SET post_mail = post_mail + 1 WHERE darkmatter = post_mail * 100;';
		$db->update($sql);
		$sql	= 'UPDATE %%USERS%% SET darkmatter = post_mail * 100 WHERE post_mail != 0;';//On ajoute 100x Authlevel (pour des raisons de test, ça sera post_mail par la suite) à tous les utilisateurs.
		$db->update($sql);
        ClearCache();
	}
}