{block name="title" prepend}{$LNG.lm_spy_reports}{/block}
{block name="content"}
<div class="empire-page spy-reports-page" data-label-resize="{$LNG.spy_label_width}" data-new-reports="{if $newReportIds}{','|implode:$newReportIds}{/if}">
{include file="page.spyReports.inner.tpl"}
<div id="empireSettingsMenu" class="empire-settings-menu spy-settings-menu" hidden>
	<label><input type="checkbox" id="empireOptZeros"> {$LNG.zero_show|default:'Afficher les lignes à 0'}</label>
	<label><input type="checkbox" id="empireOptCompact"> {$LNG.compact_option|default:'Nombres compactés'}</label>
	<label class="spy-opt-width">{$LNG.spy_label_width}
		<input type="range" id="spyOptColWidth" min="56" max="220" step="1">
	</label>
	<label><input type="checkbox" id="spyOptSkipDeleteConfirm"> {$LNG.spy_skip_delete_confirm}</label>
	<form class="spy-filters" method="get" action="game.php">
		<input type="hidden" name="page" value="spyReports">
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
		<div class="spy-filter-row">
			<label>{$LNG.spy_filter_sort}
				<select name="sort">
					<option value="time"{if $filters.sort == 'time'} selected{/if}>{$LNG.spy_sort_time}</option>
					<option value="coords"{if $filters.sort == 'coords'} selected{/if}>{$LNG.spy_sort_coords}</option>
					<option value="name"{if $filters.sort == 'name'} selected{/if}>{$LNG.spy_sort_name}</option>
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
			<button type="button" class="spy-filter-close">{$LNG.spy_filter_close}</button>
			<button type="submit">{$LNG.spy_filter_apply}</button>
		</div>
	</form>
</div>
</div>
{/block}
{block name="script" append}
<script>
var spyExtraParam = {$extraParam|json};
var spyQueryBase = {$queryBase|json};
var spyDialogCancelLabel = {$LNG.spy_delete_cancel|json};
var spyDialogDontAskLabel = {$LNG.spy_delete_dont_ask|json};
{literal}
var spyReloadXhr = null;
var spyMenuHost = null;
var spyMenuEl = null;
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
		spyFetch(opts.params);
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

function spyFetch(params, opts) {
	opts = opts || {};
	closeSpyMenu();
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
		location.assign('game.php?' + query.toString());
	}
	spyReloadXhr = $.ajax({
		url: 'game.php?' + query.toString(),
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
		var extraField = document.getElementById('spyExtraField');
		if (extraField) {
			extraField.value = spyExtraParam;
		}
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
		$(document).on('click', '.spy-history-toggle', function (event) {
			event.preventDefault();
			event.stopPropagation();
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
			if (!spyMenuHost) {
				return;
			}
			if (event.target.closest('.spy-history-menu, .spy-purge-menu, .spy-history-toggle, .spy-purge-toggle')) {
				return;
			}
			closeSpyMenu();
		});
		document.addEventListener('keydown', function (event) {
			if (event.key !== 'Escape') {
				return;
			}
			closeSpyMenu();
		});
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
		$(document).on('change', 'select[name="sort"]', function () {
			var order = document.querySelector('select[name="order"]');
			if (!order) {
				return;
			}
			if (this.value === 'coords') {
				order.value = 'asc';
			} else if (this.value === 'time') {
				order.value = 'desc';
			}
		});
		$(document).on('submit', '.spy-filters', function (event) {
			event.preventDefault();
			spyFetch(new URLSearchParams(new FormData(this)));
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
					spyFetch(url.searchParams);
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
