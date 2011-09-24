/**
 * Javascript calls for list-assignments screens
 */

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "aoColumns": [
        { "sWidth": "60%" },
        { "sWidth": "10%" },
        { "sWidth": "10%" },
        { "sWidth": "20%" }
    ],
    "oLanguage": oLanguage,
    "aaSorting": [[ 3, "desc" ]],
    "sPaginationType": "full_numbers"
} );