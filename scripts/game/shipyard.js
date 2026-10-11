var v  			= new Date();

function ShipyardInit() {
	Shipyard		= data.Queue;
	Amount			= new DecimalNumber(Shipyard[0][1],0);
	hanger_id		= data.b_hangar_id_plus;
	$('#timeleft').text(data.pretty_time_b_hangar);
	ShipyardList();
	BuildlistShipyard();
	ShipyardInterval	= window.setInterval(BuildlistShipyard, 1000);
}

function BuildlistShipyard() {
	var n = new Date();
	var s = Shipyard[0][2] - hanger_id - Math.round((n.getTime() - v.getTime()) / 1000);
	var s = Math.round(s);
	var m = 0;
	var h = 0;
	if (s <= 0) {
		Amount.sub('1');
		$('#val_'+Shipyard[0][3]).each(function(){
			var $val = $(this);
			var $qty = $val.find('.build-card-sub-qty');
			var raw = $qty.length ? $qty.text() : String($val.text());
			var n = parseInt(raw.replace(/\./g, '').replace(/.*?(\d+)\s*$/, '$1'), 10);
			if (isNaN(n)) {
				n = 0;
			}
			var readable = NumberGetHumanReadable(n + 1);
			if ($qty.length) {
				$qty.text(readable);
				return;
			}
			$val.html('<span class="build-card-sub-full">' + $.trim(bd_available) + '</span><span class="build-card-sub-short">' + bd_available_short + '</span> <span class="build-card-sub-qty">' + readable + '</span>');
		});
		if (Amount.toString() == '0') {
			Shipyard.shift();
			if (Shipyard.length == 0) {
				$("#bx").html(Ready);
				document.getElementById('auftr').options[0] = new Option(Ready);
				var reloadUrl = new URL(document.location.href);
				reloadUrl.searchParams.set('passive_reload', 'queue');
				document.location.href = reloadUrl.href;
				window.clearInterval(ShipyardInterval);
				return;
			}
			Amount = Amount.reset(Shipyard[0][1]);
			ShipyardList();
		} else {
			document.getElementById('auftr').options[0].innerHTML	= Amount.toString() + " " + Shipyard[0][0] + " " + bd_operating;
		}
		hanger_id = 0;
		v = new Date();
		s = 0;
	}
	$("#bx").html(Shipyard[0][0]+" "+GetRestTimeFormat(s));
}

function ShipyardList() {
	while (document.getElementById('auftr').length > 0)
		document.getElementById('auftr').options[document.getElementById('auftr').length - 1] = null;

	for (iv = 0; iv <= Shipyard.length - 1; iv++ ) {
		if(iv == 0)
			document.getElementById('auftr').options[iv] = new Option(Amount.toString()+ " " + Shipyard[iv][0] + " " + bd_operating, iv);
		else
			document.getElementById('auftr').options[iv] = new Option(Shipyard[iv][1]+ " " + Shipyard[iv][0] + " " + bd_operating, iv);
	}
}