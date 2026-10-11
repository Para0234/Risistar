{block name="title" prepend}{$LNG.lm_topkb}{/block}
{block name="content"}
<div class="hof-page">
<table class="hof-table">
<tbody>
<tr>
    <th class="hof-title" colspan="4">{$LNG.tkb_top}</th>
</tr>
<tr>
    <td class="hof-intro" colspan="4">{$LNG.tkb_gratz}</td>
</tr>
<tr class="hof-cols">
    <th class="hof-rank">{$LNG.tkb_platz}</th>
	<th class="hof-owners">{$LNG.tkb_owners}</th>
    <th class="hof-date{if $order == "date"} is-sorted is-{$sort|lower}{/if}"{if $order == "date"} aria-sort="{if $sort == "ASC"}ascending{else}descending{/if}"{else} aria-sort="none"{/if}><a href="game.php?page=battleHall&amp;order=date&amp;sort={if $sort == "ASC"}DESC{else}ASC{/if}">{$LNG.tkb_datum}<span class="hof-sort" aria-hidden="true"></span></a></th>
	<th class="hof-units{if $order == "units"} is-sorted is-{$sort|lower}{/if}"{if $order == "units"} aria-sort="{if $sort == "ASC"}ascending{else}descending{/if}"{else} aria-sort="none"{/if}><a href="game.php?page=battleHall&amp;order=units&amp;sort={if $sort == "ASC"}DESC{else}ASC{/if}">{$LNG.tkb_units}<span class="hof-sort" aria-hidden="true"></span></a></th>
</tr>
{foreach $TopKBList as $row}
    <tr class="hof-row day{floor($row.time / 86400)} week{floor($row.time / 604800)}">
        <td class="hof-rank">{$row@iteration}</td>
        <td class="hof-owners">
			<a class="hof-fight" href="game.php?page=raport&amp;mode=battlehall&amp;raport={$row.rid}" target="_blank">
			{if $row.result == "a"}
			<span class="hof-side is-win">{$row.attacker}</span><span class="hof-vs">VS</span><span class="hof-side is-loss">{$row.defender}</span>
			{elseif $row.result == "r"}
			<span class="hof-side is-loss">{$row.attacker}</span><span class="hof-vs">VS</span><span class="hof-side is-win">{$row.defender}</span>
			{else}
			<span class="hof-side">{$row.attacker}</span><span class="hof-vs">VS</span><span class="hof-side">{$row.defender}</span>
			{/if}
			</a>
		</td>
        <td class="hof-date">{$row.date}</td>
        <td class="hof-units">{$row.units|number}</td>
    </tr>
{/foreach}
<tr class="hof-legend">
<td colspan="4">{$LNG.tkb_legende}<span style="color:#00FF00">{$LNG.tkb_gewinner}</span><span style="color:#FF0000">{$LNG.tkb_verlierer}</span></td></tr>
</tbody>
</table>
</div>
{/block}
