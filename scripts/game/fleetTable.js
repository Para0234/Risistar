function fleetParseAmount(value) {
	var n = parseInt(String(value).replace(/[.\s\u00a0\u202f]/g, ''), 10);
	return isNaN(n) ? 0 : n;
}

function fleetSyncRange(input) {
	var range = $(input).closest('.fleet-ship-qty').find('.fleet-ship-range')[0];
	if (!range) {
		return;
	}
	var max = parseInt(range.max, 10) || 0;
	var n = fleetParseAmount(input.value);
	if (n < 0) {
		n = 0;
	}
	if (n > max) {
		n = max;
	}
	range.value = String(n);
	range.style.setProperty('--factor', (max > 0 ? (n / max) * 100 : 0) + '%');
}

function fleetSyncInput(range) {
	var input = $(range).closest('.fleet-ship-qty').find('input[name^="ship"]')[0];
	if (!input) {
		return;
	}
	input.value = range.value;
	var max = parseInt(range.max, 10) || 0;
	var n = parseInt(range.value, 10) || 0;
	range.style.setProperty('--factor', (max > 0 ? (n / max) * 100 : 0) + '%');
}

$(function() {
	$('.fleet-ship-qty input[name^="ship"]').on('input', function() {
		fleetSyncRange(this);
	});
	$('.fleet-ship-range').on('input', function() {
		fleetSyncInput(this);
	});

	window.setInterval(function() {
		$('.fleets').each(function() {
			var s		= $(this).data('fleet-time') - (serverTime.getTime() - startTime) / 1000;
			if(s <= 0) {
				$(this).text('-');
			} else {
				$(this).text(GetRestTimeFormat(s));
			}
		})
	}, 1000);
});