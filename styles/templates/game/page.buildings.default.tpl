{block name="title" prepend}{$LNG.lm_buildings}{/block}
{block name="content"}
{if !empty($Queue)}
<div id="buildlist" class="buildlist">
	<table style="width:760px">
		{foreach $Queue as $List}
		{$ID = $List.element}
		<tr>
			<td style="width:70%;vertical-align:top;" class="left">
				{$List@iteration}.: 
				{if !($isBusy.research && ($ID == 6 || $ID == 31)) && !($isBusy.shipyard && ($ID == 15 || $ID == 21)) && $RoomIsOk && $CanBuildElement && $BuildInfoList[$ID].buyable}
				<form class="build_form" action="game.php?page=buildings" method="post">
					<input type="hidden" name="cmd" value="insert">
					<input type="hidden" name="building" value="{$ID}">
					<button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if $List.destroy} {$LNG.bd_dismantle}{/if}</button>
				</form>
				{else}{$LNG.tech.{$ID}} {$List.level} {if $List.destroy}{$LNG.bd_dismantle}{/if}{/if}
				{if $List@first}
				<br><br><div id="progressbar" data-time="{$List.resttime}"></div>
			</td>
			<td>
				<div id="time" data-time="{$List.time}"><br></div>
				<form action="game.php?page=buildings" method="post" class="build_form">
					<input type="hidden" name="cmd" value="cancel">
					<button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
				</form>
				{else}
			</td>
			<td>
				<form action="game.php?page=buildings" method="post" class="build_form">
					<input type="hidden" name="cmd" value="remove">
					<input type="hidden" name="listid" value="{$List@iteration}">
					<button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
				</form>
				{/if}
				<br><span style="color:lime" data-time="{$List.endtime}" class="timer">{$List.display}</span>
			</td>
		</tr>
	{/foreach}
	</table>
</div>
{/if}

	{foreach $BuildInfoList as $ID => $Element}
	<div style="position:relative; border-radius: 5px; width:172px; margin:5px; border:1px solid #000; height:324px; float:left; background:rgba(0,0,0,0.95);">
		<div  onclick="return Dialog.info({$ID})" style=" width:172px; height:172px; background:url('{$dpath}gebaeude/{$ID}.gif'); background-size:100% 100%;"><div style="background:rgba(13, 16, 20, 0.95); width:100%;"><a class="tooltip" data-tooltip-content="
		<div style='max-width:200px;'>
		{$LNG.shortDescription.{$ID}|replace:'"':'\''}<br><br>
		{if !empty($Element.infoEnergy)}
		{$LNG.bd_next_level|replace:'"':'\''}<br>
		{$Element.infoEnergy|replace:'"':'\''}<br><br>
		{/if}
		{$LNG.bd_remaining|replace:'"':'\''}<br>
		{foreach $Element.costOverflow as $ResType => $ResCount}
		{$LNG.tech.{$ResType}|replace:'"':'\''}: 
		<span style='font-weight:700'>{$ResCount|number}</span><br>
						{/foreach} </div>" style="font-weight:bold; font-size:1.1em;" href="#" onclick="return Dialog.info({$ID})">{$LNG.tech.{$ID}}<br>{if $Element.level > 0}({$LNG.bd_lvl} {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if}){else}<br>{/if}</a></div></div>
		

		
	
		<div>{$LNG.fgf_time}:<span style="float:right;">{$Element.elementTime|time}</span></div>
									
				
					<div style="; height:100px; text-align:right;" >
					<span>{foreach $Element.costResources as $RessID => $RessAmount}
					<b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number}</span></b> <img src="{$dpath}images/{$RessID}.gif" alt="{$LNG.tech.{$RessID}}" width="20" height="20"><br>
					{/foreach}</span>
					</div>
						<div style="position:absolute;  top:156px; left:0px;">		{if $Element.level > 0}
							{if $ID == 43}<a href="#" onclick="return Dialog.info({$ID})">{$LNG.bd_jump_gate_action}</a>{/if}
							{if ($ID == 44 && !$HaveMissiles) ||  $ID != 44}<div style="background:url('{$dpath}pic/abort.gif'); width:16px; height:16px; " class="tooltip_sticky" data-tooltip-content="
								{* Start Destruction Popup *}
								<div style='width:300px'>
									<div>
										<th colspan='2'>{$LNG.bd_price_for_destroy} {$LNG.tech.{$ID}} {$Element.level}</th>
									</div>
									{foreach $Element.destroyResources as $ResType => $ResCount}
									<div>
										{$LNG.tech.{$ResType}}
										<span style='color:{if empty($Element.destroyOverflow[$RessID])}lime{else}red{/if}'>{$ResCount|number}</span>
									</div>
									{/foreach}
									<div>
										{$LNG.bd_destroy_time}
										{$Element.destroyTime|time}
									</div>
									<div>

											<form action='game.php?page=buildings' method='post' class='build_form'>
												<input type='hidden' name='cmd' value='destroy'>
												<input type='hidden' name='building' value='{$ID}'>
												<button type='submit' class='build_submit onlist'>{$LNG.bd_dismantle}</button>
											</form>
										
									</div>
								</div>
								{* End Destruction Popup *}
								"></div>{/if}
						{else}
							&nbsp;
						{/if}	
</div>						
<div style="text-align:center; border:1px solid #000; border-radius: 5px; height:34px;
					{if $Element.maxLevel == $Element.levelToBuild}
						 background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{$LNG.bd_maxlevel}</span>
					{elseif ($isBusy.research && ($ID == 6 || $ID == 31)) || ($isBusy.shipyard && ($ID == 15 || $ID == 21))}
						 background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{$LNG.bd_working}</span>
					{else}
						{if $RoomIsOk}
							{if $CanBuildElement && $Element.buyable}
							 background:green;"><form action="game.php?page=buildings" method="post" class="build_form">
								<input type="hidden" name="cmd" value="insert">
								<input type="hidden" name="building" value="{$ID}">
								<button type="submit" class="build_submit" style="color:#fff; font-weight:bold; ">{if $Element.level == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</button>
							</form>
							{else}
							background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{if $Element.level == 0}{$LNG.bd_build}{else}{$LNG.bd_build_next_level}{$Element.levelToBuild + 1}{/if}</span>
							{/if}
						{else}
						background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{$LNG.bd_no_more_fields}</span>
						{/if}
					{/if}
										
</div>
	</div>
	{/foreach}

{/block}