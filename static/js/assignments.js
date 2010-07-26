/**
 * Javascript calls for assignments screens
 */

// Localize DateTimePicker
dtpLanguage['monthNames']   = dtpLanguage['monthNames'].split( ',' );
dtpLanguage['dayNamesMin']  = dtpLanguage['dayNamesMin'].split( ',' );
dtpLanguage['firstDay']     = eval( dtpLanguage['firstDay'] );
dtpLanguage['isRTL']        = eval( dtpLanguage['isRTL'] );
dtpLanguage['showMonthAfterYear'] = eval( dtpLanguage['showMonthAfterYear'] );
jQuery.datepicker.setDefaults( dtpLanguage );

jQuery( "input[name$='assignment[due_date]']" ).datetimepicker({
    holdDatepickerOpen: false,
    showButtonPanel: false,
    timeFormat: 'hh:mm:ss',
    dateFormat: 'yy-mm-dd',
});
