<?php setup_postdata( $course ); ?>
<div id="courseware-course">
    <div class="course-content">
        <h4 id="course-title"><?php echo $course->post_title; ?></h4>
        <div id="course-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="course-meta">
        <span class="meta">
        <?php
            printf(
                $course_meta_title,
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