<?php setup_postdata( $schedule ); ?>
<div id="courseware-schedule">
    <div class="schedule-content">
        <div id="schedule-desc"><?php the_content(); ?></div>
        <?php if( !empty( $schedule->course ) ): ?>
        <div id="schedule-courseinfo">
            <span>
                <?php _e( 'Course:', 'bpsp' ); ?>
            </span>
            <a href="<?php echo $current_uri . '/course/' . $schedule->course->post_name; ?>">
                <?php echo $schedule->course->post_title; ?>
            </a>
        </div>
        <?php endif; ?>
        <?php if( $schedule->location ): ?>
        <div id="schedule-location">
            <span>
                <?php _e( 'Location:', 'bpsp' ); ?>
            </span>
            <?php echo $schedule->location; ?>
        </div>
        <?php endif; ?>
        <div id="schedule-startdate">
            <span>
                <?php _e( 'Start Date:', 'bpsp' ); ?>
            </span>
            <?php bpsp_date( $schedule->start_date ); ?>
        </div>
        <?php if( !empty( $schedule->end_date ) ) : ?>
        <div id="schedule-enddate">
            <span>
                <?php _e( 'End Date:', 'bpsp' ); ?>
            </span>
            <?php bpsp_date( $schedule->end_date ); ?>
        </div>
        <?php endif; ?>
    </div>
    <div class="schedule-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s.', 'bpsp' ),
                bpsp_get_date( $schedule->post_date ),
                bp_core_get_userlink( $schedule->post_author )
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $schedule_edit_uri; ?>"><?php _e( 'Edit Schedule', 'bpsp' ); ?></a>
            </span>
        <?php endif; ?>
    </div>
</div>