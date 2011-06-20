/**
 * Javascript calls for new assignment screens
 */

jQuery('#courseware-assignment-builder').formbuilder({
    'save_url': '',
    'load_url': '',
    'control_box_target': '#new-assignment-formbuilder-control-box',
    'messages': fbLanguage
});
jQuery("#courseware-assignment-builder ul").sortable({ opacity: 0.6, cursor: 'move'});