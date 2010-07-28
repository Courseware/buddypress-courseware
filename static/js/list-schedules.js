/**
 * Javascript calls for schedule screens
 */
jQuery('.courseware-fullcalendar').fullCalendar({
    editable: false,
    events: "?courseware-schedules",
    header: {
        left: 'prev,next today',
        center: 'title',
        right: 'month,agendaWeek,agendaDay'
    },
    firstDay: 1,
    //monthNames: fc_months,
    //dayNamesShort: fc_days,
    loading: function(bool) {
        if (bool) jQuery('#loading').show();
        else jQuery('#loading').hide();
    }
});