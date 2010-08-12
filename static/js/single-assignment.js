/**
 * Javascript calls for assignment screens
 */
jQuery('#courseware-responses-list').hide();

jQuery('#responses').bind('click', function(){
    jQuery('#courseware-responses-list').slideToggle();
});