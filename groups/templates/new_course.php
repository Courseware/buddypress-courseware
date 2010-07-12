<?php
include_once ABSPATH . '/wp-admin/includes/media.php' ;
require_once ABSPATH . '/wp-admin/includes/post.php' ;
require_once BPSP_PLUGIN_DIR . '/groups/templates/helpers/editor_helpers.php' ;
?>
<?php if( $message ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<form action="<?php echo $current_option; ?>" method="post" id="new-course-form">
    <h5><?php _e( 'Add a new course', 'bpsp' ); ?></h5>
    <div id="new-course-content">
        <div id="new-course-content-title">
            <label for="content[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" id="course-title" name="course[title]"/>
        </div>
        <div id="new-course-content-textarea">
            <div id="editor-toolbar">
                <?php
                    echo bpsp_media_buttons();
                    the_editor( '', 'course[content]', 'course[title]', false );
                ?>
            </div>
        </div>
        <div id="new-course-content-options">
            <input type="hidden" id="new-course-post-object" name="course[object]" value="group"/>
            <input type="hidden" id="new-course-post-in" name="course[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-course-content-submit">
                <input type="submit" name="course[submit]" id="new-course-submit" value="<?php _e( 'Add a new course', 'bpsp' ); ?>">
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