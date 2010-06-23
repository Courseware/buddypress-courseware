<?php if( $message ): ?>
<div id="courseware-message" class="updated">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<div id="courseware-courses-list">
    <ul>
    <?php
    if( empty( $assignments ) ):
    ?>
        <li><?php echo $no_assignments_title; ?></li>
    <?php
    else:
        foreach ( $assignments as $assignment ):
    ?>
        <li>
            <a href="<?php echo $assignments_hanlder_uri . $assignment->post_name; ?>"><?php echo $assignment->post_title; ?></a>
            <?php
                printf(
                    $assignments_meta_title,
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