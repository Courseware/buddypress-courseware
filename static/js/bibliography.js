/**
 * Javascript calls for bibliographies screens
 */
// FlexSelect
jQuery("select[name$='bib[existing]']").flexselect();

// Toggle Bibs
jQuery("#courseware-bibs-list").hide();
jQuery("#courseware-bibs-form .add").hide();
jQuery("ul.courseware-meta li.add").hide();

jQuery("ul.courseware-meta li.show-bibs a").on( 'click', function() {
    jQuery("ul.courseware-meta li.add").slideToggle();
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-list").slideToggle();
	event.preventDefault();
	return false;
})

jQuery("ul.courseware-meta li.add.bib").on( 'click', function() {
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .existing").slideToggle();
	event.preventDefault();
	return false;
})

jQuery("ul.courseware-meta li.add.book").on( 'click', function() {
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .book").slideToggle();
	event.preventDefault();
	return false;
})

jQuery("ul.courseware-meta li.add.www").on( 'click', function() {
    jQuery("#courseware-bibs-form .add").hide();
    jQuery("#courseware-bibs-form .www").slideToggle();
	event.preventDefault();
	return false;
})

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