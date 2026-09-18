(function (window, document, $) {
	'use strict';

	var NAV_KEY = 'risistar.nav';
	var ZERO_KEY = 'risistar.showZeros';
	var COMPACT_KEY = 'risistar.compactNumbers';
	var NARROW_QUERY = '(max-width: 1100px)';
	var SPY_LABEL_KEY = 'risistar.spyLabelWidth';
	var EMPIRE_LABEL_KEY = 'risistar.empireLabelWidth';
	var COMBAT_LABEL_KEY = 'risistar.combatLabelWidth';
	var EMPIRE_SECTIONS_KEY = 'risistar.empireCollapsed';
	var SPY_SECTIONS_KEY = 'risistar.spyCollapsed';
	var LABEL_MIN = 56;
	var LABEL_MAX = 220;
	var LABEL_EDGE = 10;
	var labelResizer = null;
	var labelResizeY = 0;
	var labelResizing = false;
	var labelResizeSide = 'left';

	function getLNG() {
		return window.LayoutLNG || {
			navExpand: 'Afficher le menu',
			navCollapse: 'Réduire le menu',
			zeroShow: 'Afficher les lignes à 0',
			zeroHide: 'Masquer les lignes à 0'
		};
	}

	function readStorage(key) {
		try {
			return window.localStorage.getItem(key);
		} catch (e) {
			return null;
		}
	}

	function writeStorage(key, value) {
		try {
			window.localStorage.setItem(key, value);
		} catch (e) {}
	}

	function isCollapsed() {
		return document.documentElement.classList.contains('nav-collapsed');
	}

	function applyNavState(collapsed, persist) {
		var root = document.documentElement;
		root.classList.toggle('nav-collapsed', collapsed);
		root.classList.toggle('nav-expanded', !collapsed);

		var toggle = document.getElementById('navToggle');
		if (toggle) {
			var lng = getLNG();
			toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			toggle.setAttribute('title', collapsed ? lng.navExpand : lng.navCollapse);
		}

		if (persist) {
			writeStorage(NAV_KEY, collapsed ? 'collapsed' : 'expanded');
		}
		window.setTimeout(function () {
			syncEmpireLayout();
			syncBuildCardSubs();
		}, 0);
	}

	function markActiveNav() {
		var params = new URLSearchParams(window.location.search);
		var page = params.get('page') || 'overview';
		var mode = params.get('mode') || '';

		$('#menu a.nav-link[href]').each(function () {
			var href = this.getAttribute('href') || '';
			if (href.indexOf('page=' + page) === -1) {
				return;
			}
			if (page === 'shipyard' && mode && href.indexOf('mode=' + mode) === -1) {
				return;
			}
			$(this).addClass('is-active');
		});
	}

	function initNav() {
		if (!document.getElementById('leftmenu')) {
			return;
		}

		var stored = readStorage(NAV_KEY);
		var collapsed;
		if (stored === 'collapsed') {
			collapsed = true;
		} else if (stored === 'expanded') {
			collapsed = false;
		} else {
			collapsed = window.matchMedia(NARROW_QUERY).matches;
		}
		applyNavState(collapsed, false);

		var toggle = document.getElementById('navToggle');
		if (toggle) {
			toggle.addEventListener('click', function () {
				applyNavState(!isCollapsed(), true);
			});
		}

		$(document).on('click', '.nav-backdrop', function () {
			if (window.matchMedia('(max-width: 640px)').matches) {
				applyNavState(true, true);
			}
		});

		markActiveNav();
	}

	function zerosShown() {
		return document.documentElement.classList.contains('show-zeros');
	}

	function zeroButtonLabel(show) {
		var lng = getLNG();
		return show ? lng.zeroHide : lng.zeroShow;
	}

	function applyZeros(show, persist) {
		document.documentElement.classList.toggle('show-zeros', show);
		$('.zero-toggle').text(zeroButtonLabel(show));
		var zerosBox = document.getElementById('empireOptZeros');
		if (zerosBox) {
			zerosBox.checked = show;
		}
		if (persist !== false) {
			writeStorage(ZERO_KEY, show ? '1' : '0');
		}
		syncEmpireLayout();
	}

	function makeZeroButton(extraClass) {
		var btn = document.createElement('button');
		btn.type = 'button';
		btn.className = extraClass ? 'zero-toggle ' + extraClass : 'zero-toggle';
		btn.textContent = zeroButtonLabel(zerosShown());
		btn.addEventListener('click', function () {
			applyZeros(!zerosShown(), true);
		});
		return btn;
	}

	function compactShown() {
		return document.documentElement.classList.contains('compact-numbers');
	}

	function applyCompact(show, persist) {
		document.documentElement.classList.toggle('compact-numbers', show);
		var compactBox = document.getElementById('empireOptCompact');
		if (compactBox) {
			compactBox.checked = show;
		}
		if (persist !== false) {
			writeStorage(COMPACT_KEY, show ? '1' : '0');
		}
		syncEmpireLayout();
	}

	var compactFormatter = null;

	function getCompactFormatter() {
		if (!compactFormatter && window.Intl && Intl.NumberFormat) {
			compactFormatter = new Intl.NumberFormat('en', {
				notation: 'compact',
				compactDisplay: 'short',
				maximumSignificantDigits: 3,
				minimumSignificantDigits: 3
			});
		}
		return compactFormatter;
	}

	function formatCompactNumber(value) {
		var n = Number(value);
		if (!isFinite(n) || n === 0) {
			return '0';
		}
		if (Math.abs(n) < 1000) {
			return String(Math.round(n));
		}
		var fmt = getCompactFormatter();
		if (!fmt) {
			return String(n);
		}
		return fmt.format(n).replace(/[\s\u00a0]/g, '').replace(/\./g, ',');
	}

	function enhanceEmpireQuantities(root) {
		$(root).find('.empire-qty').each(function () {
			if (this.getAttribute('data-compact-ready')) {
				return;
			}
			this.setAttribute('data-compact-ready', '1');
			var full = this.textContent.trim();
			var compact = formatCompactNumber(this.getAttribute('data-value'));
			this.textContent = '';
			var fullSpan = document.createElement('span');
			fullSpan.className = 'num-full';
			fullSpan.textContent = full;
			var compactSpan = document.createElement('span');
			compactSpan.className = 'num-compact';
			compactSpan.textContent = compact;
			this.appendChild(fullSpan);
			this.appendChild(compactSpan);
		});
	}

	function empireSettingsToggle() {
		var sticky = document.getElementById('spyPlanetSticky');
		if (sticky && !sticky.hidden) {
			var stuckBtn = sticky.querySelector('.empire-settings-toggle');
			if (stuckBtn) {
				return stuckBtn;
			}
		}
		return document.getElementById('empireSettingsToggle');
	}

	function closeEmpireSettings() {
		var menu = document.getElementById('empireSettingsMenu');
		if (!menu || menu.hidden) {
			return;
		}
		menu.hidden = true;
		document.querySelectorAll('.empire-settings-toggle').forEach(function (btn) {
			btn.setAttribute('aria-expanded', 'false');
		});
	}

	function placeEmpireSettingsMenu() {
		var toggle = empireSettingsToggle();
		var menu = document.getElementById('empireSettingsMenu');
		if (!toggle || !menu || menu.hidden) {
			return;
		}

		var pad = 8;
		var vw = window.innerWidth;
		var vh = window.innerHeight;
		var rect = toggle.getBoundingClientRect();
		var isSpy = menu.classList.contains('spy-settings-menu');
		var sheet = isSpy && (vw < 720 || vh < 520);
		menu.classList.toggle('is-sheet', sheet);

		if (sheet) {
			menu.style.left = pad + 'px';
			menu.style.right = pad + 'px';
			menu.style.width = 'auto';
			menu.style.minWidth = '0';
			menu.style.maxWidth = 'none';
			var top = pad;
			var maxH = Math.max(160, vh - pad * 2);
			var needed = Math.min(menu.scrollHeight || maxH, maxH);
			if (rect.bottom + 4 + needed <= vh - pad) {
				top = rect.bottom + 4;
			} else if (rect.top - 4 - needed >= pad) {
				top = Math.max(pad, rect.top - 4 - needed);
			}
			menu.style.top = Math.round(top) + 'px';
			menu.style.maxHeight = Math.max(160, vh - pad - top) + 'px';
			return;
		}

		menu.style.right = '';
		menu.style.width = '';
		menu.style.minWidth = '';
		menu.style.maxWidth = '';
		menu.style.maxHeight = '';

		var spaceBelow = vh - pad - (rect.bottom + 4);
		var spaceAbove = rect.top - pad - 4;
		var openBelow = spaceBelow >= 160 || spaceBelow >= spaceAbove;
		var available = Math.max(120, Math.floor(openBelow ? spaceBelow : spaceAbove));
		menu.style.maxHeight = available + 'px';

		var menuWidth = menu.offsetWidth || 230;
		var left = rect.left;
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

	function clampLabelWidth(px) {
		return Math.min(LABEL_MAX, Math.max(LABEL_MIN, Math.round(px)));
	}

	function defaultLabelWidth() {
		var mobile = window.matchMedia('(max-width: 640px)').matches;
		if (document.querySelector('.combat-report')) {
			return mobile ? 120 : 168;
		}
		return mobile ? 88 : 148;
	}

	function labelPage() {
		return document.querySelector('.empire-page, .combat-report');
	}

	function labelStorageKey() {
		if (document.querySelector('.spy-reports-page')) {
			return SPY_LABEL_KEY;
		}
		if (document.querySelector('.combat-report')) {
			return COMBAT_LABEL_KEY;
		}
		return EMPIRE_LABEL_KEY;
	}

	function labelResizeTargets() {
		var nodes = [];
		var wrap = document.querySelector('.empire-wrap');
		if (wrap) {
			nodes.push(wrap);
		}
		document.querySelectorAll('.combat-compare-scroll').forEach(function (el) {
			if (el.offsetWidth > 0 && el.offsetHeight > 0) {
				nodes.push(el);
			}
		});
		return nodes;
	}

	function applySpyLabelWidth(px, persist) {
		var page = labelPage();
		if (!page) {
			return;
		}
		var width = clampLabelWidth(px);
		page.style.setProperty('--spy-label-col', width + 'px');
		var range = document.getElementById('spyOptColWidth');
		if (range && String(range.value) !== String(width)) {
			range.value = String(width);
		}
		if (persist) {
			writeStorage(labelStorageKey(), String(width));
		}
		syncEmpireLayout();
	}

	function currentSpyLabelWidth() {
		var page = labelPage();
		if (!page) {
			return defaultLabelWidth();
		}
		var raw = parseInt(getComputedStyle(page).getPropertyValue('--spy-label-col'), 10);
		return isNaN(raw) ? defaultLabelWidth() : raw;
	}

	function prefersFineHover() {
		return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
	}

	function labelColumnCellIn(wrap) {
		if (!wrap) {
			return null;
		}
		if (wrap.classList.contains('empire-wrap')) {
			var cells = wrap.querySelectorAll('.spy-label-cell');
			var i;
			for (i = 0; i < cells.length; i++) {
				if (cells[i].offsetWidth > 0) {
					return cells[i];
				}
			}
			return wrap.querySelector('.empire-table thead tr.empire-colhead > :first-child')
				|| wrap.querySelector('.empire-table tbody td:first-child');
		}
		return wrap.querySelector('.combat-compare tbody .combat-label-col.is-start')
			|| wrap.querySelector('.combat-compare tbody .combat-label-col')
			|| wrap.querySelector('.combat-compare .combat-label-col');
	}

	function combatLabelResizeHit(wrap, clientX) {
		if (!wrap || wrap.classList.contains('empire-wrap')) {
			return null;
		}
		var start = wrap.querySelector('.combat-compare tbody .combat-label-col.is-start');
		var end = wrap.querySelector('.combat-compare tbody .combat-label-col.is-end');
		var hits = [];
		var i;
		var dist;
		var best = null;
		var bestDist = LABEL_EDGE;
		if (start && start.offsetWidth > 0) {
			hits.push({
				cell: start,
				side: 'left',
				edge: start.getBoundingClientRect().right
			});
		}
		if (end && end.offsetWidth > 0 && getComputedStyle(end).display !== 'none') {
			hits.push({
				cell: end,
				side: 'right',
				edge: end.getBoundingClientRect().left
			});
		}
		for (i = 0; i < hits.length; i++) {
			dist = Math.abs(clientX - hits[i].edge);
			if (dist <= bestDist) {
				bestDist = dist;
				best = hits[i];
			}
		}
		return best;
	}

	function labelResizeEdgeX(cell, side) {
		var edge = cell.getBoundingClientRect();
		return side === 'right' ? edge.left : edge.right;
	}

	function labelColumnCell() {
		var targets = labelResizeTargets();
		var i;
		var cell;
		for (i = 0; i < targets.length; i++) {
			cell = labelColumnCellIn(targets[i]);
			if (cell && cell.offsetWidth > 0) {
				return cell;
			}
		}
		return null;
	}

	function labelTargetAt(clientX, clientY) {
		var targets = labelResizeTargets();
		var i;
		var box;
		var yMatch = null;
		for (i = 0; i < targets.length; i++) {
			box = targets[i].getBoundingClientRect();
			if (clientY < box.top || clientY > box.bottom) {
				continue;
			}
			if (yMatch === null) {
				yMatch = targets[i];
			}
			if (clientX == null || (clientX >= box.left && clientX <= box.right)) {
				return targets[i];
			}
		}
		return yMatch || targets[0] || null;
	}

	function isOnLabelResizeEdge(event) {
		if (labelResizer && event.target.closest && event.target.closest('.spy-col-resizer')) {
			return true;
		}
		if (event.target.closest && (event.target.closest('.empire-settings-menu') || event.target.closest('.spy-history-menu'))) {
			return false;
		}
		var wrap = labelTargetAt(event.clientX, event.clientY);
		var cell = labelColumnCellIn(wrap);
		if (!wrap || !cell) {
			return false;
		}
		var box = wrap.getBoundingClientRect();
		if (event.clientY < box.top || event.clientY > box.bottom) {
			return false;
		}
		if (event.clientX < box.left || event.clientX > box.right) {
			return false;
		}
		var combatHit = combatLabelResizeHit(wrap, event.clientX);
		if (combatHit) {
			labelResizeSide = combatHit.side;
			return true;
		}
		if (!cell) {
			return false;
		}
		labelResizeSide = 'left';
		return Math.abs(event.clientX - cell.getBoundingClientRect().right) <= LABEL_EDGE;
	}

	function placeLabelResizer(clientY) {
		if (!labelResizer) {
			return;
		}
		var wrap = labelTargetAt(null, clientY);
		var cell = labelColumnCellIn(wrap);
		if (wrap && labelResizeSide === 'right') {
			var end = wrap.querySelector('.combat-compare tbody .combat-label-col.is-end');
			if (end && end.offsetWidth > 0 && getComputedStyle(end).display !== 'none') {
				cell = end;
			}
		}
		if (!wrap || !cell) {
			labelResizer.classList.remove('is-visible');
			return;
		}
		var box = wrap.getBoundingClientRect();
		var y = clientY;
		if (y == null || isNaN(y)) {
			y = (box.top + box.bottom) / 2;
		}
		labelResizeY = Math.max(box.top + 12, Math.min(box.bottom - 12, y));
		labelResizer.style.left = Math.round(labelResizeEdgeX(cell, labelResizeSide)) + 'px';
		labelResizer.style.top = Math.round(labelResizeY) + 'px';
		if (!prefersFineHover() || labelResizing) {
			labelResizer.classList.add('is-visible');
		}
	}

	function showLabelResizer(clientY) {
		if (!labelResizer) {
			return;
		}
		placeLabelResizer(clientY);
		labelResizer.classList.add('is-visible');
	}

	function hideLabelResizer() {
		if (!labelResizer || labelResizing || !prefersFineHover()) {
			return;
		}
		labelResizer.classList.remove('is-visible');
	}

	function initSpyLabelColumn() {
		var page = labelPage();
		if (!page) {
			return;
		}

		var stored = parseInt(readStorage(labelStorageKey()), 10);
		applySpyLabelWidth(isNaN(stored) ? defaultLabelWidth() : stored, false);

		if (!labelResizer) {
			labelResizer = document.createElement('span');
			labelResizer.id = 'spyColResizer';
			labelResizer.className = 'spy-col-resizer';
			labelResizer.setAttribute('role', 'separator');
			labelResizer.setAttribute('aria-orientation', 'vertical');
			document.body.appendChild(labelResizer);
		}
		var resizeName = page.getAttribute('data-label-resize');
		if (resizeName) {
			labelResizer.setAttribute('aria-label', resizeName);
		}

		if (!prefersFineHover()) {
			showLabelResizer();
		}

		if (page.getAttribute('data-label-bound') === '1') {
			return;
		}
		page.setAttribute('data-label-bound', '1');

		var range = document.getElementById('spyOptColWidth');
		if (range) {
			range.addEventListener('input', function () {
				applySpyLabelWidth(parseInt(range.value, 10) || defaultLabelWidth(), false);
			});
			range.addEventListener('change', function () {
				applySpyLabelWidth(parseInt(range.value, 10) || defaultLabelWidth(), true);
			});
		}

		var startX = 0;
		var startW = 0;

		function stopResize(event) {
			if (!labelResizing) {
				return;
			}
			if (event && labelResizer.hasPointerCapture(event.pointerId)) {
				labelResizer.releasePointerCapture(event.pointerId);
			}
			labelResizing = false;
			document.documentElement.classList.remove('spy-resizing');
			applySpyLabelWidth(currentSpyLabelWidth(), true);
			if (!event || !isOnLabelResizeEdge(event)) {
				hideLabelResizer();
			}
		}

		function startResize(event) {
			event.preventDefault();
			event.stopPropagation();
			labelResizing = true;
			startX = event.clientX;
			startW = currentSpyLabelWidth();
			document.documentElement.classList.add('spy-resizing');
			showLabelResizer(event.clientY);
			try {
				labelResizer.setPointerCapture(event.pointerId);
			} catch (e) {}
		}

		page.addEventListener('pointerdown', function (event) {
			if (event.pointerType === 'mouse' && event.button !== 0) {
				return;
			}
			if (!isOnLabelResizeEdge(event)) {
				return;
			}
			startResize(event);
		});
		document.addEventListener('pointermove', function (event) {
			if (!labelPage()) {
				hideLabelResizer();
				document.documentElement.classList.remove('spy-resize-hover');
				return;
			}
			if (labelResizing) {
				var delta = event.clientX - startX;
				if (labelResizeSide === 'right') {
					delta = -delta;
				}
				applySpyLabelWidth(startW + delta, false);
				placeLabelResizer(event.clientY);
				return;
			}
			if (!prefersFineHover()) {
				placeLabelResizer(labelResizeY);
				return;
			}
			if (isOnLabelResizeEdge(event)) {
				document.documentElement.classList.add('spy-resize-hover');
				showLabelResizer(event.clientY);
			} else {
				document.documentElement.classList.remove('spy-resize-hover');
				hideLabelResizer();
			}
		});
		labelResizer.addEventListener('pointerdown', function (event) {
			if (event.pointerType === 'mouse' && event.button !== 0) {
				return;
			}
			startResize(event);
		});
		labelResizer.addEventListener('pointerup', stopResize);
		labelResizer.addEventListener('pointercancel', stopResize);
		page.addEventListener('pointerup', stopResize);
		page.addEventListener('pointercancel', stopResize);
		document.addEventListener('scroll', function () {
			if (labelResizer && (labelResizing || labelResizer.classList.contains('is-visible') || !prefersFineHover())) {
				placeLabelResizer(labelResizeY);
			}
		}, true);
		document.querySelectorAll('details.combat-round').forEach(function (el) {
			el.addEventListener('toggle', function () {
				if (labelResizer && (labelResizing || labelResizer.classList.contains('is-visible') || !prefersFineHover())) {
					placeLabelResizer(labelResizeY);
				}
			});
		});
	}

	function initEmpireSettings() {
		var menu = document.getElementById('empireSettingsMenu');
		if (!menu) {
			return;
		}

		if (menu.parentNode !== document.body) {
			document.body.appendChild(menu);
		}

		if (menu.getAttribute('data-empire-bound') === '1') {
			return;
		}
		menu.setAttribute('data-empire-bound', '1');

		var zerosBox = document.getElementById('empireOptZeros');
		var compactBox = document.getElementById('empireOptCompact');
		if (zerosBox) {
			zerosBox.checked = zerosShown();
			zerosBox.addEventListener('change', function () {
				applyZeros(zerosBox.checked, true);
			});
		}
		if (compactBox) {
			compactBox.checked = compactShown();
			compactBox.addEventListener('change', function () {
				applyCompact(compactBox.checked, true);
			});
		}

		document.addEventListener('click', function (event) {
			var btn = event.target.closest('.empire-settings-toggle');
			if (btn) {
				event.stopPropagation();
				if (menu.hidden) {
					menu.hidden = false;
					document.querySelectorAll('.empire-settings-toggle').forEach(function (el) {
						el.setAttribute('aria-expanded', 'true');
					});
					placeEmpireSettingsMenu();
					window.requestAnimationFrame(placeEmpireSettingsMenu);
				} else {
					closeEmpireSettings();
				}
				return;
			}
			if (!menu.hidden && !menu.contains(event.target)) {
				closeEmpireSettings();
			}
		});

		menu.addEventListener('click', function (event) {
			event.stopPropagation();
			if (event.target.closest('.spy-filter-close')) {
				closeEmpireSettings();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeEmpireSettings();
			}
		});
		window.addEventListener('resize', placeEmpireSettingsMenu);
	}

	function applyNewSpyColumns() {
		var page = document.querySelector('.spy-reports-page');
		if (!page) {
			return;
		}
		var idSet = Object.create(null);
		String(page.getAttribute('data-new-reports') || '').split(',').forEach(function (id) {
			if (id) {
				idSet[id] = true;
			}
		});
		var heads = page.querySelectorAll('.empire-wrap .spy-planet-head th[data-report-id]');
		var flags = [];
		Array.prototype.forEach.call(heads, function (th) {
			var isNew = !!idSet[th.getAttribute('data-report-id')];
			flags.push(isNew);
			th.classList.toggle('spy-col-new', isNew);
		});
		if (!flags.length) {
			return;
		}
		var tables = document.querySelectorAll('.spy-reports-page .empire-wrap .empire-table, .spy-planet-sticky .empire-table');
		Array.prototype.forEach.call(tables, function (table) {
			Array.prototype.forEach.call(table.rows, function (row) {
				var offset = 0;
				Array.prototype.forEach.call(row.cells, function (cell) {
					var span = cell.colSpan || 1;
					if (span !== 1) {
						offset += span;
						return;
					}
					if (offset === 0) {
						offset += 1;
						return;
					}
					var col = offset - 1;
					if (col >= 0 && col < flags.length) {
						cell.classList.toggle('spy-col-new', flags[col]);
					}
					offset += 1;
				});
			});
		});
	}

	function syncSpyNewWaves() {
		var wrap = document.querySelector('.spy-reports-page .empire-wrap');
		var table = wrap ? wrap.querySelector('.empire-table') : null;
		var host = document.getElementById('spyColWaves');
		var heads = wrap ? wrap.querySelectorAll('.spy-planet-head th.spy-col-new') : [];
		if (!wrap || !table || !heads.length) {
			if (host) {
				host.remove();
			}
			return;
		}
		if (!host) {
			host = document.createElement('div');
			host.id = 'spyColWaves';
			host.className = 'spy-col-waves';
			host.setAttribute('aria-hidden', 'true');
			wrap.appendChild(host);
		}
		host.style.top = table.offsetTop + 'px';
		host.style.left = table.offsetLeft + 'px';
		host.style.width = table.offsetWidth + 'px';
		host.style.height = table.offsetHeight + 'px';
		while (host.children.length > heads.length) {
			host.removeChild(host.lastChild);
		}
		while (host.children.length < heads.length) {
			var wave = document.createElement('div');
			wave.className = 'spy-col-wave';
			host.appendChild(wave);
		}
		var tableRect = table.getBoundingClientRect();
		Array.prototype.forEach.call(heads, function (th, i) {
			var rect = th.getBoundingClientRect();
			host.children[i].style.left = (rect.left - tableRect.left) + 'px';
			host.children[i].style.width = rect.width + 'px';
		});
	}

	function refreshEmpire(root) {
		var wrap = root;
		if (!wrap || !wrap.querySelectorAll) {
			wrap = document.querySelector('.empire-wrap');
		}
		if (!wrap) {
			return;
		}
		enhanceEmpireQuantities(wrap);
		applyZeros(zerosShown(), false);
		applyCompact(compactShown(), false);
		applySpyLabelWidth(currentSpyLabelWidth(), false);
		initEmpireSections(wrap);
		applyNewSpyColumns();
		syncEmpireLayout();
		initSpyPlanetSticky();
		var menu = document.getElementById('empireSettingsMenu');
		var toggle = empireSettingsToggle();
		if (toggle && menu) {
			toggle.setAttribute('aria-expanded', menu.hidden ? 'false' : 'true');
		}
		if (menu && !menu.hidden) {
			placeEmpireSettingsMenu();
		}
	}

	function parseAmount(text) {
		if (!text) {
			return 0;
		}
		var cleaned = String(text).replace(/\s/g, '').replace(/\u00a0/g, '').replace(/\./g, '').replace(',', '.');
		var n = parseFloat(cleaned);
		return isNaN(n) ? 0 : n;
	}

	function enhanceSpyReports(root) {
		$(root).find('.spyRaport').each(function () {
			var $report = $(this);
			if ($report.data('zerosReady')) {
				return;
			}
			$report.data('zerosReady', true);

			if (!$report.children('.zero-toggle').length) {
				$report.prepend(makeZeroButton());
			}

			$report.find('.spyRaportContainer').each(function () {
				var $container = $(this);
				var $items = $container.children('.spyRaportItem');
				if ($items.length) {
					if ($items.length && $items.filter('.is-zero').length === $items.length) {
						$container.addClass('is-zero');
					}
					return;
				}

				$container.find('.spyRaportContainerRow').each(function () {
					var $cells = $(this).children('.spyRaportContainerCell');
					var i;
					for (i = 0; i + 1 < $cells.length; i += 2) {
						if (parseAmount($cells.eq(i + 1).text()) === 0) {
							$cells.eq(i).addClass('is-zero');
							$cells.eq(i + 1).addClass('is-zero');
						}
					}
				});

				var $all = $container.find('.spyRaportContainerCell');
				if ($all.length && $all.filter('.is-zero').length === $all.length) {
					$container.addClass('is-zero');
				}
			});
		});
	}

	function buildSpyPlanetSticky() {
		var row = document.querySelector('.spy-planet-head');
		var bar = document.getElementById('spyPlanetSticky');
		if (!row) {
			if (bar) {
				bar.remove();
			}
			return null;
		}
		if (!bar) {
			bar = document.createElement('div');
			bar.id = 'spyPlanetSticky';
			bar.className = 'spy-planet-sticky';
			bar.hidden = true;
			bar.innerHTML = '<table class="empire-table"><thead></thead></table>';
			document.body.appendChild(bar);
		}
		var thead = bar.querySelector('thead');
		var clone = row.cloneNode(true);
		clone.querySelectorAll('[id]').forEach(function (el) {
			el.removeAttribute('id');
		});
		thead.innerHTML = '';
		thead.appendChild(clone);
		return bar;
	}

	function syncSpyPlanetSticky() {
		var row = document.querySelector('.spy-planet-head');
		var wrap = document.querySelector('.spy-reports-page .empire-wrap');
		var bar = document.getElementById('spyPlanetSticky');
		if (!row || !wrap) {
			if (bar) {
				bar.hidden = true;
			}
			return;
		}
		if (!bar || bar.querySelectorAll('th').length !== row.children.length) {
			bar = buildSpyPlanetSticky();
		}
		if (!bar) {
			return;
		}
		var stuck = row.getBoundingClientRect().top < 0;
		bar.hidden = !stuck;
		if (!stuck) {
			return;
		}
		var wrapRect = wrap.getBoundingClientRect();
		var srcTable = row.closest('table');
		bar.style.top = '0px';
		bar.style.left = wrapRect.left + 'px';
		bar.style.width = wrap.clientWidth + 'px';
		var cloneRow = bar.querySelector('tr');
		if (!cloneRow) {
			return;
		}
		var srcCells = row.children;
		var dstCells = cloneRow.children;
		var i;
		for (i = 0; i < srcCells.length && i < dstCells.length; i++) {
			var width = srcCells[i].getBoundingClientRect().width + 'px';
			dstCells[i].style.width = width;
			dstCells[i].style.minWidth = width;
			dstCells[i].style.maxWidth = width;
		}
		var cloneTable = bar.querySelector('table');
		var tableWidth = srcTable ? srcTable.getBoundingClientRect().width : wrap.scrollWidth;
		cloneTable.style.width = tableWidth + 'px';
		cloneTable.style.marginLeft = srcTable
			? Math.round(srcTable.getBoundingClientRect().left - wrapRect.left + wrap.scrollLeft) + 'px'
			: '0px';
		bar.scrollLeft = wrap.scrollLeft;
	}

	var spyStickyBound = false;

	function initSpyPlanetSticky() {
		if (!document.querySelector('.spy-reports-page')) {
			var leftover = document.getElementById('spyPlanetSticky');
			if (leftover) {
				leftover.remove();
			}
			return;
		}
		buildSpyPlanetSticky();
		if (!spyStickyBound) {
			spyStickyBound = true;
			window.addEventListener('scroll', syncSpyPlanetSticky, {passive: true});
			window.addEventListener('resize', syncSpyPlanetSticky);
			document.addEventListener('scroll', function (event) {
				if (event.target && event.target.classList && event.target.classList.contains('empire-wrap')) {
					syncSpyPlanetSticky();
				}
			}, true);
		}
		syncSpyPlanetSticky();
	}

	function syncEmpireLayout() {
		var wrap = document.querySelector('.empire-wrap');
		if (!wrap) {
			return;
		}
		wrap.style.maxHeight = '';
		if (document.body && document.body.id === 'imperium') {
			var top = wrap.getBoundingClientRect().top;
			var available = Math.floor(window.innerHeight - top - 8);
			if (available > 80) {
				wrap.style.maxHeight = available + 'px';
			}
		}

		var offset = 0;
		var rows = wrap.querySelectorAll('.empire-table thead tr.empire-colhead');
		var i;
		for (i = 0; i < rows.length; i++) {
			var cells = rows[i].querySelectorAll('th, td');
			var j;
			for (j = 0; j < cells.length; j++) {
				cells[j].style.top = offset + 'px';
			}
			offset += rows[i].getBoundingClientRect().height;
		}
		syncSpyPlanetSticky();
		if (labelResizer && (labelResizing || labelResizer.classList.contains('is-visible') || !prefersFineHover())) {
			placeLabelResizer(labelResizeY);
		}
		syncSpyNewWaves();
	}

	function sectionStorageKey() {
		return document.querySelector('.spy-reports-page') ? SPY_SECTIONS_KEY : EMPIRE_SECTIONS_KEY;
	}

	function readCollapsedSections() {
		try {
			var parsed = JSON.parse(readStorage(sectionStorageKey()) || '[]');
			return Array.isArray(parsed) ? parsed.map(String) : [];
		} catch (e) {
			return [];
		}
	}

	function writeCollapsedSections(ids) {
		writeStorage(sectionStorageKey(), JSON.stringify(ids));
	}

	function applySectionCollapsed(section, collapsed) {
		section.classList.toggle('is-collapsed', collapsed);
		var btn = section.querySelector('.empire-section-toggle');
		if (btn) {
			btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
		}
	}

	var empireSectionsBound = false;

	function initEmpireSections(root) {
		var wrap = root && root.querySelectorAll ? root : document.querySelector('.empire-wrap');
		if (!wrap) {
			return;
		}
		var collapsed = readCollapsedSections();
		wrap.querySelectorAll('.empire-section').forEach(function (section) {
			var id = section.getAttribute('data-section') || '';
			applySectionCollapsed(section, collapsed.indexOf(id) !== -1);
		});
		if (empireSectionsBound) {
			return;
		}
		empireSectionsBound = true;
		document.addEventListener('click', function (event) {
			var head = event.target.closest('.empire-section-head');
			if (!head) {
				return;
			}
			var section = head.closest('.empire-section');
			if (!section) {
				return;
			}
			var id = section.getAttribute('data-section') || '';
			var next = !section.classList.contains('is-collapsed');
			applySectionCollapsed(section, next);
			var ids = readCollapsedSections();
			var idx = ids.indexOf(id);
			if (next && idx === -1) {
				ids.push(id);
			} else if (!next && idx !== -1) {
				ids.splice(idx, 1);
			}
			writeCollapsedSections(ids);
			syncEmpireLayout();
		});
	}

	function initEmpire() {
		var $wrap = $('.empire-wrap');
		initSpyLabelColumn();
		if (!$wrap.length) {
			return;
		}
		enhanceEmpireQuantities($wrap);
		initEmpireSettings();
		initSpyPlanetSticky();
		initEmpireSections($wrap[0]);
		syncEmpireLayout();
		window.addEventListener('resize', syncEmpireLayout);
	}

	var CARD_NARROW_QUERY = '(max-width: 640px)';
	var SUB_GAP = 8;

	function resetBuildCardSubs() {
		$('.build-card-meta').each(function () {
			this.classList.remove('is-sub-wrapped');
			var costs = this.querySelector('.build-card-costs');
			if (costs) {
				costs.style.paddingBottom = '';
			}
		});
	}

	function lastCostLine(costs) {
		var imgs = costs.querySelectorAll('img');
		if (!imgs.length) {
			return null;
		}
		var img = imgs[imgs.length - 1];
		var imgRect = img.getBoundingClientRect();
		var left = imgRect.left;
		var prev = img.previousElementSibling;
		if (prev) {
			left = Math.min(left, prev.getBoundingClientRect().left);
		}
		return {
			left: left,
			top: imgRect.top,
			bottom: imgRect.bottom
		};
	}

	function wrapOverlappingSubs() {
		$('.build-card-meta').each(function () {
			var wrap = this.querySelector('.build-card-subwrap');
			var sub = this.querySelector('.build-card-sub');
			var costs = this.querySelector('.build-card-costs');
			if (!wrap || !sub || !costs || !sub.offsetParent || !sub.textContent.trim()) {
				return;
			}
			var line = lastCostLine(costs);
			if (!line) {
				return;
			}
			var subRect = wrap.getBoundingClientRect();
			var sameBand = subRect.bottom > line.top + 1 && subRect.top < line.bottom - 1;
			if (sameBand && subRect.right + SUB_GAP > line.left) {
				this.classList.add('is-sub-wrapped');
				costs.style.paddingBottom = Math.ceil(subRect.height + 4) + 'px';
			}
		});
	}

	var subSyncRaf = 0;
	var lastGridWidth = -1;

	function syncBuildCardSubs() {
		if (subSyncRaf) {
			window.cancelAnimationFrame(subSyncRaf);
		}
		subSyncRaf = window.requestAnimationFrame(function () {
			subSyncRaf = 0;
			resetBuildCardSubs();
			if (!document.querySelector('.build-card-meta') || !window.matchMedia(CARD_NARROW_QUERY).matches) {
				return;
			}
			wrapOverlappingSubs();
		});
	}

	function initBuildCards() {
		if (!document.querySelector('.build-grid')) {
			return;
		}
		syncBuildCardSubs();
		window.addEventListener('resize', syncBuildCardSubs);
		if (window.ResizeObserver) {
			document.querySelectorAll('.build-grid').forEach(function (grid) {
				new ResizeObserver(function (entries) {
					var width = Math.round(entries[0].contentRect.width);
					if (width === lastGridWidth) {
						return;
					}
					lastGridWidth = width;
					syncBuildCardSubs();
				}).observe(grid);
			});
		}
	}

	function init() {
		initNav();
		initEmpire();
		initBuildCards();
		applyZeros(readStorage(ZERO_KEY) === '1', false);
		applyCompact(readStorage(COMPACT_KEY) === '1', false);
		enhanceSpyReports(document);
	}

	window.RisistarLayout = {
		enhanceSpyReports: enhanceSpyReports,
		refreshEmpire: refreshEmpire
	};

	$(init);
}(window, document, window.jQuery));
