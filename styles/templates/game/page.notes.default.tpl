{block name="title" prepend}{$LNG.lm_notes}{/block}
{block name="content"}
<form class="notes-form" action="game.php?page=notes&amp;mode=delete" method="post">
	<table class="notes-list">
		<tr class="notes-title">
			<th colspan="4">{$LNG.nt_notes}</th>
		</tr>
		<tr class="notes-create">
			<td colspan="4"><a href="game.php?page=notes&amp;mode=detail">{$LNG.nt_create_new_note}</a></td>
		</tr>
		<tr class="notes-cols">
			<th class="notes-check">{$LNG.nt_dlte_note}</th>
			<th class="notes-date">{$LNG.nt_date_note}</th>
			<th class="notes-subject">{$LNG.nt_subject_note}</th>
			<th class="notes-size">{$LNG.nt_size_note}</th>
		</tr>
		{foreach $notesList as $notesID => $notesRow}
		<tr class="notes-row">
			<td class="notes-check"><input name="delmes[{$notesID}]" type="checkbox"></td>
			<td class="notes-date"><span class="notes-meta-label">{$LNG.nt_date_note}</span>{$notesRow.time}</td>
			<td class="notes-subject"><a href="game.php?page=notes&amp;mode=detail&amp;id={$notesID}">
			<span class="notes-priority notes-priority-{$notesRow.priority}">{$notesRow.title}</span>
			</a></td>
			<td class="notes-size"><span class="notes-meta-label">{$LNG.nt_size_note}</span>{$notesRow.size|number}</td>
		</tr>
		{if $notesRow@last}
		<tr class="notes-actions">
			<td colspan="4" class="right"><input value="{$LNG.nt_dlte_note}" type="submit"></td>
		</tr>
		{/if}
		{foreachelse}
		<tr class="notes-empty">
			<td colspan="4">{$LNG.nt_you_dont_have_notes}</td>
		</tr>
		{/foreach}
	</table>
</form>
{/block}
