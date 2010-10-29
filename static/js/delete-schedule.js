/**
 * Javascript calls for delete schedule screen
 */
// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "bJQueryUI": true,
    "oLanguage": oLanguage,
    "aaSorting": [[ 1, "desc" ]],
    "sPaginationType": "full_numbers"
} );