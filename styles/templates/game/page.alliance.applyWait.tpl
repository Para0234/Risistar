{block name="title" prepend}{$LNG.lm_alliance}{/block}
{block name="content"}
<form action="game.php?page=alliance&amp;mode=cancelApply" method="post" data-rs-confirm="{$cancel_request_confirm|escape:'html'}">
	<table class="table519 alliance-table alliance-apply-wait-table">
	<tr>
		<th>{$LNG.al_your_request_title}</th>
	</tr>
	<tr>
		<td>{$request_text}</td>
	</tr>
	<tr>
		<td><input type="submit" value="{$LNG.al_cancel_request}"></td>
	</tr>
</table>
</form>
{/block}
