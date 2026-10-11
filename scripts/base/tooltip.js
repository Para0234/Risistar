/**
 *  2Moons
 *   by Jan-Otto Kröpke 2009-2016
 *
 * For the full copyright and license information, please view the LICENSE
 *
 * @package 2Moons
 * @author Jan-Otto Kröpke <slaver7@gmail.com>
 * @copyright 2009 Lucky
 * @copyright 2016 Jan-Otto Kröpke <slaver7@gmail.com>
 * @licence MIT
 * @version 1.8.0
 * @link https://github.com/jkroepke/2Moons
 */

function placeTip(tip, pageX, pageY, center) {
	var $w = $(window);
	var margin = 8;
	var wasHidden = !tip.is(':visible');

	if (wasHidden) {
		tip.css({visibility: 'hidden', display: 'block', left: 0, top: 0});
	}

	var width = tip.outerWidth();
	var height = tip.outerHeight();
	var viewW = $w.width();
	var viewH = $w.height();
	var scrollX = $w.scrollLeft();
	var scrollY = $w.scrollTop();
	var left = center ? pageX - width / 2 : pageX;
	var top = center ? pageY - height / 2 : pageY;
	var maxLeft = scrollX + viewW - width - margin;
	var maxTop = scrollY + viewH - height - margin;

	left = Math.max(scrollX + margin, Math.min(left, maxLeft));
	top = Math.max(scrollY + margin, Math.min(top, maxTop));

	tip.css({
		left: left,
		top: top,
		visibility: 'visible'
	});
}

$(document).ready(function () {
	$(".tooltip").live({
		mouseenter : function (e) {
			var tip = $('#tooltip');
			tip.html($(this).attr('data-tooltip-content'));
			placeTip(tip, e.pageX + 20, e.pageY + 20, false);
			tip.show();
		},
		mouseleave : function () {
			var tip = $('#tooltip');
			tip.hide();
		},
		mousemove : function (e) {
			placeTip($('#tooltip'), e.pageX + 20, e.pageY + 20, false);
		}
	});
	$(".tooltip_sticky").live('mouseenter', function (e) {
		var tip = $('#tooltip');
		var pageX = e.pageX;
		var pageY = e.pageY;
		if (!pageX && !pageY) {
			var offset = $(this).offset();
			pageX = offset.left + $(this).outerWidth() / 2;
			pageY = offset.top + $(this).outerHeight() / 2;
		}
		tip.html($(this).attr('data-tooltip-content'));
		tip.addClass('tooltip_sticky_div');
		placeTip(tip, pageX, pageY, true);
		tip.show();
	});
	$(".tooltip_sticky_div").live('mouseleave', function () {
		var tip = $('#tooltip');
		tip.removeClass('tooltip_sticky_div');
		tip.hide();
	});
});
