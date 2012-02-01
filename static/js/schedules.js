/**
 * Javascript calls for schedule screens
 */

// Localize DateTimePicker
dtpLanguage['monthNames'] = dtpLanguage['monthNames'].split( ',' );
dtpLanguage['dayNamesMin'] = dtpLanguage['dayNamesMin'].split( ',' );
dtpLanguage['firstDay'] = eval( dtpLanguage['firstDay'] );
dtpLanguage['isRTL'] = eval( dtpLanguage['isRTL'] );
dtpLanguage['showMonthAfterYear'] = eval( dtpLanguage['showMonthAfterYear'] );
jQuery.datepicker.setDefaults( dtpLanguage );

// This will enable the calendar for start date field
jQuery( "#schedule-startdate" ).datetimepicker({
    holdDatepickerOpen: true,
    showButtonPanel: true,
    timeFormat: 'hh:mm:ss',
    dateFormat: 'yy-mm-dd'
});

// Function will check the start date field and ensure the end date will not be older
function courseware_toggle_datefields( reset ) {
    if( reset == true ) {
        var title = jQuery( "#schedule-enddate" ).attr('title');
        jQuery( "#schedule-enddate" ).val(title);
    }
    
    jQuery( "#schedule-enddate" ).datepicker('destroy');
    var start_date = jQuery( "#schedule-startdate" ).datepicker('getDate');
    if( start_date != null ) {
        jQuery( "#schedule-enddate" ).datetimepicker({
            holdDatepickerOpen: true,
            showButtonPanel: true,
            timeFormat: 'hh:mm:ss',
            dateFormat: 'yy-mm-dd',
            minDate: start_date
        });
        jQuery( "#schedule-end-date" ).show();
    }
}

// This will enable calendar for end date field on edit screen
var cw_start_date = jQuery( "#schedule-startdate" ).val();
if( cw_start_date != null )
    courseware_toggle_datefields( false );

// This will enable the calendar for end date field
// but only if start date field is populated
jQuery( "#new-schedule-form #schedule-end-date" ).hide();
jQuery( "#schedule-startdate" )
    .change( function() { courseware_toggle_datefields( true ) });
jQuery( ".schedule-form" ).submit( function() { jQuery('#schedule-startdate').unbind('change'); } );

/* Editor Screens */
jQuery('.schedule-form input[type="text"]').inputHint();
jQuery('.schedule-form textarea').inputHint();

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "aoColumns": [
        { "sWidth": "65%" },
        { "sWidth": "15%" },
        { "sWidth": "15%" },
        { "sWidth": "5%" }
    ],
    "oLanguage": oLanguage,
    "aaSorting": [[ 1, "desc" ]],
    "sPaginationType": "full_numbers"
} );
