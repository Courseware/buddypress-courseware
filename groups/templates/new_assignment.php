<?php
bpsp_load_editor_files();
wp_tiny_mce();
?>
<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-assignment-form">
    <h5><?php _e( 'Add a new assignment', 'bpsp' ); ?></h5>
    <div id="new-assignment-content">
        <div id="new-assignment-content-title">
            <label for="assignment[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" id="assignment-title" name="assignment[title]"/>
        </div>
        <div id="new-assignment-course">
            <label for="assignment[course_id]"><?php _e( 'Course', 'bpsp' ); ?></label>
                <select name="assignment[course_id]">
                    <?php foreach( $courses as $c ): ?>
                        <option value="<?php echo $c->ID; ?>"><?php echo $c->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div id="new-assignment-due-date">
            <label for="assignment[due_date]"><?php _e( 'Due date', 'bpsp' ); ?></label>
                <input type="text" name="assignment[due_date]" />
        </div>
        <div id="new-assignment-content-textarea">
            <div id="editor-toolbar">
                <?php
                    echo bpsp_media_buttons();
                    the_editor( '', 'assignment[content]', 'assignment[title]', false );
                ?>
            </div>
        </div>
        <div id="new-assignment-content-options">
            <input type="hidden" id="new-assignment-post-object" name="assignment[object]" value="group"/>
            <input type="hidden" id="new-assignment-post-in" name="assignment[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-assignment-content-submit">
                <input type="submit" name="assignment[submit]" id="new-assignment-submit" value="<?php _e( 'Add a new assignment', 'bpsp' ); ?>">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>