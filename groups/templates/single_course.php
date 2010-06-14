<div id="courseware-course">
    <div class="course-content">
        <h4 id="course-title"><?php echo $course->post_title; ?></h4>
        <div id="course-body">
            <?php echo $course->post_content; ?>
        </div>
    </div>
    <div class="course-meta">
        <span class="meta">
        <?php
            printf(
                __( 'Added on %1$s by %2$s.' ),
                mysql2date( get_option('date_format'), $course->post_date ),
                bp_core_get_userlink( $course->post_author )
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $course_edit_uri; ?>"><?php echo $show_edit ?></a>
            </span>
        <?php endif; ?>
    </div>
</div>