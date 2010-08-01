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
    events: "?json",
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

// Append iCal icon to FullCalendar nonth name
var iCal_link = '<td><span class="fc-header-space"></span></td><td><div class="fc-state-default fc-corner-left fc-corner-right"><a href="?ical" title="iCal"><span>iCal<img src="' + fcLanguage.ical_img + '" class="alignleft" alt="" /></span></a></td>';
jQuery( '.fc-header-left tr' ).append( iCal_link );