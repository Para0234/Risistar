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

class ShowRaportPage extends AbstractGamePage
{
	public static $requireModule = 0;
	
	protected $disableEcoSystem = true;

	function __construct() 
	{
		parent::__construct();
	}
	
	private function BCWrapperPreRev2321($combatReport)
	{
		if(isset($combatReport['moon']['desfail']))
		{
			$combatReport['moon']	= array(
				'moonName'				=> $combatReport['moon']['name'],
				'moonChance'			=> $combatReport['moon']['chance'],
				'moonDestroySuccess'	=> !$combatReport['moon']['desfail'],
				'fleetDestroyChance'	=> $combatReport['moon']['chance2'],
				'fleetDestroySuccess'	=> !$combatReport['moon']['fleetfail']
			);			
		}
		elseif(isset($combatReport['moon'][0]))
		{
			$combatReport['moon']	= array(
				'moonName'				=> $combatReport['moon'][1],
				'moonChance'			=> $combatReport['moon'][0],
				'moonDestroySuccess'	=> !$combatReport['moon'][2],
				'fleetDestroyChance'	=> $combatReport['moon'][3],
				'fleetDestroySuccess'	=> !$combatReport['moon'][4]
			);			
		}
		
		if(isset($combatReport['simu']))
		{
			$combatReport['additionalInfo'] = $combatReport['simu'];
		}
		
		if(isset($combatReport['debris'][0]))
		{
            $combatReport['debris'] = array(
                901	=> $combatReport['debris'][0],
                902	=> $combatReport['debris'][1]
            );
		}
		
		if (!empty($combatReport['steal']['metal']))
		{
			$combatReport['steal'] = array(
				901	=> $combatReport['steal']['metal'],
				902	=> $combatReport['steal']['crystal'],
				903	=> $combatReport['steal']['deuterium']
			);
		}
		
		return $combatReport;
	}

	private function decorateCombatReport(array $combatReport): array
	{
		if (empty($combatReport['rounds']) || !is_array($combatReport['rounds'])) {
			$combatReport['rounds'] = array();
			return $combatReport;
		}

		foreach ($combatReport['rounds'] as &$round) {
			$shipIds = array();
			foreach (array('attacker', 'defender') as $side) {
				if (empty($round[$side]) || !is_array($round[$side])) {
					$round[$side] = array();
					continue;
				}
				foreach ($round[$side] as $player) {
					if (empty($player['ships']) || !is_array($player['ships'])) {
						continue;
					}
					foreach ($player['ships'] as $shipId => $shipData) {
						$shipIds[(int) $shipId] = true;
					}
				}
			}
			ksort($shipIds, SORT_NUMERIC);
			$round['shipIds'] = array_keys($shipIds);
			$round['attackLabels'] = array();
			foreach ($round['shipIds'] as $shipId) {
				if (self::isAttackerCombatLabel((int) $shipId)) {
					$round['attackLabels'][$shipId] = true;
				}
			}
		}
		unset($round);

		return $combatReport;
	}

	/**
	 * Attack fleets cannot carry planetary defenses or orbital stations.
	 * shipclass 3 in uni1_vars: solar satellite and orbital stations.
	 */
	public static function isAttackerCombatLabel(int $shipId): bool
	{
		global $reslist;

		$defense = isset($reslist['defense']) ? $reslist['defense'] : array();
		$missile = isset($reslist['missile']) ? $reslist['missile'] : array();
		if (in_array($shipId, $defense, false) || in_array($shipId, $missile, false)) {
			return false;
		}

		static $orbitalIds = array(212, 227, 228, 229, 230, 231);
		if (in_array($shipId, $orbitalIds, true)) {
			return false;
		}

		return true;
	}
	
	function battlehall() 
	{
		global $LNG, $USER;
		
		$LNG->includeData(array('FLEET'));
		$this->setWindow('popup');

		$db = Database::get();

		$RID		= HTTP::_GP('raport', '');

		$sql = "SELECT 
			raport, time,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as attacker
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 1)
			) as attacker,
			(
				SELECT
				GROUP_CONCAT(username SEPARATOR ' & ') as defender
				FROM %%USERS%%
				WHERE id IN (SELECT uid FROM %%TOPKB_USERS%% WHERE %%TOPKB_USERS%%.rid = %%RW%%.rid AND role = 2)
			) as defender
			FROM %%RW%%
			WHERE rid = :reportID
			AND EXISTS (
				SELECT 1 FROM %%TOPKB%%
				WHERE %%TOPKB%%.rid = %%RW%%.rid
				AND %%TOPKB%%.universe = :universe
			);";
		$reportData = $db->selectSingle($sql, array(
			':reportID'	=> $RID,
			':universe'	=> Universe::current(),
		));

		if(empty($reportData)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}

		$Info		= array($reportData["attacker"], $reportData["defender"]);
		
		$combatReport			= unserialize($reportData['raport']);
		$combatReport['time']	= _date($LNG['php_tdformat'], $combatReport['time'], $USER['timezone']);
		$combatReport			= $this->decorateCombatReport($this->BCWrapperPreRev2321($combatReport));
		
		$this->assign(array(
			'Raport'	=> $combatReport,
			'Info'		=> $Info,
			'pageTitle'	=> $LNG['lm_topkb']
		));
		
		$this->display('shared.mission.raport.tpl');
	}
	
	function show() 
	{
		global $LNG, $USER;
		
		$LNG->includeData(array('FLEET'));		
		$this->setWindow('popup');

		$db = Database::get();

		$RID		= HTTP::_GP('raport', '');

		$sql = "SELECT raport,attacker,defender FROM %%RW%% WHERE rid = :reportID;";
		$reportData = $db->selectSingle($sql, array(
			':reportID'	=> $RID
		));

		if(empty($reportData)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}
		$isAdmin = 0;
		// empty is BC for pre r2484
		$isAttacker = empty($reportData['attacker']) || in_array($USER['id'], explode(",", $reportData['attacker']));
		$isDefender = empty($reportData['defender']) || in_array($USER['id'], explode(",", $reportData['defender']));
		if($USER['authlevel'] == 3)
		{
			$isAdmin = 1;
		}
		else
		{
			$isAdmin = 0;
		}
		
		if(empty($reportData) || (!$isAttacker && !$isDefender && !$isAdmin)) {
			$this->printMessage($LNG['sys_raport_not_found']);
		}

		$combatReport			= unserialize($reportData['raport']);
		if($isAttacker && !$isDefender && $combatReport['result'] == 'r' && count($combatReport['rounds']) <= 2) {
			$this->printMessage($LNG['sys_raport_lost_contact']);
		}
		
		$combatReport['time']	= _date($LNG['php_tdformat'], $combatReport['time'], $USER['timezone']);
		$combatReport			= $this->decorateCombatReport($this->BCWrapperPreRev2321($combatReport));
		
		$this->assign(array(
			'Raport'	=> $combatReport,
			'pageTitle'	=> $LNG['sys_mess_attack_report']
		));
		
		$this->display('shared.mission.raport.tpl');
	}
}