/**
 * Javascript calls for gradebook screens
 */
jQuery("#courseware-gradebook table").dataTable( {
    "oLanguage": oLanguage,
    "aoColumns": [
        { "bSortable": true },
        { "bSortable": false },
        { "bSortable": false },
        { "bSortable": false },
        { "bSortable": false },
    ],
    "aaSorting": [[ 0, "desc" ]],
    "sPaginationType": "full_numbers"
} );