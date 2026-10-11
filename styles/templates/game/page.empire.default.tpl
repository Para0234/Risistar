{block name="title" prepend}{$LNG.lm_empire}{/block}
{block name="content"}
{function name=empire_qty n=0}
<td class="empire-qty" data-value="{$n}" title="{$n|number}">{$n|number}</td>
{/function}
{function name=empire_label text=''}
<td class="spy-label-cell" title="{$text}">
	<span class="spy-label-text">{$text}</span>
</td>
{/function}
<div class="empire-page">
<div class="empire-wrap">
<table class="empire-table" style="--empire-preferred-width: {$colspan*140 + 80}px">
	<thead>
		<tr>
			<th colspan="{$colspan}"><span class="empire-sticky-title">{$LNG.lv_imperium_title}</span></th>
		</tr>
		<tr class="empire-colhead">
			<th class="empire-settings-cell">
				<button type="button" class="empire-settings-toggle" id="empireSettingsToggle" aria-expanded="false" aria-haspopup="true" aria-controls="empireSettingsMenu" title="{$LNG.empire_settings|default:'Options d\'affichage'}">
					<span class="empire-settings-icon" aria-hidden="true"></span>
				</button>
			</th>
			<th style="font-size: 50px;">&Sigma;</th>
			{foreach $planetList.image as $planetID => $image}
			<th><a href="game.php?page=overview&amp;cp={$planetID}"><img class="empire-planet-thumb" width="80" height="80" border="0" src="{$dpath}planeten/small/s_{$image}.jpg"></a></th>
			{/foreach}
		</tr>
		<tr class="empire-colhead">
			<th>{$LNG.lv_name}</th>
			<th>{$LNG.lv_total}</th>
			{foreach $planetList.name as $name}
				<th class="empire-planet-name" title="{$name}"><span class="spy-planet-label">{$name}</span></th>
			{/foreach}
		</tr>
	</thead>
	<tbody>
		<tr>
			{empire_label text="{$LNG.lv_coords}"}
			<td>-</td>
			{foreach $planetList.coords as $coords}
				<td><a href="game.php?page=galaxy&amp;galaxy={$coords.galaxy}&amp;system={$coords.system}">[{$coords.galaxy}:{$coords.system}:{$coords.planet}]</a></td>
			{/foreach}
		</tr>
		<tr>
			{empire_label text="{$LNG.lv_fields}"}
			<td>-</td>
			{foreach $planetList.field as $field}
				<td>{$field.current} / {$field.max}</td>
			{/foreach}
		</tr>
	</tbody>
	<tbody class="empire-section" data-section="resources">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.lv_resources}
				</button>
			</th>
		</tr>
		{foreach $planetList.resource as $elementID => $resourceArray}
		<tr>
			{empire_label text="{$LNG.tech.$elementID}"}
			{empire_qty n=$resourceArray|@array_sum}
			{foreach $resourceArray as $planetID => $resource}
				{empire_qty n=$resource}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
	<tbody class="empire-section" data-section="buildings">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.lv_buildings}
				</button>
			</th>
		</tr>
		{foreach $planetList.build as $elementID => $buildArray}
		<tr{if array_sum($buildArray) == 0} class="is-zero"{/if}>
			{empire_label text="{$LNG.tech.$elementID}"}
			{empire_qty n=$buildArray|@array_sum}
			{foreach $buildArray as $planetID => $build}
				{empire_qty n=$build}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
	<tbody class="empire-section" data-section="tech">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.lv_technology}
				</button>
			</th>
		</tr>
		{foreach $planetList.tech as $elementID => $tech}
		<tr{if $tech == 0} class="is-zero"{/if}>
			{empire_label text="{$LNG.tech.$elementID}"}
			{empire_qty n=$tech}
			{foreach $planetList.name as $name}
				{empire_qty n=$tech}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
	<tbody class="empire-section" data-section="ships">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.lv_ships}
				</button>
			</th>
		</tr>
		{foreach $planetList.fleet as $elementID => $fleetArray}
		<tr{if array_sum($fleetArray) == 0} class="is-zero"{/if}>
			{empire_label text="{$LNG.tech.$elementID}"}
			{empire_qty n=$fleetArray|@array_sum}
			{foreach $fleetArray as $planetID => $fleet}
				{empire_qty n=$fleet}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
	<tbody class="empire-section" data-section="defenses">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.lv_defenses}
				</button>
			</th>
		</tr>
		{foreach $planetList.defense as $elementID => $fleetArray}
		<tr{if array_sum($fleetArray) == 0} class="is-zero"{/if}>
			{empire_label text="{$LNG.tech.$elementID}"}
			{empire_qty n=$fleetArray|@array_sum}
			{foreach $fleetArray as $planetID => $fleet}
				{empire_qty n=$fleet}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
</table>
</div>
<div id="empireSettingsMenu" class="empire-settings-menu" hidden>
	<div class="empire-settings-bar">
		<button type="button" class="empire-settings-close" aria-label="{$LNG.spy_filter_close}"></button>
	</div>
	<div class="empire-settings-body">
	<label><input type="checkbox" id="empireOptZeros"> {$LNG.zero_show|default:'Afficher les lignes à 0'}</label>
	<label><input type="checkbox" id="empireOptCompact"> {$LNG.compact_option|default:'Nombres compactés'}</label>
	</div>
</div>
</div>
{/block}
