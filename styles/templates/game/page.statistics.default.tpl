{block name="title" prepend}{$LNG.lm_statistics}{/block}
{block name="content"}
<div class="stats-page">
	<form class="stats-filters" name="stats" id="stats" method="post" action="" hidden>
		<label for="who">{$LNG.st_show}</label>
		<select name="who" id="who" onchange="$('#stats').submit();">{html_options options=$Selectors.who selected=$who}</select>
		<label for="type">{$LNG.st_per}</label>
		<select name="type" id="type" onchange="$('#stats').submit();">{html_options options=$Selectors.type selected=$type}</select>
		<label for="range">{$LNG.st_in_the_positions}</label>
		<select name="range" id="range" onchange="$('#stats').submit();">{html_options options=$Selectors.range selected=$range}</select>
	</form>
	<table class="stats-table{if $who == 2} is-alliance{/if}">
		<tr>
			<th class="stats-title" colspan="5">
				<span class="stats-title-text">{$LNG.st_statistics}</span>
				<span class="stats-title-date">{$LNG.st_updated}: {$stat_date}</span>
			</th>
		</tr>
		{if $who == 1}
			{include file="shared.statistics.playerTable.tpl"}
		{elseif $who == 2}
			{include file="shared.statistics.allianceTable.tpl"}
		{/if}
	</table>
</div>
{/block}
