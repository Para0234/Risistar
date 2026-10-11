{block name="title" prepend}{$LNG.lm_resources}{/block}
{block name="content"}
{function name=res_qty n=0 kind='prod'}
<td class="empire-qty{if $kind == 'cap'} is-cap{elseif $n > 0} is-plus{elseif $n < 0} is-minus{/if}" data-value="{$n}" title="{$n|number}">{$n|number}</td>
{/function}
{function name=res_label text='' extra=''}
<td class="spy-label-cell" title="{$text}{if $extra} ({$extra}){/if}">
	<span class="spy-label-text">{$text}</span>
	{if $extra}<span class="resources-level">{$extra}</span>{/if}
</td>
{/function}
<div class="empire-page resources-page">
<form class="resources-form" action="?page=resources" method="post">
<input type="hidden" name="mode" value="send">
<div class="empire-wrap">
<table class="empire-table resources-table">
	<thead>
		<tr>
			<th colspan="6"><span class="empire-sticky-title">{$header}</span></th>
		</tr>
		<tr class="empire-colhead">
			<th class="empire-settings-cell">
				<button type="button" class="empire-settings-toggle" id="empireSettingsToggle" aria-expanded="false" aria-haspopup="true" aria-controls="empireSettingsMenu" title="{$LNG.empire_settings|default:'Options d\'affichage'}">
					<span class="empire-settings-icon" aria-hidden="true"></span>
				</button>
			</th>
			<th class="resources-res" title="{$LNG.tech.901}">
				<img src="{$dpath}images/metal.gif" alt="">
				<span class="resources-res-name">{$LNG.tech.901}</span>
			</th>
			<th class="resources-res" title="{$LNG.tech.902}">
				<img src="{$dpath}images/crystal.gif" alt="">
				<span class="resources-res-name">{$LNG.tech.902}</span>
			</th>
			<th class="resources-res" title="{$LNG.tech.903}">
				<img src="{$dpath}images/deuterium.gif" alt="">
				<span class="resources-res-name">{$LNG.tech.903}</span>
			</th>
			<th class="resources-res" title="{$LNG.tech.911}">
				<img src="{$dpath}images/energy.gif" alt="">
				<span class="resources-res-name">{$LNG.tech.911}</span>
			</th>
			<th class="resources-factor-head">
				<span class="resources-factor-head-full">{$LNG.rs_factor|default:'Facteur de production'}</span>
				<span class="resources-factor-head-short">
					<span>%</span>
					<button type="button" class="resources-factor-help" aria-expanded="false" aria-label="{$LNG.rs_factor_help_label|escape:'html'}" data-help="{$LNG.rs_factor_help|escape:'html'}">?</button>
				</span>
			</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			{res_label text="{$LNG.rs_basic_income}"}
			{res_qty n=$basicProduction.901}
			{res_qty n=$basicProduction.902}
			{res_qty n=$basicProduction.903}
			{res_qty n=$basicProduction.911}
			<td class="resources-factor is-empty"></td>
		</tr>
		{foreach $productionList as $productionID => $productionRow}
		{if $productionID > 200}
			{capture assign=resExtra}{$LNG.rs_amount} {$productionRow.elementLevel}{/capture}
		{else}
			{capture assign=resExtra}{$LNG.rs_lvl} {$productionRow.elementLevel}{/capture}
		{/if}
		{assign var=resFactor value=$productionRow.prodLevel}
		{assign var=resEnergy value=$productionRow.production.911}
		<tr>
			{res_label text="{$LNG.tech.$productionID}" extra=$resExtra}
			{res_qty n=$productionRow.production.901}
			{res_qty n=$productionRow.production.902}
			{res_qty n=$productionRow.production.903}
			{res_qty n=$productionRow.production.911}
			<td class="resources-factor{if $resEnergy > 0} is-energy-plus{elseif $resEnergy < 0} is-energy-minus{/if}">
				<input type="hidden" name="prod[{$productionID}]" value="{$resFactor}">
				<button type="button" class="resources-fader" data-level="{$resFactor}" aria-label="{$LNG.rs_factor|default:'Facteur de production'}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{$resFactor*10}" title="{$LNG.rs_factor|default:'Facteur de production'}">
					<span class="resources-fader-value">{$resFactor*10}%</span>
					<span class="resources-fader-grip" aria-hidden="true"></span>
				</button>
				<label class="resources-slider">
					<input type="range" class="resources-factor-range" min="0" max="10" step="1" value="{$resFactor}" style="--factor: {$resFactor*10}%" aria-label="{$LNG.rs_factor|default:'Facteur de production'}">
					<span class="resources-slider-value">{$resFactor*10}%</span>
				</label>
			</td>
		</tr>
		{/foreach}
		<tr class="resources-summary">
			{res_label text="{$LNG.rs_ress_bonus}"}
			{res_qty n=$bonusProduction.901}
			{res_qty n=$bonusProduction.902}
			{res_qty n=$bonusProduction.903}
			{res_qty n=$bonusProduction.911}
			<td class="resources-factor is-empty"></td>
		</tr>
		<tr class="resources-summary">
			{res_label text="{$LNG.rs_storage_capacity}"}
			{res_qty n=$storage.901 kind='cap'}
			{res_qty n=$storage.902 kind='cap'}
			{res_qty n=$storage.903 kind='cap'}
			<td class="resources-empty">-</td>
			<td class="resources-factor is-empty"></td>
		</tr>
		<tr class="resources-summary">
			{res_label text="{$LNG.rs_sum}"}
			{res_qty n=$totalProduction.901}
			{res_qty n=$totalProduction.902}
			{res_qty n=$totalProduction.903}
			{res_qty n=$totalProduction.911}
			<td class="resources-factor is-empty"></td>
		</tr>
		<tr class="resources-summary">
			{res_label text="{$LNG.rs_daily}"}
			{res_qty n=$dailyProduction.901}
			{res_qty n=$dailyProduction.902}
			{res_qty n=$dailyProduction.903}
			{res_qty n=$dailyProduction.911}
			<td class="resources-factor is-empty"></td>
		</tr>
		<tr class="resources-summary">
			{res_label text="{$LNG.rs_weekly}"}
			{res_qty n=$weeklyProduction.901}
			{res_qty n=$weeklyProduction.902}
			{res_qty n=$weeklyProduction.903}
			{res_qty n=$weeklyProduction.911}
			<td class="resources-factor is-empty"></td>
		</tr>
	</tbody>
</table>
</div>
<div class="resources-actions">
	<input value="{$LNG.rs_calculate}" type="submit">
</div>
</form>
<div id="empireSettingsMenu" class="empire-settings-menu" hidden>
	<label><input type="checkbox" id="empireOptCompact"> {$LNG.compact_option|default:'Nombres compactés'}</label>
</div>
</div>
{/block}
