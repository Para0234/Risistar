{block name="title" prepend}{$LNG.lm_research}{/block}
{block name="content"}
{if !empty($Queue)}
<div id="buildlist" class="buildlist">
	<table style="width:100%;max-width:760px">
		{foreach $Queue as $List}
		{$ID = $List.element}
		<tr>
			<td style="width:70%;vertical-align:top;" class="left">
				{if isset($ResearchList[$List.element])}
				{$CQueue = $ResearchList[$List.element]}
				{/if}
				{$List@iteration}.: 
				{if isset($CQueue) && $CQueue.maxLevel != $CQueue.level && !$IsFullQueue && $CQueue.buyable}
				<form action="game.php?page=research" method="post" class="build_form">
					<input type="hidden" name="cmd" value="insert">
					<input type="hidden" name="tech" value="{$ID}">
					<button type="submit" class="build_submit onlist">{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}</button>
				</form>
				{else}
				{$LNG.tech.{$ID}} {$List.level}{if !empty($List.planet)} @ {$List.planet}{/if}
				{/if}
				{if $List@first}
				<br><br><div id="progressbar" data-time="{$List.resttime}"></div>
			</td>
			<td>
				<div id="time" data-time="{$List.time}"><br></div>
				<form action="game.php?page=research" method="post" class="build_form">
					<input type="hidden" name="cmd" value="cancel">
					<button type="submit" class="build_submit onlist">{$LNG.bd_cancel}</button>
				</form>
				{else}
			</td>
			<td>
				<form action="game.php?page=research" method="post" class="build_form">
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
{if $IsLabinBuild}<table width="70%" id="infobox" style="border: 2px solid red; text-align:center;background:transparent"><tr><td>{$LNG.bd_building_lab}</td></tr></table><br><br>{/if}

<div class="build-grid">
	{foreach $ResearchList as $ID => $Element}
	<div class="build-card" style="position:relative; border-radius: 5px; width:172px; margin:5px; border:1px solid #000; height:324px; overflow:hidden; float:left; background:rgba(0,0,0,0.95);">
		<div class="build-card-art" onclick="return Dialog.info({$ID})" style=" width:172px; height:172px; background:url('{$dpath}gebaeude/{$ID}.gif'); background-size:100% 100%;"><div style="background:rgba(13, 16, 20, 0.95); border-bottom: 1px solid #000; padding-left:2px; width:100%;"><a class="tooltip" data-tooltip-content="<div style='max-width:200px;'>{$LNG.shortDescription.{$ID}|replace:'"':'\''}<br><br>						{$LNG.bd_remaining|replace:'"':'\''}<br>
						{foreach $Element.costOverflow as $ResType => $ResCount}
						{$LNG.tech.{$ResType}|replace:'"':'\''}: <span style='font-weight:700'>{$ResCount|number:($ResType == 921)}</span><br>
						{/foreach} </div>" style="font-weight:bold; font-size:1.1em;" href="#" onclick="return Dialog.info({$ID})">
		{$LNG.tech.{$ID}}</a></div></div>

		<div class="build-card-time">{$LNG.fgf_time}:<span style="float:right;">{$Element.elementTime|time}</span></div>
		<div class="build-card-meta">
			<div class="build-card-costs" style="; height:100px; text-align:right;" >
					<span>{foreach $Element.costResources as $RessID => $RessAmount}
					<b><span style="color:{if $Element.costOverflow[$RessID] == 0}lime{else}red{/if}">{$RessAmount|number:($RessID == 921)}</span></b> <img src="{$dpath}images/{$RessID}.gif" alt="{$LNG.tech.{$RessID}}" width="20" height="20"><br>
					{/foreach}</span>
					</div>
			{if $Element.level != 0}<div class="build-card-subwrap"><span class="build-card-sub"><span class="build-card-sub-full">{$LNG.bd_lvl}</span><span class="build-card-sub-short">{$LNG.bd_lvl_short}</span> {$Element.level}{if $Element.maxLevel != 255}/{$Element.maxLevel}{/if}</span></div>{/if}
		</div>

					
					<div class="build-card-action" style="text-align:center; border:1px solid #000; border-radius: 5px; height:34px;
					{if $Element.maxLevel == $Element.levelToBuild}
						 background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{$LNG.bd_maxlevel}</span>
						 {elseif $IsLabinBuild || $IsFullQueue || !$Element.buyable}
						background-color: rgba(200, 0, 0, 1);"><span style="color:#fff">{if $Element.level == 0}{$LNG.bd_tech}{else}{$LNG.bd_tech_next_level}{$Element.levelToBuild + 1}{/if}</span>
					{else}
						background:green;">						<form action="game.php?page=research" method="post" class="build_form">
							<input type="hidden" name="cmd" value="insert">
							<input type="hidden" name="tech" value="{$ID}">
								<button type="submit" class="build_submit" style="color:#fff; font-weight:bold; ">{if $Element.level == 0}{$LNG.bd_tech}{else}{$LNG.bd_tech_next_level}{$Element.levelToBuild + 1}{/if}</button>
							</form>
							
					{/if}
										
</div>
	</div>
					
					
			
	
	{/foreach}
</div>
{/block}
{block name="script" append}
    {if !empty($Queue)}
        <script src="scripts/game/research.js"></script>
    {/if}
{/block}