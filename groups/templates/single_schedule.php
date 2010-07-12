<?php if( $message ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<div id="courseware-schedule">
    <div class="schedule-content">
        <h4 id="schedule-desc"><?php echo $schedule->post_title; ?></h4>
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
            <?php echo mysql2date( get_option('date_format'), $schedule->start_date ); ?>
            <?php echo mysql2date( get_option('time_format'), $schedule->start_date ); ?>
        </div>
        <div id="schedule-enddate">
            <span>
                <?php _e( 'End Date:', 'bpsp' ); ?>
            </span>
            <?php echo mysql2date( get_option('date_format'), $schedule->end_date ); ?>
            <?php echo mysql2date( get_option('time_format'), $schedule->end_date ); ?>
        </div>
    </div>
    <div class="schedule-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s.', 'bpsp' ),
                mysql2date( get_option('date_format'), $schedule->post_date ),
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