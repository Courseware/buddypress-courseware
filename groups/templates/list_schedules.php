<?php if( $message ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<div id="courseware-schedules-list">
    <ul>
    <?php
    if( empty( $schedules ) ):
    ?>
        <li><?php _e( 'No schedules were added.', 'bpsp' ); ?></li>
    <?php
    else:
        foreach ( $schedules as $schedule ):
    ?>
        <li>
            <a href="<?php echo $schedules_hanlder_uri . $schedule->post_name; ?>"><?php echo $schedule->post_title; ?></a>
            <?php
                printf(
                    __( 'added on %1$s by %2$s.', 'bpsp' ),
                    mysql2date( get_option('date_format'), $schedule->post_date ),
                    bp_core_get_userlink( $schedule->post_author )
                );
            ?>
        </li>
    <?php
        endforeach;
    endif;
    ?>
    </ul>
</div>