<?php
include_once ABSPATH . '/wp-admin/includes/media.php' ;
require_once ABSPATH . '/wp-admin/includes/post.php' ;
require_once BPSP_PLUGIN_DIR . '/groups/templates/helpers/editor_helpers.php' ;
?>
<?php if( $message ): ?>
<div id="courseware-message" class="updated">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<form action="<?php echo $assignment_edit_uri; ?>" method="post" id="new-assignment-form">
    <h5><?php _e( 'Edit assignment', 'bpsp' ); ?> &mdash; <a href="<?php echo $assignment_permalink ?>"><?php _e( 'Preview', 'bpsp' ); ?></a></h5>
    <div id="new-assignment-content">
        <div id="new-assignment-content-title">
            <label for="assignment[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" id="assignment-title" name="assignment[title]" value="<?php echo $assignment->post_title; ?>" />
        </div>
        <div id="new-assignment-course">
            <label for="assignment[course_id]"><?php _e( 'Course', 'bpsp' ); ?></label>
                <select name="assignment[course_id]">
                    <?php foreach( $courses as $c ): ?>
                        <option value="<?php echo $c->ID; ?>" <?php if ( $c->ID == $assignment->course->ID ) echo 'selected="selected"'; ?>><?php echo $c->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div id="new-assignment-due-date">
            <script type="text/javascript">
                var dtpicker_months = { months: [<?php _e( "'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'", 'bpsp' ); ?>] };
            </script>
            <label for="assignment[due_date]"><?php _e( 'Due date', 'bpsp' ); ?></label>
                <input type="datetime-local" min="<?php echo date('c'); ?>" name="assignment[due_date]" value="<?php echo $assignment->due_date; ?>" />
        </div>
        <div id="new-assignment-content-textarea">
            <div id="editor-toolbar">
                <?php
                    echo bpsp_media_buttons();
                    the_editor( $assignment->post_content, 'assignment[content]', 'assignment[title]', false );
                ?>
            </div>
        </div>
        <div id="new-assignment-content-options">
            <input type="hidden" id="new-assignment-post-object" name="assignment[object]" value="group"/>
            <input type="hidden" id="new-assignment-post-in" name="assignment[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-assignment-content-submit">
                <input type="submit" name="assignment[submit]" id="new-assignment-submit" value="<?php _e( 'Update assignment', 'bpsp' ); ?>">
                <?php if( $delete_nonce ): ?>
                    <a href="<?php echo $delete_nonce; ?>"><?php _e( 'Delete Assignment', 'bpsp' ); ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>
<?php
wp_tiny_mce();
?>