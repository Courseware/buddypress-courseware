/**
 * Javascript calls for edit assignment screens
 */

jQuery('#courseware-assignment-builder').formbuilder({
    'load_url': '?get_form_data',
    'control_box_target': '#edit-assignment-formbuilder-control-box',
    'messages': fbLanguage
});

jQuery("#courseware-assignment-builder ul").sortable({ opacity: 0.6, cursor: 'move'});
jQuery("#edit-assignment-submit").on( 'click', function() {
    var form_data = jQuery("#courseware-assignment-builder ul").serializeFormList({'prepend': 'assignment-frmb'});
    jQuery("#edit-assignment-post-form").val(form_data);
});
