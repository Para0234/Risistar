(function (window, document) {
	'use strict';

	var FOCUS_KEY = 'risistar.galaxyFocus';
	var PLACEHOLDER_PARTS = ['1', '234', '5'];
	var PLACEHOLDER_SEPARATORS = [':', ' ', '.', '/', ';', '-'];
	var PLACEHOLDER_MS = 2000;
	var controllers = [];

	function placeholderExample(index) {
		var sep = PLACEHOLDER_SEPARATORS[index % PLACEHOLDER_SEPARATORS.length];
		return PLACEHOLDER_PARTS.join(sep);
	}

	function fits(input, value) {
		var max = input ? parseInt(input.getAttribute('maxlength'), 10) : 3;
		if (!max) {
			return true;
		}
		return String(value).length <= max;
	}

	function parseCoords(raw, mode, galaxyInput, systemInput, planetInput) {
		var cleaned = String(raw).replace(/[\[\]\(\)]/g, ' ').trim();
		if (cleaned === '') {
			return null;
		}
		var bits = cleaned.split(/[:./;\s-]+/);
		var parts = [];
		var i;
		for (i = 0; i < bits.length; i++) {
			if (bits[i] !== '') {
				parts.push(bits[i]);
			}
		}
		if (mode === 'fleet' && parts.length !== 3) {
			return null;
		}
		if (mode === 'galaxy' && parts.length !== 2 && parts.length !== 3) {
			return null;
		}
		var nums = [];
		for (i = 0; i < parts.length; i++) {
			if (!/^\d+$/.test(parts[i])) {
				return null;
			}
			var n = parseInt(parts[i], 10);
			if (n < 1) {
				return null;
			}
			nums.push(n);
		}
		if (!fits(galaxyInput, nums[0]) || !fits(systemInput, nums[1])) {
			return null;
		}
		if (nums.length === 3 && planetInput && !fits(planetInput, nums[2])) {
			return null;
		}
		if (nums.length === 3 && !planetInput && String(nums[2]).length > 3) {
			return null;
		}
		return nums;
	}

	function focusPlanet(planet) {
		var row = document.getElementById('galaxy-pos-' + planet);
		if (!row) {
			return;
		}
		row.classList.add('coord-focus');
		if (row.scrollIntoView) {
			row.scrollIntoView({ block: 'center', behavior: 'smooth' });
		}
		window.setTimeout(function () {
			row.classList.remove('coord-focus');
		}, 8000);
	}

	function revealGalaxyFocus() {
		var raw = null;
		try {
			raw = window.sessionStorage.getItem(FOCUS_KEY);
			window.sessionStorage.removeItem(FOCUS_KEY);
		} catch (e) {
			return;
		}
		if (!raw) {
			return;
		}
		var data;
		try {
			data = JSON.parse(raw);
		} catch (e) {
			return;
		}
		if (!data || !data.at || Date.now() - data.at > 20000) {
			return;
		}
		var form = document.getElementById('galaxy_form');
		if (!form) {
			return;
		}
		var galaxyInput = form.querySelector('input[name="galaxy"]');
		var systemInput = form.querySelector('input[name="system"]');
		if (!galaxyInput || !systemInput) {
			return;
		}
		if (String(galaxyInput.value) !== String(data.galaxy) || String(systemInput.value) !== String(data.system)) {
			return;
		}
		focusPlanet(data.planet);
	}

	function bind(root) {
		var mode = root.getAttribute('data-coord-quick');
		var galaxyInput = root.querySelector('input[name="galaxy"]');
		var systemInput = root.querySelector('input[name="system"]');
		var planetInput = root.querySelector('input[name="planet"]');
		var quick = root.querySelector('.coord-quick');
		var toggle = root.querySelector('.coord-quick-toggle');
		var fieldsRow = root.querySelector('.galaxy-nav-fields');
		var parts = root.querySelectorAll('.coord-part');
		if (!galaxyInput || !systemInput || !quick || !toggle) {
			return;
		}

		var open = false;
		var placeholderTimer = null;
		var label = toggle.getAttribute('data-label') || toggle.textContent;
		var labelBack = toggle.getAttribute('data-label-back') || 'Retour';

		function stopPlaceholder() {
			if (placeholderTimer) {
				window.clearInterval(placeholderTimer);
				placeholderTimer = null;
			}
		}

		function startPlaceholder() {
			var index = 0;
			stopPlaceholder();
			quick.placeholder = placeholderExample(index);
			placeholderTimer = window.setInterval(function () {
				index += 1;
				quick.placeholder = placeholderExample(index);
			}, PLACEHOLDER_MS);
		}

		function setToggleLabel(text) {
			toggle.textContent = text;
		}

		function setOpen(next) {
			open = next;
			quick.classList.toggle('is-hidden', !open);
			setToggleLabel(open ? labelBack : label);
			var i;
			for (i = 0; i < parts.length; i++) {
				parts[i].classList.toggle('is-quick-hidden', open);
			}
			if (fieldsRow) {
				fieldsRow.classList.toggle('is-quick-hidden', open);
			}
			if (open) {
				startPlaceholder();
			} else {
				stopPlaceholder();
				quick.classList.remove('invalid');
				quick.value = '';
				quick.placeholder = placeholderExample(0);
			}
		}

		function dismiss() {
			if (!open) {
				return;
			}
			setOpen(false);
		}

		function applyParsed(nums) {
			var previousGalaxy = galaxyInput.value;
			var previousSystem = systemInput.value;
			galaxyInput.value = String(nums[0]);
			systemInput.value = String(nums[1]);
			if (planetInput && nums.length === 3) {
				planetInput.value = String(nums[2]);
			}
			setOpen(false);
			if (mode === 'fleet') {
				if (typeof window.updateVars === 'function') {
					window.updateVars();
				}
				return;
			}
			var samePlace = String(previousGalaxy) === String(nums[0]) && String(previousSystem) === String(nums[1]);
			if (nums.length === 3 && samePlace) {
				focusPlanet(nums[2]);
				return;
			}
			if (nums.length < 3 && samePlace) {
				return;
			}
			if (nums.length === 3) {
				try {
					window.sessionStorage.setItem(FOCUS_KEY, JSON.stringify({
						galaxy: nums[0],
						system: nums[1],
						planet: nums[2],
						at: Date.now()
					}));
				} catch (e) {}
			}
			if (typeof root.submit === 'function') {
				root.submit();
			}
		}

		function tryApply(force) {
			if (!open || !force) {
				return;
			}
			var nums = parseCoords(quick.value, mode, galaxyInput, systemInput, planetInput);
			if (!nums) {
				if (quick.value.trim() !== '') {
					quick.classList.add('invalid');
				}
				return;
			}
			applyParsed(nums);
		}

		toggle.addEventListener('mousedown', function (event) {
			event.preventDefault();
		});
		toggle.addEventListener('click', function () {
			if (open) {
				dismiss();
				return;
			}
			setOpen(true);
			quick.value = '';
			quick.classList.remove('invalid');
			quick.focus();
		});
		quick.addEventListener('keydown', function (event) {
			if (event.key !== 'Enter') {
				return;
			}
			event.preventDefault();
			tryApply(true);
		});
		quick.addEventListener('input', function (event) {
			quick.classList.remove('invalid');
			var pasted = event.inputType === 'insertFromPaste' || event.inputType === 'insertFromDrop';
			tryApply(pasted);
		});
		quick.addEventListener('paste', function () {
			window.setTimeout(function () {
				tryApply(true);
			}, 0);
		});
		quick.addEventListener('blur', function () {
			if (!open) {
				return;
			}
			if (quick.value.trim() === '') {
				dismiss();
				return;
			}
			tryApply(true);
		});

		controllers.push({ dismiss: dismiss });
	}

	function boot() {
		var nodes = document.querySelectorAll('[data-coord-quick]');
		var i;
		for (i = 0; i < nodes.length; i++) {
			bind(nodes[i]);
		}
		revealGalaxyFocus();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}

	window.CoordQuick = {
		dismiss: function () {
			var i;
			for (i = 0; i < controllers.length; i++) {
				controllers[i].dismiss();
			}
		}
	};
})(window, document);
