<tr class="stats-cols">
	<th class="stats-rank">{$LNG.st_position}</th>
	<th class="stats-name">{$LNG.st_alliance}</th>
	<th class="stats-members">{$LNG.st_members}</th>
	<th class="stats-points">{$LNG.st_points}</th>
	<th class="stats-per">{$LNG.st_per_member}</th>
</tr>
{foreach name=RangeList item=RangeInfo from=$RangeList}
<tr class="stats-row{if $RangeInfo.id == $CUser_ally} is-self{/if}">
	<td class="stats-rank"><a class="tooltip" data-tooltip-content="{if $RangeInfo.ranking == 0}<span style='color:#87CEEB'>*</span>{elseif $RangeInfo.ranking < 0}<span style='color:red'>{$RangeInfo.ranking}</span>{elseif $RangeInfo.ranking > 0}<span style='color:green'>+{$RangeInfo.ranking}</span>{/if}">{$RangeInfo.rank}</a></td>
	<td class="stats-name"><a href="game.php?page=alliance&amp;mode=info&amp;id={$RangeInfo.id}" target="ally"{if $RangeInfo.id == $CUser_ally} style="color:lime"{/if}>{$RangeInfo.name}</a></td>
	<td class="stats-members" data-label="{$LNG.st_members}">{$RangeInfo.members}</td>
	<td class="stats-points">{$RangeInfo.points}</td>
	<td class="stats-per" data-label="{$LNG.st_per_member}">{$RangeInfo.mppoints}</td>
</tr>
{/foreach}
