{block name="title" prepend}{$LNG.lm_spy_reports}{/block}
{block name="content"}
<div class="empire-page spy-reports-page" data-new-reports="{if $newReportIds}{','|implode:$newReportIds}{/if}">
{include file="page.spyReports.inner.tpl"}
<div id="empireSettingsMenu" class="empire-settings-menu spy-settings-menu" hidden>
	<div class="empire-settings-bar">
		<button type="button" class="empire-settings-close" aria-label="{$LNG.spy_filter_close}"></button>
	</div>
	<div class="empire-settings-body">
	<label><input type="checkbox" id="empireOptZeros"> {$LNG.zero_show|default:'Afficher les lignes à 0'}</label>
	<label><input type="checkbox" id="empireOptCompact"> {$LNG.compact_option|default:'Nombres compactés'}</label>
	<label><input type="checkbox" id="spyOptSkipDeleteConfirm"> {$LNG.spy_skip_delete_confirm}</label>
	</div>
</div>
<div id="spySearchMenu" class="empire-settings-menu spy-settings-menu spy-tool-menu" hidden>
	<div class="empire-settings-bar">
		<button type="button" class="empire-settings-close" aria-label="{$LNG.spy_filter_close}"></button>
	</div>
	<div class="empire-settings-body">
	<form class="spy-filters spy-search-form" method="get" action="game.php">
		<input type="hidden" name="page" value="spyReports">
		<input type="hidden" name="sort" value="{$filters.sort}">
		<input type="hidden" name="order" value="{$filters.order}">
		{if $filters.user}<input type="hidden" name="user" value="{$filters.user}">{/if}
		<input type="hidden" name="extra" id="spyExtraField" value="{$extraParam}">
		<div class="spy-filter-row">
			<label>{$LNG.spy_filter_galaxy}
				<input type="number" name="galaxy" value="{if $filters.galaxy}{$filters.galaxy}{/if}" min="1" class="spy-filter-num">
			</label>
			<label>{$LNG.spy_filter_type}
				<select name="planet_type">
					<option value="0"{if $filters.planet_type == 0} selected{/if}>{$LNG.spy_type_all}</option>
					<option value="1"{if $filters.planet_type == 1} selected{/if}>{$LNG.spy_type_planet}</option>
					<option value="3"{if $filters.planet_type == 3} selected{/if}>{$LNG.spy_type_moon}</option>
				</select>
			</label>
		</div>
		<div class="spy-filter-row">
			<label>{$LNG.spy_filter_from}
				<input type="date" name="from" value="{$filters.from_value}">
			</label>
			<label>{$LNG.spy_filter_to}
				<input type="date" name="to" value="{$filters.to_value}">
			</label>
		</div>
		<label>{$LNG.spy_filter_player}
			<input type="text" name="player" value="{$filters.player}" maxlength="32">
		</label>
		<div class="spy-filter-row spy-filter-actions">
			<button type="submit">{$LNG.spy_search}</button>
		</div>
	</form>
	</div>
</div>
<div id="spySortMenu" class="empire-settings-menu spy-settings-menu spy-tool-menu spy-sort-menu" hidden>
	<div class="empire-settings-bar">
		<button type="button" class="empire-settings-close" aria-label="{$LNG.spy_filter_close}"></button>
	</div>
	<div class="empire-settings-body">
	<form class="spy-filters spy-sort-form" method="get" action="game.php">
		<input type="hidden" name="page" value="spyReports">
		<input type="hidden" name="galaxy" value="{if $filters.galaxy}{$filters.galaxy}{/if}">
		<input type="hidden" name="planet_type" value="{$filters.planet_type}">
		<input type="hidden" name="from" value="{$filters.from_value}">
		<input type="hidden" name="to" value="{$filters.to_value}">
		<input type="hidden" name="player" value="{$filters.player}">
		{if $filters.user}<input type="hidden" name="user" value="{$filters.user}">{/if}
		<input type="hidden" name="extra" value="{$extraParam}">
		<div class="spy-filter-row">
			<label>{$LNG.spy_filter_sort}
				<select name="sort">
					<option value="time"{if $filters.sort == 'time'} selected{/if}>{$LNG.spy_sort_time}</option>
					<option value="coords"{if $filters.sort == 'coords'} selected{/if}>{$LNG.spy_sort_coords}</option>
					<option value="resources"{if $filters.sort == 'resources'} selected{/if} title="{$LNG.spy_sort_resources_hint}">{$LNG.spy_sort_resources}</option>
				</select>
			</label>
			<label>
				<select name="order" aria-label="{$LNG.spy_filter_sort}">
					<option value="desc"{if $filters.order == 'desc'} selected{/if}>{$LNG.spy_sort_desc}</option>
					<option value="asc"{if $filters.order == 'asc'} selected{/if}>{$LNG.spy_sort_asc}</option>
				</select>
			</label>
		</div>
		<div class="spy-filter-row spy-filter-actions">
			<button type="submit">{$LNG.spy_sort_apply}</button>
		</div>
	</form>
	</div>
</div>
</div>
{/block}
{block name="script" append}
<script>
var spyExtraParam = {$extraParam|json};
var spyQueryBase = {$queryBase|json};
var spyToken = {$spyToken|json};
var spyDialogCancelLabel = {$LNG.spy_delete_cancel|json};
var spyDialogDontAskLabel = {$LNG.spy_delete_dont_ask|json};
{literal}
var spyReloadXhr = null;
var spyMenuHost = null;
var spyMenuEl = null;
var spyToolMenu = null;
var spyToolSelector = null;
var SPY_SKIP_DELETE_KEY = 'risistar.spySkipDeleteConfirm';

function spyMenuToggle(host) {
	return host ? host.querySelector('.spy-history-toggle, .spy-purge-toggle') : null;
}

function closeSpyMenu() {
	var host = spyMenuHost;
	var menu = spyMenuEl;
	spyMenuHost = null;
	spyMenuEl = null;
	if (host) {
		host.classList.remove('is-open');
		var toggle = spyMenuToggle(host);
		if (toggle) {
			toggle.setAttribute('aria-expanded', 'false');
		}
	}
	if (!menu) {
		return;
	}
	menu.hidden = true;
	if (host && document.body.contains(host)) {
		host.appendChild(menu);
	} else if (menu.parentNode) {
		menu.parentNode.removeChild(menu);
	}
}

function placeSpyMenu(toggle, menu, host) {
	var pad = 8;
	var vw = window.innerWidth;
	var vh = window.innerHeight;
	var rect = toggle.getBoundingClientRect();
	menu.style.left = '0px';
	menu.style.top = '0px';
	menu.style.maxHeight = '';
	var menuWidth = menu.offsetWidth;
	var menuHeight = menu.offsetHeight;
	var left = host && host.classList.contains('spy-purge')
		? rect.left
		: rect.left + (rect.width - menuWidth) / 2;
	if (left + menuWidth > vw - pad) {
		left = vw - pad - menuWidth;
	}
	left = Math.max(pad, left);
	var top = rect.bottom + 4;
	if (top + menuHeight > vh - pad && rect.top - 4 - menuHeight >= pad) {
		top = rect.top - 4 - menuHeight;
	}
	menu.style.left = Math.round(left) + 'px';
	menu.style.top = Math.round(top) + 'px';
	menu.style.maxHeight = Math.max(120, vh - pad - Math.round(top)) + 'px';
}

function openSpyMenu(host, toggleSel, menuSel) {
	var toggle = host.querySelector(toggleSel);
	var menu = host.querySelector(menuSel);
	if (!toggle || !menu) {
		return;
	}
	closeSpyMenu();
	spyMenuHost = host;
	spyMenuEl = menu;
	host.classList.add('is-open');
	toggle.setAttribute('aria-expanded', 'true');
	document.body.appendChild(menu);
	menu.hidden = false;
	placeSpyMenu(toggle, menu, host);
}

function spyPrefOn(key) {
	try {
		return localStorage.getItem(key) === '1';
	} catch (e) {
		return false;
	}
}

function spyWritePref(key, on) {
	try {
		localStorage.setItem(key, on ? '1' : '0');
	} catch (e) {}
}

function spySkipDeleteConfirm() {
	var box = document.getElementById('spyOptSkipDeleteConfirm');
	if (box) {
		return box.checked;
	}
	return spyPrefOn(SPY_SKIP_DELETE_KEY);
}

function spySetSkipDeleteConfirm(on) {
	spyWritePref(SPY_SKIP_DELETE_KEY, on);
	var box = document.getElementById('spyOptSkipDeleteConfirm');
	if (box) {
		box.checked = on;
	}
}

function openSpyDialog(opts) {
	opts = opts || {};
	if (!opts.params) {
		return;
	}
	RisistarDialog.confirm(opts.title || '', {
		confirmLabel: opts.confirm || '',
		cancelLabel: spyDialogCancelLabel,
		checkboxLabel: opts.skipPref ? spyDialogDontAskLabel : '',
		danger: true
	}).then(function (result) {
		if (!result.confirmed) {
			return;
		}
		if (opts.skipPref && result.checked) {
			spySetSkipDeleteConfirm(true);
		}
		spyFetch(opts.params, {post: true});
	});
}

function initSpyPrefs() {
	var skipBox = document.getElementById('spyOptSkipDeleteConfirm');
	document.cookie = 'risistar_spyAutoPurge=; Path=/; Max-Age=0; SameSite=Lax';
	if (!skipBox) {
		return;
	}
	skipBox.checked = spyPrefOn(SPY_SKIP_DELETE_KEY);
	skipBox.addEventListener('change', function () {
		spyWritePref(SPY_SKIP_DELETE_KEY, skipBox.checked);
	});
}

function spyHistoryChange(id, current, latest) {
	id = String(id || '');
	current = String(current || '');
	latest = String(latest || '');
	if (!id || id === current) {
		return;
	}
	var ids = spyExtraParam ? String(spyExtraParam).split(',').filter(Boolean) : [];
	ids = ids.filter(function (value) {
		return value !== current && value !== id;
	});
	if (id !== latest) {
		ids.push(id);
	}
	var params = new URLSearchParams(spyQueryBase);
	params.set('page', 'spyReports');
	params.set('extra', ids.join(','));
	spyFetch(params);
}
function spyScrollState() {
	var wrap = document.querySelector('.empire-wrap');
	var se = document.scrollingElement || document.documentElement;
	return {
		x: se.scrollLeft,
		y: se.scrollTop,
		left: wrap ? wrap.scrollLeft : 0,
		top: wrap ? wrap.scrollTop : 0
	};
}

function spyRestoreScroll(state) {
	if (!state) {
		return;
	}
	var wrap = document.querySelector('.empire-wrap');
	if (wrap) {
		wrap.scrollLeft = state.left;
		wrap.scrollTop = state.top;
	}
	window.scrollTo(state.x, state.y);
}

function closeEmpireOptions() {
	var menu = document.getElementById('empireSettingsMenu');
	if (menu) {
		menu.hidden = true;
	}
	document.querySelectorAll('.empire-settings-toggle').forEach(function (btn) {
		btn.setAttribute('aria-expanded', 'false');
	});
}

function closeSpyToolMenus() {
	document.querySelectorAll('.spy-tool-menu').forEach(function (menu) {
		menu.hidden = true;
	});
	document.querySelectorAll('.spy-tool-toggle').forEach(function (btn) {
		btn.setAttribute('aria-expanded', 'false');
	});
	spyToolMenu = null;
	spyToolSelector = null;
}

function spyVisibleToggle(selector) {
	var sticky = document.getElementById('spyPlanetSticky');
	if (sticky && !sticky.hidden) {
		var stuck = sticky.querySelector(selector);
		if (stuck) {
			return stuck;
		}
	}
	var wrap = document.querySelector('.empire-wrap');
	return wrap ? wrap.querySelector(selector) : document.querySelector(selector);
}

function placeSpyToolMenu() {
	var menu = spyToolMenu;
	if (!menu || menu.hidden || !spyToolSelector) {
		return;
	}
	var toggle = spyVisibleToggle(spyToolSelector);
	if (!toggle) {
		closeSpyToolMenus();
		return;
	}
	var pad = 8;
	var vw = window.innerWidth;
	var vh = window.innerHeight;
	var rect = toggle.getBoundingClientRect();
	menu.style.right = '';
	menu.style.width = '';
	menu.style.maxHeight = '';
	var spaceBelow = vh - pad - (rect.bottom + 4);
	var spaceAbove = rect.top - pad - 4;
	var openBelow = spaceBelow >= 160 || spaceBelow >= spaceAbove;
	var available = Math.max(120, Math.floor(openBelow ? spaceBelow : spaceAbove));
	menu.style.maxHeight = available + 'px';
	var menuWidth = menu.offsetWidth || 260;
	var left = rect.right + 4;
	if (left + menuWidth > vw - pad) {
		left = vw - pad - menuWidth;
	}
	left = Math.max(pad, left);
	menu.style.left = Math.round(left) + 'px';
	if (openBelow) {
		menu.style.top = Math.round(rect.bottom + 4) + 'px';
	} else {
		var menuHeight = Math.min(menu.offsetHeight || available, available);
		menu.style.top = Math.round(Math.max(pad, rect.top - 4 - menuHeight)) + 'px';
	}
}

function openSpyToolMenu(toggle) {
	var menu = document.getElementById(toggle.getAttribute('aria-controls'));
	if (!menu) {
		return;
	}
	var selector = toggle.classList.contains('spy-search-toggle') ? '.spy-search-toggle' : '.spy-sort-toggle';
	if (spyToolMenu === menu) {
		closeSpyToolMenus();
		return;
	}
	closeSpyToolMenus();
	closeSpyMenu();
	closeEmpireOptions();
	if (menu.parentNode !== document.body) {
		document.body.appendChild(menu);
	}
	spyToolMenu = menu;
	spyToolSelector = selector;
	menu.hidden = false;
	document.querySelectorAll(selector).forEach(function (btn) {
		btn.setAttribute('aria-expanded', 'true');
	});
	placeSpyToolMenu();
	window.requestAnimationFrame(placeSpyToolMenu);
}

function spyFieldValue(form, name) {
	if (!form || !form.elements[name]) {
		return '';
	}
	return String(form.elements[name].value || '');
}

function spySyncFilterMirrors() {
	var search = document.querySelector('.spy-search-form');
	var sortForm = document.querySelector('.spy-sort-form');
	if (!search || !sortForm) {
		return;
	}
	['galaxy', 'planet_type', 'from', 'to', 'player'].forEach(function (name) {
		var source = search.elements[name];
		var target = sortForm.elements[name];
		if (source && target && target !== source) {
			target.value = source.value;
		}
	});
	['sort', 'order'].forEach(function (name) {
		var source = sortForm.elements[name];
		var target = search.elements[name];
		if (source && target && target !== source) {
			target.value = source.value;
		}
	});
}

function spyCollectFilters() {
	var search = document.querySelector('.spy-search-form');
	var sortForm = document.querySelector('.spy-sort-form');
	var params = new URLSearchParams();
	var galaxy = spyFieldValue(search, 'galaxy');
	var planetType = spyFieldValue(search, 'planet_type');
	var from = spyFieldValue(search, 'from');
	var to = spyFieldValue(search, 'to');
	var player = spyFieldValue(search, 'player');
	var user = spyFieldValue(search, 'user') || spyFieldValue(sortForm, 'user');
	var sortBy = spyFieldValue(sortForm, 'sort') || 'time';
	var order = spyFieldValue(sortForm, 'order');
	params.set('page', 'spyReports');
	if (galaxy !== '' && galaxy !== '0') {
		params.set('galaxy', galaxy);
	}
	if (planetType !== '' && planetType !== '0') {
		params.set('planet_type', planetType);
	}
	if (from !== '') {
		params.set('from', from);
	}
	if (to !== '') {
		params.set('to', to);
	}
	if (player !== '') {
		params.set('player', player);
	}
	if (user !== '' && user !== '0') {
		params.set('user', user);
	}
	if (sortBy !== 'time') {
		params.set('sort', sortBy);
	}
	if (order && order !== (sortBy === 'coords' ? 'asc' : 'desc')) {
		params.set('order', order);
	}
	if (spyExtraParam) {
		params.set('extra', spyExtraParam);
	}
	return params;
}

function spyApplyQueryToForms(query) {
	var params = new URLSearchParams(query || '');
	var search = document.querySelector('.spy-search-form');
	var sortForm = document.querySelector('.spy-sort-form');
	var sortBy = params.get('sort') || 'time';
	if (sortBy !== 'coords' && sortBy !== 'resources') {
		sortBy = 'time';
	}
	var order = params.get('order') || (sortBy === 'coords' ? 'asc' : 'desc');
	if (order !== 'asc') {
		order = 'desc';
	}
	if (search) {
		if (search.elements.galaxy) {
			search.elements.galaxy.value = params.get('galaxy') || '';
		}
		if (search.elements.planet_type) {
			search.elements.planet_type.value = params.get('planet_type') || '0';
		}
		if (search.elements.from) {
			search.elements.from.value = params.get('from') || '';
		}
		if (search.elements.to) {
			search.elements.to.value = params.get('to') || '';
		}
		if (search.elements.player) {
			search.elements.player.value = params.get('player') || '';
		}
	}
	if (sortForm) {
		if (sortForm.elements.sort) {
			sortForm.elements.sort.value = sortBy;
		}
		if (sortForm.elements.order) {
			sortForm.elements.order.value = order;
		}
	}
	spySyncFilterMirrors();
}

function spyFetch(params, opts) {
	opts = opts || {};
	closeSpyMenu();
	closeSpyToolMenus();
	var scroll = spyScrollState();
	var query = params instanceof URLSearchParams ? params : new URLSearchParams(params);
	query.set('page', 'spyReports');
	query.set('ajax', '1');
	var wrap = document.querySelector('.empire-wrap');
	if (wrap) {
		wrap.classList.add('is-loading');
	}
	if (spyReloadXhr && spyReloadXhr.abort) {
		spyReloadXhr.abort();
	}
	function spyFallback() {
		query.delete('ajax');
		if (opts.post) {
			var form = document.createElement('form');
			form.method = 'post';
			form.action = 'game.php?' + query.toString();
			var token = document.createElement('input');
			token.type = 'hidden';
			token.name = 'token';
			token.value = spyToken;
			form.appendChild(token);
			document.body.appendChild(form);
			form.submit();
			return;
		}
		location.assign('game.php?' + query.toString());
	}
	spyReloadXhr = $.ajax({
		url: 'game.php?' + query.toString(),
		type: opts.post ? 'POST' : 'GET',
		data: opts.post ? {token: spyToken} : undefined,
		dataType: 'html',
		cache: false,
		xhrFields: {withCredentials: true}
	}).done(function (html) {
		var next = $(html).filter('.empire-wrap');
		if (!next.length) {
			next = $(html).find('.empire-wrap');
		}
		if (!next.length) {
			spyFallback();
			return;
		}
		$('.empire-wrap').replaceWith(next);
		wrap = document.querySelector('.empire-wrap');
		spyRestoreScroll(scroll);
		spyExtraParam = wrap.getAttribute('data-extra') || '';
		spyQueryBase = wrap.getAttribute('data-query') || '';
		spyApplyQueryToForms(spyQueryBase);
		document.querySelectorAll('input[name="extra"]').forEach(function (field) {
			field.value = spyExtraParam;
		});
		try {
			localStorage.setItem('risistar.spyExtra', spyExtraParam);
		} catch (e) {}
		if (opts.push !== false) {
			var state = new URLSearchParams(spyQueryBase);
			state.set('page', 'spyReports');
			state.set('extra', spyExtraParam);
			history.pushState({spyReports: true}, '', 'game.php?' + state.toString());
		}
		if (window.RisistarLayout && typeof window.RisistarLayout.refreshEmpire === 'function') {
			window.RisistarLayout.refreshEmpire(wrap);
		}
		spyRestoreScroll(scroll);
		requestAnimationFrame(function () {
			spyRestoreScroll(scroll);
		});
	}).fail(function (xhr, status) {
		if (status === 'abort') {
			return;
		}
		spyFallback();
	}).always(function () {
		wrap = document.querySelector('.empire-wrap');
		if (wrap) {
			wrap.classList.remove('is-loading');
		}
	});
}
(function () {
	$(function () {
		initSpyPrefs();
		try {
			var params = new URLSearchParams(location.search);
			if (params.has('extra')) {
				localStorage.setItem('risistar.spyExtra', params.get('extra') || '');
			} else {
				var stored = localStorage.getItem('risistar.spyExtra');
				if (stored) {
					params.set('page', 'spyReports');
					params.set('extra', stored);
					spyFetch(params, {push: true});
				}
			}
		} catch (e) {}
		$(document).on('click', '.spy-tool-toggle', function (event) {
			event.preventDefault();
			event.stopPropagation();
			openSpyToolMenu(this);
		});
		$(document).on('click', '.empire-settings-toggle', function () {
			closeSpyToolMenus();
			closeSpyMenu();
		});
		$(document).on('click', '.spy-tool-menu .empire-settings-close', function (event) {
			event.preventDefault();
			event.stopPropagation();
			closeSpyToolMenus();
		});
		$(document).on('click', '.spy-history-toggle', function (event) {
			event.preventDefault();
			event.stopPropagation();
			closeSpyToolMenus();
			var host = this.closest('.spy-history');
			if (!host) {
				return;
			}
			if (spyMenuHost === host) {
				closeSpyMenu();
				return;
			}
			openSpyMenu(host, '.spy-history-toggle', '.spy-history-menu');
		});
		$(document).on('click', '.spy-purge-toggle', function (event) {
			event.preventDefault();
			event.stopPropagation();
			closeSpyToolMenus();
			var host = this.closest('.spy-purge');
			if (!host) {
				return;
			}
			if (spyMenuHost === host) {
				closeSpyMenu();
				return;
			}
			openSpyMenu(host, '.spy-purge-toggle', '.spy-purge-menu');
		});
		$(document).on('click', '.spy-history-option', function (event) {
			event.preventDefault();
			event.stopPropagation();
			var host = spyMenuHost;
			var id = this.getAttribute('data-id');
			var current = host ? host.getAttribute('data-current') : '';
			var latest = host ? host.getAttribute('data-latest') : '';
			closeSpyMenu();
			spyHistoryChange(id, current, latest);
		});
		document.addEventListener('click', function (event) {
			if (spyMenuHost) {
				if (!event.target.closest('.spy-history-menu, .spy-purge-menu, .spy-history-toggle, .spy-purge-toggle')) {
					closeSpyMenu();
				}
			}
			if (!spyToolMenu || spyToolMenu.hidden) {
				return;
			}
			if (event.target.closest('.spy-tool-menu, .spy-tool-toggle')) {
				return;
			}
			closeSpyToolMenus();
		});
		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}
			closeSpyMenu();
			closeSpyToolMenus();
		});
		window.addEventListener('resize', placeSpyToolMenu);
		window.addEventListener('scroll', placeSpyToolMenu, true);
		window.addEventListener('resize', closeSpyMenu);
		window.addEventListener('scroll', function (event) {
			if (!spyMenuHost) {
				return;
			}
			if (event.target && event.target.closest && event.target.closest('.spy-history-menu, .spy-purge-menu')) {
				return;
			}
			closeSpyMenu();
		}, true);
		$(document).on('change', '.spy-sort-form select[name="sort"]', function () {
			var order = this.form ? this.form.querySelector('select[name="order"]') : null;
			if (!order) {
				return;
			}
			order.value = this.value === 'coords' ? 'asc' : 'desc';
		});
		$(document).on('submit', '.spy-filters', function (event) {
			event.preventDefault();
			spySyncFilterMirrors();
			spyFetch(spyCollectFilters());
		});
		$(document).on('click', '.spy-delete, .spy-hide, .spy-delete-all, .spy-delete-old', function (event) {
			event.preventDefault();
			var url = new URL(this.href, location.href);
			if (this.classList.contains('spy-delete-all') || this.classList.contains('spy-delete-old')) {
				closeSpyMenu();
				openSpyDialog({
					params: url.searchParams,
					title: this.getAttribute('data-confirm') || '',
					confirm: this.getAttribute('data-confirm-label') || '',
					skipPref: false
				});
				return;
			}
			if (this.classList.contains('spy-delete')) {
				if (spySkipDeleteConfirm()) {
					spyFetch(url.searchParams, {post: true});
					return;
				}
				openSpyDialog({
					params: url.searchParams,
					title: this.getAttribute('data-confirm') || '',
					confirm: this.getAttribute('data-confirm-label') || '',
					skipPref: true
				});
				return;
			}
			spyFetch(url.searchParams);
		});
		window.addEventListener('popstate', function () {
			if (!document.querySelector('.spy-reports-page')) {
				return;
			}
			spyFetch(new URLSearchParams(location.search), {push: false});
		});
	});
})();
{/literal}
</script>
{/block}
