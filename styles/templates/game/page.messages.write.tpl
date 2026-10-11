{block name="title" prepend}{$LNG.write_message}{/block}
{block name="content"}
<form name="message" id="message" class="messages-write-form" action="">
	<table class="messages-write">
		<tr>
			<th colspan="2">{$LNG.mg_send_new}</th>
		</tr>
		<tr>
			<td class="messages-write-label">{$LNG.mg_send_to}</td>
			<td class="messages-write-field"><input type="text" name="to" size="40" value="{$OwnerRecord.username} [{$OwnerRecord.galaxy}:{$OwnerRecord.system}:{$OwnerRecord.planet}]"></td>
		</tr>
		<tr>
			<td class="messages-write-label">{$LNG.mg_subject}</td>
			<td class="messages-write-field"><input type="text" name="subject" id="subject" size="40" maxlength="40" value="{if !empty($subject)}{$subject}{else}{$LNG.mg_no_subject}{/if}"></td>
		</tr>
		<tr>
			<td class="messages-write-label">{$LNG.mg_message}<br>(<span id="cntChars">0</span>&nbsp;/&nbsp;5.000&nbsp;{$LNG.mg_characters})</td>
			<td class="messages-write-field"><textarea name="text" id="text" cols="40" rows="10" onkeyup="$('#cntChars').text($(this).val().length);"></textarea></td>
		</tr>
		<tr>
			<td class="messages-write-submit" colspan="2"><input id="submit" type="button" onClick="check();" name="button" value="{$LNG.mg_send}"></td>
		</tr>
	</table>
</form>
{/block}
{block name="script" append}
<script type="text/javascript">
function check(){
	if($('#text').val().length == 0) {
		RisistarDialog.alert('{$LNG.mg_empty_text|escape:'javascript'}');
		return false;
	} else {
		$('submit').attr('disabled','disabled');
		$.post('game.php?page=messages&mode=send&id={$id}&ajax=1', $('#message').serialize(), function(data) {
			RisistarDialog.alert(data).then(function() {
				parent.$.fancybox.close();
			});
		}, 'json');
	}
}
</script>
{/block}
