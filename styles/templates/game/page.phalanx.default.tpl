{block name="title" prepend}{$LNG.gl_phalanx}{/block}
{block name="content"}
<table width="90%">
<tr>
    <th colspan="4">{$LNG.px_scan_position} [{$galaxy}:{$system}:{$planet}] ({$name})</th>
</tr>
<tr>
    <th colspan="4">{$LNG.px_fleet_movement}</th>
</tr>
<tr>
    <th colspan="1">{$LNG.px_remaining_time}</th>
    <th colspan="1">{$LNG.px_fleet_arrival}</th>
    <th colspan="1">{$LNG.px_fleet_message}</th>
</tr>
{foreach $fleetTable as $index => $fleet}
	<tr>
		<td id="fleettime_{$index}" class="fleets" data-fleet-end-time="{$fleet.returntime}" data-fleet-time="{$fleet.resttime}">00:00:00</td>
		<td>{_date($LNG['php_tdformat'],$fleet.returntime)}</td>
		<td>{$fleet.text}</td>
	</tr>
{foreachelse}
	<tr><td colspan="2">{$LNG.px_no_fleet}</td></tr>
{/foreach}
</table>
{/block}