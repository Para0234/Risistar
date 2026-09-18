function add(){
	$("#form").attr('action', 'game.php?page=battleSimulator&action=moreslots');
	$("#form").attr('method', 'POST');
	$("#form").submit();
	return true;
}

function check(){
	var usePopup = window.matchMedia('(min-width: 1101px)').matches;
	var kb = null;
	if (usePopup) {
		kb = window.open('about:blank', 'kb', 'scrollbars=yes,statusbar=no,toolbar=no,location=no,directories=no,resizable=yes,menubar=no,width='+screen.width+',height='+screen.height+', screenX=0, screenY=0, top=0, left=0');
	}
	$("#submit").hide();
	$("#wait").show();
	$.post('game.php?page=battleSimulator&mode=send', $('#form').serialize(), function(data){
		var url;
		try{
			data	= $.parseJSON(data);
			url = 'CombatReport.php?raport='+data;
			if (kb && !kb.closed) {
				kb.focus();
				kb.location.href = url;
			} else {
				window.location.href = url;
			}
		} catch(e) {
			if (kb && !kb.closed) {
				kb.close();
			}
			RisistarDialog.alert(data);
		}
	});

	window.setTimeout(function(){
		$("#submit").show();
		$("#wait").hide();
	}, 10000);
	return true;
}

$(function() {
	$("#tabs").tabs();

	$('#form').on('click', '.reset', function(e) {
		e.preventDefault();

		var index = $(this).parent().index();

		$(this).closest('tr').nextAll('tr').each(function() {
			$(this).children('td').eq(index).find('input').val(0);
		});
		return false;
	});
});
