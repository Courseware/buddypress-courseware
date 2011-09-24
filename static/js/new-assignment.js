/**
 * Javascript calls for new assignment screens
 */

jQuery('#courseware-assignment-builder').formbuilder({
    'control_box_target': '#new-assignment-formbuilder-control-box',
    'messages': fbLanguage
});

jQuery("#courseware-assignment-builder ul").sortable({ opacity: 0.6, cursor: 'move'});
jQuery("#new-assignment-submit").live( 'click', function() {
    var form_data = jQuery("#courseware-assignment-builder ul").serializeFormList({'prepend': 'assignment-frmb'});
    jQuery("#new-assignment-post-form").val(form_data);
});