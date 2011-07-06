/**
 * Javascript calls for bibliographies screens
 */
// FlexSelect
jQuery("select.[name$='bib[existing]']").flexselect();

// Toggle Bibs
jQuery("#courseware-bibs-list").hide();
jQuery("#courseware-bibs-form .add").hide();
jQuery("ul.courseware-meta li.add").hide();

jQuery("ul.courseware-meta li.show-bibs a").bind('click', function(){
    jQuery("ul.courseware-meta li.add").slideToggle();
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-list").slideToggle();
})

jQuery("ul.courseware-meta li.add.bib").bind('click', function(){
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .existing").slideToggle();
})

jQuery("ul.courseware-meta li.add.book").bind('click', function(){
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .book").slideToggle();
})

jQuery("ul.courseware-meta li.add.www").bind('click', function(){
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .www").slideToggle();
})

// Didn't find another way to 'localize' this
oLanguage.oPaginate             = [];
oLanguage.oPaginate.sFirst      = '&laquo;';
oLanguage.oPaginate.sPrevious   = '&lsaquo;';
oLanguage.oPaginate.sNext       = '&rsaquo;';
oLanguage.oPaginate.sLast       = '&raquo;';

// Load DataTables
jQuery("table.datatables").dataTable( {
    "aoColumns": [
        { "sWidth": "5%" },
        { "sWidth": "70%" },
        { "sWidth": "15%" },
        { "sWidth": "10%" }
    ],
    "oLanguage": oLanguage,
    "aaSorting": [[ 1, "asc" ]],
    "sPaginationType": "full_numbers"
} );