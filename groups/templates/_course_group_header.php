<?php setup_postdata( $course ); ?>
<div id="courseware-course-header">
    <div class="course-content">
        <div id="course-body">
            <?php the_excerpt(); ?>
        </div>
    </div>
    <div class="course-meta">
        <?php 
            printf(
                __( 'Course started on %1$s by %2$s.', 'bpsp' ),
                bpsp_get_date( $course->post_date ),
                bp_core_get_userlink( $course->post_author )
            );
        ?>
    </div>
</div>