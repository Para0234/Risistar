{block name="title" prepend}{$LNG.lm_statistics}{/block}
{block name="content"}
<form class="container-fluid" name="stats" id="stats" method="post" action="">
	<table class="container-fluid" class="table519">
		<tr>
			<th>{$LNG.st_statistics} ({$LNG.st_updated}: {$stat_date})</th>
		</tr>
		<tr>
			<td style="display:none"> Enlever le display none en bas pour ravoir un selecteur </td>
			<td style="display:none">
				<label class="container-fluid" for="who">{$LNG.st_show}</label> <select name="who" id="who" onchange="$('#stats').submit();">{html_options options=$Selectors.who selected=$who}</select>
				<label class="container-fluid" for="type">{$LNG.st_per}</label> <select name="type" id="type" onchange="$('#stats').submit();">{html_options options=$Selectors.type selected=$type}</select>
				<label class="container-fluid" for="range">{$LNG.st_in_the_positions}</label> <select name="range" id="range" onchange="$('#stats').submit();">{html_options options=$Selectors.range selected=$range}</select>
			</td>
		</tr>
	</table>
</form>
<table class="container-fluid" class="table519">
{if $who == 1}
	{include file="shared.statistics.playerTable.tpl"}
{elseif $who == 2}
	{include file="shared.statistics.allianceTable.tpl"}
{/if}
</table>
{/block}