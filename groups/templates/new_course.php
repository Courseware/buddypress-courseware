<?php bpsp_load_editor_files(); ?>

<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-course-form">
    <div id="new-course-content">
        <h4 class="meta"><?php _e( 'Add a New Course', 'bpsp' ); ?></h4>
        <div id="new-course-content-title">
            <label for="content[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" id="course-title" class="long" name="course[title]" title="<?php _e( 'Course Title', 'bpsp' ); ?>"/>
        </div>
        <div id="new-course-content-textarea">
            <div id="editor-toolbar">
               <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php the_editor( '', 'course[content]', 'course[title]', false ); ?>
            </div>
        </div>
        <div id="new-course-content-options">
            <input type="hidden" id="new-course-post-object" name="course[object]" value="group"/>
            <input type="hidden" id="new-course-post-in" name="course[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-course-content-submit">
                <input type="submit" name="course[submit]" id="new-course-submit" class="safe" value="<?php _e( 'Add a new course', 'bpsp' ); ?>">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>

<?php wp_tiny_mce(); ?>