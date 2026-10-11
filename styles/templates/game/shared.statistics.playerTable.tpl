<tr class="stats-cols">
	<th class="stats-rank">{$LNG.st_position}</th>
	<th class="stats-name">{$LNG.st_player}</th>
	<th class="stats-pm">&nbsp;</th>
	<th class="stats-ally">{$LNG.st_alliance}</th>
	<th class="stats-points">{$LNG.st_points}</th>
</tr>
{foreach name=RangeList item=RangeInfo from=$RangeList}
<tr class="stats-row{if $RangeInfo.id == $CUser_id} is-self{/if}">
	<td class="stats-rank"><a class="tooltip" data-tooltip-content="{if $RangeInfo.ranking == 0}<span style='color:#87CEEB'>*</span>{elseif $RangeInfo.ranking < 0}<span style='color:red'>-{$RangeInfo.ranking}</span>{elseif $RangeInfo.ranking > 0}<span style='color:green'>+{$RangeInfo.ranking}</span>{/if}">{$RangeInfo.rank}</a></td>
	<td class="stats-name"><a href="#" onclick="return Dialog.Playercard({$RangeInfo.id}, '{$RangeInfo.name}');"{if $RangeInfo.id == $CUser_id} style="color:lime"{/if}>{$RangeInfo.name}</a></td>
	<td class="stats-pm">{if $RangeInfo.id != $CUser_id}<a href="#" onclick="return Dialog.PM({$RangeInfo.id});" title="{$LNG.st_write_message}"><img src="{$dpath}img/m.gif" alt="{$LNG.st_write_message}"></a>{/if}</td>
	<td class="stats-ally">{if $RangeInfo.allyid != 0}<a href="game.php?page=alliance&amp;mode=info&amp;id={$RangeInfo.allyid}">{if $RangeInfo.allyid == $CUser_ally}<span style="color:#33CCFF">{$RangeInfo.allyname}</span>{else}{$RangeInfo.allyname}{/if}</a>{else}-{/if}</td>
	<td class="stats-points">{$RangeInfo.total}</td>
</tr>
{/foreach}
