/*
 * jquery.juice.inputhint.js
 *
 * Juice Library Input Hint v0.1.0
 * Date: 2009-09-10
 * Requires: jQuery v1.3 or later
 * Dual licensed under the MIT and GPL licenses:
 *   http://www.opensource.org/licenses/mit-license.php
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Copyright 2009 Steve Whiteley (http://jui.ce.it)
 */

(function($) {
	$.fn.inputHint = function(callerSettings) {
		var settings = $.extend(true, {}, $.fn.inputHint.settings, callerSettings);
		return this.each(function() {
			var n = $(this), v = settings.value || $(this).attr('title'), f = $(this.form);
			n.focus(function() {
				n.val(n.val() == v ? '' : n.val())
					.removeClass(settings.className);
			})
			.blur(function() {
				n.val(n.val() == '' ? v : n.val())
					.addClass(settings.className);
			})
			.blur();
			if (f) {
				f.submit(function(e) {
					n.trigger('focus');
				});
			}
		});
	};
	$.fn.inputHint.settings = {
		value: false,
		className: false
	};
})(jQuery);