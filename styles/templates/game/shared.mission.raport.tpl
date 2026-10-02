{block name="title" prepend}{$pageTitle}{/block}
{block name="content"}
<div class="combat-report">
{if !empty($Info)}
	<div class="combat-report-vs">
		<div class="combat-report-vs-side{if $Raport.result == 'a'} is-win{elseif $Raport.result == 'r'} is-lose{/if}">{$Info[0]}</div>
		<div class="combat-report-vs-label">VS</div>
		<div class="combat-report-vs-side{if $Raport.result == 'r'} is-win{elseif $Raport.result == 'a'} is-lose{/if}">{$Info[1]}</div>
	</div>
{/if}
	<p class="combat-report-intro">
		<span class="combat-report-intro-title">{if $Raport.mode == 1}{$LNG.sys_destruc_title}{else}{$LNG.sys_attack_title}{/if}</span>
		<span class="combat-report-intro-time">{$Raport.time}</span>
	</p>
{foreach $Raport.rounds as $Round => $RoundInfo}
	<details class="combat-round"{if $RoundInfo@first || $RoundInfo@last} open{/if}>
		<summary class="combat-round-title">
			<span class="combat-round-label">{if $RoundInfo@last && !$RoundInfo@first}{$LNG.sys_attack_after}{else}{$LNG.sys_attack_round} {$RoundInfo@iteration}{/if}</span>
			{if !$RoundInfo@last && isset($RoundInfo.info)}
			<span class="combat-round-fire-mini">
				<span class="is-att"><span class="combat-round-fire-label">{$LNG.sys_round_damage_att}</span> <span class="combat-round-fire-value">{$RoundInfo.info[0]|number}</span></span>
				<span class="is-def"><span class="combat-round-fire-label">{$LNG.sys_round_damage_def}</span> <span class="combat-round-fire-value">{$RoundInfo.info[2]|number}</span></span>
			</span>
			{/if}
		</summary>
		<div class="combat-round-body">
			{if !empty($RoundInfo.shipIds)}
			<div class="combat-compare-scroll">
				<table class="combat-compare{if (($RoundInfo.attacker|@count) + ($RoundInfo.defender|@count)) > 4} is-crowded{elseif ($RoundInfo.attacker|@count) == 1 && ($RoundInfo.defender|@count) == 1} is-duel{/if}">
					<colgroup>
						<col class="combat-col-label is-start">
						{foreach $RoundInfo.attacker as $Player}
						<col class="combat-col-att">
						{/foreach}
						{foreach $RoundInfo.defender as $Player}
						<col class="combat-col-def">
						{/foreach}
						<col class="combat-col-label is-end">
					</colgroup>
					<thead>
						<tr>
							<th class="combat-label-col is-start"></th>
							<th class="is-att" colspan="{$RoundInfo.attacker|@count}">{$LNG.sys_attack_attacker_pos}</th>
							<th class="is-def" colspan="{$RoundInfo.defender|@count}">{$LNG.sys_attack_defender_pos}</th>
							<th class="combat-label-col is-end"></th>
						</tr>
						<tr>
							<th class="combat-label-col is-start"><span class="combat-ship-label">{$LNG.sys_ship_type}</span></th>
							{foreach $RoundInfo.attacker as $Player}
							<th class="is-att">{include file="shared.mission.raport.player.tpl" Player=$Player Role="attacker"}</th>
							{/foreach}
							{foreach $RoundInfo.defender as $Player}
							<th class="is-def">{include file="shared.mission.raport.player.tpl" Player=$Player Role="defender"}</th>
							{/foreach}
							<th class="combat-label-col is-end"><span class="combat-ship-label">{$LNG.sys_ship_type}</span></th>
						</tr>
					</thead>
					<tbody>
						{foreach $RoundInfo.shipIds as $ShipID}
						<tr>
							<th class="combat-label-col is-start{if empty($RoundInfo.attackLabels[$ShipID])} is-defense{/if}" scope="row">
								<span class="combat-ship">
									<img class="combat-ship-icon" src="{$dpath}gebaeude/{$ShipID}.gif" alt="">
									<span class="combat-ship-label" title="{$LNG.shortNames.{$ShipID}}">{$LNG.shortNames.{$ShipID}}</span>
								</span>
							</th>
							{foreach $RoundInfo.attacker as $Player}
							{if !empty($Player.ships[$ShipID][0])}
							<td class="is-att">{$Player.ships[$ShipID][0]|number}</td>
							{else}
							<td class="is-att is-empty">&ndash;</td>
							{/if}
							{/foreach}
							{foreach $RoundInfo.defender as $Player}
							{if !empty($Player.ships[$ShipID][0])}
							<td class="is-def">{$Player.ships[$ShipID][0]|number}</td>
							{else}
							<td class="is-def is-empty">&ndash;</td>
							{/if}
							{/foreach}
							<td class="combat-label-col is-end" aria-hidden="true">
								<span class="combat-ship">
									<img class="combat-ship-icon" src="{$dpath}gebaeude/{$ShipID}.gif" alt="">
									<span class="combat-ship-label" title="{$LNG.shortNames.{$ShipID}}">{$LNG.shortNames.{$ShipID}}</span>
								</span>
							</td>
						</tr>
						{/foreach}
					</tbody>
				</table>
			</div>
			{/if}
		</div>
	</details>
{/foreach}
	<section class="combat-result">
		{if $Raport.result == "a"}
		<p class="combat-result-winner is-win">{$LNG.sys_attacker_won}</p>
		<p class="combat-result-line">{$LNG.sys_stealed_ressources} {foreach $Raport.steal as $elementID => $amount}{$amount|number} {$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.steal)} {$LNG.sys_and} {elseif !$amount@last}, {/if}{/foreach}</p>
		{elseif $Raport.result == "r"}
		<p class="combat-result-winner is-lose">{$LNG.sys_defender_won}</p>
		{else}
		<p class="combat-result-winner">{$LNG.sys_both_won}</p>
		{/if}
		<p class="combat-result-line">{$LNG.sys_attacker_lostunits} {$Raport['units'][0]|number} {$LNG.sys_units}</p>
		<p class="combat-result-line">{$LNG.sys_defender_lostunits} {$Raport['units'][1]|number} {$LNG.sys_units}</p>
		<p class="combat-result-line">{$LNG.debree_field_1} {foreach $Raport.debris as $elementID => $amount}{$amount|number} {$LNG.tech.$elementID}{if ($amount@index + 2) == count($Raport.debris)} {$LNG.sys_and} {elseif !$amount@last}, {/if}{/foreach}{$LNG.debree_field_2}</p>
		{if $Raport.mode == 1}
		<div class="combat-result-moon">
			{if $Raport.moon.moonDestroySuccess == -1}
			<p class="combat-result-line">{$LNG.sys_destruc_stop}</p>
			{else}
			<p class="combat-result-line">{sprintf($LNG.sys_destruc_lune, "{$Raport.moon.moonDestroyChance}")}<br>{$LNG.sys_destruc_mess1}
				{if $Raport.moon.moonDestroySuccess == 1}
				{$LNG.sys_destruc_reussi}
				{elseif $Raport.moon.moonDestroySuccess == 0}
				{$LNG.sys_destruc_null}
				{/if}
			</p>
			<p class="combat-result-line">{sprintf($LNG.sys_destruc_rip, "{$Raport.moon.fleetDestroyChance}")}
				{if $Raport.moon.fleetDestroySuccess == 1}
				<br>{$LNG.sys_destruc_echec}
				{/if}
			</p>
			{/if}
		</div>
		{else}
		<p class="combat-result-line">{$LNG.sys_moonproba} {$Raport.moon.moonChance} %</p>
		{if !empty($Raport.moon.moonName)}
			{if !empty($Info)}
			<p class="combat-result-line">{sprintf($LNG.sys_moonbuilt, "{$Raport.moon.moonName}", "XX", "XX", "XX")}</p>
			{else}
			<p class="combat-result-line">{sprintf($LNG.sys_moonbuilt, "{$Raport.moon.moonName}", "{$Raport.koords[0]}", "{$Raport.koords[1]}", "{$Raport.koords[2]}")}</p>
			{/if}
		{/if}
		{/if}
		{if !empty($Raport.additionalInfo)}
		<div class="combat-result-extra">{$Raport.additionalInfo}</div>
		{/if}
	</section>
	<div class="combat-report-close-wrap">
		<button type="button" class="combat-report-close" id="combatReportClose">{$LNG.sys_raport_close}</button>
	</div>
</div>
<script>
$(function () {
	if (window.matchMedia('(min-width: 641px)').matches) {
		$('details.combat-round').prop('open', true);
	}
	$('#combatReportClose').on('click', function () {
		window.close();
		window.setTimeout(function () {
			if (window.history.length > 1) {
				window.history.back();
				return;
			}
			window.location.href = 'game.php';
		}, 120);
	});
});
</script>
{/block}
