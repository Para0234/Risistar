{function name=empire_qty n=0 class=""}
<td class="empire-qty{$class}" data-value="{$n}" title="{$n|number}">{$n|number}</td>
{/function}
<div class="empire-wrap" data-extra="{$extraParam|escape:'html'}" data-query="{$queryBase|escape:'html'}">
{if $columns|@count == 0}
<div class="spy-empty-state">
	<div class="spy-empty-tools">
		<div class="spy-header-tools">
			<button type="button" class="empire-settings-toggle" id="empireSettingsToggle" aria-expanded="false" aria-haspopup="true" aria-controls="empireSettingsMenu" title="{$LNG.empire_settings|default:'Options d\'affichage'}">
				<span class="empire-settings-icon" aria-hidden="true"></span>
			</button>
			<button type="button" class="spy-tool-toggle spy-search-toggle{if $searchActive} is-on{/if}" aria-expanded="false" aria-haspopup="true" aria-controls="spySearchMenu" title="{$LNG.spy_search}" aria-label="{$LNG.spy_search}">
				<span class="spy-search-icon" aria-hidden="true"></span>
			</button>
			<button type="button" class="spy-tool-toggle spy-sort-toggle{if $sortActive} is-on{/if}" aria-expanded="false" aria-haspopup="true" aria-controls="spySortMenu" title="{$LNG.spy_filter_sort}" aria-label="{$LNG.spy_filter_sort}">
				<span class="spy-sort-icon" aria-hidden="true"></span>
			</button>
		</div>
	</div>
	<div class="spy-empty-transmission">
		<div class="spy-empty-visual">
			<img class="spy-empty-image" src="styles/resource/images/spy-reports-empty.png" alt="">
			<span class="spy-empty-glitch spy-empty-glitch-one" aria-hidden="true"></span>
			<span class="spy-empty-glitch spy-empty-glitch-two" aria-hidden="true"></span>
			<span class="spy-empty-glitch spy-empty-glitch-three" aria-hidden="true"></span>
		</div>
	</div>
	<p class="spy-empty-label">{$LNG.spy_empty}</p>
</div>
{else}
<table class="empire-table" style="--empire-preferred-width: {$colspan*140 + 80}px">
	<thead>
		<tr>
			<th colspan="{$colspan}"><span class="empire-sticky-title">{$LNG.spy_title}</span></th>
		</tr>
		<tr class="empire-colhead spy-planet-head">
			<th class="empire-settings-cell" rowspan="2">
				<div class="spy-header-tools">
					<button type="button" class="empire-settings-toggle" id="empireSettingsToggle" aria-expanded="false" aria-haspopup="true" aria-controls="empireSettingsMenu" title="{$LNG.empire_settings|default:'Options d\'affichage'}">
						<span class="empire-settings-icon" aria-hidden="true"></span>
					</button>
					<button type="button" class="spy-tool-toggle spy-search-toggle{if $searchActive} is-on{/if}" aria-expanded="false" aria-haspopup="true" aria-controls="spySearchMenu" title="{$LNG.spy_search}" aria-label="{$LNG.spy_search}">
						<span class="spy-search-icon" aria-hidden="true"></span>
					</button>
					<button type="button" class="spy-tool-toggle spy-sort-toggle{if $sortActive} is-on{/if}" aria-expanded="false" aria-haspopup="true" aria-controls="spySortMenu" title="{$LNG.spy_filter_sort}" aria-label="{$LNG.spy_filter_sort}">
						<span class="spy-sort-icon" aria-hidden="true"></span>
					</button>
				</div>
			</th>
			{foreach $columns as $column}
			<th class="empire-planet-name{if $column.is_new} spy-col-new{/if}" data-report-id="{$column.report_id}" title="{$column.name}{if $column.planet_type == 3} ({$LNG.fcm_moon}){/if}">
				<span class="spy-planet-title">
					{if $column.planet_type == 3}<span class="spy-moon-icon" role="img" aria-label="{$LNG.fcm_moon}"></span>{/if}
					<span class="spy-planet-label">{$column.name}</span>
				</span>
				{if $column.username}<div class="spy-col-user">{$column.username}</div>{/if}
			</th>
			{/foreach}
		</tr>
		{if $columns|@count > 0}
		<tr class="empire-colhead spy-coord-head">
			{foreach $columns as $column}
			<th{if $column.is_new} class="spy-col-new"{/if}>
				<a href="game.php?page=galaxy&amp;galaxy={$column.galaxy}&amp;system={$column.system}">[{$column.galaxy}:{$column.system}:{$column.planet}]</a>
				<div class="spy-col-time" title="{$column.time}">
					<span class="spy-col-date">{$column.time_date}</span>
					<span class="spy-col-clock">{$column.time_clock}</span>
				</div>
			</th>
			{/foreach}
		</tr>
		<tr class="empire-colhead spy-actions-head">
			<th class="spy-purge-cell">
				{if $isOwnIntel}
				<div class="spy-purge">
					<button type="button" class="spy-purge-toggle" aria-haspopup="menu" aria-expanded="false">
						{$LNG.spy_purge}
						<span class="spy-history-caret" aria-hidden="true"></span>
					</button>
					<div class="spy-purge-menu" hidden role="menu" aria-label="{$LNG.spy_purge}">
						<a href="game.php?page=spyReports&amp;mode=deleteAll{if $queryBase}&amp;{$queryBase}{/if}" class="spy-purge-option spy-purge-option-delete spy-delete-all" role="menuitem" data-confirm="{$LNG.spy_delete_all_confirm}" data-confirm-label="{$LNG.spy_delete_all_yes}">
							<span class="spy-action-icon" aria-hidden="true"></span>
							<span>{$LNG.spy_delete_all}</span>
						</a>
						<a href="game.php?page=spyReports&amp;mode=deleteOld{if $queryBase}&amp;{$queryBase}{/if}{if $extraParam}&amp;extra={$extraParam}{/if}" class="spy-purge-option spy-purge-option-old spy-delete-old" role="menuitem" data-confirm="{$LNG.spy_purge_old_confirm}" data-confirm-label="{$LNG.spy_purge_old_yes}" title="{$LNG.spy_purge_old}">
							<span class="spy-action-icon" aria-hidden="true"></span>
							<span class="spy-purge-option-lines">
								<span>{$LNG.spy_purge_old_line1}</span>
								<span>{$LNG.spy_purge_old_line2}</span>
							</span>
						</a>
					</div>
				</div>
				{/if}
			</th>
			{foreach $columns as $column}
			<th class="spy-col-actions{if $column.is_new} spy-col-new{/if}">
				<div class="spy-action-row">
					<a href="{$column.attack_url}" class="spy-action spy-action-attack" title="{$LNG.type_mission_1}" aria-label="{$LNG.type_mission_1}"><span class="spy-action-icon" aria-hidden="true"></span></a>
					{if $isBattleSim && $column.sim_url}<a href="{$column.sim_url}" class="spy-action spy-action-sim" title="{$LNG.fl_simulate}" aria-label="{$LNG.fl_simulate}"><span class="spy-action-icon" aria-hidden="true"></span></a>{/if}
					<a href="game.php?page=spyReports&amp;mode=delete&amp;id={$column.report_id}{if $queryBase}&amp;{$queryBase}{/if}{if $extraParam}&amp;extra={$extraParam}{/if}" class="spy-action spy-action-delete spy-delete" data-confirm="{$LNG.spy_delete_confirm}" data-confirm-label="{$LNG.spy_delete_yes}" title="{$LNG.spy_delete}" aria-label="{$LNG.spy_delete}"><span class="spy-action-icon" aria-hidden="true"></span></a>
				</div>
			</th>
			{/foreach}
		</tr>
		{if $showSnapshotRow}
		<tr class="empire-colhead spy-snapshot-head">
			<th></th>
			{foreach $columns as $column}
			<th class="spy-col-snapshot{if $column.is_new} spy-col-new{/if}">
				{if $column.history|@count > 1}
				<div class="spy-history" data-current="{$column.report_id}" data-latest="{$column.latest_id}">
					<button type="button" class="spy-history-toggle" aria-haspopup="listbox" aria-expanded="false">
						{$LNG.spy_snapshot}
						<span class="spy-history-caret" aria-hidden="true"></span>
					</button>
					<div class="spy-history-menu" hidden role="listbox" aria-label="{$LNG.spy_snapshot}">
						{foreach $column.history as $snap}
						<button type="button" class="spy-history-option" role="option" data-id="{$snap.id}"{if $snap.selected} aria-selected="true"{/if}>{$snap.label}</button>
						{/foreach}
					</div>
				</div>
				{/if}
				{if $column.is_extra}<div><a href="{$column.hide_url}" class="spy-hide">{$LNG.spy_hide}</a></div>{/if}
			</th>
			{/foreach}
		</tr>
		{/if}
		{/if}
	</thead>
		{foreach $sections as $section}
	<tbody class="empire-section" data-section="{$section.classID}">
		<tr class="empire-section-head">
			<th colspan="{$colspan}">
				<button type="button" class="empire-section-toggle empire-sticky-title" aria-expanded="true">
					<span class="empire-section-chevron" aria-hidden="true"></span>
					{$LNG.tech.{$section.classID}}
				</button>
			</th>
		</tr>
		{foreach $section.rows as $row}
		<tr{if $row.isZero} class="is-zero"{/if}>
			<td class="spy-label-cell" title="{$LNG.tech.{$row.elementID}}">
				<span class="spy-label-text">{$LNG.tech.{$row.elementID}}</span>
			</td>
			{foreach $row.values as $amount}
				{if $amount === null}
				<td class="spy-unscanned{if $columns[$amount@index].is_new} spy-col-new{/if}"></td>
				{else}
				{empire_qty n=$amount class="{if $columns[$amount@index].is_new} spy-col-new{/if}"}
				{/if}
			{/foreach}
		</tr>
		{/foreach}
	</tbody>
		{/foreach}
</table>
{/if}
</div>
