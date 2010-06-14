<?php
if( $course == null )
    wp_redirect( $current_uri );
?>
<div id="courseware-course">
    <div class="course-content">
        <h4 id="course-title"><?php echo $course->post_title; ?></h4>
        <div id="course-body">
            <?php echo $course->post_content; ?>
        </div>
    </div>
    <div class="course-meta">
        <?php
            printf(
                __( 'Added on %1$s by %2$s.' ),
                mysql2date( get_option('date_format'), $course->post_date ),
                bp_core_get_userlink( $course->post_author )
            );
        ?>
    </div>
</div>