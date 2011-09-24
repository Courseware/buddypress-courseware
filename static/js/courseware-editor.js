/**
 * Javascript calls for editor screens
 */

jQuery('#new-course-content label').hide();
jQuery('input[name="course[title]"]').inputHint();
jQuery('input[name="assignment[title]"]').inputHint();
jQuery('input[name="assignment[due_date]"]').inputHint();
jQuery('input[name="response[title]"]').inputHint();
jQuery('input[name="lecture[title]"]').inputHint();
jQuery('input[name="lecture[order]"]').inputHint();

// Distraction free writing compatibility
jQuery(function($) {
    // Components that use editor
    var components = [ 'course', 'assignment', 'response', 'lecture'];
    
    // Cycle through all the components and try to find the editor IDs
    $(components).each( function(i,c) {
        var title_id = $("*[name='" + c + "\\[title\\]']").attr('id');
        var content_id = $("*[name='" + c + "\\[content\\]']").attr('id');
        
        // Try to update the fullscreen variable settings
        if ( typeof title_id != 'undefined' )
            fullscreen.settings.title_id = title_id;
        if ( typeof content_id != 'undefined' )
            fullscreen.settings.editor_id = content_id;
    })
    
    // Try to check for content_id, wp-fullscreen fails here
    $("#wp-fullscreen-body").one("mousemove", function(){
        var content_elem = document.getElementById( fullscreen.settings.editor_id );
        var editor_mode = $(content_elem).is(':hidden') ? 'tinymce' : 'html';
        fullscreen.switchmode(editor_mode);
    });
    
    // Delete the loader, it won't load anyway
    $('#wp-fullscreen-save img').remove();
    
    // Make word counts work
    $(document).triggerHandler('wpcountwords', [ $(edCanvas).val() ] );
});