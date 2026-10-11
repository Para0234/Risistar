{block name="title" prepend}{$LNG.lm_notes}{/block}
{block name="content"}
<form class="notes-form" action="?page=notes&amp;mode=insert" method="post">
	<input type="hidden" name="id" value="{$noteDetail.id}">
	<table class="notes-edit">
		<tr class="notes-title">
			<th colspan="2">{if $noteDetail.id == 0}{$LNG.nt_create_note}{else}{$LNG.nt_edit_note}{/if}</th>
		</tr>
		<tr>
			<td class="notes-label"><label for="priority">{$LNG.nt_priority}</label></td>
			<td class="notes-field">
				{html_options id=priority name=priority options=$PriorityList selected=$noteDetail.priority}
			</td>
		</tr>
		<tr>
			<td class="notes-label"><label for="title">{$LNG.nt_subject_note}</label></td>
			<td class="notes-field">
				<input type="text" id="title" name="title" size="30" maxlength="30" value="{$noteDetail.title}">
			</td>
		</tr>
		<tr>
			<td class="notes-label"><label for="text">{$LNG.nt_note}</label> (<span id="cntChars">0</span>&nbsp;/&nbsp;5.000&nbsp;{$LNG.nt_characters})</td>
			<td class="notes-field">
				<textarea name="text" id="text" cols="60" rows="10" onkeyup="$('#cntChars').text($(this).val().length);">{$noteDetail.text}</textarea>
			</td>
		</tr>
		<tr class="notes-actions">
			<td class="notes-label"><a href="game.php?page=notes">{$LNG.nt_back}</a></td>
			<td class="notes-field notes-buttons">
				<input type="reset" value="{$LNG.nt_reset}">
				<input type="submit" value="{$LNG.nt_save}">
			</td>
		</tr>
	</table>
</form>
{/block}
