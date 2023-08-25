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

require_once 'includes/classes/cronjob/CronjobTask.interface.php';

class TeamSpeakCronjob implements CronjobTask
{
	function run()
	{
      	//Okay soooo... For some reason, since switching to php 7.4, the cronjobs aren't being executed. Unless there is a cronjob that is executed CONSTANTLY. And when I say "constantly", I mean on every single refresh. For the tests, I used the dark matter cronjob, but becauseit actually has an use, I decided to put this one instead, as it is completely useless.
/*		Cache::get()->add('teamspeak', 'TeamspeakBuildCache');
		Cache::get()->flush('teamspeak');*/
	}
}