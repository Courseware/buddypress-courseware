<?php setup_postdata( $assignment ); ?>
<?php if( $message ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<div id="courseware-assignment">
    <div class="assignment-content">
        <h4 id="assignment-title"><?php echo $assignment->post_title; ?></h4>
        <h5 id="assignment-due-date">
            <?php _e( 'Due date' ); ?>: <?php echo mysql2date( get_option('date_format'), $assignment->due_date ); ?>
        </h5>
        <?php if( isset( $user_grade ) ): ?>
            <div id="assignment-grade">
                <em><?php _e( 'Your grade for this assignment was:' ); ?></em>
                <strong>
                    <?php if( !empty( $user_grade['format'] ) && 'percentage' == $user_grade['format'] ): ?>
                    <?php echo $user_grade['value']; ?>%
                    <?php else: ?>
                    <?php echo $user_grade['value']; ?>
                    <?php endif; ?>
                </strong>
            </div>
        <?php endif; ?>
        <div id="assignment-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="assignment-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s for %3$s.', 'bpsp' ),
                mysql2date( get_option('date_format'), $assignment->post_date ),
                bp_core_get_userlink( $assignment->post_author ),
                '<a href="' . $course_permalink . '" >' . $assignment->course->post_title . '</a>'
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $assignment_edit_uri; ?>"><?php _e( 'Edit Assignment', 'bpsp' ); ?></a>
            </span>
        <?php endif; ?>
        <span class="gradebook-link">
            <?php if( isset( $has_gradebook_caps ) && $has_gradebook_caps ): ?>
                <a href="<?php echo $assignment_permalink . '/gradebook'; ?>"><?php _e( 'Gradebook', 'bpsp' ); ?></a>
            <?php endif; ?>
        </span>
    </div>
</div>
<?php
    require_once BPSP_PLUGIN_DIR . '/groups/templates/_bibs.php';
?>