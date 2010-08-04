<?php setup_postdata( $course ); ?>
<div id="courseware-course-header">
    <div class="course-content">
        <div id="course-body">
            <?php the_excerpt(); ?>
        </div>
    </div>
    <div class="course-meta">
        <span class="meta">
        <?php 
            printf(
                __( '%1$s by %2$s.' ),
                bpsp_get_date( $course->post_date ),
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