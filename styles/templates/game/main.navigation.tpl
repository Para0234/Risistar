<div class="nav-backdrop"></div>
<div id="leftmenu">
	<button type="button" id="navToggle" class="nav-toggle" aria-controls="menu" aria-expanded="true" title="{$LNG.nav_collapse|default:'Réduire le menu'}">
		<span class="nav-toggle-icon" aria-hidden="true"></span>
	</button>
	<ul id="menu">
		<li class="menu-head"><a href="game.php?page=changelog">{$LNG.lm_changelog}</a></li>
		<li class="menucat1-head"></li>
		<li><a class="nav-link nav-overview" href="game.php?page=overview" title="{$LNG.lm_overview}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_overview}</span></a></li>
		{if isModuleAvailable($smarty.const.MODULE_IMPERIUM)}<li><a class="nav-link nav-imperium" href="game.php?page=imperium" title="{$LNG.lm_empire}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_empire}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_MESSAGES)}<li><a class="nav-link nav-spyreports" href="game.php?page=spyReports" title="{$LNG.lm_spy_reports}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_spy_reports}</span>{if $spy_unseen > 0}<span class="nav-badge">{$spy_unseen}</span>{/if}</a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_RESEARCH)}<li><a class="nav-link nav-research" href="game.php?page=research" title="{$LNG.lm_research}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_research}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_BUILDING)}<li><a class="nav-link nav-buildings" href="game.php?page=buildings" title="{$LNG.lm_buildings}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_buildings}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_SHIPYARD_FLEET)}<li><a class="nav-link nav-shipyard" href="game.php?page=shipyard&amp;mode=fleet" title="{$LNG.lm_shipshard}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_shipshard}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_SHIPYARD_DEFENSIVE)}<li><a class="nav-link nav-defense" href="game.php?page=shipyard&amp;mode=defense" title="{$LNG.lm_defenses}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_defenses}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_OFFICIER) || isModuleAvailable($smarty.const.MODULE_DMEXTRAS)}<li><a class="nav-link nav-officier" href="game.php?page=officier" title="{$LNG.lm_officiers}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_officiers}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_TRADER)}<li><a class="nav-link nav-trader" href="game.php?page=trader" title="{$LNG.lm_trader}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_trader}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_FLEET_TRADER)}<li><a class="nav-link nav-fleettrader" href="game.php?page=fleetDealer" title="{$LNG.lm_fleettrader}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_fleettrader}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_TRADER)}<li><a class="nav-link nav-fleet" href="game.php?page=fleetTable" title="{$LNG.lm_fleet}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_fleet}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_TECHTREE)}<li><a class="nav-link nav-technology" href="game.php?page=techtree" title="{$LNG.lm_technology}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_technology}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_RESSOURCE_LIST)}<li><a class="nav-link nav-resources" href="game.php?page=resources" title="{$LNG.lm_resources}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_resources}</span></a></li>{/if}
		<li class="menucat2-head"></li>
		<li><a class="nav-link nav-achievements" href="game.php?page=achievements" title="{$LNG.lm_achievements}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_achievements}</span></a></li>
		{if isModuleAvailable($smarty.const.MODULE_GALAXY)}<li><a class="nav-link nav-galaxy" href="game.php?page=galaxy" title="{$LNG.lm_galaxy}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_galaxy}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_ALLIANCE)}<li><a class="nav-link nav-alliance" href="game.php?page=alliance" title="{$LNG.lm_alliance}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_alliance}</span></a></li>{/if}
		{if !empty($hasBoard)}<li><a class="nav-link nav-forums" href="game.php?page=board" target="forum" title="{$LNG.lm_forums}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_forums}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_STATISTICS)}<li><a class="nav-link nav-statistics" href="game.php?page=statistics" title="{$LNG.lm_statistics}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_statistics}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_RECORDS)}<li><a class="nav-link nav-records" href="game.php?page=records" title="{$LNG.lm_records}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_records}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_BATTLEHALL)}<li><a class="nav-link nav-topkb" href="game.php?page=battleHall" title="{$LNG.lm_topkb}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_topkb}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_SEARCH)}<li><a class="nav-link nav-search" href="game.php?page=search" title="{$LNG.lm_search}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_search}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_CHAT)}<li><a class="nav-link nav-chat" href="game.php?page=chat" title="{$LNG.lm_chat}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_chat}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_SUPPORT)}<li><a class="nav-link nav-support" href="game.php?page=ticket" title="{$LNG.lm_support}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_support}</span></a></li>{/if}
		<li><a class="nav-link nav-faq" href="game.php?page=questions" title="{$LNG.lm_faq}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_faq}</span></a></li>
		{if isModuleAvailable($smarty.const.MODULE_BANLIST)}<li><a class="nav-link nav-banned" href="game.php?page=banList" title="{$LNG.lm_banned}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_banned}</span></a></li>{/if}
		<li><a class="nav-link nav-rules" href="index.php?page=rules" target="rules" title="{$LNG.lm_rules}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_rules}</span></a></li>
		{if isModuleAvailable($smarty.const.MODULE_SIMULATOR)}<li><a class="nav-link nav-battlesim" href="game.php?page=battleSimulator" title="{$LNG.lm_battlesim}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_battlesim}</span></a></li>{/if}

		<li class="menucat3-head"></li>
		{if isModuleAvailable($smarty.const.MODULE_MESSAGES)}<li><a class="nav-link nav-messages" href="game.php?page=messages" title="{$LNG.lm_messages}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_messages}</span>{nocache}{if $new_message > 0}<span id="newmes" class="nav-badge"><span id="newmesnum">{$new_message}</span></span>{/if}{/nocache}</a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_NOTICE)}<li><a class="nav-link nav-notes" href="javascript:OpenNotes();" title="{$LNG.lm_notes}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_notes}</span></a></li>{/if}
		{if isModuleAvailable($smarty.const.MODULE_BUDDYLIST)}<li><a class="nav-link nav-buddylist" href="game.php?page=buddyList" title="{$LNG.lm_buddylist}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_buddylist}</span></a></li>{/if}
		<li><a class="nav-link nav-options" href="game.php?page=settings" title="{$LNG.lm_options}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_options}</span></a></li>
		<li><a class="nav-link nav-logout" href="game.php?page=logout" title="{$LNG.lm_logout}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_logout}</span></a></li>
		{if $authlevel > 0}<li><a class="nav-link nav-administration" href="./admin.php" style="color:lime" title="{$LNG.lm_administration}"><span class="nav-icon" aria-hidden="true"></span><span class="nav-label">{$LNG.lm_administration} ({$VERSION})</span></a></li>{/if}
		<li class="menu-footer"></li>
	</ul>
	<div id="disclamer"><a href="index.php?page=disclamer" target="_blank">{$LNG.lm_disclamer}</a></div>
</div>
