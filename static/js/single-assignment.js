/**
 * Javascript calls for assignment screens
 */
jQuery('#courseware-responses-list').hide();

jQuery('#responses').on('click', function() {
	jQuery('#courseware-responses-list').slideToggle();
	event.preventDefault();
	return false;
});