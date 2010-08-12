/**
 * Javascript calls for gradebook screens
 */

jQuery('#courseware-gradebook .import-gradebook-form').hide();
jQuery('#courseware-gradebook a').bind( 'click', function(){
    jQuery('#courseware-gradebook .import-gradebook-form').slideToggle();
});

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("#courseware-gradebook table").dataTable( {
    "bJQueryUI": true,
    "oLanguage": oLanguage,
    "aoColumns": [
        { "bSortable": true },
        { "bSortable": false },
        { "bSortable": false },
        { "bSortable": false },
        { "bSortable": false },
    ],
    "aaSorting": [[ 0, "desc" ]],
    "bPaginate": false
} );