<?php
include_once ABSPATH . '/wp-admin/includes/media.php' ;
require_once ABSPATH . '/wp-admin/includes/post.php' ;
require_once BPSP_PLUGIN_DIR . '/groups/templates/helpers/editor_helpers.php' ;
wp_tiny_mce();
?>
<form action="<?php echo $course_edit_uri; ?>" method="post" class="standard-form" id="update-course-form">
    <div id="update-course-content">
        <div id="update-course-content-title">
            <input type="text" id="course-title" class="long" name="course[title]" value="<?php echo $course->post_title; ?>"/>
        </div>
        <div id="update-course-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php the_editor( $course->post_content, 'course[content]', 'course[title]', false ); ?>
            </div>
        </div>
        <div id="update-course-content-options">
            <input type="hidden" id="update-course-post-object" name="course[object]" value="group"/>
            <input type="hidden" id="update-course-post-in" name="course[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="update-course-submit">
                <input type="submit" name="course[submit]" id="update-course-submit" value="<?php _e( 'Update course', 'bpsp' ); ?>">
                <div class="alignright submits">
                    <?php if( $delete_nonce ): ?>
                        <a href="<?php echo $delete_nonce; ?>" class="action alert"><?php echo $course_delete_title; ?></a>
                    <?php endif; ?>
                    <a href="<?php echo $course_permalink ?>" class="action safe"><?php _e( 'Cancel/Go Back', 'bpsp' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>