/**
 * Javascript calls for assignments screens
 */
var due_date = jQuery( "input[name$='assignment[due_date]']" ).datetimepicker({
    holdDatepickerOpen: false,
    showButtonPanel: false,
    timeFormat: 'hh:mm:ss',
    dateFormat: 'yy-mm-dd',
});