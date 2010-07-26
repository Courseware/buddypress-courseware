/**
 * Javascript calls for gradebook screens
 */

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

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