<!-- groups/templates/single_course.php -->
<?php setup_postdata( $course ); ?>
<div id="courseware-course">
    <div id="course-meta" class="courseware-sidebar">
        <h4 class="meta courses"><span class="icon"></span><?php _e( 'Course Info', 'bpsp' ) ?></h4>
        <ul class="courseware-meta">
            <li class="date">
            <?php
                printf(
                    __( 'Date: %1$s', 'bpsp' ),
                    bpsp_get_date( $course->post_date )
                );
            ?>
            </li>
            <li class="author">
            <?php
                printf(
                    __( 'Author: %1$s', 'bpsp' ),
                    bp_core_get_userlink( $course->post_author )
                );
            ?>
            </li>
            <li class="show-bibs">
                <a href="#courseware-bibs-list" class="action"><?php _e( 'Bibliography', 'bpsp' ); ?></a>
            </li>
            <?php if( $show_edit ): ?>
                <li class="add bib">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Bibliography', 'bpsp' ); ?></a>
                </li>
                <li class="add book">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Book', 'bpsp' ); ?></a>
                </li>
                <li class="add www">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Webpage', 'bpsp' ); ?></a>
                </li>
                <li class="edit-link">
                    <a href="<?php echo $course_edit_uri; ?>" class="action"><?php _e( 'Edit Course', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="course-content" class="courseware-content-wrapper">
        <h4 id="course-title" class="courseware-title">
            <?php echo get_the_title( $course->ID ); ?>
        </h4>
        <div id="course-body" class="courseware-content">
            <?php the_content(); ?>
        </div>
    </div>
</div>
<?php
    // Load lectures tree
    bpsp_partial( $templates_path, '_lectures', get_defined_vars() );
?>
<?php
    // Load bibs
    bpsp_partial( $templates_path, '_bibs', get_defined_vars() );
?>