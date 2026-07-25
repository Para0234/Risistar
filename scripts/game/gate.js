/*
Porte de saut V1 sans bouton
var Gate	= {
	max: function(ID) {
		$('#ship'+ID+'_input').val($('#ship'+ID+'_value').text().replace(/\./g, ""));
	},
	
	submit: function() {
		$.getJSON('?page=information&mode=sendFleet&'+$('.jumpgate').serialize(), function(data) {
			alert(data.message);
			if(!data.error)
			{
				parent.$.fancybox.close();
			}
		});		
	}
}

*/

// Porte de Saut ajout bouton aucun/tous les vaisseaux

var Gate = {
    max: function(ID) {
        $('#ship' + ID + '_input').val($('#ship' + ID + '_value').text().replace(/\./g, ""));
    },

    submit: function() {
        $.getJSON('?page=information&mode=sendFleet&' + $('.jumpgate').serialize(), function(data) {
            alert(data.message);
            if (!data.error) {
                parent.$.fancybox.close();
            }
        });
    },

    noShips: function() {
        $("input.jumpgate[type='text']").val(0);
    },

    maxShips: function() {
        $("input.jumpgate[type='text']").each(function() {
            var id = this.id.replace("ship", "").replace("_input", "");
            var maxValue = $("#ship" + id + "_value").text().replace(/\./g, "");
            $(this).val(maxValue);
        });
    }
};