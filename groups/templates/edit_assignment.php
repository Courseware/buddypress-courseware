<?php
include_once ABSPATH . '/wp-admin/includes/media.php' ;
require_once ABSPATH . '/wp-admin/includes/post.php' ;
require_once BPSP_PLUGIN_DIR . '/groups/templates/helpers/editor_helpers.php' ;
wp_tiny_mce();
?>
<form action="<?php echo $assignment_edit_uri; ?>" method="post" class="standard-form" id="new-assignment-form">
    <div id="new-assignment-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Course &amp; Due Date', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="new-assignment-course">
                    <select name="assignment[course_id]">
                        <?php foreach( $courses as $c ): ?>
                            <option value="<?php echo $c->ID; ?>" <?php selected( $c->ID, $assignment->course->ID ); ?>><?php echo $c->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
            </li>
            <li id="new-assignment-due-date">
                <input type="text" name="assignment[due_date]" value="<?php echo $assignment->due_date; ?>" />
            </li>
        </ul>
    </div>
    <div id="new-assignment-content" class="courseware-content-wrapper">
        <div id="new-assignment-content-title">
            <input type="text" id="assignment-title" name="assignment[title]" class="long" value="<?php echo $assignment->post_title; ?>" />
        </div>
        <div id="new-assignment-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php the_editor( $assignment->post_content, 'assignment[content]', 'assignment[title]', false ); ?>
            </div>
        </div>
        <div id="new-assignment-content-options">
            <input type="hidden" id="new-assignment-post-object" name="assignment[object]" value="group"/>
            <input type="hidden" id="new-assignment-post-in" name="assignment[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <input type="submit" name="assignment[submit]" id="new-assignment-submit" value="<?php _e( 'Update assignment', 'bpsp' ); ?>">
            <div class="alignright submits">
                <?php if( $delete_nonce ): ?>
                    <a href="<?php echo $delete_nonce; ?>" class="action alert"><?php _e( 'Delete Assignment', 'bpsp' ); ?></a>
                <?php endif; ?>
                <a href="<?php echo $assignment_permalink ?>" class="action safe"><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>