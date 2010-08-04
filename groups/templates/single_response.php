<?php setup_postdata( $response ); ?>
<div id="courseware-course">
    <div class="course-content">
        <h4 id="course-title"><?php echo get_the_title( $response->ID ); ?></h4>
        <div id="course-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="course-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s.', 'bpsp' ),
                bpsp_get_date( $response->post_date ),
                bp_core_get_userlink( get_the_author() )
            );
        ?>
        </span>
        <span class="assignment-link">
            <a href="<?php echo $assignment_permalink; ?>"><?php _e( 'Assignment page', 'bpsp' ); ?></a>
        </span>
        <?php if( isset( $response_delete_uri ) ): ?>
            <span class="delete-link">
                <a href="<?php echo $response_delete_uri; ?>"><?php _e( 'Delete Response', 'bpsp' ); ?></a>
            </span>
        <?php endif; ?>
    </div>
</div>