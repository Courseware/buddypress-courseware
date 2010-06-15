<?php if( $message ): ?>
<div id="courseware-message" class="updated">
    <?php echo $message; ?>
</div>
<?php endif; ?>
<div id="courseware-courses-list">
    <ul>
    <?php
    if( empty( $courses ) ):
    ?>
        <li><?php echo $no_courses_title; ?></li>
    <?php
    else:
        foreach ( $courses as $course ):
    ?>
        <li>
            <a href="<?php echo $courses_hanlder_uri . $course->post_name; ?>"><?php echo $course->post_title; ?></a>
            <?php
                printf(
                    $courses_meta_title,
                    mysql2date( get_option('date_format'), $course->post_date ),
                    bp_core_get_userlink( $course->post_author )
                );
            ?>
        </li>
    <?php
        endforeach;
    endif;
    ?>
    </ul>
</div>