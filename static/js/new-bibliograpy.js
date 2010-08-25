/**
 * Javascript calls for new bibliography screen
 */

jQuery('#bibliography-form-content label').hide();
jQuery(".courseware-content-wrapper").hide();
jQuery("ul.courseware-meta li.bib-types-list").hide();
jQuery("#courseware-bibs-list").show();
jQuery("ul.courseware-meta li.add").show();
jQuery('.courseware-form-section input[type="text"]').inputHint();
jQuery('.courseware-form-section textarea').inputHint();

jQuery("ul.courseware-meta li.add-new-bib").bind('click', function(){
    jQuery(".courseware-content-wrapper").slideToggle();
    jQuery("ul.courseware-meta li.bib-types-list").slideToggle();
    jQuery("#courseware-bibs-list").slideToggle();
})

jQuery("ul.courseware-meta li.show-bibs a").bind('click', function(){
    jQuery(".courseware-content-wrapper").hide();
    jQuery("ul.courseware-meta li.bib-types-list").hide();
    jQuery("ul.courseware-meta li.add").show();
});