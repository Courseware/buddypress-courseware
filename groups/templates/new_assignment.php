<?php
bpsp_load_editor_files();
wp_tiny_mce();
?>
<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-assignment-form">
    <div id="new-assignment-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Course &amp; Due Date', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="new-assignment-course">
                    <select name="assignment[course_id]">
                        <?php foreach( $courses as $c ): ?>
                            <option value="<?php echo $c->ID; ?>"><?php echo $c->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
            </li>
            <li id="new-assignment-due-date">
                <input type="text" name="assignment[due_date]" title="<?php _e( 'Due date', 'bpsp' ); ?>"
                    value="<?php echo $posted_data['due_date'] ? $posted_data['due_date'] : ''; ?>"/>
            </li>
        </ul>
    </div>
    <div id="new-assignment-content" class="courseware-content-wrapper" >
        <div id="new-assignment-content-title">
            <input type="text" id="assignment-title" name="assignment[title]"
                   value="<?php echo $posted_data['title'] ? $posted_data['title'] : ''; ?>"
                   class="long" title="<?php _e( 'Assignment Title', 'bpsp' ); ?>"/>
        </div>
        <div id="new-assignment-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php $content = $posted_data['content'] ? $posted_data['content'] : ''; ?>
                <?php the_editor( $content, 'assignment[content]', 'assignment[title]', false ); ?>
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