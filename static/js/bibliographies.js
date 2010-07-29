/**
 * Javascript calls for bibliographies screens
 */
jQuery("select.[name$='bib[existing]']").flexselect();

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "oLanguage": oLanguage,
    "aaSorting": [[ 1, "asc" ]],
    "sPaginationType": "full_numbers"
} );