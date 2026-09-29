{$PlayerInfo = $Raport.players[$Player.userID]}
<div class="combat-player combat-player--{$Role}{if empty($Player.ships)} is-destroyed{/if}">
	<div class="combat-player-name">{$PlayerInfo.name}</div>
	{if !empty($Info)}
	<div class="combat-player-coords">[XX:XX:XX]</div>
	{else}
	<div class="combat-player-coords">[{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]{if isset($PlayerInfo.koords[3])} ({$LNG["type_planet_short_{$PlayerInfo.koords[3]}"]}){/if}</div>
	{/if}
	<div class="combat-player-tech">
		<span class="combat-tech" title="{$LNG.sys_ship_weapon}">
			<img class="combat-tech-icon" src="{$dpath}gebaeude/109.gif" alt="{$LNG.sys_ship_weapon}">
			<span class="combat-tech-value">{$PlayerInfo.tech[0]|number}%</span>
		</span>
		<span class="combat-tech" title="{$LNG.sys_ship_armour}">
			<img class="combat-tech-icon" src="{$dpath}gebaeude/110.gif" alt="{$LNG.sys_ship_armour}">
			<span class="combat-tech-value">{$PlayerInfo.tech[1]|number}%</span>
		</span>
		<span class="combat-tech" title="{$LNG.sys_ship_shield}">
			<img class="combat-tech-icon" src="{$dpath}gebaeude/111.gif" alt="{$LNG.sys_ship_shield}">
			<span class="combat-tech-value">{$PlayerInfo.tech[2]|number}%</span>
		</span>
	</div>
	{if empty($Player.ships)}
	<div class="combat-destroyed">{$LNG.sys_destroyed}</div>
	{/if}
</div>
