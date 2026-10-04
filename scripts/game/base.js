function number_format (number, decimals) {
    number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
    var n = !isFinite(+number) ? 0 : +number,
        prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
        sep = '.',
        dec = ',',
        s = '',
        toFixedFix = function (n, prec) {
            var k = Math.pow(10, prec);
            return '' + Math.round(n * k) / k;
        };
    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
    }
    if ((s[1] || '').length < prec) {
        s[1] = s[1] || '';
        s[1] += new Array(prec - s[1].length + 1).join('0');    }
    return s.join(dec);
}

function NumberGetHumanReadable(value, dec) {
	if(typeof dec === "undefined") {
		dec = 0;
	}
	if(dec == 0)
	{
		value	= removeE(Math.floor(value));
	}
	return number_format(value, dec);
}

function shortly_number(number)
{
    var unit	= ["", "K", "M", "B", "T", "Q", "Q+", "S", "S+", "O", "N"];
	var negate	= number < 0 ? -1 : 1;
	var key		= 0;
	number		= Math.abs(number);
	
	if(number >= 1000000) {
		++key;
		while(number >= 1000000)
		{
			++key;
			number = number / 1000000;
		}
	} else if(number >= 1000) {
		++key;
		number = number / 1000;
	}
	
	decial	= key != 0 && number != 0 && number < 100;
	return NumberGetHumanReadable(negate * number, decial)+(key !== 0 ? '&nbsp;'+unit[key] : '');
}

function removeE(Number) {
	Number = String(Number);
	if (Number.search(/e\+/) == -1) 
		return Number;
	var e = parseInt(Number.replace(/\S+.?e\+/g, ''));
	if (isNaN(e) || e == 0) 
		return Number;
	else if ($.browser.webkit || $.browser.msie) 
		return parseFloat(Number).toPrecision(Math.min(e + 1, 21));
	else 
		return parseFloat(Number).toPrecision(e + 1);
}

function getFormatedDate(timestamp, format) {
	var currTime = new Date();
	currTime.setTime(timestamp + (ServerTimezoneOffset * 1000));
	str = format;
	str = str.replace('[d]', dezInt(currTime.getDate(), 2));
	str = str.replace('[D]', days[currTime.getDay()]);
	str = str.replace('[m]', dezInt(currTime.getMonth() + 1, 2));
	str = str.replace('[M]', months[currTime.getMonth()]);
	str = str.replace('[j]', parseInt(currTime.getDate()));
	str = str.replace('[Y]', currTime.getFullYear());
	str = str.replace('[y]', currTime.getFullYear().toString().substr(2, 4));
	str = str.replace('[G]', currTime.getHours());
	str = str.replace('[H]', dezInt(currTime.getHours(), 2));
	str = str.replace('[i]', dezInt(currTime.getMinutes(), 2));
	str = str.replace('[s]', dezInt(currTime.getSeconds(), 2));
	return str;
}
function dezInt(num, size, prefix) {
	prefix = (prefix) ? prefix : "0";
	var minus = (num < 0) ? "-" : "", 
	result = (prefix == "0") ? minus : "";
	num = Math.abs(parseInt(num, 10));
	size -= ("" + num).length;
	for (var i = 1; i <= size; i++) {
		result += "" + prefix;
	}
	result += ((prefix != "0") ? minus : "") + num;
	return result;
}

function getFormatedTime(time) {
	hours = Math.floor(time / 3600);
	timeleft = time % 3600;
	minutes = Math.floor(timeleft / 60);
	timeleft = timeleft % 60;
	seconds = timeleft;
	return dezInt(hours, 2) + ":" + dezInt(minutes, 2) + ":" + dezInt(seconds, 2);
}

function GetRestTimeFormat(Secs) {
	var s = Secs;
	var m = 0;
	var h = 0;
	if (s > 59) {
		m = Math.floor(s / 60);
		s = s - m * 60;
	}
	if (m > 59) {
		h = Math.floor(m / 60);
		m = m - h * 60;
	}
	return dezInt(h, 2) + ':' + dezInt(m, 2) + ":" + dezInt(s, 2);
}

function OpenPopup(target_url, win_name, width, height) {
	var new_win = window.open(target_url+'&ajax=1', win_name, 'scrollbars=yes,statusbar=no,toolbar=no,location=no,directories=no,resizable=no,menubar=no,width='+width+',height='+height+',screenX='+((screen.width-width) / 2)+",screenY="+((screen.height-height) / 2)+",top="+((screen.height-height) / 2)+",left="+((screen.width-width) / 2));
	new_win.focus();
}

function OpenNotes() {
	var width = 720;
	var height = 300;
	if (window.innerWidth <= 640) {
		width = window.innerWidth;
		height = window.innerHeight;
	}
	return OpenPopup('?page=notes', 'notes', width, height);
}

function DestroyMissiles() {
	$.getJSON('?page=information&mode=destroyMissiles&'+$('.missile').serialize(), function(data) {
		$('#missile_502').text(NumberGetHumanReadable(data[0]));
		$('#missile_503').text(NumberGetHumanReadable(data[1]));
		$('.missile').val('');
	});
}

function handleErr(errMessage, url, line) 
{ 
	error = "There is an error at this page.\n";
	error += "Error: " + errMessage+ "\n";
	error += "URL: " + url + "\n";
	error += "Line: " + line + "\n\n";
	error += "Click OK to continue viewing this page,\n";
	RisistarDialog.alert(error);
	if(typeof console == "object")
		console.log(error);
 
	return true; 
}

var Dialog	= {	
	info: function(ID){
		var height = (ID > 600 && ID < 800) ? 210 : ((ID > 100 && ID < 200) ? 300 : 620);
		if (window.innerWidth <= 640 && ID > 600 && ID < 800) {
			height = 396;
		}
		return Dialog.open('game.php?page=information&id='+ID, 590, height, {
			autoHeight: true
		});
	},
	
	alert: function(msg, callback){
		return RisistarDialog.alert(msg).then(function() {
			if(typeof callback === "function") {
				callback();
			}
		});
	},
	
	PM: function(ID, Subject, Message) {
		if(typeof Subject !== 'string')
			Subject	= '';

		var width = 650;
		var height = 350;
		if (window.innerWidth <= 640) {
			width = Math.min(650, window.innerWidth - 16);
			height = Math.min(480, Math.max(360, window.innerHeight - 48));
		}

		return Dialog.open('game.php?page=messages&mode=write&id='+ID+'&subject='+encodeURIComponent(Subject)+'&message='+encodeURIComponent(Subject), width, height);
	},
	
	Playercard: function(ID) {
		return isPlayerCardActive && Dialog.open('game.php?page=playerCard&id='+ID, 650, 600);
	},
	
	Buddy: function(ID) {
		return Dialog.open('game.php?page=buddyList&mode=request&id='+ID, 650, 300);
	},
	
	PlanetAction: function() {
		return Dialog.open('game.php?page=overview&mode=actions', 400, 180);
	},
	
	AllianceChat: function() {
	    return OpenPopup('game.php?page=chat&action=alliance', "alliance_chat", 960, 900);
	},
	
	open: function(url, width, height, options) {
		options = options || {};
		var maxW = Math.max(280, window.innerWidth - 16);
		var maxH = Math.max(200, window.innerHeight - 16);
		var autoHeightTimer = null;
		var fancyboxOptions = {
			width: Math.min(width, maxW),
			padding: 0,
			height: Math.min(height, maxH),
			type: 'iframe',
			href: url
		};

		if (options.autoHeight) {
			fancyboxOptions.transitionIn = 'none';
			fancyboxOptions.onStart = function() {
				$('#fancybox-wrap').css('visibility', 'hidden');
			};
			fancyboxOptions.onComplete = function() {
				var frame = document.getElementById('fancybox-frame');
				var reveal = function() {
					if (Dialog.fitIframeHeight(frame)) {
						window.clearTimeout(autoHeightTimer);
						$(frame).off('load.rsInfoAutoHeight', reveal);
						return true;
					}
					return false;
				};

				$('#fancybox-wrap').css('visibility', 'hidden');
				$.fancybox.showActivity();
				$(frame).on('load.rsInfoAutoHeight', reveal);
				if (!reveal()) {
					autoHeightTimer = window.setTimeout(function() {
						if (!reveal()) {
							$.fancybox.hideActivity();
							$('#fancybox-wrap').css('visibility', 'visible');
						}
					}, 2000);
				}
			};
			fancyboxOptions.onClosed = function() {
				window.clearTimeout(autoHeightTimer);
				$.fancybox.hideActivity();
				$('#fancybox-wrap').css('visibility', 'visible');
			};
		}

		$.fancybox(fancyboxOptions);
		
		return false;
	},

	fitIframeHeight: function(frame) {
		var wrap = $('#fancybox-wrap');
		var content = $('#fancybox-content');
		var naturalHeight = Dialog.measureIframeHeight(frame);

		if (naturalHeight <= 0) {
			return false;
		}

		var currentHeight = content.height();
		var chromeHeight = Math.max(0, wrap.outerHeight() - currentHeight);
		var maxHeight = Math.max(280, Math.floor(window.innerHeight * 0.78) - chromeHeight);
		var fittedHeight = Math.min(naturalHeight, maxHeight);
		content.height(fittedHeight);
		$(frame).height(fittedHeight);
		$.fancybox.center(0);
		$.fancybox.hideActivity();
		wrap.css('visibility', 'visible');
		return true;
	},

	measureIframeHeight: function(frame) {
		try {
			var doc = frame.contentDocument || frame.contentWindow.document;
			var body = doc.body;
			var content = doc.getElementById('content');
			if (!body || body.id !== 'information' || !content) {
				return 0;
			}

			var view = doc.defaultView;
			var chrome = body.querySelector('.dialog-chrome');
			var contentStyle = view.getComputedStyle(content);
			var height = chrome ? chrome.offsetHeight : 0;
			height += parseFloat(contentStyle.paddingTop) || 0;
			height += parseFloat(contentStyle.paddingBottom) || 0;
			height += parseFloat(contentStyle.borderTopWidth) || 0;
			height += parseFloat(contentStyle.borderBottomWidth) || 0;
			var i;

			for (i = 0; i < content.children.length; i++) {
				var child = content.children[i];
				var childStyle = view.getComputedStyle(child);
				var childHeight = child.offsetHeight;
				if (child.classList.contains('info-level-scroll') && child.firstElementChild) {
					childHeight = child.firstElementChild.offsetHeight;
					if (child.scrollWidth > child.clientWidth) {
						childHeight += child.offsetHeight - child.clientHeight;
					}
				}
				height += childHeight;
				height += parseFloat(childStyle.marginTop) || 0;
				height += parseFloat(childStyle.marginBottom) || 0;
			}

			return Math.ceil(height);
		} catch (error) {
			return 0;
		}
	}
}

function NotifyBox(text) {
	tip = $('#tooltip')
	tip.html(text).addClass('notify').css({
		left : (($(window).width() - $('#leftmenu').width()) / 2 - tip.outerWidth() / 2) + $('#leftmenu').width(),
	}).show();
	window.setTimeout(function(){tip.fadeOut(1000, function() {tip.removeClass('notify')})}, 500);
}


function UhrzeitAnzeigen() {
   $(".servertime").text(getFormatedDate(serverTime.getTime(), tdformat));
}


$.widget("custom.catcomplete", $.ui.autocomplete, {
	_renderMenu: function( ul, items ) {
		var self = this,
			currentCategory = "";
		$.each( items, function( index, item ) {
			if ( item.category != currentCategory ) {
				ul.append( "<li class='ui-autocomplete-category'>" + item.category + "</li>" );
				currentCategory = item.category;
			}
			self._renderItem( ul, item );
		});
	}
});

$(function() {
	// The marker describes this automatic request only, not a later manual refresh.
	var currentUrl = new URL(window.location.href);
	if (currentUrl.searchParams.has('passive_reload')) {
		currentUrl.searchParams.delete('passive_reload');
		window.history.replaceState(null, '', currentUrl.href);
	}
	$('#drop-admin').on('click', function() {
		$.get('admin.php?page=logout', function() {
			$('.globalWarning').animate({
				'height' :0,
				'padding' :0,
				'opacity' :0
			}, function() {
				$(this).hide();
			});
		});
	});
	
	
	window.setInterval(function() {
		$('.countdown').each(function() {
			var s		= $(this).data('time') - (serverTime.getTime() - startTime) / 1000;
			if(s <= 0) {
				$(this).text('-');
			} else {
				$(this).text(GetRestTimeFormat(s));
			}
		});
	}, 1000);
	
	$('#planetSelector').on('change', function() {
		document.location = '?'+queryString+'&cp='+$(this).val();
	});

	UhrzeitAnzeigen();
	setInterval(UhrzeitAnzeigen, 1000);
	
	$("button#create_new_alliance_rank").click(function() {
		$("div#new_alliance_rank").dialog(		{
			draggable: false,
			resizable: false,
			modal: true,
			width: 760
		});

		return false;
	});
});
