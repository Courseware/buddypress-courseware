<form action="<?php echo $course_edit_uri; ?>" method="post" class="standard-form" id="update-course-form">
    <div id="update-course-content">
        <div id="update-course-content-title">
            <input type="text" id="course-editor-title" class="long" name="course[title]" value="<?php echo $course->post_title; ?>"/>
        </div>
        <div id="update-course-content-textarea">
            <?php courseware_editor( $course->post_content, 'course-editor', array('textarea_name' => 'course[content]') ); ?>
        </div>
        <div id="update-course-content-options">
            <input type="hidden" id="update-course-post-object" name="course[object]" value="group"/>
            <input type="hidden" id="update-course-post-in" name="course[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="update-course-submit">
                <input type="submit" name="course[submit]" id="update-course-submit" value="<?php _e( 'Update course', 'bpsp' ); ?>">
                <div class="alignright submits">
                    <a href="<?php echo $course_permalink ?>" class="action"><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>