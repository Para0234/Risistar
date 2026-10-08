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


class ShowFleetDealerPage extends AbstractGamePage
{
	public static $requireModule = MODULE_FLEET_TRADER;

	function __construct() 
	{
		parent::__construct();
	}
	
	public function send()
	{
		global $USER, $PLANET, $LNG, $resource;
		
		$shipID			= HTTP::_GP('shipID', 0);
		$Count			= max(0, round(HTTP::_GP('count', 0.0)));
		$allowedShipIDs	= explode(',', Config::get()->trade_allowed_ships);
		
		if(!empty($shipID) && !empty($Count) && in_array($shipID, $allowedShipIDs) && $PLANET[$resource[$shipID]] >= $Count)
		{
			$tradeCharge					= 1 - (Config::get()->trade_charge / 100);
			$costResources					= BuildFunctions::getElementPrice($USER, $PLANET, $shipID, false, $Count) + array(901 => 0, 902 => 0, 903 => 0, 921 => 0);
			$PLANET[$resource[901]]			+= $costResources[901] * $tradeCharge;
			$PLANET[$resource[902]]			+= $costResources[902] * $tradeCharge;
			$PLANET[$resource[903]]			+= $costResources[903] * $tradeCharge;
			$USER[$resource[921]]			+= $costResources[921] * $tradeCharge;
			
			$PLANET[$resource[$shipID]]		-= $Count;

            $sql = 'UPDATE %%PLANETS%% SET '.$resource[$shipID].' = '.$resource[$shipID].' - :count WHERE id = :planetID;';
			Database::get()->update($sql, array(
                ':count'        => $Count,
                ':planetID'     => $PLANET['id']
            ));

            $this->printMessage($LNG['tr_exchange_done'], array(array(
				'label'	=> $LNG['sys_forward'],
				'url'	=> 'game.php?page=fleetDealer'
			)));
		}
		else
		{
			$this->printMessage($LNG['tr_exchange_error'], array(array(
				'label'	=> $LNG['sys_back'],
				'url'	=> 'game.php?page=fleetDealer'
			)));
		}
		
	}
	
	function show()
	{
		global $PLANET, $USER, $LNG, $resource, $reslist;
		
		$Cost		= array();
		
		$allowedShipIDs	= explode(',', Config::get()->trade_allowed_ships);
		
		foreach($allowedShipIDs as $shipID)
		{
			if(in_array($shipID, $reslist['fleet']) || in_array($shipID, $reslist['defense'])) {
				$costResources	= BuildFunctions::getElementPrice($USER, $PLANET, $shipID, false, 1) + array(901 => 0, 902 => 0, 903 => 0, 921 => 0);
				$Cost[$shipID]	= array($PLANET[$resource[$shipID]], $LNG['tech'][$shipID], $costResources);
			}
		}
		
		if(empty($Cost))
		{
			$this->printMessage($LNG['ft_empty'], array(array(
				'label'	=> $LNG['sys_back'],
				'url'	=> 'game.php?page=fleetDealer'
			)));
		}

		$this->assign(array(
			'shipIDs'	=> $allowedShipIDs,
			'CostInfos'	=> $Cost,
			'Charge'	=> Config::get()->trade_charge,
		));
		
		$this->display('page.fleetDealer.default.tpl');
	}
}