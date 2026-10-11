{block name="title" prepend}{$LNG.lm_messages}{/block}
{block name="content"}
<div class="messages-page">
	<table class="messages-cats">
		<tr>
			<th>{$LNG.mg_overview}<span id="loading" style="display:none;"> ({$LNG.loading})</span></th>
		</tr>
		<tr>
			<td class="messages-cats-cell">
				<div class="messages-cats-grid">
					{foreach $CategoryList as $CategoryID => $CategoryRow}
					<a class="messages-cat" href="#" onclick="Message.getMessages({$CategoryID});return false;" style="color:{$CategoryRow.color};">
						<span class="messages-cat-name">{$LNG.mg_type.{$CategoryID}}</span>
						<span class="messages-cat-counts"><span id="unread_{$CategoryID}">{$CategoryRow.unread}</span>/<span id="total_{$CategoryID}">{$CategoryRow.total}</span></span>
					</a>
					{/foreach}
				</div>
			</td>
		</tr>
	</table>
	<table class="messages-ops">
		<tr>
			<th>{$LNG.mg_game_operators}</th>
		</tr>
		{foreach $OperatorList as $OperatorName => $OperatorEmail}
		<tr>
			<td>{$OperatorName}<a href="mailto:{$OperatorEmail}" title="{$LNG.mg_write_mail_to_ops} {{$OperatorName}}"><img src="{$dpath}img/m.gif" alt=""></a></td>
		</tr>
		{/foreach}
	</table>
</div>
{/block}
{block name="script" append}
{if !empty($category)}
<script>$(function() {
	Message.getMessages({$category}, {$side});
})</script>
{/if}
{/block}
