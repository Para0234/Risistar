{block name="title" prepend}{$LNG.lm_technology}{/block}
{block name="content"}
<style>
#tabse{
	width:100%;
	text-align: center;
}
.active{
	background:green;
	font-size: 1.2em;
	font-weight: bold;
	display:inline; 
	border:1px solid #000; 
	font-size: 1.2em; 
	padding:4px 5px; 
	cursor:default; 
}
.inactive{
cursor:pointer; 
display:inline; 
border:1px solid #000; 
font-size: 1.2em; 
padding:4px 5px; 
background:rgba(13, 16, 20, 0.95);
}
.inactivetab{
	display:none;
}
.activetab{
	display:block;
	width:100%
}
</style>
<div style="min-width:569px;width:569px; margin: auto;">
<div id="tabse">
{foreach $TechTreeList as $elementID => $requireList}
{if $elementID == 0}

<div onclick="tabse(this,'tab{$elementID}');" id="but0" class="active">{$LNG.tech.$requireList}</div>
{else}
{if !is_array($requireList)}
<div onclick="tabse(this,'tab{$elementID}');" class="inactive">{$LNG.tech.$requireList}</div>
{/if}{/if}
{/foreach}
</div><br><br>
{foreach $TechTreeList as $elementID => $requireList}
{if $elementID == 0}
<table id="tab{$elementID}" class="activetab">
{else}
{if !is_array($requireList)}
</table><table id="tab{$elementID}" class="inactivetab">
{else}
<tr>
	<td><a href="#" onclick="return Dialog.info({$elementID})"><img src="{$dpath}gebaeude/{$elementID}.{if $elementID >=600 && $elementID <= 699}jpg{else}gif{/if}" width="50" height="50"></a></td>
	<td  style="width:30%"><a href="#" onclick="return Dialog.info({$elementID})">{$LNG.tech.$elementID}</a></td>
	<td style="width:60%">
	{if $requireList}
		{foreach $requireList as $requireID => $NeedLevel}
			<a href="#" onclick="return Dialog.info({$elementID})"><span style="color:{if $NeedLevel.own < $NeedLevel.count}red{else}lime{/if};">{$LNG.tech.$requireID} ({$LNG.tt_lvl} {min($NeedLevel.count, $NeedLevel.own)}/{$NeedLevel.count})</span></a>{if !$NeedLevel@last}<br>{/if}
		{/foreach}
	{/if}
	</td>
</tr>{/if}{/if}
{/foreach}
</table></div><script>
var aclass = document.getElementById('but0');
var atab = document.getElementById('tab0');
function tabse(geklickt,etab){
	aclass.className ="inactive";
	atab.className = "inactivetab";
	aclass = geklickt;
	atab = document.getElementById(etab);	
	aclass.className = "active";
	atab.className = 'activetab';	
	}</script>
{/block}