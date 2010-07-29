/**
 * Javascript calls for schedule screens
 */
// Parse language strings
fcLanguage.firstDay = eval( fcLanguage.firstDay );
fcLanguage.buttonTextKeys = fcLanguage.buttonTextKeys.split( ',' );
fcLanguage.buttonTextVals = fcLanguage.buttonTextVals.split( ',' );
fcLanguage.buttonText = new Object;
jQuery.each( fcLanguage.buttonTextKeys, function( i, v ){
    fcLanguage.buttonText[v] = fcLanguage.buttonTextVals[i];
} );
fcLanguage.monthNames = fcLanguage.monthNames.split( ',' );
fcLanguage.monthNamesShort = fcLanguage.monthNamesShort.split( ',' );
fcLanguage.dayNames = fcLanguage.dayNames.split( ',' );
fcLanguage.dayNamesShort = fcLanguage.dayNamesShort.split( ',' );

// Load the FullCalendar
jQuery('.courseware-fullcalendar').fullCalendar({
    editable: false,
    events: "?courseware-schedules",
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    timeFormat: fcLanguage.timeFormat,
    firstDay: fcLanguage.firstDay,
    buttonText: fcLanguage.buttonText,
    monthNames: fcLanguage.monthNames,
    monthNamesShort: fcLanguage.monthNamesShort,
    dayNames: fcLanguage.dayNames,
    dayNamesShort: fcLanguage.dayNamesShort,
    loading: function(bool) {
        if (bool) jQuery('#loading').show();
        else jQuery('#loading').hide();
    }
});

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "oLanguage": oLanguage,
    "aaSorting": [[ 1, "desc" ]],
    "sPaginationType": "full_numbers"
} );