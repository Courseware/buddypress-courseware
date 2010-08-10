<?php setup_postdata( $course ); ?>
<div id="courseware-course">
    <div class="course-content">
        <h4 id="course-title"><?php echo get_the_title( $course->ID ); ?></h4>
        <div id="course-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="course-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s.', 'bpsp' ),
                bpsp_get_date( $course->post_date ),
                bp_core_get_userlink( $course->post_author )
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $course_edit_uri; ?>"><?php _e( 'Edit Course', 'bpsp' ); ?></a>
            </span>
        <?php endif; ?>
    </div>
</div>
<?php
    // Load bibs
    bpsp_partial( $templates_path, '_bibs', get_defined_vars() );
?>