<?php
	
 
class ShowAchievementsPage extends AbstractGamePage 
{
	public static $requireModule = 0;
	function __construct() 
	{
parent::__construct();
	}

function show()
{
	global $USER, $PLANET, $LNG, $CONF, $reslist, $resource;
	
	$db = Database::get();
	
	/* Rewards */
	
	$metal1_metal = 			0;
    $metal1_crystal = 			0; 
    $metal1_deuterium = 		0; 
	$metal1_darkmatter = 		0;
	
	$metal2_metal = 			0;
    $metal2_crystal = 			0; 
    $metal2_deuterium = 		0; 
	$metal2_darkmatter =		0;
	
	$metal3_metal = 			0;
    $metal3_crystal = 			0; 
    $metal3_deuterium = 		0; 	
	$metal3_darkmatter = 		0;
	
	$metal4_metal = 			0;
    $metal4_crystal = 			0; 
    $metal4_deuterium = 		0; 	
	$metal4_darkmatter = 		0;
	
	$tech1_metal = 				0;
    $tech1_crystal = 			0; 
    $tech1_deuterium = 			0; 		
	$tech1_darkmatter = 		0;
	
	$tech2_metal = 				0;
    $tech2_crystal = 			0; 
    $tech2_deuterium = 			0; 		
	$tech2_darkmatter = 		0;
	
	$tech3_metal = 				0;
    $tech3_crystal = 			0; 
    $tech3_deuterium = 			0; 		
	$tech3_darkmatter = 		0;
	
	$tech4_metal = 				0;
    $tech4_crystal = 			0; 
    $tech4_deuterium = 			0; 		
	$tech4_darkmatter =			0;

	$engine1_metal =			0;
    $engine1_crystal = 			0; 
    $engine1_deuterium = 		0; 		
	$engine1_darkmatter = 		0;
	
	$engine2_metal = 			0;
    $engine2_crystal = 			0; 
    $engine2_deuterium = 		0; 		
	$engine2_darkmatter = 		0;
	
	$engine3_metal = 			0;
    $engine3_crystal = 			0; 
    $engine3_deuterium = 		0; 		
	$engine3_darkmatter = 		0;
	
	$engine4_metal = 			0;
    $engine4_crystal = 			0; 
    $engine4_deuterium = 		0; 		
	$engine4_darkmatter = 		0;
	
	$colonisation1_metal = 		0;
    $colonisation1_crystal = 	0; 
    $colonisation1_deuterium = 	0; 		
	$colonization1_darkmatter = 0;
	
	$colonisation2_metal = 		0;
    $colonisation2_crystal = 	0; 
    $colonisation2_deuterium = 	0; 		
	$colonization2_darkmatter = 0;
	
	$colonisation3_metal = 		0;
    $colonisation3_crystal = 	0; 
    $colonisation3_deuterium = 	0; 		
	$colonization3_darkmatter = 0;
	
	$moon1_metal = 				0;
    $moon1_crystal = 			0; 
    $moon1_deuterium = 			0; 		
	$moon1_darkmatter = 		0;
	
	$moon2_metal = 				0;
    $moon2_crystal = 			0; 
    $moon2_deuterium = 			0; 		
	$moon2_darkmatter = 		0;
	
	$moon3_metal = 				0;
    $moon3_crystal = 			0; 
    $moon3_deuterium = 			0; 		
	$moon3_darkmatter = 		0;
	
	$war1_metal = 				0;
    $war1_crystal = 			0; 
    $war1_deuterium = 			0; 		
	$war1_darkmatter = 			0;
	
	$war2_metal = 				0;
    $war2_crystal = 			0; 
    $war2_deuterium = 			0; 		
	$war2_darkmatter = 			0;
	
	$war3_metal = 				0;
    $war3_crystal = 			0; 
    $war3_deuterium = 			0; 		
	$war3_darkmatter = 			0;
	
	$destroy1_metal = 			0;
    $destroy1_crystal = 		0; 
    $destroy1_deuterium = 		0; 		
	$destroy1_darkmatter = 		0;
	
	$destroy2_metal = 			0;
    $destroy2_crystal = 		0; 
    $destroy2_deuterium = 		0; 		
	$destroy2_darkmatter = 		0;
	
	$destroy3_metal = 			0;
    $destroy3_crystal = 		0; 
    $destroy3_deuterium = 		0; 		
	$destroy3_darkmatter = 		0;
	
	$destroy4_metal = 			0;
    $destroy4_crystal = 		0; 
    $destroy4_deuterium = 		0; 		
	$destroy4_darkmatter = 		0;
	
	$time1_metal = 				0;
    $time1_crystal = 			0; 
    $time1_deuterium = 			0; 		
	$time1_darkmatter = 		0;
	
	$time2_metal = 				0;
    $time2_crystal = 			0; 
    $time2_deuterium = 			0; 		
	$time2_darkmatter = 		0;
	
	$time3_metal = 				0;
    $time3_crystal = 			0; 
    $time3_deuterium = 			0; 		
	$time3_darkmatter = 		0;
	
	$storage1_metal = 			0;
    $storage1_crystal = 		0; 
    $storage1_deuterium = 		0; 		
	$storage1_darkmatter = 		0;
	
	$storage2_metal = 			0;
    $storage2_crystal = 		0; 
    $storage2_deuterium = 		0; 		
	$storage2_darkmatter = 		0;
	
	$storage3_metal = 			0;
    $storage3_crystal = 		0; 
    $storage3_deuterium = 		0; 		
	$storage3_darkmatter = 		0;
	
	$storage4_metal = 			0;
    $storage4_crystal = 		0; 
    $storage4_deuterium = 		0; 		
	$storage4_darkmatter = 		0;
	
	$community1_metal = 		0;
    $community1_crystal = 		0; 
    $community1_deuterium = 	0; 		
	$community1_darkmatter = 	100;
	
	$community2_metal = 		0;
    $community2_crystal = 		0; 
    $community2_deuterium = 	0; 		
	$community2_darkmatter = 	0;
	
	$community3_metal = 		0;
    $community3_crystal =	 	0; 
    $community3_deuterium = 	0; 		
	$community3_darkmatter = 	0;
	
	$fleet1_metal = 			0;
    $fleet1_crystal = 			0; 
    $fleet1_deuterium = 		0; 		
	$fleet1_darkmatter = 		0;
	
	$fleet2_metal = 			0;
    $fleet2_crystal = 			0; 
    $fleet2_deuterium = 		0; 		
	$fleet2_darkmatter = 		0;
	
	$fleet3_metal = 			0;
    $fleet3_crystal = 			0; 
    $fleet3_deuterium = 		0; 		
	$fleet3_darkmatter = 		0;
	
	$fleet4_metal = 			0;
    $fleet4_crystal = 			0; 
    $fleet4_deuterium = 		0; 		
	$fleet4_darkmatter = 		0;
	
	$darkmatter1_metal = 		0;
    $darkmatter1_crystal = 		0; 
    $darkmatter1_deuterium = 	0; 		
	$darkmatter1_darkmatter = 	0;
	
	$darkmatter2_metal = 		0;
    $darkmatter2_crystal = 		0; 
    $darkmatter2_deuterium = 	0; 		
	$darkmatter2_darkmatter = 	0;
	
	$darkmatter3_metal = 		0;
    $darkmatter3_crystal = 		0; 
    $darkmatter3_deuterium = 	0; 		
	$darkmatter3_darkmatter = 	0;
	
	$darkmatter4_metal = 		0;
    $darkmatter4_crystal = 		0; 
    $darkmatter4_deuterium = 	0; 		
	$darkmatter4_darkmatter = 	0;
	
	$planet1_metal = 			0;
    $planet1_crystal = 			0; 
    $planet1_deuterium = 		0; 		
	$planet1_darkmatter = 		0;
	
	$planet2_metal = 			0;
    $planet2_crystal = 			0; 
    $planet2_deuterium = 		0; 		
	$planet2_darkmatter = 		0;
	
	$planet3_metal = 			0;
    $planet3_crystal = 			0; 
    $planet3_deuterium = 		0; 		
	$planet3_darkmatter = 		0;
	
	$planet4_metal = 			0;
    $planet4_crystal = 			0; 
    $planet4_deuterium = 		0; 		
	$planet4_darkmatter = 		0;
	
	$lab1_metal = 				0;
    $lab1_crystal = 			0; 
    $lab1_deuterium = 			0; 		
	$lab1_darkmatter = 			0;
	
	$lab2_metal = 				0;
    $lab2_crystal = 			0; 
    $lab2_deuterium = 			0; 		
	$lab2_darkmatter = 			0;
	
	$lab3_metal = 				0;
    $lab3_crystal = 			0; 
    $lab3_deuterium = 			0; 		
	$lab3_darkmatter = 			0;
	
	$lab4_metal = 				0;
    $lab4_crystal = 			0; 
    $lab4_deuterium = 			0; 		
	$lab4_darkmatter = 			0;
	
	
	
######### Mines Achievements Level 1

	if(empty($USER['achievements_mines1']))
	{
if($PLANET['metal_mine'] >= 9 && $PLANET['crystal_mine'] >= 8 && $PLANET['deuterium_sintetizer'] >=6)
{
	$db->query("UPDATE uni1_users SET `achievements_mines1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_mines` = achievements_mines+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $metal1_darkmatter;
	$PLANET[$resource[901]]	+= $metal1_metal;
    $PLANET[$resource[902]]	+= $metal1_crystal; 
    $PLANET[$resource[903]]	+= $metal1_deuterium; 
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_mines1_success']);	
		
}
if($PLANET['metal_mine'] >=9)
	$mines1_req1 = '<span style="color:lime">'.$LNG['achievements_mines1_require_1'].'</span>';
else
	$mines1_req1 = '<span style="color:red">'.$LNG['achievements_mines1_require_1'].'</span>';
if($PLANET['crystal_mine'] >=8)
	$mines1_req2 = '<span style="color:lime">'.$LNG['achievements_mines1_require_2'].'</span>';
else
	$mines1_req2 = '<span style="color:red">'.$LNG['achievements_mines1_require_2'].'</span>';
if($PLANET['deuterium_sintetizer'] >=6)
	$mines1_req3 = '<span style="color:lime">'.$LNG['achievements_mines1_require_3'].'</span>';
else
	$mines1_req3 = '<span style="color:red">'.$LNG['achievements_mines1_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'mines1_req1'    	=> $mines1_req1,
	'mines1_req2'    	=> $mines1_req2,
	'mines1_req3'    	=> $mines1_req3,
	'mines1_title'    => ' <span style="color:red">'.$LNG['achievements_mines1'].'</span>',
	'mines1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_mines1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'mines1_title'    => ' <span style="color:lime">'.$LNG['achievements_mines1'].'</span>',
	'mines1_req1'    => ' <span style="color:lime">'.$LNG['achievements_mines1_done'].'</span>',
	'mines1_req2'    	=> ' <span style="color:lime">'.'</span>',
	'mines1_req3'    	=> ' <span style="color:lime">'.'</span>',
	'mines1_img'    => ' <img border="1" src="styles/achievements/mines1_done.png" align="top" width="80" height="80"></td>',
));
	}

	
######### Mines Achievements Level 2

	if(empty($USER['achievements_mines2']))
	{
if($PLANET['metal_mine'] >= 17 && $PLANET['crystal_mine'] >= 15 && $PLANET['deuterium_sintetizer'] >=12)
{
	$db->query("UPDATE uni1_users SET `achievements_mines2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_mines` = achievements_mines+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $metal2_darkmatter;
	$PLANET[$resource[901]]	+= $metal2_metal;
    $PLANET[$resource[902]]	+= $metal2_crystal; 
    $PLANET[$resource[903]]	+= $metal2_deuterium; 
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_mines2_success']);	
	
}
if($PLANET['metal_mine'] >=17)
	$mines2_req1 = '<span style="color:lime">'.$LNG['achievements_mines2_require_1'].'</span>';
else
	$mines2_req1 = '<span style="color:red">'.$LNG['achievements_mines2_require_1'].'</span>';
if($PLANET['crystal_mine'] >=15)
	$mines2_req2 = '<span style="color:lime">'.$LNG['achievements_mines2_require_2'].'</span>';
else
	$mines2_req2 = '<span style="color:red">'.$LNG['achievements_mines2_require_2'].'</span>';
if($PLANET['deuterium_sintetizer'] >=12)
	$mines2_req3 = '<span style="color:lime">'.$LNG['achievements_mines2_require_3'].'</span>';
else
	$mines2_req3 = '<span style="color:red">'.$LNG['achievements_mines2_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'mines2_req1'    	=> $mines2_req1,
	'mines2_req2'    	=> $mines2_req2,
	'mines2_req3'    	=> $mines2_req3,
	'mines2_title'    => ' <span style="color:red">'.$LNG['achievements_mines2'].'</span>',
	'mines2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_mines2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'mines2_title'    => ' <span style="color:lime">'.$LNG['achievements_mines2'].'</span>',
	'mines2_req1'    => ' <span style="color:lime">'.$LNG['achievements_mines2_done'].'</span>',
	'mines2_req2'    	=> ' <span style="color:lime">'.'</span>',
	'mines2_req3'    	=> ' <span style="color:lime">'.'</span>',
	'mines2_img'    => ' <img border="1" src="styles/achievements/mines2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Mines Achievements Level 3

	if(empty($USER['achievements_mines3'] ))
	{
if($PLANET['metal_mine'] >= 24 && $PLANET['crystal_mine'] >= 22 && $PLANET['deuterium_sintetizer'] >=21)
{
	$db->query("UPDATE uni1_users SET `achievements_mines3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_mines` = achievements_mines+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $metal3_darkmatter;
	$PLANET[$resource[901]]	+= $metal3_metal;
    $PLANET[$resource[902]]	+= $metal3_crystal; 
    $PLANET[$resource[903]]	+= $metal3_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_mines3_success']);	
	
}
if($PLANET['metal_mine'] >=24)
	$mines3_req1 = '<span style="color:lime">'.$LNG['achievements_mines3_require_1'].'</span>';
else
	$mines3_req1 = '<span style="color:red">'.$LNG['achievements_mines3_require_1'].'</span>';
if($PLANET['crystal_mine'] >=22)
	$mines3_req2 = '<span style="color:lime">'.$LNG['achievements_mines3_require_2'].'</span>';
else
	$mines3_req2 = '<span style="color:red">'.$LNG['achievements_mines3_require_2'].'</span>';
if($PLANET['deuterium_sintetizer'] >=21)
	$mines3_req3 = '<span style="color:lime">'.$LNG['achievements_mines3_require_3'].'</span>';
else
	$mines3_req3 = '<span style="color:red">'.$LNG['achievements_mines3_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'mines3_req1'    	=> $mines3_req1,
	'mines3_req2'    	=> $mines3_req2,
	'mines3_req3'    	=> $mines3_req3,
	'mines3_title'    => ' <span style="color:red">'.$LNG['achievements_mines3'].'</span>',
	'mines3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_mines3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'mines3_title'    => ' <span style="color:lime">'.$LNG['achievements_mines3'].'</span>',
	'mines3_req1'    => ' <span style="color:lime">'.$LNG['achievements_mines3_done'].'</span>',
	'mines3_req2'    	=> ' <span style="color:lime">'.'</span>',
	'mines3_req3'    	=> ' <span style="color:lime">'.'</span>',
	'mines3_img'    => ' <img border="1" src="styles/achievements/mines3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Mines Achievements Level 4

	if(empty($USER['achievements_mines4'] ))
	{
if($PLANET['metal_mine'] >= 30 && $PLANET['crystal_mine'] >= 29 && $PLANET['deuterium_sintetizer'] >=27)
{
	$db->query("UPDATE uni1_users SET `achievements_mines4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_mines` = achievements_mines+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $metal4_darkmatter;
	$PLANET[$resource[901]]	+= $metal4_metal;
    $PLANET[$resource[902]]	+= $metal4_crystal; 
    $PLANET[$resource[903]]	+= $metal4_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_mines4_success']);	
		
}
if($PLANET['metal_mine'] >=30)
	$mines4_req1 = '<span style="color:lime">'.$LNG['achievements_mines4_require_1'].'</span>';
else
	$mines4_req1 = '<span style="color:red">'.$LNG['achievements_mines4_require_1'].'</span>';
if($PLANET['crystal_mine'] >=29)
	$mines4_req2 = '<span style="color:lime">'.$LNG['achievements_mines4_require_2'].'</span>';
else
	$mines4_req2 = '<span style="color:red">'.$LNG['achievements_mines4_require_2'].'</span>';
if($PLANET['deuterium_sintetizer'] >=27)
	$mines4_req3 = '<span style="color:lime">'.$LNG['achievements_mines4_require_3'].'</span>';
else
	$mines4_req3 = '<span style="color:red">'.$LNG['achievements_mines4_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'mines4_req1'    	=> $mines4_req1,
	'mines4_req2'    	=> $mines4_req2,
	'mines4_req3'    	=> $mines4_req3,
	'mines4_title'    => ' <span style="color:red">'.$LNG['achievements_mines4'].'</span>',
	'mines4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_mines4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'mines4_title'    => ' <span style="color:lime">'.$LNG['achievements_mines4'].'</span>',
	'mines4_req1'    => ' <span style="color:lime">'.$LNG['achievements_mines4_done'].'</span>',
	'mines4_req2'    	=> ' <span style="color:lime">'.'</span>',
	'mines4_req3'    	=> ' <span style="color:lime">'.'</span>',
	'mines4_img'    => ' <img border="1" src="styles/achievements/mines4_done.png" align="top" width="80" height="80"></td>',
));
	}

######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_mines'] < 4)
	{
$this->tplObj->assign_vars(array(
	'mines_title'    => ' <span style="color:red">'.$LNG['achievements_mines'].'</span>',
));
	}
	
if($USER['achievements_mines'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'mines_title'    => ' <span style="color:lime">'.$LNG['achievements_mines'].'</span>',
));
	}
	
######################################################################################
######### Tech Achievements Level 1

	if(empty($USER['achievements_tech1'] ))
	{
if($USER['military_tech'] >= 8 && $USER['defence_tech'] >= 9 && $USER['shield_tech'] >=7)
{
	$db->query("UPDATE uni1_users SET `achievements_tech1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_tech` = achievements_tech+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $tech1_darkmatter;
	$PLANET[$resource[901]]	+= $tech1_metal;
    $PLANET[$resource[902]]	+= $tech1_crystal; 
    $PLANET[$resource[903]]	+= $tech1_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_tech1_success']);	
	
}
if($USER['military_tech'] >=8)
	$tech1_req1 = '<span style="color:lime">'.$LNG['achievements_tech1_require_1'].'</span>';
else
	$tech1_req1 = '<span style="color:red">'.$LNG['achievements_tech1_require_1'].'</span>';
if($USER['defence_tech'] >=9)
	$tech1_req2 = '<span style="color:lime">'.$LNG['achievements_tech1_require_2'].'</span>';
else
	$tech1_req2 = '<span style="color:red">'.$LNG['achievements_tech1_require_2'].'</span>';
if($USER['shield_tech'] >=7)
	$tech1_req3 = '<span style="color:lime">'.$LNG['achievements_tech1_require_3'].'</span>';
else
	$tech1_req3 = '<span style="color:red">'.$LNG['achievements_tech1_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'tech1_req1'    	=> $tech1_req1,
	'tech1_req2'    	=> $tech1_req2,
	'tech1_req3'    	=> $tech1_req3,
	'tech1_title'    => ' <span style="color:red">'.$LNG['achievements_tech1'].'</span>',
	'tech1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_tech1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'tech1_title'    => ' <span style="color:lime">'.$LNG['achievements_tech1'].'</span>',
	'tech1_req1'    => ' <span style="color:lime">'.$LNG['achievements_tech1_done'].'</span>',
	'tech1_req2'    	=> ' <span style="color:lime">'.'</span>',
	'tech1_req3'    	=> ' <span style="color:lime">'.'</span>',
	'tech1_img'    => ' <img border="1" src="styles/achievements/tech1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Tech Achievements Level 2

	if(empty($USER['achievements_tech2'] ))
	{
if($USER['military_tech'] >= 13 && $USER['defence_tech'] >= 11 && $USER['shield_tech'] >=12)
{
	$db->query("UPDATE uni1_users SET `achievements_tech2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_tech` = achievements_tech+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $tech2_darkmatter;
	$PLANET[$resource[901]]	+= $tech2_metal;
    $PLANET[$resource[902]]	+= $tech2_crystal; 
    $PLANET[$resource[903]]	+= $tech2_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_tech2_success']);
		
}
if($USER['military_tech'] >=13)
	$tech2_req1 = '<span style="color:lime">'.$LNG['achievements_tech2_require_1'].'</span>';
else
	$tech2_req1 = '<span style="color:red">'.$LNG['achievements_tech2_require_1'].'</span>';
if($USER['defence_tech'] >=11)
	$tech2_req2 = '<span style="color:lime">'.$LNG['achievements_tech2_require_2'].'</span>';
else
	$tech2_req2 = '<span style="color:red">'.$LNG['achievements_tech2_require_2'].'</span>';
if($USER['shield_tech'] >=12)
	$tech2_req3 = '<span style="color:lime">'.$LNG['achievements_tech2_require_3'].'</span>';
else
	$tech2_req3 = '<span style="color:red">'.$LNG['achievements_tech2_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'tech2_req1'    	=> $tech2_req1,
	'tech2_req2'    	=> $tech2_req2,
	'tech2_req3'    	=> $tech2_req3,
	'tech2_title'    => ' <span style="color:red">'.$LNG['achievements_tech2'].'</span>',
	'tech2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_tech2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'tech2_title'    => ' <span style="color:lime">'.$LNG['achievements_tech2'].'</span>',
	'tech2_req1'    => ' <span style="color:lime">'.$LNG['achievements_tech2_done'].'</span>',
	'tech2_req2'    	=> ' <span style="color:lime">'.'</span>',
	'tech2_req3'    	=> ' <span style="color:lime">'.'</span>',
	'tech2_img'    => ' <img border="1" src="styles/achievements/tech2_done.png" align="top" width="80" height="80"></td>',
));
	}

######################################################################################
######### Tech Achievements Level 3

	if(empty($USER['achievements_tech3'] ))
	{
if($USER['military_tech'] >= 16 && $USER['defence_tech'] >= 14 && $USER['shield_tech'] >=14)
{
	$db->query("UPDATE uni1_users SET `achievements_tech3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_tech` = achievements_tech+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $tech3_darkmatter;
	$PLANET[$resource[901]]	+= $tech3_metal;
    $PLANET[$resource[902]]	+= $tech3_crystal; 
    $PLANET[$resource[903]]	+= $tech3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_tech3_success']);
		
}
if($USER['military_tech'] >=16)
	$tech3_req1 = '<span style="color:lime">'.$LNG['achievements_tech3_require_1'].'</span>';
else
	$tech3_req1 = '<span style="color:red">'.$LNG['achievements_tech3_require_1'].'</span>';
if($USER['defence_tech'] >=14)
	$tech3_req2 = '<span style="color:lime">'.$LNG['achievements_tech3_require_2'].'</span>';
else
	$tech3_req2 = '<span style="color:red">'.$LNG['achievements_tech3_require_2'].'</span>';
if($USER['shield_tech'] >=14)
	$tech3_req3 = '<span style="color:lime">'.$LNG['achievements_tech3_require_3'].'</span>';
else
	$tech3_req3 = '<span style="color:red">'.$LNG['achievements_tech3_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'tech3_req1'    	=> $tech3_req1,
	'tech3_req2'    	=> $tech3_req2,
	'tech3_req3'    	=> $tech3_req3,
	'tech3_title'    => ' <span style="color:red">'.$LNG['achievements_tech3'].'</span>',
	'tech3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_tech3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'tech3_title'    => ' <span style="color:lime">'.$LNG['achievements_tech3'].'</span>',
	'tech3_req1'    => ' <span style="color:lime">'.$LNG['achievements_tech3_done'].'</span>',
	'tech3_req2'    	=> ' <span style="color:lime">'.'</span>',
	'tech3_req3'    	=> ' <span style="color:lime">'.'</span>',
	'tech3_img'    => ' <img border="1" src="styles/achievements/tech3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Tech Achievements Level 4

	if(empty($USER['achievements_tech4'] ))
	{
if($USER['military_tech'] >= 20 && $USER['defence_tech'] >= 18 && $USER['shield_tech'] >=17)
{
	$db->query("UPDATE uni1_users SET `achievements_tech4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_tech` = achievements_tech+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $tech4_darkmatter;
	$PLANET[$resource[901]]	+= $tech4_metal;
    $PLANET[$resource[902]]	+= $tech4_crystal; 
    $PLANET[$resource[903]]	+= $tech4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_tech4_success']);
		
}
if($USER['military_tech'] >=20)
	$tech4_req1 = '<span style="color:lime">'.$LNG['achievements_tech4_require_1'].'</span>';
else
	$tech4_req1 = '<span style="color:red">'.$LNG['achievements_tech4_require_1'].'</span>';
if($USER['defence_tech'] >=18)
	$tech4_req2 = '<span style="color:lime">'.$LNG['achievements_tech4_require_2'].'</span>';
else
	$tech4_req2 = '<span style="color:red">'.$LNG['achievements_tech4_require_2'].'</span>';
if($USER['shield_tech'] >=17)
	$tech4_req3 = '<span style="color:lime">'.$LNG['achievements_tech4_require_3'].'</span>';
else
	$tech4_req3 = '<span style="color:red">'.$LNG['achievements_tech4_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'tech4_req1'    	=> $tech4_req1,
	'tech4_req2'    	=> $tech4_req2,
	'tech4_req3'    	=> $tech4_req3,
	'tech4_title'    => ' <span style="color:red">'.$LNG['achievements_tech4'].'</span>',
	'tech4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_tech4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'tech4_title'    => ' <span style="color:lime">'.$LNG['achievements_tech4'].'</span>',
	'tech4_req1'    => ' <span style="color:lime">'.$LNG['achievements_tech4_done'].'</span>',
	'tech4_req2'    	=> ' <span style="color:lime">'.'</span>',
	'tech4_req3'    	=> ' <span style="color:lime">'.'</span>',
	'tech4_img'    => ' <img border="1" src="styles/achievements/tech4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_tech'] < 4)
	{
$this->tplObj->assign_vars(array(
	'tech_title'    => ' <span style="color:red">'.$LNG['achievements_tech'].'</span>',
));
	}
	
if($USER['achievements_tech'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'tech_title'    => ' <span style="color:lime">'.$LNG['achievements_tech'].'</span>',
));
	}
	
######################################################################################
######### Engine Achievements Level 1

	if(empty($USER['achievements_engine1'] ))
	{
if($USER['combustion_tech'] >= 9 && $USER['impulse_motor_tech'] >= 10 && $USER['hyperspace_motor_tech'] >=6)
{
	$db->query("UPDATE uni1_users SET `achievements_engine1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_engine` = achievements_engine+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $engine1_darkmatter;
	$PLANET[$resource[901]]	+= $engine1_metal;
    $PLANET[$resource[902]]	+= $engine1_crystal; 
    $PLANET[$resource[903]]	+= $engine1_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_engine1_success']);
	
}
if($USER['combustion_tech'] >=9)
	$engine1_req1 = '<span style="color:lime">'.$LNG['achievements_engine1_require_1'].'</span>';
else
	$engine1_req1 = '<span style="color:red">'.$LNG['achievements_engine1_require_1'].'</span>';
if($USER['impulse_motor_tech'] >=10)
	$engine1_req2 = '<span style="color:lime">'.$LNG['achievements_engine1_require_2'].'</span>';
else
	$engine1_req2 = '<span style="color:red">'.$LNG['achievements_engine1_require_2'].'</span>';
if($USER['hyperspace_motor_tech'] >=6)
	$engine1_req3 = '<span style="color:lime">'.$LNG['achievements_engine1_require_3'].'</span>';
else
	$engine1_req3 = '<span style="color:red">'.$LNG['achievements_engine1_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'engine1_req1'    	=> $engine1_req1,
	'engine1_req2'    	=> $engine1_req2,
	'engine1_req3'    	=> $engine1_req3,
	'engine1_title'    => ' <span style="color:red">'.$LNG['achievements_engine1'].'</span>',
	'engine1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_engine1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'engine1_title'    => ' <span style="color:lime">'.$LNG['achievements_engine1'].'</span>',
	'engine1_req1'    => ' <span style="color:lime">'.$LNG['achievements_engine1_done'].'</span>',
	'engine1_req2'    	=> ' <span style="color:lime">'.'</span>',
	'engine1_req3'    	=> ' <span style="color:lime">'.'</span>',
	'engine1_img'    => ' <img border="1" src="styles/achievements/engine1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Engine Achievements Level 2

	if(empty($USER['achievements_engine2'] ))
	{
if($USER['combustion_tech'] >= 12 && $USER['impulse_motor_tech'] >= 11 && $USER['hyperspace_motor_tech'] >=9)
{
	$db->query("UPDATE uni1_users SET `achievements_engine2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_engine` = achievements_engine+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $engine2_darkmatter;
	$PLANET[$resource[901]]	+= $engine2_metal;
    $PLANET[$resource[902]]	+= $engine2_crystal; 
    $PLANET[$resource[903]]	+= $engine2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_engine2_success']);
		
}
if($USER['combustion_tech'] >=12)
	$engine2_req1 = '<span style="color:lime">'.$LNG['achievements_engine2_require_1'].'</span>';
else
	$engine2_req1 = '<span style="color:red">'.$LNG['achievements_engine2_require_1'].'</span>';
if($USER['impulse_motor_tech'] >=11)
	$engine2_req2 = '<span style="color:lime">'.$LNG['achievements_engine2_require_2'].'</span>';
else
	$engine2_req2 = '<span style="color:red">'.$LNG['achievements_engine2_require_2'].'</span>';
if($USER['hyperspace_motor_tech'] >=9)
	$engine2_req3 = '<span style="color:lime">'.$LNG['achievements_engine2_require_3'].'</span>';
else
	$engine2_req3 = '<span style="color:red">'.$LNG['achievements_engine2_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'engine2_req1'    	=> $engine2_req1,
	'engine2_req2'    	=> $engine2_req2,
	'engine2_req3'    	=> $engine2_req3,
	'engine2_title'    => ' <span style="color:red">'.$LNG['achievements_engine2'].'</span>',
	'engine2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_engine2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'engine2_title'    => ' <span style="color:lime">'.$LNG['achievements_engine2'].'</span>',
	'engine2_req1'    => ' <span style="color:lime">'.$LNG['achievements_engine2_done'].'</span>',
	'engine2_req2'    	=> ' <span style="color:lime">'.'</span>',
	'engine2_req3'    	=> ' <span style="color:lime">'.'</span>',
	'engine2_img'    => ' <img border="1" src="styles/achievements/engine2_done.png" align="top" width="80" height="80"></td>',
));
	}

######################################################################################
######### Engine Achievements Level 3

	if(empty($USER['achievements_engine3'] ))
	{
if($USER['combustion_tech'] >= 15 && $USER['impulse_motor_tech'] >= 14 && $USER['hyperspace_motor_tech'] >=11)
{
	$db->query("UPDATE uni1_users SET `achievements_engine3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_engine` = achievements_engine+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $engine3_darkmatter;
	$PLANET[$resource[901]]	+= $engine3_metal;
    $PLANET[$resource[902]]	+= $engine3_crystal; 
    $PLANET[$resource[903]]	+= $engine3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_engine3_success']);
		
}
if($USER['combustion_tech'] >=15)
	$engine3_req1 = '<span style="color:lime">'.$LNG['achievements_engine3_require_1'].'</span>';
else
	$engine3_req1 = '<span style="color:red">'.$LNG['achievements_engine3_require_1'].'</span>';
if($USER['impulse_motor_tech'] >=14)
	$engine3_req2 = '<span style="color:lime">'.$LNG['achievements_engine3_require_2'].'</span>';
else
	$engine3_req2 = '<span style="color:red">'.$LNG['achievements_engine3_require_2'].'</span>';
if($USER['hyperspace_motor_tech'] >=11)
	$engine3_req3 = '<span style="color:lime">'.$LNG['achievements_engine3_require_3'].'</span>';
else
	$engine3_req3 = '<span style="color:red">'.$LNG['achievements_engine3_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'engine3_req1'    	=> $engine3_req1,
	'engine3_req2'    	=> $engine3_req2,
	'engine3_req3'    	=> $engine3_req3,
	'engine3_title'    => ' <span style="color:red">'.$LNG['achievements_engine3'].'</span>',
	'engine3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_engine3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'engine3_title'    => ' <span style="color:lime">'.$LNG['achievements_engine3'].'</span>',
	'engine3_req1'    => ' <span style="color:lime">'.$LNG['achievements_engine3_done'].'</span>',
	'engine3_req2'    	=> ' <span style="color:lime">'.'</span>',
	'engine3_req3'    	=> ' <span style="color:lime">'.'</span>',
	'engine3_img'    => ' <img border="1" src="styles/achievements/engine3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Engine Achievements Level 4

	if(empty($USER['achievements_engine4'] ))
	{
if($USER['combustion_tech'] >= 20 && $USER['impulse_motor_tech'] >= 18 && $USER['hyperspace_motor_tech'] >=17)
{
	$db->query("UPDATE uni1_users SET `achievements_engine4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_engine` = achievements_engine+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $engine4_darkmatter;
	$PLANET[$resource[901]]	+= $engine4_metal;
    $PLANET[$resource[902]]	+= $engine4_crystal; 
    $PLANET[$resource[903]]	+= $engine4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_engine4_success']);
		
}
if($USER['combustion_tech'] >=20)
	$engine4_req1 = '<span style="color:lime">'.$LNG['achievements_engine4_require_1'].'</span>';
else
	$engine4_req1 = '<span style="color:red">'.$LNG['achievements_engine4_require_1'].'</span>';
if($USER['impulse_motor_tech'] >=18)
	$engine4_req2 = '<span style="color:lime">'.$LNG['achievements_engine4_require_2'].'</span>';
else
	$engine4_req2 = '<span style="color:red">'.$LNG['achievements_engine4_require_2'].'</span>';
if($USER['hyperspace_motor_tech'] >=17)
	$engine4_req3 = '<span style="color:lime">'.$LNG['achievements_engine4_require_3'].'</span>';
else
	$engine4_req3 = '<span style="color:red">'.$LNG['achievements_engine4_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'engine4_req1'    	=> $engine4_req1,
	'engine4_req2'    	=> $engine4_req2,
	'engine4_req3'    	=> $engine4_req3,
	'engine4_title'    => ' <span style="color:red">'.$LNG['achievements_engine4'].'</span>',
	'engine4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_engine4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'engine4_title'    => ' <span style="color:lime">'.$LNG['achievements_engine4'].'</span>',
	'engine4_req1'    => ' <span style="color:lime">'.$LNG['achievements_engine4_done'].'</span>',
	'engine4_req2'    	=> ' <span style="color:lime">'.'</span>',
	'engine4_req3'    	=> ' <span style="color:lime">'.'</span>',
	'engine4_img'    => ' <img border="1" src="styles/achievements/engine4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_engine'] < 4)
	{
$this->tplObj->assign_vars(array(
	'engine_title'    => ' <span style="color:red">'.$LNG['achievements_engine'].'</span>',
));
	}
	
if($USER['achievements_engine'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'engine_title'    => ' <span style="color:lime">'.$LNG['achievements_engine'].'</span>',
));
	}
	
######################################################################################
######### Colonization Achievements Level 1

	if(empty($USER['achievements_colonization1'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS planet_count FROM uni1_planets WHERE `planet_type` = '". 1 ."' AND `destruyed` = '0' AND `id_owner` = '". $USER['id'] ."';");
	$metal_count = $query['planet_count'];
	if($metal_count >=2)
		{
	$db->query("UPDATE uni1_users SET `achievements_colonization1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_colonization` = achievements_colonization+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $colonization1_darkmatter;
	$PLANET[$resource[901]]	+= $colonisation1_metal;
    $PLANET[$resource[902]]	+= $colonisation1_crystal; 
    $PLANET[$resource[903]]	+= $colonisation1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_colonization1_success']);	
	
}
if($metal_count >=2)
	$colonization1_req1 = '<span style="color:lime">'.$LNG['achievements_colonization1_require_1'].'</span>';
else
	$colonization1_req1 = '<span style="color:red">'.$LNG['achievements_colonization1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'colonization1_req1'    	=> $colonization1_req1,
	'colonization1_title'    => ' <span style="color:red">'.$LNG['achievements_colonization1'].'</span>',
	'colonization1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_colonization1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'colonization1_title'    => ' <span style="color:lime">'.$LNG['achievements_colonization1'].'</span>',
	'colonization1_req1'    => ' <span style="color:lime">'.$LNG['achievements_colonization1_done'].'</span>',
	'colonization1_img'    => ' <img border="1" src="styles/achievements/colonization1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Colonization Achievements Level 2

	if(empty($USER['achievements_colonization2'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS planet_count FROM uni1_planets WHERE `planet_type` = '". 1 ."' AND `destruyed` = '0' AND `id_owner` = '". $USER['id'] ."';");
	$metal_count = $query['planet_count'];
	if($metal_count >=5)
		{
	$db->query("UPDATE uni1_users SET `achievements_colonization2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_colonization` = achievements_colonization+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $colonization2_darkmatter;
	$PLANET[$resource[901]]	+= $colonisation2_metal;
    $PLANET[$resource[902]]	+= $colonisation2_crystal; 
    $PLANET[$resource[903]]	+= $colonisation2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_colonization2_success']);	
		
}
if($metal_count >=5)
	$colonization2_req1 = '<span style="color:lime">'.$LNG['achievements_colonization2_require_1'].'</span>';
else
	$colonization2_req1 = '<span style="color:red">'.$LNG['achievements_colonization2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'colonization2_req1'    	=> $colonization2_req1,
	'colonization2_title'    => ' <span style="color:red">'.$LNG['achievements_colonization2'].'</span>',
	'colonization2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_colonization2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'colonization2_title'    => ' <span style="color:lime">'.$LNG['achievements_colonization2'].'</span>',
	'colonization2_req1'    => ' <span style="color:lime">'.$LNG['achievements_colonization2_done'].'</span>',
	'colonization2_img'    => ' <img border="1" src="styles/achievements/colonization2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Colonization Achievements Level 3

	if(empty($USER['achievements_colonization3'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS planet_count FROM uni1_planets WHERE `planet_type` = '". 1 ."' AND `destruyed` = '0' AND `id_owner` = '". $USER['id'] ."';");
	$metal_count = $query['planet_count'];
	if($metal_count >=11)
		{
	$db->query("UPDATE uni1_users SET `achievements_colonization3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_colonization` = achievements_colonization+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $colonization3_darkmatter;
	$PLANET[$resource[901]]	+= $colonisation3_metal;
    $PLANET[$resource[902]]	+= $colonisation3_crystal; 
    $PLANET[$resource[903]]	+= $colonisation3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_colonization3_success']);	
		
}
if	($metal_count >= 15)						//($metal_count == $CONF['planets_tech'])
	$colonization3_req1 = '<span style="color:lime">'.$LNG['achievements_colonization3_require_1'].'</span>';
else
	$colonization3_req1 = '<span style="color:red">'.$LNG['achievements_colonization3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'colonization3_req1'    	=> $colonization3_req1,
	'colonization3_title'    => ' <span style="color:red">'.$LNG['achievements_colonization3'].'</span>',
	'colonization3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_colonization3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'colonization3_title'    => ' <span style="color:lime">'.$LNG['achievements_colonization3'].'</span>',
	'colonization3_req1'    => ' <span style="color:lime">'.$LNG['achievements_colonization3_done'].'</span>',
	'colonization3_img'    => ' <img border="1" src="styles/achievements/colonization3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_colonization'] < 3)
	{
$this->tplObj->assign_vars(array(
	'colonization_title'    => ' <span style="color:red">'.$LNG['achievements_colonization'].'</span>',
));
	}
	
if($USER['achievements_colonization'] >= 3)
	{
$this->tplObj->assign_vars(array(
	'colonization_title'    => ' <span style="color:lime">'.$LNG['achievements_colonization'].'</span>',
));
	}
	
######################################################################################
######### Moon Achievements Level 1

	if(empty($USER['achievements_moon1'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS moon_count FROM uni1_planets WHERE `planet_type` = '". 3 ."' AND `id_owner` = '". $USER['id'] ."';");
	$moon_count = $query['moon_count'];
	if($moon_count >=1)
		{
	$db->query("UPDATE uni1_users SET `achievements_moon1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_moon` = achievements_moon+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $moon1_darkmatter;
	$PLANET[$resource[901]]	+= $moon1_metal;
    $PLANET[$resource[902]]	+= $moon1_crystal; 
    $PLANET[$resource[903]]	+= $moon1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_moon1_success']);	
	
}
if($moon_count >=1)
	$moon1_req1 = '<span style="color:lime">'.$LNG['achievements_moon1_require_1'].'</span>';
else
	$moon1_req1 = '<span style="color:red">'.$LNG['achievements_moon1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'moon1_req1'    	=> $moon1_req1,
	'moon1_title'    => ' <span style="color:red">'.$LNG['achievements_moon1'].'</span>',
	'moon1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_moon1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'moon1_title'    => ' <span style="color:lime">'.$LNG['achievements_moon1'].'</span>',
	'moon1_req1'    => ' <span style="color:lime">'.$LNG['achievements_moon1_done'].'</span>',
	'moon1_img'    => ' <img border="1" src="styles/achievements/moon1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Moon Achievements Level 2

	if(empty($USER['achievements_moon2'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS moon_count FROM uni1_planets WHERE `planet_type` = '". 3 ."' AND `id_owner` = '". $USER['id'] ."';");
	$moon_count = $query['moon_count'];
	if($moon_count >=5)
		{
	$db->query("UPDATE uni1_users SET `achievements_moon2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_moon` = achievements_moon+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $moon2_darkmatter;
	$PLANET[$resource[901]]	+= $moon2_metal;
    $PLANET[$resource[902]]	+= $moon2_crystal; 
    $PLANET[$resource[903]]	+= $moon2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_moon2_success']);	
		
}
if($moon_count >=5)
	$moon2_req1 = '<span style="color:lime">'.$LNG['achievements_moon2_require_1'].'</span>';
else
	$moon2_req1 = '<span style="color:red">'.$LNG['achievements_moon2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'moon2_req1'    	=> $moon2_req1,
	'moon2_title'    => ' <span style="color:red">'.$LNG['achievements_moon2'].'</span>',
	'moon2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_moon2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'moon2_title'    => ' <span style="color:lime">'.$LNG['achievements_moon2'].'</span>',
	'moon2_req1'    => ' <span style="color:lime">'.$LNG['achievements_moon2_done'].'</span>',
	'moon2_img'    => ' <img border="1" src="styles/achievements/moon2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Moon Achievements Level 3

	if(empty($USER['achievements_moon3'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS moon_count FROM uni1_planets WHERE `planet_type` = '". 3 ."' AND `id_owner` = '". $USER['id'] ."';");
	$moon_count = $query['moon_count'];
	if	($moon_count >= 11)						//($moon_count == $CONF['planets_tech'])
		{
	$db->query("UPDATE uni1_users SET `achievements_moon3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_moon` = achievements_moon+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $moon3_darkmatter;
	$PLANET[$resource[901]]	+= $moon3_metal;
    $PLANET[$resource[902]]	+= $moon3_crystal; 
    $PLANET[$resource[903]]	+= $moon3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_moon3_success']);	
		
}
if($moon_count >= 11)	
	$moon3_req1 = '<span style="color:lime">'.$LNG['achievements_moon3_require_1'].'</span>';
else
	$moon3_req1 = '<span style="color:red">'.$LNG['achievements_moon3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'moon3_req1'    	=> $moon3_req1,
	'moon3_title'    => ' <span style="color:red">'.$LNG['achievements_moon3'].'</span>',
	'moon3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_moon3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'moon3_title'    => ' <span style="color:lime">'.$LNG['achievements_moon3'].'</span>',
	'moon3_req1'    => ' <span style="color:lime">'.$LNG['achievements_moon3_done'].'</span>',
	'moon3_img'    => ' <img border="1" src="styles/achievements/moon3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_moon'] < 3)
	{
$this->tplObj->assign_vars(array(
	'moon_title'    => ' <span style="color:red">'.$LNG['achievements_moon'].'</span>',
));
	}
	
if($USER['achievements_moon'] >= 3)
	{
$this->tplObj->assign_vars(array(
	'moon_title'    => ' <span style="color:lime">'.$LNG['achievements_moon'].'</span>',
));
	}
	
######################################################################################
######### War Achievements Level 1

	if(empty($USER['achievements_war1'] ))
	{
	if($USER['wons'] >= 50)
		{
	$db->query("UPDATE uni1_users SET `achievements_war1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_war` = achievements_war+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $war1_darkmatter;
	$PLANET[$resource[901]]	+= $war1_metal;
    $PLANET[$resource[902]]	+= $war1_crystal; 
    $PLANET[$resource[903]]	+= $war1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_war1_success']);	
	
}
	if($USER['wons'] >= 50)
	$war1_req1 = '<span style="color:lime">'.$LNG['achievements_war1_require_1'].'</span>';
else
	$war1_req1 = '<span style="color:red">'.$LNG['achievements_war1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'war1_req1'    	=> $war1_req1,
	'war1_title'    => ' <span style="color:red">'.$LNG['achievements_war1'].'</span>',
	'war1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_war1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'war1_title'    => ' <span style="color:lime">'.$LNG['achievements_war1'].'</span>',
	'war1_req1'    => ' <span style="color:lime">'.$LNG['achievements_war1_done'].'</span>',
	'war1_img'    => ' <img border="1" src="styles/achievements/war1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### War Achievements Level 2

	if(empty($USER['achievements_war2'] ))
	{
	if($USER['wons'] >= 500)
		{
	$db->query("UPDATE uni1_users SET `achievements_war2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_war` = achievements_war+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $war2_darkmatter;
	$PLANET[$resource[901]]	+= $war2_metal;
    $PLANET[$resource[902]]	+= $war2_crystal; 
    $PLANET[$resource[903]]	+= $war2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_war2_success']);	
		
}
if($USER['wons'] >= 500)
	$war2_req1 = '<span style="color:lime">'.$LNG['achievements_war2_require_1'].'</span>';
else
	$war2_req1 = '<span style="color:red">'.$LNG['achievements_war2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'war2_req1'    	=> $war2_req1,
	'war2_title'    => ' <span style="color:red">'.$LNG['achievements_war2'].'</span>',
	'war2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_war2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'war2_title'    => ' <span style="color:lime">'.$LNG['achievements_war2'].'</span>',
	'war2_req1'    => ' <span style="color:lime">'.$LNG['achievements_war2_done'].'</span>',
	'war2_img'    => ' <img border="1" src="styles/achievements/war2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### War Achievements Level 3

	if(empty($USER['achievements_war3'] ))
	{
	if($USER['wons'] >= 5000)
		{
	$db->query("UPDATE uni1_users SET `achievements_war3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_war` = achievements_war+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $war3_darkmatter;
	$PLANET[$resource[901]]	+= $war3_metal;
    $PLANET[$resource[902]]	+= $war3_crystal; 
    $PLANET[$resource[903]]	+= $war3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_war3_success']);	
		
}
if($USER['wons'] >= 5000)
	$war3_req1 = '<span style="color:lime">'.$LNG['achievements_war3_require_1'].'</span>';
else
	$war3_req1 = '<span style="color:red">'.$LNG['achievements_war3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'war3_req1'    	=> $war3_req1,
	'war3_title'    => ' <span style="color:red">'.$LNG['achievements_war3'].'</span>',
	'war3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_war3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'war3_title'    => ' <span style="color:lime">'.$LNG['achievements_war3'].'</span>',
	'war3_req1'    => ' <span style="color:lime">'.$LNG['achievements_war3_done'].'</span>',
	'war3_img'    => ' <img border="1" src="styles/achievements/war3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_war'] < 3)
	{
$this->tplObj->assign_vars(array(
	'war_title'    => ' <span style="color:red">'.$LNG['achievements_war'].'</span>',
));
	}
	
if($USER['achievements_war'] >= 3)
	{
$this->tplObj->assign_vars(array(
	'war_title'    => ' <span style="color:lime">'.$LNG['achievements_war'].'</span>',
));
	}
	
######################################################################################
######### Destroy Achievements Level 1

	if(empty($USER['achievements_destroy1'] ))
	{
	if($USER['kbmetal'] >= 100000000 && $USER['kbcrystal'] >= 100000000)
		{
	$db->query("UPDATE uni1_users SET `achievements_destroy1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_destroy` = achievements_destroy+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $destroy1_darkmatter;
	$PLANET[$resource[901]]	+= $destroy1_metal;
    $PLANET[$resource[902]]	+= $destroy1_crystal; 
    $PLANET[$resource[903]]	+= $destroy1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_destroy1_success']);	
	
}
	if($USER['kbmetal'] >= 100000000 && $USER['kbcrystal'] >= 100000000)
	$destroy1_req1 = '<span style="color:lime">'.$LNG['achievements_destroy1_require_1'].'</span>';
else
	$destroy1_req1 = '<span style="color:red">'.$LNG['achievements_destroy1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'destroy1_req1'    	=> $destroy1_req1,
	'destroy1_title'    => ' <span style="color:red">'.$LNG['achievements_destroy1'].'</span>',
	'destroy1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_destroy1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'destroy1_title'    => ' <span style="color:lime">'.$LNG['achievements_destroy1'].'</span>',
	'destroy1_req1'    => ' <span style="color:lime">'.$LNG['achievements_destroy1_done'].'</span>',
	'destroy1_img'    => ' <img border="1" src="styles/achievements/destroy1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Destroy Achievements Level 2

	if(empty($USER['achievements_destroy2'] ))
	{
	if($USER['kbmetal'] >= 1000000000 && $USER['kbcrystal'] >= 1000000000)
		{
	$db->query("UPDATE uni1_users SET `achievements_destroy2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_destroy` = achievements_destroy+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $destroy2_darkmatter;
	$PLANET[$resource[901]]	+= $destroy2_metal;
    $PLANET[$resource[902]]	+= $destroy2_crystal; 
    $PLANET[$resource[903]]	+= $destroy2_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_destroy2_success']);	
		
}
	if($USER['kbmetal'] >= 1000000000 && $USER['kbcrystal'] >= 1000000000)
	$destroy2_req1 = '<span style="color:lime">'.$LNG['achievements_destroy2_require_1'].'</span>';
else
	$destroy2_req1 = '<span style="color:red">'.$LNG['achievements_destroy2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'destroy2_req1'    	=> $destroy2_req1,
	'destroy2_title'    => ' <span style="color:red">'.$LNG['achievements_destroy2'].'</span>',
	'destroy2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_destroy2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'destroy2_title'    => ' <span style="color:lime">'.$LNG['achievements_destroy2'].'</span>',
	'destroy2_req1'    => ' <span style="color:lime">'.$LNG['achievements_destroy2_done'].'</span>',
	'destroy2_img'    => ' <img border="1" src="styles/achievements/destroy2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Destroy Achievements Level 3

	if(empty($USER['achievements_destroy3'] ))
	{
	if($USER['kbmetal'] >= 10000000000 && $USER['kbcrystal'] >= 10000000000)
		{
	$db->query("UPDATE uni1_users SET `achievements_destroy3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_destroy` = achievements_destroy+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $destroy3_darkmatter;
	$PLANET[$resource[901]]	+= $destroy3_metal;
    $PLANET[$resource[902]]	+= $destroy3_crystal; 
    $PLANET[$resource[903]]	+= $destroy3_deuterium; 	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_destroy3_success']);	
		
}
	if($USER['kbmetal'] >= 10000000000 && $USER['kbcrystal'] >= 10000000000)
	$destroy3_req1 = '<span style="color:lime">'.$LNG['achievements_destroy3_require_1'].'</span>';
else
	$destroy3_req1 = '<span style="color:red">'.$LNG['achievements_destroy3_require_1'].'</span>';

$this->tplObj->assign_vars(array(
	'destroy3_req1'    	=> $destroy3_req1,
	'destroy3_title'    => ' <span style="color:red">'.$LNG['achievements_destroy3'].'</span>',
	'destroy3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_destroy3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'destroy3_title'    => ' <span style="color:lime">'.$LNG['achievements_destroy3'].'</span>',
	'destroy3_req1'    => ' <span style="color:lime">'.$LNG['achievements_destroy3_done'].'</span>',
	'destroy3_img'    => ' <img border="1" src="styles/achievements/destroy3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Destroy Achievements Level 4

	if(empty($USER['achievements_destroy4'] ))
	{
	if($USER['kbmetal'] >= 100000000000 && $USER['kbcrystal'] >= 100000000000)
		{
	$db->query("UPDATE uni1_users SET `achievements_destroy4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_destroy` = achievements_destroy+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $destroy4_darkmatter;
	$PLANET[$resource[901]]	+= $destroy4_metal;
    $PLANET[$resource[902]]	+= $destroy4_crystal; 
    $PLANET[$resource[903]]	+= $destroy4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_destroy4_success']);	
		
}
	if($USER['kbmetal'] >= 100000000000 && $USER['kbcrystal'] >= 100000000000)
	$destroy4_req1 = '<span style="color:lime">'.$LNG['achievements_destroy4_require_1'].'</span>';
else
	$destroy4_req1 = '<span style="color:red">'.$LNG['achievements_destroy4_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'destroy4_req1'    	=> $destroy4_req1,
	'destroy4_title'    => ' <span style="color:red">'.$LNG['achievements_destroy4'].'</span>',
	'destroy4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_destroy4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'destroy4_title'    => ' <span style="color:lime">'.$LNG['achievements_destroy4'].'</span>',
	'destroy4_req1'    => ' <span style="color:lime">'.$LNG['achievements_destroy4_done'].'</span>',
	'destroy4_img'    => ' <img border="1" src="styles/achievements/destroy4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_destroy'] < 4)
	{
$this->tplObj->assign_vars(array(
	'destroy_title'    => ' <span style="color:red">'.$LNG['achievements_destroy'].'</span>',
));
	}
	
if($USER['achievements_destroy'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'destroy_title'    => ' <span style="color:lime">'.$LNG['achievements_destroy'].'</span>',
));
	}
	
######################################################################################
######### Time Achievements Level 1

	if(empty($USER['achievements_time1'] ))
	{
//	if($PLANET['b_hangar_time'] > 86400)//Ancien, inutilisable car b_hangar_time n'est jamais mis à jour
	if($USER['achievements_time2'] == 1 && $USER['achievements_time3'] == 1)
		{
	$db->query("UPDATE uni1_users SET `achievements_time1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_time` = achievements_time+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $time1_darkmatter;
	$PLANET[$resource[901]]	+= $time1_metal;
    $PLANET[$resource[902]]	+= $time1_crystal; 
    $PLANET[$resource[903]]	+= $time1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_time1_success']);	
	
}
	if($USER['achievements_time2'] == 1 && $USER['achievements_time3'] == 1)
	$time1_req1 = '<span style="color:lime">'.$LNG['achievements_time1_require_1'].'</span>';
else
	$time1_req1 = '<span style="color:red">'.$LNG['achievements_time1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'time1_req1'    	=> $time1_req1,
	'time1_title'    => ' <span style="color:red">'.$LNG['achievements_time1'].'</span>',
	'time1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_time1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'time1_title'    => ' <span style="color:lime">'.$LNG['achievements_time1'].'</span>',
	'time1_req1'    => ' <span style="color:lime">'.$LNG['achievements_time1_done'].'</span>',
	'time1_img'    => ' <img border="1" src="styles/achievements/time1_done.png" align="top" width="80" height="80"></td>',
	
));
	}
	
######################################################################################
######### Time Achievements Level 2

	if(empty($USER['achievements_time2'] ))
	{
	if(($PLANET['b_building'] - 43200) > TIMESTAMP)
		{
	$db->query("UPDATE uni1_users SET `achievements_time2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_time` = achievements_time+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $time2_darkmatter;
	$PLANET[$resource[901]]	+= $time2_metal;
    $PLANET[$resource[902]]	+= $time2_crystal; 
    $PLANET[$resource[903]]	+= $time2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_time2_success']);
		
}
	if(($PLANET['b_building'] - 43200) > TIMESTAMP)
	$time2_req1 = '<span style="color:lime">'.$LNG['achievements_time2_require_1'].'</span>';
else
	$time2_req1 = '<span style="color:red">'.$LNG['achievements_time2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'time2_req1'    	=> $time2_req1,
	'time2_title'    => ' <span style="color:red">'.$LNG['achievements_time2'].'</span>',
	'time2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_time2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'time2_title'    => ' <span style="color:lime">'.$LNG['achievements_time2'].'</span>',
	'time2_req1'    => ' <span style="color:lime">'.$LNG['achievements_time2_done'].'</span>',
	'time2_img'    => ' <img border="1" src="styles/achievements/time2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Time Achievements Level 3

	if(empty($USER['achievements_time3'] ))
	{
	if(($USER['b_tech'] - 86400) > TIMESTAMP)
		{
	$db->query("UPDATE uni1_users SET `achievements_time3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_time` = achievements_time+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $time3_darkmatter;
	$PLANET[$resource[901]]	+= $time3_metal;
    $PLANET[$resource[902]]	+= $time3_crystal; 
    $PLANET[$resource[903]]	+= $time3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_time3_success']);
		
}
	if(($USER['b_tech'] - 86400) > TIMESTAMP)
	$time3_req1 = '<span style="color:lime">'.$LNG['achievements_time3_require_1'].'</span>';
else
	$time3_req1 = '<span style="color:red">'.$LNG['achievements_time3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'time3_req1'    	=> $time3_req1,
	'time3_title'    => ' <span style="color:red">'.$LNG['achievements_time3'].'</span>',
	'time3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_time3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'time3_title'    => ' <span style="color:lime">'.$LNG['achievements_time3'].'</span>',
	'time3_req1'    => ' <span style="color:lime">'.$LNG['achievements_time3_done'].'</span>',
	'time3_img'    => ' <img border="1" src="styles/achievements/time3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_time'] < 3)
	{
$this->tplObj->assign_vars(array(
	'time_title'    => ' <span style="color:red">'.$LNG['achievements_time'].'</span>',
));
	}
	
if($USER['achievements_time'] >= 3)
	{
$this->tplObj->assign_vars(array(
	'time_title'    => ' <span style="color:lime">'.$LNG['achievements_time'].'</span>',
));
	}
	
######################################################################################
######### Storage Achievements Level 1

//Storage Level 1
	if(empty($USER['achievements_storage1'] ))
	{
		if($PLANET['metal_store'] >= 8 && $PLANET['crystal_store'] >= 8 && $PLANET['deuterium_store'] >=7)
		{

			$db->query("UPDATE uni1_users SET `achievements_storage1` = 1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements_storage` = achievements_storage+1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $storage1_darkmatter;
	$PLANET[$resource[901]]	+= $storage1_metal;
    $PLANET[$resource[902]]	+= $storage1_crystal; 
    $PLANET[$resource[903]]	+= $storage1_deuterium; 	
			echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
			$this->printMessage($LNG['achievements_storage1_success']);	
			
}			

if($PLANET['metal_store'] >=8)
	$store1_req1 = '<span style="color:lime">'.$LNG['achievements_storage1_require_1'].'</span>';
else
	$store1_req1 = '<span style="color:red">'.$LNG['achievements_storage1_require_1'].'</span>';
if($PLANET['crystal_store'] >=8)
	$store1_req2 = '<span style="color:lime">'.$LNG['achievements_storage1_require_2'].'</span>';
else
	$store1_req2 = '<span style="color:red">'.$LNG['achievements_storage1_require_2'].'</span>';
if($PLANET['deuterium_store'] >=7)
	$store1_req3 = '<span style="color:lime">'.$LNG['achievements_storage1_require_3'].'</span>';
else
	$store1_req3 = '<span style="color:red">'.$LNG['achievements_storage1_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'store1_req1'    	=> $store1_req1,
	'store1_req2'    	=> $store1_req2,
	'store1_req3'    	=> $store1_req3,
	'store1_title'    => ' <span style="color:red">'.$LNG['achievements_storage1'].'</span>',
	'store1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
	if($USER['achievements_storage1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'store1_title'    => ' <span style="color:lime">'.$LNG['achievements_storage1'].'</span>',
	'store1_req1'    => ' <span style="color:lime">'.$LNG['achievements_storage1_done'].'</span>',
	'store1_req2'    	=> ' <span style="color:lime">'.'</span>',
	'store1_req3'    	=> ' <span style="color:lime">'.'</span>',
	'store1_img'    => ' <img border="1" src="styles/achievements/storage1_done.png" align="top" width="80" height="80"></td>',
));
	}

	
######################################################################################
######### Storage Achievements Level 2

//Storage Level 2
	if(empty($USER['achievements_storage2'] ))
	{
		if($PLANET['metal_store'] >= 12 && $PLANET['crystal_store'] >= 11 && $PLANET['deuterium_store'] >=12)
		{

			$db->query("UPDATE uni1_users SET `achievements_storage2` = 1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements_storage` = achievements_storage+1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $storage2_darkmatter;
	$PLANET[$resource[901]]	+= $storage2_metal;
    $PLANET[$resource[902]]	+= $storage2_crystal; 
    $PLANET[$resource[903]]	+= $storage2_deuterium; 				
			echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
			$this->printMessage($LNG['achievements_storage2_success']);	
			
}			

if($PLANET['metal_store'] >=12)
	$store2_req1 = '<span style="color:lime">'.$LNG['achievements_storage2_require_1'].'</span>';
else
	$store2_req1 = '<span style="color:red">'.$LNG['achievements_storage2_require_1'].'</span>';
if($PLANET['crystal_store'] >=11)
	$store2_req2 = '<span style="color:lime">'.$LNG['achievements_storage2_require_2'].'</span>';
else
	$store2_req2 = '<span style="color:red">'.$LNG['achievements_storage2_require_2'].'</span>';
if($PLANET['deuterium_store'] >=12)
	$store2_req3 = '<span style="color:lime">'.$LNG['achievements_storage2_require_3'].'</span>';
else
	$store2_req3 = '<span style="color:red">'.$LNG['achievements_storage2_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'store2_req1'    	=> $store2_req1,
	'store2_req2'    	=> $store2_req2,
	'store2_req3'    	=> $store2_req3,
	'store2_title'    => ' <span style="color:red">'.$LNG['achievements_storage2'].'</span>',
	'store2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
	if($USER['achievements_storage2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'store2_title'    => ' <span style="color:lime">'.$LNG['achievements_storage2'].'</span>',
	'store2_req1'    => ' <span style="color:lime">'.$LNG['achievements_storage2_done'].'</span>',
	'store2_req2'    	=> ' <span style="color:lime">'.'</span>',
	'store2_req3'    	=> ' <span style="color:lime">'.'</span>',
	'store2_img'    => ' <img border="1" src="styles/achievements/storage2_done.png" align="top" width="80" height="80"></td>',
));
	}
######################################################################################
######### Storage Achievements Level 3

//Storage Level 3
	if(empty($USER['achievements_storage3'] ))
	{
		if($PLANET['metal_store'] >= 14 && $PLANET['crystal_store'] >= 15 && $PLANET['deuterium_store'] >=16)
		{

			$db->query("UPDATE uni1_users SET `achievements_storage3` = 1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements_storage` = achievements_storage+1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $storage3_darkmatter;
	$PLANET[$resource[901]]	+= $storage3_metal;
    $PLANET[$resource[902]]	+= $storage3_crystal; 
    $PLANET[$resource[903]]	+= $storage3_deuterium; 	
			echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
			$this->printMessage($LNG['achievements_storage3_success']);	
			
}			

if($PLANET['metal_store'] >=14)
	$store3_req1 = '<span style="color:lime">'.$LNG['achievements_storage3_require_1'].'</span>';
else
	$store3_req1 = '<span style="color:red">'.$LNG['achievements_storage3_require_1'].'</span>';
if($PLANET['crystal_store'] >=15)
	$store3_req2 = '<span style="color:lime">'.$LNG['achievements_storage3_require_2'].'</span>';
else
	$store3_req2 = '<span style="color:red">'.$LNG['achievements_storage3_require_2'].'</span>';
if($PLANET['deuterium_store'] >=16)
	$store3_req3 = '<span style="color:lime">'.$LNG['achievements_storage3_require_3'].'</span>';
else
	$store3_req3 = '<span style="color:red">'.$LNG['achievements_storage3_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'store3_req1'    	=> $store3_req1,
	'store3_req2'    	=> $store3_req2,
	'store3_req3'    	=> $store3_req3,
	'store3_title'    => ' <span style="color:red">'.$LNG['achievements_storage3'].'</span>',
	'store3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
	if($USER['achievements_storage3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'store3_title'    => ' <span style="color:lime">'.$LNG['achievements_storage3'].'</span>',
	'store3_req1'    => ' <span style="color:lime">'.$LNG['achievements_storage3_done'].'</span>',
	'store3_req2'    	=> ' <span style="color:lime">'.'</span>',
	'store3_req3'    	=> ' <span style="color:lime">'.'</span>',
	'store3_img'    => ' <img border="1" src="styles/achievements/storage3_done.png" align="top" width="80" height="80"></td>',
));
	}
######################################################################################
######### Storage Achievements Level 4

//Storage Level 4
	if(empty($USER['achievements_storage4'] ))
	{
		if($PLANET['metal_store'] >= 18 && $PLANET['crystal_store'] >= 19 && $PLANET['deuterium_store'] >=20)
		{

			$db->query("UPDATE uni1_users SET `achievements_storage4` = 1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements_storage` = achievements_storage+1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $storage4_darkmatter;
	$PLANET[$resource[901]]	+= $storage4_metal;
    $PLANET[$resource[902]]	+= $storage4_crystal; 
    $PLANET[$resource[903]]	+= $storage4_deuterium; 		
			echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
			$this->printMessage($LNG['achievements_storage4_success']);	
			
}			

if($PLANET['metal_store'] >=18)
	$store4_req1 = '<span style="color:lime">'.$LNG['achievements_storage4_require_1'].'</span>';
else
	$store4_req1 = '<span style="color:red">'.$LNG['achievements_storage4_require_1'].'</span>';
if($PLANET['crystal_store'] >=19)
	$store4_req2 = '<span style="color:lime">'.$LNG['achievements_storage4_require_2'].'</span>';
else
	$store4_req2 = '<span style="color:red">'.$LNG['achievements_storage4_require_2'].'</span>';
if($PLANET['deuterium_store'] >=20)
	$store4_req3 = '<span style="color:lime">'.$LNG['achievements_storage4_require_3'].'</span>';
else
	$store4_req3 = '<span style="color:red">'.$LNG['achievements_storage4_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'store4_req1'    	=> $store4_req1,
	'store4_req2'    	=> $store4_req2,
	'store4_req3'    	=> $store4_req3,
	'store4_title'    => ' <span style="color:red">'.$LNG['achievements_storage4'].'</span>',
	'store4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
	if($USER['achievements_storage4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'store4_title'    => ' <span style="color:lime">'.$LNG['achievements_storage4'].'</span>',
	'store4_req1'    => ' <span style="color:lime">'.$LNG['achievements_storage4_done'].'</span>',
	'store4_req2'    	=> ' <span style="color:lime">'.'</span>',
	'store4_req3'    	=> ' <span style="color:lime">'.'</span>',
	'store4_img'    => ' <img border="1" src="styles/achievements/storage4_done.png" align="top" width="80" height="80"></td>',
));
	}

	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_storage'] < 4)
	{
$this->tplObj->assign_vars(array(
	'store_title'    => ' <span style="color:red">'.$LNG['achievements_storage'].'</span>',
));
	}
	
if($USER['achievements_storage'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'store_title'    => ' <span style="color:lime">'.$LNG['achievements_storage'].'</span>',
));
	}
	
	
	
	
######################################################################################
######### Community Achievements Level 1

	if(empty($USER['achievements_community1'] ))
	{
	$query = $db->selectsingle("SELECT * FROM uni1_ticket WHERE `ownerID` = '".$USER['id']."';");
	$ticket = $query;
	$db->query("UPDATE uni1_users SET `post_mail` = `post_mail` + 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_community1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_community` = achievements_community+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");	
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_community1_success']);
	if($ticket >=1)
	$community1_req1 = '<span style="color:lime">'.$LNG['achievements_community1_require_1'].'</span>';
else
	$community1_req1 = '<span style="color:red">'.$LNG['achievements_community1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'community1_req1'    	=> $community1_req1,
	'community1_title'    => ' <span style="color:red">'.$LNG['achievements_community1'].'</span>',
	'community1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_community1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'community1_title'    => ' <span style="color:lime">'.$LNG['achievements_community1'].'</span>',
	'community1_req1'    => ' <span style="color:lime">'.$LNG['achievements_community1_done'].'</span>',
	'community1_img'    => ' <img border="1" src="styles/achievements/community1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Community Achievements Level 2

	if(empty($USER['achievements_community2'] ))
	{
	if($USER['ally_id'] >=1)
		{
	$db->query("UPDATE uni1_users SET `achievements_community2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_community` = achievements_community+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $community2_darkmatter;
	$PLANET[$resource[901]]	+= $community2_metal;
    $PLANET[$resource[902]]	+= $community2_crystal; 
    $PLANET[$resource[903]]	+= $community2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_community2_success']);
		
}
	if($USER['ally_id'] >=1)
	$community2_req1 = '<span style="color:lime">'.$LNG['achievements_community2_require_1'].'</span>';
else
	$community2_req1 = '<span style="color:red">'.$LNG['achievements_community2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'community2_req1'    	=> $community2_req1,
	'community2_title'    => ' <span style="color:red">'.$LNG['achievements_community2'].'</span>',
	'community2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_community2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'community2_title'    => ' <span style="color:lime">'.$LNG['achievements_community2'].'</span>',
	'community2_req1'    => ' <span style="color:lime">'.$LNG['achievements_community2_done'].'</span>',
	'community2_img'    => ' <img border="1" src="styles/achievements/community2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Community Achievements Level 3

	if(empty($USER['achievements_community3'] ))
	{
	$query = $db->selectsingle("SELECT * FROM uni1_fleets WHERE `fleet_mission` = '5' AND `fleet_owner` = '". $USER['id'] ."';");
	$fleet = $query;
	if($fleet >=1)
		{
	$db->query("UPDATE uni1_users SET `achievements_community3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_community` = achievements_community+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $community3_darkmatter;
	$PLANET[$resource[901]]	+= $community3_metal;
    $PLANET[$resource[902]]	+= $community3_crystal; 
    $PLANET[$resource[903]]	+= $community3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_community3_success']);
		
}
	if($fleet >=1)
	$community3_req1 = '<span style="color:lime">'.$LNG['achievements_community3_require_1'].'</span>';
else
	$community3_req1 = '<span style="color:red">'.$LNG['achievements_community3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'community3_req1'    	=> $community3_req1,
	'community3_title'    => ' <span style="color:red">'.$LNG['achievements_community3'].'</span>',
	'community3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_community3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'community3_title'    => ' <span style="color:lime">'.$LNG['achievements_community3'].'</span>',
	'community3_req1'    => ' <span style="color:lime">'.$LNG['achievements_community3_done'].'</span>',
	'community3_img'    => ' <img border="1" src="styles/achievements/community3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_community'] < 3)
	{
$this->tplObj->assign_vars(array(
	'community_title'    => ' <span style="color:red">'.$LNG['achievements_community'].'</span>',
));
	}
	
if($USER['achievements_community'] >= 3)
	{
$this->tplObj->assign_vars(array(
	'community_title'    => ' <span style="color:lime">'.$LNG['achievements_community'].'</span>',
));
	}
	
######################################################################################
######### Fleet Achievements Level 1

	if(empty($USER['achievements_fleet1'] ))
	{
	$query = $db->selectsingle("SELECT count(*) AS attacks FROM `uni1_fleets` WHERE `fleet_mission` = '". 1 ."' AND `fleet_owner` = '". $USER['id'] ."';");
	$fleet1 = $query['attacks'];
	if($fleet1 >=10)
		{
	$db->query("UPDATE uni1_users SET `achievements_fleet1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_fleet` = achievements_fleet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $fleet1_darkmatter;
	$PLANET[$resource[901]]	+= $fleet1_metal;
    $PLANET[$resource[902]]	+= $fleet1_crystal; 
    $PLANET[$resource[903]]	+= $fleet1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_fleet1_success']);
	
}
	if($fleet1 >=10)
	$fleet1_req1 = '<span style="color:lime">'.$LNG['achievements_fleet1_require_1'].'</span>';
else
	$fleet1_req1 = '<span style="color:red">'.$LNG['achievements_fleet1_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'fleet1_req1'    	=> $fleet1_req1,
	'fleet1_title'    => ' <span style="color:red">'.$LNG['achievements_fleet1'].'</span>',
	'fleet1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_fleet1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'fleet1_title'    => ' <span style="color:lime">'.$LNG['achievements_fleet1'].'</span>',
	'fleet1_req1'    => ' <span style="color:lime">'.$LNG['achievements_fleet1_done'].'</span>',
	'fleet1_img'    => ' <img border="1" src="styles/achievements/fleet1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Fleet Achievements Level 2

	if(empty($USER['achievements_fleet2'] ))
	{
	$query = $db->selectsingle("SELECT count(*) FROM uni1_fleets WHERE `fleet_mission` = '8' AND `fleet_resource_metal` > '999999999' AND `fleet_resource_crystal` > '999999999' AND `fleet_owner` = '". $USER['id'] ."';");
	$fleet2 = $query;
	$query = $db->selectsingle("SELECT count(*) FROM uni1_fleets WHERE `fleet_mission` = '8' AND `fleet_resource_metal` > '999999999' AND `fleet_resource_crystal` > '999999999' AND `fleet_owner` = '0';");
	$comparateur2 = $query;
//	$fleet2 = 0;
	if($fleet2 != $comparateur2)
		{
			$db->query("UPDATE uni1_users SET `achievements_fleet2` = 1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements_fleet` = achievements_fleet+1 WHERE `id` = ".$USER['id'].";");
			$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
			$USER['darkmatter'] += $fleet2_darkmatter;
			$PLANET[$resource[901]]	+= $fleet2_metal;
			$PLANET[$resource[902]]	+= $fleet2_crystal; 
			$PLANET[$resource[903]]	+= $fleet2_deuterium; 		
			echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
			$this->printMessage($LNG['achievements_fleet2_success']);
				
		}
	if($fleet2 >=1)
	$fleet2_req1 = '<span style="color:lime">'.$LNG['achievements_fleet2_require_1'].'</span>';
else
	$fleet2_req1 = '<span style="color:red">'.$LNG['achievements_fleet2_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'fleet2_req1'    	=> $fleet2_req1,
	'fleet2_title'    => ' <span style="color:red">'.$LNG['achievements_fleet2'].'</span>',
	'fleet2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_fleet2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'fleet2_title'    => ' <span style="color:lime">'.$LNG['achievements_fleet2'].'</span>',
	'fleet2_req1'    => ' <span style="color:lime">'.$LNG['achievements_fleet2_done'].'</span>',
	'fleet2_img'    => ' <img border="1" src="styles/achievements/fleet2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Fleet Achievements Level 3

	if(empty($USER['achievements_fleet3'] ))
	{
	$query = $db->selectsingle("SELECT * FROM uni1_fleets WHERE `fleet_mission` = '11' AND  `fleet_owner` = '". $USER['id'] ."';");
	$fleet3 = $query;
	if(1 == 1)
		{
	$db->query("UPDATE uni1_users SET `achievements_fleet3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_fleet` = achievements_fleet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $fleet3_darkmatter;
	$PLANET[$resource[901]]	+= $fleet3_metal;
    $PLANET[$resource[902]]	+= $fleet3_crystal; 
    $PLANET[$resource[903]]	+= $fleet3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_fleet3_success']);
		
}
	if($fleet3 >=1)
	$fleet3_req1 = '<span style="color:lime">'.$LNG['achievements_fleet3_require_1'].'</span>';
else
	$fleet3_req1 = '<span style="color:red">'.$LNG['achievements_fleet3_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'fleet3_req1'    	=> $fleet3_req1,
	'fleet3_title'    => ' <span style="color:red">'.$LNG['achievements_fleet3'].'</span>',
	'fleet3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_fleet3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'fleet3_title'    => ' <span style="color:lime">'.$LNG['achievements_fleet3'].'</span>',
	'fleet3_req1'    => ' <span style="color:lime">'.$LNG['achievements_fleet3_done'].'</span>',
	'fleet3_img'    => ' <img border="1" src="styles/achievements/fleet3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Fleet Achievements Level 4

	if(empty($USER['achievements_fleet4'] ))
	{
	$query = $db->selectsingle("SELECT count(*) FROM uni1_fleets WHERE `fleet_group` != '0' AND  `fleet_owner` = '". $USER['id'] ."';");
	$fleet4 = $query;
	$query = $db->selectsingle("SELECT count(*) FROM uni1_fleets WHERE `fleet_group` != '0' AND  `fleet_owner` = '0';");
	$comparateur4 = $query;
//	$fleet4 = 0;
	if($fleet4 != $comparateur4)
		{
	$db->query("UPDATE uni1_users SET `achievements_fleet4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_fleet` = achievements_fleet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $fleet4_darkmatter;
	$PLANET[$resource[901]]	+= $fleet4_metal;
    $PLANET[$resource[902]]	+= $fleet4_crystal; 
    $PLANET[$resource[903]]	+= $fleet4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_fleet4_success']);
		
}
	if($fleet4 >=1)
	$fleet4_req1 = '<span style="color:lime">'.$LNG['achievements_fleet4_require_1'].'</span>';
else
	$fleet4_req1 = '<span style="color:red">'.$LNG['achievements_fleet4_require_1'].'</span>';


$this->tplObj->assign_vars(array(
	'fleet4_req1'    	=> $fleet4_req1,
	'fleet4_title'    => ' <span style="color:red">'.$LNG['achievements_fleet4'].'</span>',
	'fleet4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_fleet4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'fleet4_title'    => ' <span style="color:lime">'.$LNG['achievements_fleet4'].'</span>',
	'fleet4_req1'    => ' <span style="color:lime">'.$LNG['achievements_fleet4_done'].'</span>',
	'fleet4_img'    => ' <img border="1" src="styles/achievements/fleet4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_fleet'] < 4)
	{
$this->tplObj->assign_vars(array(
	'fleet_title'    => ' <span style="color:red">'.$LNG['achievements_fleet'].'</span>',
));
	}
	
if($USER['achievements_fleet'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'fleet_title'    => ' <span style="color:lime">'.$LNG['achievements_fleet'].'</span>',
));
	}
######### Dark Matter Achievements Level 1

	if(empty($USER['achievements_darkmatter1'] ))
	{
if($USER['darkmatter'] >= 10000)
{
	$db->query("UPDATE uni1_users SET `achievements_darkmatter1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_darkmatter` = achievements_darkmatter+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $darkmatter1_darkmatter;
	$PLANET[$resource[901]]	+= $darkmatter1_metal;
    $PLANET[$resource[902]]	+= $darkmatter1_crystal; 
    $PLANET[$resource[903]]	+= $darkmatter1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_darkmatter1_success']);
	
}
if($USER['darkmatter'] >=10000)
	$darkmatter1_req1 = '<span style="color:lime">'.$LNG['achievements_darkmatter1_require_1'].'</span>';
else
	$darkmatter1_req1 = '<span style="color:red">'.$LNG['achievements_darkmatter1_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'darkmatter1_req1'    	=> $darkmatter1_req1,
	'darkmatter1_title'    => ' <span style="color:red">'.$LNG['achievements_darkmatter1'].'</span>',
	'darkmatter1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_darkmatter1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'darkmatter1_title'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter1'].'</span>',
	'darkmatter1_req1'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter1_done'].'</span>',
	'darkmatter1_img'    => ' <img border="1" src="styles/achievements/darkmatter1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Dark Matter Achievements Level 2

	if(empty($USER['achievements_darkmatter2'] ))
	{
if($USER['darkmatter'] >= 15000)
{
	$db->query("UPDATE uni1_users SET `achievements_darkmatter2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_darkmatter` = achievements_darkmatter+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $darkmatter2_darkmatter;
	$PLANET[$resource[901]]	+= $darkmatter2_metal;
    $PLANET[$resource[902]]	+= $darkmatter2_crystal; 
    $PLANET[$resource[903]]	+= $darkmatter2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_darkmatter2_success']);
		
}
if($USER['darkmatter'] >=15000)
	$darkmatter2_req1 = '<span style="color:lime">'.$LNG['achievements_darkmatter2_require_1'].'</span>';
else
	$darkmatter2_req1 = '<span style="color:red">'.$LNG['achievements_darkmatter2_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'darkmatter2_req1'    	=> $darkmatter2_req1,
	'darkmatter2_title'    => ' <span style="color:red">'.$LNG['achievements_darkmatter2'].'</span>',
	'darkmatter2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_darkmatter2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'darkmatter2_title'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter2'].'</span>',
	'darkmatter2_req1'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter2_done'].'</span>',
	'darkmatter2_img'    => ' <img border="1" src="styles/achievements/darkmatter2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Dark Matter Achievements Level 3

	if(empty($USER['achievements_darkmatter3'] ))
	{
if($USER['darkmatter'] >= 250000)
{
	$db->query("UPDATE uni1_users SET `achievements_darkmatter3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_darkmatter` = achievements_darkmatter+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $darkmatter3_darkmatter;
	$PLANET[$resource[901]]	+= $darkmatter3_metal;
    $PLANET[$resource[902]]	+= $darkmatter3_crystal; 
    $PLANET[$resource[903]]	+= $darkmatter3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_darkmatter3_success']);
		
}
if($USER['darkmatter'] >=250000)
	$darkmatter3_req1 = '<span style="color:lime">'.$LNG['achievements_darkmatter3_require_1'].'</span>';
else
	$darkmatter3_req1 = '<span style="color:red">'.$LNG['achievements_darkmatter3_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'darkmatter3_req1'    	=> $darkmatter3_req1,
	'darkmatter3_title'    => ' <span style="color:red">'.$LNG['achievements_darkmatter3'].'</span>',
	'darkmatter3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_darkmatter3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'darkmatter3_title'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter3'].'</span>',
	'darkmatter3_req1'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter3_done'].'</span>',
	'darkmatter3_img'    => ' <img border="1" src="styles/achievements/darkmatter3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Dark Matter Achievements Level 4

	if(empty($USER['achievements_darkmatter4'] ))
	{
if($USER['darkmatter'] >= 1000000)
{
	$db->query("UPDATE uni1_users SET `achievements_darkmatter4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_darkmatter` = achievements_darkmatter+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $darkmatter4_darkmatter;
	$PLANET[$resource[901]]	+= $darkmatter4_metal;
    $PLANET[$resource[902]]	+= $darkmatter4_crystal; 
    $PLANET[$resource[903]]	+= $darkmatter4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_darkmatter4_success']);
		
}
if($USER['darkmatter'] >=1000000)
	$darkmatter4_req1 = '<span style="color:lime">'.$LNG['achievements_darkmatter4_require_1'].'</span>';
else
	$darkmatter4_req1 = '<span style="color:red">'.$LNG['achievements_darkmatter4_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'darkmatter4_req1'    	=> $darkmatter4_req1,
	'darkmatter4_title'    => ' <span style="color:red">'.$LNG['achievements_darkmatter4'].'</span>',
	'darkmatter4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_darkmatter4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'darkmatter4_title'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter4'].'</span>',
	'darkmatter4_req1'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter4_done'].'</span>',
	'darkmatter4_img'    => ' <img border="1" src="styles/achievements/darkmatter4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_darkmatter'] < 4)
	{
$this->tplObj->assign_vars(array(
	'darkmatter_title'    => ' <span style="color:red">'.$LNG['achievements_darkmatter'].'</span>',
));
	}
	
if($USER['achievements_darkmatter'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'darkmatter_title'    => ' <span style="color:lime">'.$LNG['achievements_darkmatter'].'</span>',
));
	}
	
######### Planet Achievements Level 1

	if(empty($USER['achievements_planet1'] ))
	{
if($PLANET['metal_perhour'] >= 2000000)
{
	$db->query("UPDATE uni1_users SET `achievements_planet1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_planet` = achievements_planet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $planet1_darkmatter;
	$PLANET[$resource[901]]	+= $planet1_metal;
    $PLANET[$resource[902]]	+= $planet1_crystal; 
    $PLANET[$resource[903]]	+= $planet1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_planet1_success']);	
	
}
if($PLANET['metal_perhour'] >=  2000000)
	$metal1_req1 = '<span style="color:lime">'.$LNG['achievements_planet1_require_1'].'</span>';
else
	$metal1_req1 = '<span style="color:red">'.$LNG['achievements_planet1_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'planet1_req1'    	=> $metal1_req1,
	'planet1_title'    => ' <span style="color:red">'.$LNG['achievements_planet1'].'</span>',
	'planet1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_planet1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'planet1_title'    => ' <span style="color:lime">'.$LNG['achievements_planet1'].'</span>',
	'planet1_req1'    => ' <span style="color:lime">'.$LNG['achievements_planet1_done'].'</span>',
	'planet1_img'    => ' <img border="1" src="styles/achievements/planet1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Planet Achievements Level 2

	if(empty($USER['achievements_planet2'] ))
	{
if($PLANET['crystal_perhour'] >= 1160000)
{
	$db->query("UPDATE uni1_users SET `achievements_planet2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_planet` = achievements_planet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $planet2_darkmatter;
	$PLANET[$resource[901]]	+= $planet2_metal;
    $PLANET[$resource[902]]	+= $planet2_crystal; 
    $PLANET[$resource[903]]	+= $planet2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_planet2_success']);	
		
}
if($PLANET['crystal_perhour'] >= 1160000)
	$metal2_req1 = '<span style="color:lime">'.$LNG['achievements_planet2_require_1'].'</span>';
else
	$metal2_req1 = '<span style="color:red">'.$LNG['achievements_planet2_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'planet2_req1'    	=> $metal2_req1,
	'planet2_title'    => ' <span style="color:red">'.$LNG['achievements_planet2'].'</span>',
	'planet2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_planet2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'planet2_title'    => ' <span style="color:lime">'.$LNG['achievements_planet2'].'</span>',
	'planet2_req1'    => ' <span style="color:lime">'.$LNG['achievements_planet2_done'].'</span>',
	'planet2_img'    => ' <img border="1" src="styles/achievements/planet2_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Planet Achievements Level 3

	if(empty($USER['achievements_planet3'] ))
	{
if($PLANET['deuterium_perhour'] >= 700000)
{
	$db->query("UPDATE uni1_users SET `achievements_planet3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_planet` = achievements_planet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $planet3_darkmatter;
	$PLANET[$resource[901]]	+= $planet3_metal;
    $PLANET[$resource[902]]	+= $planet3_crystal; 
    $PLANET[$resource[903]]	+= $planet3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_planet3_success']);	
		
}
if($PLANET['deuterium_perhour'] >= 700000)
	$metal3_req1 = '<span style="color:lime">'.$LNG['achievements_planet3_require_1'].'</span>';
else
	$metal3_req1 = '<span style="color:red">'.$LNG['achievements_planet3_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'planet3_req1'    	=> $metal3_req1,
	'planet3_title'    => ' <span style="color:red">'.$LNG['achievements_planet3'].'</span>',
	'planet3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_planet3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'planet3_title'    => ' <span style="color:lime">'.$LNG['achievements_planet3'].'</span>',
	'planet3_req1'    => ' <span style="color:lime">'.$LNG['achievements_planet3_done'].'</span>',
	'planet3_img'    => ' <img border="1" src="styles/achievements/planet3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### Planet Achievements Level 4
	
	if(empty($USER['achievements_planet4'] ))
	{
if($USER['achievements_planet'] >= 3)
{
	$db->query("UPDATE uni1_users SET `achievements_planet4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_planet` = achievements_planet+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $planet4_darkmatter;
	$PLANET[$resource[901]]	+= $planet4_metal;
    $PLANET[$resource[902]]	+= $planet4_crystal; 
    $PLANET[$resource[903]]	+= $planet4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_planet4_success']);	
		
}
if($USER['achievements_planet'] >= 3)
	$metal4_req1 = '<span style="color:lime">'.$LNG['achievements_planet4_require_1'].'</span>';
else
	$metal4_req1 = '<span style="color:red">'.$LNG['achievements_planet4_require_1'].'</span>';
$this->tplObj->assign_vars(array(
	'planet4_req1'    	=> $metal4_req1,
	'planet4_title'    => ' <span style="color:red">'.$LNG['achievements_planet4'].'</span>',
	'planet4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_planet4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'planet4_title'    => ' <span style="color:lime">'.$LNG['achievements_planet4'].'</span>',
	'planet4_req1'    => ' <span style="color:lime">'.$LNG['achievements_planet4_done'].'</span>',
	'planet4_img'    => ' <img border="1" src="styles/achievements/planet4_done.png" align="top" width="80" height="80"></td>',
));
	}

######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_planet'] < 4)
	{
$this->tplObj->assign_vars(array(
	'planet_title'    => ' <span style="color:red">'.$LNG['achievements_planet'].'</span>',
));
	}
	
if($USER['achievements_planet'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'planet_title'    => ' <span style="color:lime">'.$LNG['achievements_planet'].'</span>',
));
	}
	
######################################################################################
######### Lab Achievements Level 1

	if(empty($USER['achievements_lab1'] ))
	{
if($USER['metal_proc_tech'] >= 15 && $USER['crystal_proc_tech'] >= 15 && $USER['deuterium_proc_tech'] >=15)
{
	$db->query("UPDATE uni1_users SET `achievements_lab1` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_lab` = achievements_lab+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $lab1_darkmatter;
	$PLANET[$resource[901]]	+= $lab1_metal;
    $PLANET[$resource[902]]	+= $lab1_crystal; 
    $PLANET[$resource[903]]	+= $lab1_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_lab1_success']);	
	
}
if($USER['metal_proc_tech'] >=15)
	$lab1_req1 = '<span style="color:lime">'.$LNG['achievements_lab1_require_1'].'</span>';
else
	$lab1_req1 = '<span style="color:red">'.$LNG['achievements_lab1_require_1'].'</span>';
if($USER['crystal_proc_tech'] >=15)
	$lab1_req2 = '<span style="color:lime">'.$LNG['achievements_lab1_require_2'].'</span>';
else
	$lab1_req2 = '<span style="color:red">'.$LNG['achievements_lab1_require_2'].'</span>';
if($USER['deuterium_proc_tech'] >=15)
	$lab1_req3 = '<span style="color:lime">'.$LNG['achievements_lab1_require_3'].'</span>';
else
	$lab1_req3 = '<span style="color:red">'.$LNG['achievements_lab1_require_3'].'</span>';

$this->tplObj->assign_vars(array(
	'lab1_req1'    	=> $lab1_req1,
	'lab1_req2'    	=> $lab1_req2,
	'lab1_req3'    	=> $lab1_req3,
	'lab1_title'    => ' <span style="color:red">'.$LNG['achievements_lab1'].'</span>',
	'lab1_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_lab1'] == 1)
	{
$this->tplObj->assign_vars(array(
	'lab1_title'    => ' <span style="color:lime">'.$LNG['achievements_lab1'].'</span>',
	'lab1_req1'    => ' <span style="color:lime">'.$LNG['achievements_lab1_done'].'</span>',
	'lab1_req2'    	=> ' <span style="color:lime">'.'</span>',
	'lab1_req3'    	=> ' <span style="color:lime">'.'</span>',
	'lab1_img'    => ' <img border="1" src="styles/achievements/lab1_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Lab Achievements Level 2

	if(empty($USER['achievements_lab2'] ))
	{
if($USER['spy_tech'] >= 20)
{
	$db->query("UPDATE uni1_users SET `achievements_lab2` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_lab` = achievements_lab+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $lab2_darkmatter;
	$PLANET[$resource[901]]	+= $lab2_metal;
    $PLANET[$resource[902]]	+= $lab2_crystal; 
    $PLANET[$resource[903]]	+= $lab2_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_lab2_success']);	
	
}
if($USER['spy_tech'] >=20)
	$lab2_req1 = '<span style="color:lime">'.$LNG['achievements_lab2_require_1'].'</span>';
else
	$lab2_req1 = '<span style="color:red">'.$LNG['achievements_lab2_require_1'].'</span>';

$this->tplObj->assign_vars(array(
	'lab2_req1'    	=> $lab2_req1,
	'lab2_title'    => ' <span style="color:red">'.$LNG['achievements_lab2'].'</span>',
	'lab2_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_lab2'] == 1)
	{
$this->tplObj->assign_vars(array(
	'lab2_title'    => ' <span style="color:lime">'.$LNG['achievements_lab2'].'</span>',
	'lab2_req1'    => ' <span style="color:lime">'.$LNG['achievements_lab2_done'].'</span>',
	'lab2_img'    => ' <img border="1" src="styles/achievements/lab2_done.png" align="top" width="80" height="80"></td>',
));
	}

######################################################################################
######### Lab Achievements Level 3

	if(empty($USER['achievements_lab3'] ))
	{
if($USER['graviton_tech'] >= 5)
{
	$db->query("UPDATE uni1_users SET `achievements_lab3` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_lab` = achievements_lab+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $lab3_darkmatter;
	$PLANET[$resource[901]]	+= $lab3_metal;
    $PLANET[$resource[902]]	+= $lab3_crystal; 
    $PLANET[$resource[903]]	+= $lab3_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_lab3_success']);	
		
}
if($USER['graviton_tech'] >=5)
	$lab3_req1 = '<span style="color:lime">'.$LNG['achievements_lab3_require_1'].'</span>';
else
	$lab3_req1 = '<span style="color:red">'.$LNG['achievements_lab3_require_1'].'</span>';

$this->tplObj->assign_vars(array(
	'lab3_req1'    	=> $lab3_req1,
	'lab3_title'    => ' <span style="color:red">'.$LNG['achievements_lab3'].'</span>',
	'lab3_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_lab3'] == 1)
	{
$this->tplObj->assign_vars(array(
	'lab3_title'    => ' <span style="color:lime">'.$LNG['achievements_lab3'].'</span>',
	'lab3_req1'    => ' <span style="color:lime">'.$LNG['achievements_lab3_done'].'</span>',
	'lab3_img'    => ' <img border="1" src="styles/achievements/lab3_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######################################################################################
######### Lab Achievements Level 4

	if(empty($USER['achievements_lab4'] ))
	{
if($USER['rpg_empereur'] >= 1)
{
	$db->query("UPDATE uni1_users SET `achievements_lab4` = 1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements_lab` = achievements_lab+1 WHERE `id` = ".$USER['id'].";");
	$db->query("UPDATE uni1_users SET `achievements` = achievements+1 WHERE `id` = ".$USER['id'].";");
	$USER['darkmatter'] += $lab4_darkmatter;
	$PLANET[$resource[901]]	+= $lab4_metal;
    $PLANET[$resource[902]]	+= $lab4_crystal; 
    $PLANET[$resource[903]]	+= $lab4_deuterium; 		
	echo '<script>setTimeout(\'location="game.php?page=achievements"\', 3000)</script>';
	$this->printMessage($LNG['achievements_lab4_success']);	
		
}
if($USER['rpg_empereur'] >=1)
	$lab4_req1 = '<span style="color:lime">'.$LNG['achievements_lab4_require_1'].'</span>';
else
	$lab4_req1 = '<span style="color:red">'.$LNG['achievements_lab4_require_1'].'</span>';

$this->tplObj->assign_vars(array(
	'lab4_req1'    	=> $lab4_req1,
	'lab4_title'    => ' <span style="color:red">'.$LNG['achievements_lab4'].'</span>',
	'lab4_img'    => ' <img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80"></td>',
));
	}
	
if($USER['achievements_lab4'] == 1)
	{
$this->tplObj->assign_vars(array(
	'lab4_title'    => ' <span style="color:lime">'.$LNG['achievements_lab4'].'</span>',
	'lab4_req1'    => ' <span style="color:lime">'.$LNG['achievements_lab4_done'].'</span>',
	'lab4_img'    => ' <img border="1" src="styles/achievements/lab4_done.png" align="top" width="80" height="80"></td>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, If the "achievement mom" is the same number of missions complete in the category
	
if($USER['achievements_lab'] < 4)
	{
$this->tplObj->assign_vars(array(
	'lab_title'    => ' <span style="color:red">'.$LNG['achievements_lab'].'</span>',
));
	}
	
if($USER['achievements_lab'] >= 4)
	{
$this->tplObj->assign_vars(array(
	'lab_title'    => ' <span style="color:lime">'.$LNG['achievements_lab'].'</span>',
));
	}
	
######### DO NOT CHANGE ANYTHING THERE, except if you added any new achievement, or maded your edit
		
	// Badge 1 - Mines
if($USER['achievements_mines'] == 4)
	$mines_badge = '<img border="1" src="styles/achievements/mines_done.png" align="top" width="80" height="80">';
else
	$mines_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 2 - Tech
if($USER['achievements_tech'] == 4)
	$tech_badge = '<img border="1" src="styles/achievements/tech_done.png" align="top" width="80" height="80">';
else
	$tech_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 3 - Engine
if($USER['achievements_engine'] == 4)
	$engine_badge = '<img border="1" src="styles/achievements/engine_done.png" align="top" width="80" height="80">';
else
	$engine_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 4 - Colonization
if($USER['achievements_colonization'] == 3)
	$colonization_badge = '<img border="1" src="styles/achievements/colonization_done.png" align="top" width="80" height="80">';
else
	$colonization_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 5 - Moon
if($USER['achievements_moon'] == 3)
	$moon_badge = '<img border="1" src="styles/achievements/moon_done.png" align="top" width="80" height="80">';
else
	$moon_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 6 - War
if($USER['achievements_war'] == 3)
	$war_badge = '<img border="1" src="styles/achievements/war_done.png" align="top" width="80" height="80">';
else
	$war_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 7 - Destroy
if($USER['achievements_destroy'] == 4)
	$destroy_badge = '<img border="1" src="styles/achievements/destroy_done.png" align="top" width="80" height="80">';
else
	$destroy_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 8 - Time
if($USER['achievements_time'] == 3)
	$time_badge = '<img border="1" src="styles/achievements/time_done.png" align="top" width="80" height="80">';
else
	$time_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';
if(($USER['b_tech'] - 1) > TIMESTAMP)
	$time_tech = '<span style="color:yellow">'.$LNG['achievements_time_tech1'].'</span>';
else
	$time_tech = '<span style="color:red">'.$LNG['achievements_time_tech2'].'</span>';
if(($PLANET['b_building'] - 1) > TIMESTAMP)
	$time_build = '<span style="color:yellow">'.$LNG['achievements_time_build1'].'</span>';
else
	$time_build = '<span style="color:red">'.$LNG['achievements_time_build2'].'</span>';
if(($PLANET['b_hangar_time'] - 1) > 86400)
	$time_hangar = '<span style="color:yellow">'.$LNG['achievements_time_hangar1'].'</span>';
else
	$time_hangar = '<span style="color:red">'.$LNG['achievements_time_hangar2'].'</span>';
	
	// Badge 9 - Storage
if($USER['achievements_storage'] == 4)
	$storage_badge = '<img border="1" src="styles/achievements/storage_done.png" align="top" width="80" height="80">';
else
	$storage_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 10 - Community
if($USER['achievements_community'] == 3)
	$community_badge = '<img border="1" src="styles/achievements/community_done.png" align="top" width="80" height="80">';
else
	$community_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 10 - Fleets
if($USER['achievements_fleet'] == 4)
	$fleet_badge = '<img border="1" src="styles/achievements/fleet_done.png" align="top" width="80" height="80">';
else
	$fleet_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';

	// Badge 11 - Dark Matter
if($USER['achievements_darkmatter'] == 4)
	$darkmatter_badge = '<img border="1" src="styles/achievements/darkmatter_done.png" align="top" width="80" height="80">';
else
	$darkmatter_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';
	
	// Badge 12 - Planets
if($USER['achievements_planet'] == 4)
	$metal_badge = '<img border="1" src="styles/achievements/planet_done.png" align="top" width="80" height="80">';
else
	$metal_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';
	
	// Badge 13 - Lab
if($USER['achievements_lab'] == 4)
	$lab_badge = '<img border="1" src="styles/achievements/lab_done.png" align="top" width="80" height="80">';
else
	$lab_badge = '<img border="1" src="styles/achievements/locked.png" align="top" width="80" height="80">';
	

	// Mines Achievements - Shows the max mines of the user (Search in all planets)
	$Max_MetalMine = $db->selectsingle("SELECT MAX(metal_mine) as metal_mine FROM uni1_planets WHERE id_owner = ".$USER['id'].";");
	$Max_CrystalMine = $db->selectsingle("SELECT MAX(crystal_mine) as crystal_mine FROM uni1_planets WHERE id_owner = ".$USER['id'].";");
	$Max_DeuteriumSint = $db->selectsingle("SELECT MAX(deuterium_sintetizer) as deuterium_sintetizer FROM uni1_planets WHERE id_owner = ".$USER['id'].";");

	// Storage Achievements - Shows the max storages of the user (Search in all planets)
	$Max_MetalStorage = $db->selectsingle("SELECT MAX(metal_store) as metal_store FROM uni1_planets WHERE id_owner = ".$USER['id'].";");
	$Max_CrystalStorage = $db->selectsingle("SELECT MAX(crystal_store) as crystal_store FROM uni1_planets WHERE id_owner = ".$USER['id'].";");
	$Max_DeuteriumStorage = $db->selectsingle("SELECT MAX(deuterium_store) as deuterium_store FROM uni1_planets WHERE id_owner = ".$USER['id'].";");
	
	// Colonization & Moon Achievements - Show the amount of moons and colonies of the user
	$MyPlanets = $db->selectsingle("SELECT count(*) AS planet_count FROM uni1_planets WHERE `planet_type` = '". 1 ."' AND `destruyed` = '0' AND `id_owner` = '". $USER['id'] ."';");
	$MyMoons = $db->selectsingle("SELECT count(*) AS moon_count FROM uni1_planets WHERE `planet_type` = '". 3 ."' AND `id_owner` = '". $USER['id'] ."';");
	
	// Destroy Achievements - Amount of destroy
	$RecMetal = $db->selectsingle("SELECT count(*) AS kbmetal FROM uni1_users WHERE `id` = '". $USER['id'] ."';");
	$RecCrystal = $db->selectsingle("SELECT count(*) AS kbcrystal FROM uni1_users WHERE `id` = '". $USER['id'] ."';");

	// Fleet Achievemens - How many fleets Am I doing?
	$FleetAmount = $db->selectsingle("SELECT count(*) AS fleet_id FROM uni1_fleets WHERE `fleet_owner` = '". $USER['id'] ."';");

	$this->tplObj->assign_vars(array(
	
	// All achievements unlocked by player
		'amount'	=> $USER['achievements'],
	// Badges
		'mines_badge'	=> $mines_badge,
		'tech_badge'	=> $tech_badge,
		'engine_badge'	=> $engine_badge,
		'colonization_badge'	=> $colonization_badge,
		'moon_badge'	=> $moon_badge, 
		'war_badge'	=> $war_badge,
		'destroy_badge'	=> $destroy_badge,
		'time_badge'	=> $time_badge,
		'storage_badge'	=> $storage_badge,
		'community_badge'	=> $community_badge,
		'fleet_badge'	=> $fleet_badge,
		'darkmatter_badge'	=> $darkmatter_badge,
		'planet_badge'	=> $metal_badge,
		'lab_badge'	=> $lab_badge,
	// Amount of Achievements did
		'badge_amount'		=> $USER['achievements'],
	// Requirements for Achievements
		'badge_req1'		=> $USER['achievements'],
	// Amount of Mine Achievements did
		'mines_amount'		=> $USER['achievements_mines'],
		'mines_req'		=> 4,//$CONF['achievements_mines'],
	// Amount of Mine Achievements did
		'store_amount'		=> $USER['achievements_storage'],
		'store_req'		=> 4,//$CONF['achievements_storage'],
	// Requirements for Mine Achievements
		'mines_req1'		=> $PLANET['metal_mine'],
		'mines_req2'		=> $PLANET['crystal_mine'],
		'mines_req3'		=> $PLANET['deuterium_sintetizer'],
	// Requirements for storages
		'store_req1'		=> $PLANET['metal_store'],
		'store_req2'		=> $PLANET['crystal_store'],
		'store_req3'		=> $PLANET['deuterium_store'],
	// Max Mines in planet for Requirements for Mine Achievements
		'mines_max_req1'		=> $Max_MetalMine['metal_mine'],
		'mines_max_req2'		=> $Max_CrystalMine['crystal_mine'],
		'mines_max_req3'		=> $Max_DeuteriumSint['deuterium_sintetizer'],
	// Max Storages in planet for Requirements for Mine Achievements
		'store_max_req1'		=> $Max_MetalStorage['metal_store'],
		'store_max_req2'		=> $Max_CrystalStorage['crystal_store'],
		'store_max_req3'		=> $Max_DeuteriumStorage['deuterium_store'],	
	// Amount of Tech Achievements did
		'tech_amount'		=> $USER['achievements_tech'],
		'tech_req'		=> 4,//$CONF['achievements_tech'],
	// Requirements for Tech Achievements
		'tech_req1'		=> $USER['military_tech'],
		'tech_req2'		=> $USER['defence_tech'],
		'tech_req3'		=> $USER['shield_tech'],
	// Amount of Engine Achievements did
		'engine_amount'		=> $USER['achievements_engine'],
		'engine_req'		=> 4,//$CONF['achievements_engine'],
	// Requirements for Engine Achievements
		'engine_req1'		=> $USER['combustion_tech'],
		'engine_req2'		=> $USER['impulse_motor_tech'],
		'engine_req3'		=> $USER['hyperspace_motor_tech'],
	// Amount of Colonization Achievements did
		'colonization_amount'		=> $USER['achievements_colonization'],
		'colonization_req'		=> 4,//$CONF['achievements_colonization'],
	// Requirements for Colonization Achievements
		'colonization_req1'		=> $MyPlanets['planet_count'],
	// Amount of Moon Achievements did
		'moon_amount'		=> $USER['achievements_moon'],
		'moon_req'		=> 4,//$CONF['achievements_moon'],
	// Requirements for Moon Achievements
		'moon_req1'		=> $MyMoons['moon_count'],
	// Amount of War Achievements did
		'war_amount'		=> $USER['achievements_war'],
		'war_req'		=> 4,//$CONF['achievements_war'],
	// Requirements for War Achievements
		'war_req1'		=> 	$USER['wons'],
		'war_req2'		=> 	$USER['loos'],
	// Amount of Destroy Achievements did
		'destroy_amount'		=> $USER['achievements_destroy'],
		'destroy_req'		=> 4,//$CONF['achievements_destroy'],
	// Requirements for Destroy Achievements
		'destroy_req1'		=> 	$USER['kbmetal'],
		'destroy_req2'		=> 	$USER['kbcrystal'],
	// Amount of time Achievements did
		'time_amount'		=> $USER['achievements_time'],
		'time_req'		=> 4,//$CONF['achievements_time'],
	// Requirements for time Achievements
		'time_tech'		=> 	$time_tech,
		'time_build'		=> 	$time_build,
		'time_hangar'		=> 	$time_hangar,
	// Amount of storage Achievements did
		'storage_amount'		=> $USER['achievements_storage'],
		'storage_req'		=> 4,//$CONF['achievements_storage'],
	// Amount of Community Achievements did
		'community_amount'		=> $USER['achievements_community'],
		'community_req'		=> 4,//$CONF['achievements_community'],
	// Amount of fleet Achievements did
		'fleet_amount'		=> $USER['achievements_fleet'],
		'fleet_req'		=> 4,//$CONF['achievements_fleet'],
	// Requirements for fleet Achievements
		'fleet_req1'		=> 	$FleetAmount['fleet_id'],
	// Amount of Darkmatter Achievements did
		'darkmatter_amount'		=> $USER['achievements_darkmatter'],
		'darkmatter_req'		=> 4,//$CONF['achievements_darkmatter'],
	// Requirements for darkmatter Achievements
		'darkmatter_req1'		=> 	$USER['darkmatter'],
	// Amount of Planet Achievements did
		'planet_amount'		=> $USER['achievements_planet'],
		'planet_req'		=> 4,//$CONF['achievements_planet'],
	// Amount of Lab Achievements did
		'lab_amount'		=> $USER['achievements_lab'],
		'lab_req'		=> 4,//$CONF['achievements_lab'],
		));
	
	$this->display('page.achievements.body.tpl');
 }
}
?>
