<div id="courseware-courses-list">
    <ul>
    <?php
    if( empty( $assignments ) ):
    ?>
        <li><?php _e( 'No assignments were added.', 'bpsp' ); ?></li>
    <?php
    else:
        foreach ( $assignments as $assignment ):
    ?>
        <li>
            <a href="<?php echo $assignments_hanlder_uri . $assignment->post_name; ?>"><?php echo $assignment->post_title; ?></a>
            <?php
                printf(
                    __( 'added on %1$s by %2$s.', 'bpsp' ),
                    mysql2date( get_option('date_format'), $assignment->post_date ),
                    bp_core_get_userlink( $assignment->post_author )
                );
            ?>
        </li>
    <?php
        endforeach;
    endif;
    ?>
    </ul>
</div>