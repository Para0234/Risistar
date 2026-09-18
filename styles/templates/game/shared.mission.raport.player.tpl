{$PlayerInfo = $Raport.players[$Player.userID]}
<div class="combat-player combat-player--{$Role}{if empty($Player.ships)} is-destroyed{/if}">
	<div class="combat-player-name">{$PlayerInfo.name}</div>
	{if !empty($Info)}
	<div class="combat-player-coords">[XX:XX:XX]</div>
	{else}
	<div class="combat-player-coords">[{$PlayerInfo.koords[0]}:{$PlayerInfo.koords[1]}:{$PlayerInfo.koords[2]}]{if isset($PlayerInfo.koords[3])} ({$LNG["type_planet_short_{$PlayerInfo.koords[3]}"]}){/if}</div>
	{/if}
	<div class="combat-player-tech">{$PlayerInfo.tech[0]}/{$PlayerInfo.tech[1]}/{$PlayerInfo.tech[2]}</div>
	{if empty($Player.ships)}
	<div class="combat-destroyed">{$LNG.sys_destroyed}</div>
	{/if}
</div>
