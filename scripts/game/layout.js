(function (window, document, $) {
	'use strict';

	var NAV_KEY = 'risistar.nav';
	var FAVORITES_KEY = 'risistar.navFavorites';
	var FAVORITES_OPEN_KEY = 'risistar.navFavoritesOpen';
	var ZERO_KEY = 'risistar.showZeros';
	var COMPACT_KEY = 'risistar.compactNumbers';
	var MOBILE_NAV_QUERY = '(max-width: 640px)';
	var SPY_LABEL_KEY = 'risistar.spyLabelWidth';
	var EMPIRE_LABEL_KEY = 'risistar.empireLabelWidth';
	var COMBAT_LABEL_KEY = 'risistar.combatLabelWidth';
	var EMPIRE_SECTIONS_KEY = 'risistar.empireCollapsed';
	var SPY_SECTIONS_KEY = 'risistar.spyCollapsed';
	var LABEL_MIN = 56;
	var LABEL_MAX = 220;
	var LABEL_EDGE = 10;
	var favoritesInitialized = false;
	var labelResizer = null;
	var labelResizeY = 0;
	var labelResizing = false;
	var labelResizeSide = 'left';

	function getLNG() {
		return window.LayoutLNG || {
			navExpand: 'Afficher le menu',
			navCollapse: 'Réduire le menu',
			favoriteAdd: 'Ajouter aux favoris',
			favoriteRemove: 'Retirer des favoris',
			favoritesOpen: 'Afficher les favoris',
			favoritesClose: 'Masquer les favoris',
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
		var isMobile = window.matchMedia(MOBILE_NAV_QUERY).matches;
		collapsed = isMobile && collapsed;
		var root = document.documentElement;
		root.classList.toggle('nav-collapsed', collapsed);
		root.classList.toggle('nav-expanded', !collapsed);

		var toggle = document.getElementById('navToggle');
		if (toggle) {
			var lng = getLNG();
			var label = collapsed ? lng.navExpand : lng.navCollapse;
			toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
			toggle.setAttribute('aria-label', label);
			toggle.setAttribute('title', label);
		}

		if (persist && isMobile) {
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

		var mobileNavQuery = window.matchMedia(MOBILE_NAV_QUERY);
		var stored = readStorage(NAV_KEY);
		var collapsed = mobileNavQuery.matches && stored !== 'expanded';
		applyNavState(collapsed, false);

		var toggle = document.getElementById('navToggle');
		if (toggle) {
			toggle.addEventListener('click', function () {
				applyNavState(!isCollapsed(), true);
			});
		}

		var syncNavViewport = function (event) {
			var mobileCollapsed = event.matches && readStorage(NAV_KEY) !== 'expanded';
			applyNavState(mobileCollapsed, false);
		};
		if (mobileNavQuery.addEventListener) {
			mobileNavQuery.addEventListener('change', syncNavViewport);
		} else if (mobileNavQuery.addListener) {
			mobileNavQuery.addListener(syncNavViewport);
		}

		$(document).on('click', '.nav-backdrop', function () {
			if (window.matchMedia('(max-width: 640px)').matches) {
				applyNavState(true, true);
			}
		});

		markActiveNav();
		initDarkmatterMenu();
	}

	function initDarkmatterMenu() {
		var group = document.querySelector('#menu .nav-group');
		if (!group) {
			return;
		}

		var toggle = group.querySelector('.nav-group-toggle');
		var submenu = group.querySelector('.nav-submenu');
		if (!toggle || !submenu) {
			return;
		}

		var suppressHover = false;
		var hoverTimer = null;
		var HOVER_DELAY_MS = 200;

		function canHover() {
			return window.matchMedia('(hover: hover) and (pointer: fine)').matches;
		}

		function clearHoverTimer() {
			if (hoverTimer) {
				clearTimeout(hoverTimer);
				hoverTimer = null;
			}
		}

		function syncExpanded() {
			var open = group.classList.contains('is-open') || group.classList.contains('is-hover');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		}

		if (submenu.querySelector('a.is-active')) {
			group.classList.add('is-open');
			toggle.classList.add('is-active');
		}
		syncExpanded();

		toggle.addEventListener('click', function (event) {
			event.preventDefault();
			if (document.documentElement.classList.contains('nav-collapsed')) {
				applyNavState(false, true);
				group.classList.add('is-open');
				syncExpanded();
				return;
			}

			var nextOpen = !group.classList.contains('is-open');
			group.classList.toggle('is-open', nextOpen);
			if (!nextOpen) {
				clearHoverTimer();
				group.classList.remove('is-hover');
				suppressHover = group.matches(':hover');
			}
			syncExpanded();
		});

		group.addEventListener('mouseenter', function () {
			if (suppressHover || !canHover() || document.documentElement.classList.contains('nav-collapsed')) {
				return;
			}
			clearHoverTimer();
			hoverTimer = setTimeout(function () {
				hoverTimer = null;
				group.classList.add('is-hover');
				syncExpanded();
			}, HOVER_DELAY_MS);
		});

		group.addEventListener('mouseleave', function () {
			suppressHover = false;
			clearHoverTimer();
			group.classList.remove('is-hover');
			syncExpanded();
		});

		document.addEventListener('click', function (event) {
			if (group.contains(event.target)) {
				return;
			}
			if (!group.classList.contains('is-open')) {
				return;
			}
			group.classList.remove('is-open');
			syncExpanded();
		});
	}

	function readFavorites() {
		var stored = readStorage(FAVORITES_KEY);
		if (!stored) {
			return [];
		}
		try {
			var favorites = JSON.parse(stored);
			return Array.isArray(favorites) ? favorites.filter(function (href) {
				return typeof href === 'string';
			}) : [];
		} catch (e) {
			return [];
		}
	}

	function writeFavorites(favorites) {
		writeStorage(FAVORITES_KEY, JSON.stringify(favorites));
	}

	function favoriteLinks() {
		return Array.prototype.slice.call(document.querySelectorAll('#menu a.nav-link[href]:not(.nav-logout):not(.nav-administration)'));
	}

	function favoriteIconClass(link) {
		var classes = Array.prototype.slice.call(link.classList);
		for (var i = 0; i < classes.length; i++) {
			if (classes[i].indexOf('nav-') === 0 && classes[i] !== 'nav-link') {
				return classes[i];
			}
		}
		return '';
	}

	function setFavoriteDockOpen(open, persist) {
		var dock = document.getElementById('favoriteDock');
		var toggle = document.getElementById('favoriteDockToggle');
		if (!dock || !toggle) {
			return;
		}
		var lng = getLNG();
		dock.classList.toggle('is-open', open);
		toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		toggle.setAttribute('title', open ? lng.favoritesClose : lng.favoritesOpen);
		if (persist) {
			writeStorage(FAVORITES_OPEN_KEY, open ? '1' : '0');
		}
	}

	function makeFavoriteDockLink(source) {
		var link = document.createElement('a');
		var href = source.getAttribute('href') || '';
		var labelNode = source.querySelector('.nav-label');
		var label = labelNode ? labelNode.textContent.trim() : source.textContent.trim();
		var iconClass = favoriteIconClass(source);
		link.className = 'favorite-dock-link' + (iconClass ? ' ' + iconClass : '');
		link.href = href;
		link.title = label;
		link.setAttribute('aria-label', label);
		if (source.target) {
			link.target = source.target;
		}
		if (source.classList.contains('is-active')) {
			link.classList.add('is-active');
		}

		var icon = document.createElement('span');
		icon.className = 'nav-icon';
		icon.setAttribute('aria-hidden', 'true');
		link.appendChild(icon);
		return link;
	}

	function renderFavorites() {
		var dockLinks = document.getElementById('favoriteDockLinks');
		var empty = document.getElementById('favoriteDockEmpty');
		var count = document.querySelector('.favorite-dock-count');
		if (!dockLinks || !empty) {
			return;
		}
		var favorites = readFavorites();
		var sources = favoriteLinks();
		var available = {};
		sources.forEach(function (link) {
			available[link.getAttribute('href') || ''] = link;
		});
		favorites = favorites.filter(function (href, index) {
			return available[href] && favorites.indexOf(href) === index;
		});
		writeFavorites(favorites);
		dockLinks.textContent = '';
		favorites.forEach(function (href) {
			dockLinks.appendChild(makeFavoriteDockLink(available[href]));
		});
		empty.hidden = favorites.length > 0;
		if (count) {
			count.textContent = String(favorites.length);
		}

		document.querySelectorAll('.nav-favorite-toggle').forEach(function (button) {
			var isFavorite = favorites.indexOf(button.getAttribute('data-favorite-href')) !== -1;
			var lng = getLNG();
			button.classList.toggle('is-favorite', isFavorite);
			button.setAttribute('aria-pressed', isFavorite ? 'true' : 'false');
			button.setAttribute('title', isFavorite ? lng.favoriteRemove : lng.favoriteAdd);
			button.setAttribute('aria-label', (isFavorite ? lng.favoriteRemove : lng.favoriteAdd) + ' : ' + button.getAttribute('data-favorite-label'));
		});
	}

	function initFavorites() {
		if (favoritesInitialized || !window.matchMedia('(max-width: 640px)').matches) {
			return;
		}
		var dock = document.getElementById('favoriteDock');
		var dockToggle = document.getElementById('favoriteDockToggle');
		if (!dock || !dockToggle) {
			return;
		}
		favoritesInitialized = true;
		favoriteLinks().forEach(function (link) {
			var item = link.parentNode;
			if (!item || item.querySelector('.nav-favorite-toggle')) {
				return;
			}
			var labelNode = link.querySelector('.nav-label');
			var label = labelNode ? labelNode.textContent.trim() : link.textContent.trim();
			var button = document.createElement('button');
			button.type = 'button';
			button.className = 'nav-favorite-toggle';
			button.setAttribute('data-favorite-href', link.getAttribute('href') || '');
			button.setAttribute('data-favorite-label', label);
			button.innerHTML = '<span aria-hidden="true"></span>';
			item.classList.add('nav-favorite-item');
			item.appendChild(button);
		});

		document.getElementById('menu').addEventListener('click', function (event) {
			var button = event.target.closest('.nav-favorite-toggle');
			if (!button) {
				return;
			}
			event.preventDefault();
			var href = button.getAttribute('data-favorite-href');
			var favorites = readFavorites();
			var index = favorites.indexOf(href);
			if (index === -1) {
				favorites.push(href);
			} else {
				favorites.splice(index, 1);
			}
			writeFavorites(favorites);
			renderFavorites();
		});

		dockToggle.addEventListener('click', function () {
			setFavoriteDockOpen(!dock.classList.contains('is-open'), true);
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && dock.classList.contains('is-open')) {
				setFavoriteDockOpen(false, true);
				dockToggle.focus();
			}
		});
		setFavoriteDockOpen(readStorage(FAVORITES_OPEN_KEY) === '1', false);
		renderFavorites();
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
			var needed = Math.min(empireSettingsContentHeight(menu) || maxH, maxH);
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
		var left = isSpy ? rect.right + 4 : rect.left;
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

	function empireSettingsContentHeight(menu) {
		var body = menu.querySelector('.empire-settings-body');
		if (!body) {
			return menu.scrollHeight;
		}
		var bar = menu.querySelector('.empire-settings-bar');
		return body.scrollHeight + (bar ? bar.offsetHeight : 0);
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

	function labelResizeEnabled() {
		return false;
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
		if (!labelResizeEnabled()) {
			return;
		}
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
		if (event.target.closest && (event.target.closest('.empire-settings-menu') || event.target.closest('.spy-history-menu') || event.target.closest('.resources-fader') || event.target.closest('.resources-fader-picker'))) {
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
		if (!labelResizeEnabled()) {
			return;
		}
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
				syncLabelMarquees();
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
			if (event.target.closest('.empire-settings-close')) {
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

	function stickyHeaderRows() {
		var spyHead = document.querySelector('.spy-planet-head');
		if (spyHead) {
			return [spyHead];
		}
		return Array.prototype.slice.call(
			document.querySelectorAll('.empire-page .empire-table thead tr.empire-colhead')
		);
	}

	function stickyHeaderCellCount(rows) {
		var count = 0;
		var i;
		for (i = 0; i < rows.length; i++) {
			count += rows[i].children.length;
		}
		return count;
	}

	function syncEmpireTopScroll(wrap) {
		if (!wrap || !wrap.closest) {
			return;
		}
		var page = wrap.closest('.empire-page');
		if (!page || (document.body.id !== 'imperium' && document.body.id !== 'spyReports')) {
			return;
		}
		var bar = page.querySelector('.empire-scroll-top');
		if (!bar) {
			bar = document.createElement('div');
			bar.className = 'empire-scroll-top';
			bar.innerHTML = '<div class="empire-scroll-top-content"></div>';
			page.insertBefore(bar, wrap);
		}
		bar._empireWrap = wrap;
		if (!bar._empireScrollBound) {
			bar._empireScrollBound = true;
			bar.addEventListener('scroll', function () {
				if (bar._empireWrap && bar._empireWrap.scrollLeft !== bar.scrollLeft) {
					bar._empireWrap.scrollLeft = bar.scrollLeft;
				}
			});
		}
		if (!wrap._empireScrollBound) {
			wrap._empireScrollBound = true;
			wrap.addEventListener('scroll', function () {
				var currentBar = page.querySelector('.empire-scroll-top');
				if (currentBar && currentBar.scrollLeft !== wrap.scrollLeft) {
					currentBar.scrollLeft = wrap.scrollLeft;
				}
			});
		}

		var content = bar.querySelector('.empire-scroll-top-content');
		var table = wrap.querySelector('.empire-table');
		var contentWidth = Math.max(
			wrap.scrollWidth,
			table ? Math.ceil(table.getBoundingClientRect().width) : 0
		);
		content.style.width = contentWidth + 'px';
		bar.hidden = contentWidth <= wrap.clientWidth + 1;
		if (!bar.hidden && bar.scrollLeft !== wrap.scrollLeft) {
			bar.scrollLeft = wrap.scrollLeft;
		}
	}

	function buildSpyPlanetSticky() {
		var rows = stickyHeaderRows();
		var bar = document.getElementById('spyPlanetSticky');
		if (!rows.length) {
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
		thead.innerHTML = '';
		rows.forEach(function (row) {
			var clone = row.cloneNode(true);
			clone.querySelectorAll('[id]').forEach(function (el) {
				el.removeAttribute('id');
			});
			thead.appendChild(clone);
		});
		return bar;
	}

	function syncSpyPlanetSticky() {
		var rows = stickyHeaderRows();
		var wrap = document.querySelector('.empire-wrap');
		var bar = document.getElementById('spyPlanetSticky');
		if (!rows.length || !wrap) {
			if (bar) {
				bar.hidden = true;
			}
			return;
		}
		if (!bar || bar.querySelectorAll('th, td').length !== stickyHeaderCellCount(rows)) {
			bar = buildSpyPlanetSticky();
		}
		if (!bar) {
			return;
		}
		var stuck = rows[0].getBoundingClientRect().top < 0;
		bar.hidden = !stuck;
		if (!stuck) {
			return;
		}
		var wrapRect = wrap.getBoundingClientRect();
		var srcTable = rows[0].closest('table');
		bar.style.top = '0px';
		bar.style.left = wrapRect.left + 'px';
		bar.style.width = wrap.clientWidth + 'px';
		var cloneRows = bar.querySelectorAll('thead tr');
		var r;
		for (r = 0; r < rows.length && r < cloneRows.length; r++) {
			var srcCells = rows[r].children;
			var dstCells = cloneRows[r].children;
			var i;
			for (i = 0; i < srcCells.length && i < dstCells.length; i++) {
				var width = srcCells[i].getBoundingClientRect().width + 'px';
				dstCells[i].style.width = width;
				dstCells[i].style.minWidth = width;
				dstCells[i].style.maxWidth = width;
			}
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
		if (!stickyHeaderRows().length) {
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
		if (wrap) {
			wrap.style.maxHeight = '';

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
			if (labelResizer && (labelResizing || labelResizer.classList.contains('is-visible') || !prefersFineHover())) {
				placeLabelResizer(labelResizeY);
			}
			syncSpyNewWaves();
			syncEmpireTopScroll(wrap);
		}
		syncLabelMarquees();
		if (stickyHeaderRows().length) {
			buildSpyPlanetSticky();
		}
		syncSpyPlanetSticky();
	}

	function labelScrollInner(el) {
		var inner = el.firstElementChild;
		if (inner && inner.classList.contains('spy-label-scroll')) {
			return inner;
		}
		inner = document.createElement('span');
		inner.className = 'spy-label-scroll';
		while (el.firstChild) {
			inner.appendChild(el.firstChild);
		}
		el.appendChild(inner);
		return inner;
	}

	function syncLabelMarquees() {
		var page = document.querySelector('.empire-page, .combat-report');
		if (!page || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			return;
		}
		var nodes = page.querySelectorAll('.spy-label-text, .resources-level, .combat-ship-label, .spy-planet-label, .spy-col-user');
		var mobileNames = window.matchMedia('(max-width: 640px)').matches;
		var i;
		var el;
		var inner;
		var overflow;
		var shift;
		var duration;
		var delay;
		var style;
		var available;
		for (i = 0; i < nodes.length; i++) {
			el = nodes[i];
			if (!el.offsetWidth) {
				continue;
			}
			if (!mobileNames && (el.classList.contains('spy-planet-label') || el.classList.contains('spy-col-user'))) {
				if (el.classList.contains('is-overflowing')) {
					el.classList.remove('is-overflowing');
					el.style.removeProperty('--marquee-shift');
					el.style.removeProperty('--marquee-duration');
					el.style.removeProperty('--marquee-delay');
				}
				continue;
			}
			inner = labelScrollInner(el);
			style = window.getComputedStyle(el);
			available = el.clientWidth
				- (parseFloat(style.paddingLeft) || 0)
				- (parseFloat(style.paddingRight) || 0);
			overflow = inner.offsetWidth - available;
			if (overflow > 1) {
				shift = '-' + overflow + 'px';
				duration = Math.max(2.8, Math.min(9, overflow / 22)).toFixed(2) + 's';
				delay = ((i % 6) * 0.35).toFixed(2) + 's';
				if (el.style.getPropertyValue('--marquee-shift') !== shift) {
					el.style.setProperty('--marquee-shift', shift);
				}
				if (el.style.getPropertyValue('--marquee-duration') !== duration) {
					el.style.setProperty('--marquee-duration', duration);
				}
				if (el.style.getPropertyValue('--marquee-delay') !== delay) {
					el.style.setProperty('--marquee-delay', delay);
				}
				el.classList.add('is-overflowing');
			} else if (el.classList.contains('is-overflowing')) {
				el.classList.remove('is-overflowing');
				el.style.removeProperty('--marquee-shift');
				el.style.removeProperty('--marquee-duration');
				el.style.removeProperty('--marquee-delay');
			}
		}
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
		if ($wrap.length) {
			enhanceEmpireQuantities($wrap);
			initEmpireSettings();
			initSpyPlanetSticky();
			initEmpireSections($wrap[0]);
		}
		syncEmpireLayout();
		window.addEventListener('resize', syncEmpireLayout);
	}

	var FADER_STEP_PX = 14;
	var FADER_CLICK_PX = 8;
	var faderHud = null;
	var faderPicker = null;
	var faderState = null;
	var faderBound = false;

	function factorPercent(level) {
		return Math.max(0, Math.min(10, Number(level) || 0)) * 10;
	}

	function factorLabel(level) {
		return factorPercent(level) + '%';
	}

	function clampFactorLevel(level) {
		return Math.max(0, Math.min(10, Math.round(level)));
	}

	function applyFaderLevel(btn, level, persist) {
		level = clampFactorLevel(level);
		var percent = factorPercent(level);
		btn.setAttribute('data-level', String(level));
		btn.setAttribute('aria-valuenow', String(percent));
		var valueEl = btn.querySelector('.resources-fader-value');
		if (valueEl) {
			valueEl.textContent = factorLabel(level);
		}
		var cell = btn.parentNode;
		if (cell) {
			var range = cell.querySelector('.resources-factor-range');
			if (range) {
				if (String(range.value) !== String(level)) {
					range.value = String(level);
				}
				range.style.setProperty('--factor', percent + '%');
			}
			var sliderVal = cell.querySelector('.resources-slider-value');
			if (sliderVal) {
				sliderVal.textContent = factorLabel(level);
			}
		}
		if (persist) {
			var hidden = btn.parentNode ? btn.parentNode.querySelector('input[type="hidden"]') : null;
			if (hidden) {
				hidden.value = String(level);
			}
		}
		return level;
	}

	function ensureFaderHud() {
		if (faderHud) {
			return faderHud;
		}
		faderHud = document.createElement('div');
		faderHud.className = 'resources-fader-hud';
		faderHud.hidden = true;
		document.body.appendChild(faderHud);
		return faderHud;
	}

	function placeFaderHud(clientX, clientY, level) {
		var hud = ensureFaderHud();
		hud.textContent = factorLabel(level);
		hud.hidden = false;
		var y = Math.max(24, Math.min(window.innerHeight - 24, clientY));
		var width = hud.offsetWidth || 72;
		var left = clientX - 12 - width;
		if (left < 8) {
			left = clientX + 16;
		}
		hud.style.transform = 'translate(0, -50%)';
		hud.style.left = Math.round(left) + 'px';
		hud.style.top = Math.round(y) + 'px';
	}

	function hideFaderHud() {
		if (faderHud) {
			faderHud.hidden = true;
		}
	}

	function vibrateFactor() {
		if (!navigator.vibrate) {
			return;
		}
		try {
			navigator.vibrate(8);
		} catch (e) {}
	}

	function closeFaderPicker() {
		if (!faderPicker || faderPicker.hidden) {
			return;
		}
		faderPicker.hidden = true;
		faderPicker.removeAttribute('data-for');
	}

	function ensureFaderPicker() {
		if (faderPicker) {
			return faderPicker;
		}
		faderPicker = document.createElement('div');
		faderPicker.className = 'resources-fader-picker';
		faderPicker.hidden = true;
		faderPicker.setAttribute('role', 'listbox');
		var i;
		for (i = 10; i >= 0; i--) {
			var opt = document.createElement('button');
			opt.type = 'button';
			opt.className = 'resources-fader-option';
			opt.setAttribute('role', 'option');
			opt.setAttribute('data-level', String(i));
			opt.textContent = factorLabel(i);
			faderPicker.appendChild(opt);
		}
		document.body.appendChild(faderPicker);
		faderPicker.addEventListener('click', function (event) {
			var opt = event.target.closest('.resources-fader-option');
			if (!opt) {
				return;
			}
			var btn = document.querySelector('.resources-fader[data-picker-open="1"]');
			closeFaderPicker();
			if (!btn) {
				return;
			}
			btn.removeAttribute('data-picker-open');
			applyFaderLevel(btn, parseInt(opt.getAttribute('data-level'), 10), true);
		});
		return faderPicker;
	}

	function placeFaderPicker(btn) {
		var picker = ensureFaderPicker();
		var level = clampFactorLevel(btn.getAttribute('data-level'));
		picker.querySelectorAll('.resources-fader-option').forEach(function (opt) {
			opt.classList.toggle('is-current', opt.getAttribute('data-level') === String(level));
		});
		document.querySelectorAll('.resources-fader[data-picker-open]').forEach(function (el) {
			el.removeAttribute('data-picker-open');
		});
		btn.setAttribute('data-picker-open', '1');
		picker.hidden = false;
		var pad = 8;
		var rect = btn.getBoundingClientRect();
		var vw = window.innerWidth;
		var vh = window.innerHeight;
		var width = picker.offsetWidth || 72;
		var height = picker.offsetHeight || 280;
		var spaceRight = vw - rect.right - pad;
		var spaceLeft = rect.left - pad;
		var left = (spaceRight >= width || spaceRight >= spaceLeft) ? rect.right + 4 : rect.left - width - 4;
		left = Math.max(pad, Math.min(vw - pad - width, left));
		var top = rect.top + (rect.height / 2) - (height / 2);
		top = Math.max(pad, Math.min(vh - pad - height, top));
		picker.style.left = Math.round(left) + 'px';
		picker.style.top = Math.round(top) + 'px';
		picker.style.maxHeight = Math.max(120, vh - pad * 2) + 'px';
	}

	function openFaderPicker(btn) {
		placeFaderPicker(btn);
	}

	function stopFader(event) {
		if (!faderState) {
			return;
		}
		var state = faderState;
		var btn = state.btn;
		var moved = Math.max(Math.abs((event && event.clientX || 0) - state.startX), Math.abs((event && event.clientY || 0) - state.startY));
		var fading = state.fading;
		if (event && btn.hasPointerCapture && btn.hasPointerCapture(event.pointerId)) {
			try {
				btn.releasePointerCapture(event.pointerId);
			} catch (e) {}
		}
		document.documentElement.classList.remove('resources-fading');
		if (state.row) {
			state.row.classList.remove('is-fading');
		}
		hideFaderHud();
		faderState = null;
		if (fading) {
			applyFaderLevel(btn, state.level, true);
			return;
		}
		if (moved < FADER_CLICK_PX) {
			openFaderPicker(btn);
		}
	}

	var factorHelpPop = null;
	var factorHelpBtn = null;

	function closeFactorHelp() {
		if (factorHelpPop) {
			factorHelpPop.hidden = true;
		}
		document.querySelectorAll('.resources-factor-help[aria-expanded="true"]').forEach(function (el) {
			el.setAttribute('aria-expanded', 'false');
		});
		factorHelpBtn = null;
	}

	function placeFactorHelp() {
		if (!factorHelpPop || !factorHelpBtn || factorHelpPop.hidden) {
			return;
		}
		var pad = 8;
		var rect = factorHelpBtn.getBoundingClientRect();
		var width = factorHelpPop.offsetWidth;
		var height = factorHelpPop.offsetHeight;
		var left = rect.left - width - 8;
		if (left < pad) {
			left = Math.min(window.innerWidth - pad - width, rect.right + 8);
		}
		var top = rect.top - height - 6;
		if (top < pad) {
			top = rect.bottom + 6;
		}
		top = Math.max(pad, Math.min(window.innerHeight - pad - height, top));
		factorHelpPop.style.left = Math.round(left) + 'px';
		factorHelpPop.style.top = Math.round(top) + 'px';
	}

	function openFactorHelp(btn) {
		var text = btn.getAttribute('data-help') || '';
		if (!text) {
			return;
		}
		if (!factorHelpPop) {
			factorHelpPop = document.createElement('div');
			factorHelpPop.className = 'resources-factor-help-pop';
			factorHelpPop.setAttribute('role', 'tooltip');
			factorHelpPop.hidden = true;
			document.body.appendChild(factorHelpPop);
		}
		factorHelpBtn = btn;
		factorHelpPop.textContent = text;
		factorHelpPop.hidden = false;
		btn.setAttribute('aria-expanded', 'true');
		placeFactorHelp();
	}

	var factorHelpBound = false;

	function initResourcesFactorHelp() {
		if (factorHelpBound || !document.querySelector('.resources-factor-help')) {
			return;
		}
		factorHelpBound = true;
		document.addEventListener('click', function (event) {
			var btn = event.target.closest ? event.target.closest('.resources-factor-help') : null;
			if (!btn) {
				return;
			}
			event.preventDefault();
			event.stopPropagation();
			if (btn.getAttribute('aria-expanded') === 'true') {
				closeFactorHelp();
				return;
			}
			closeFactorHelp();
			openFactorHelp(btn);
		});
		document.addEventListener('pointerdown', function (event) {
			if (!factorHelpPop || factorHelpPop.hidden) {
				return;
			}
			if (event.target.closest && (event.target.closest('.resources-factor-help') || event.target.closest('.resources-factor-help-pop'))) {
				return;
			}
			closeFactorHelp();
		});
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeFactorHelp();
			}
		});
		window.addEventListener('resize', closeFactorHelp);
		window.addEventListener('scroll', closeFactorHelp, true);
	}

	function initResourcesFaders() {
		if (!document.querySelector('.resources-page')) {
			closeFaderPicker();
			hideFaderHud();
			closeFactorHelp();
			return;
		}
		initResourcesFactorHelp();
		if (faderBound) {
			return;
		}
		faderBound = true;

		document.addEventListener('input', function (event) {
			if (!event.target.classList || !event.target.classList.contains('resources-factor-range')) {
				return;
			}
			var cell = event.target.closest('.resources-factor');
			var btn = cell ? cell.querySelector('.resources-fader') : null;
			if (!btn) {
				return;
			}
			applyFaderLevel(btn, event.target.value, true);
		});

		document.addEventListener('pointerdown', function (event) {
			var btn = event.target.closest ? event.target.closest('.resources-fader') : null;
			if (!btn || window.getComputedStyle(btn).display === 'none') {
				if (faderPicker && !faderPicker.hidden && !event.target.closest('.resources-fader-picker')) {
					closeFaderPicker();
					document.querySelectorAll('.resources-fader[data-picker-open]').forEach(function (el) {
						el.removeAttribute('data-picker-open');
					});
				}
				return;
			}
			if (event.pointerType === 'mouse' && event.button !== 0) {
				return;
			}
			closeFaderPicker();
			faderState = {
				btn: btn,
				row: btn.closest('tr'),
				startX: event.clientX,
				startY: event.clientY,
				startLevel: clampFactorLevel(btn.getAttribute('data-level')),
				level: clampFactorLevel(btn.getAttribute('data-level')),
				fading: false
			};
			try {
				btn.setPointerCapture(event.pointerId);
			} catch (e) {}
		});

		document.addEventListener('pointermove', function (event) {
			if (!faderState) {
				return;
			}
			var state = faderState;
			var dy = event.clientY - state.startY;
			var dx = event.clientX - state.startX;
			if (!state.fading) {
				if (Math.abs(dy) < FADER_CLICK_PX && Math.abs(dx) < FADER_CLICK_PX) {
					return;
				}
				if (Math.abs(dx) > Math.abs(dy) && Math.abs(dx) >= FADER_CLICK_PX) {
					stopFader(event);
					return;
				}
				state.fading = true;
				document.documentElement.classList.add('resources-fading');
				if (state.row) {
					state.row.classList.add('is-fading');
				}
			}
			event.preventDefault();
			var next = clampFactorLevel(state.startLevel + Math.round(-dy / FADER_STEP_PX));
			if (next !== state.level) {
				state.level = next;
				applyFaderLevel(state.btn, next, false);
				vibrateFactor();
			}
			placeFaderHud(event.clientX, event.clientY, state.level);
		});

		document.addEventListener('pointerup', stopFader);
		document.addEventListener('pointercancel', stopFader);
		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				closeFaderPicker();
			}
		});
		window.addEventListener('resize', closeFaderPicker);
		window.addEventListener('scroll', function (event) {
			if (event.target && event.target.closest && event.target.closest('.resources-fader-picker')) {
				return;
			}
			closeFaderPicker();
		}, true);
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
		initFavorites();
		var favoritesQuery = window.matchMedia('(max-width: 640px)');
		if (favoritesQuery.addEventListener) {
			favoritesQuery.addEventListener('change', function (event) {
				if (event.matches) {
					initFavorites();
				}
			});
		} else if (favoritesQuery.addListener) {
			favoritesQuery.addListener(function (event) {
				if (event.matches) {
					initFavorites();
				}
			});
		}
		initEmpire();
		initResourcesFaders();
		initBuildCards();
		applyZeros(readStorage(ZERO_KEY) === '1', false);
		applyCompact(readStorage(COMPACT_KEY) === '1', false);
	}

	window.RisistarLayout = {
		refreshEmpire: refreshEmpire
	};

	$(init);
}(window, document, window.jQuery));
