<div id="courseware-courses-list">
    <table class="datatables">
        <thead>
            <tr>
                <th><?php _e( 'Description', 'bpsp' ); ?></th>
                <th><?php _e( 'Creation Date', 'bpsp' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if( empty( $courses ) ):
        ?>
            <tr>
                <td>
                    <?php _e( 'No courses were added.', 'bpsp' ); ?>
                </td>
                <td></td>
            </tr>
        <?php
        else:
            foreach ( $courses as $course ):
        ?>
            <tr>
                <td>
                    <a href="<?php echo $courses_hanlder_uri . $course->post_name; ?>"><?php echo $course->post_title; ?></a>
                    <div class="course-meta">
                        <?php
                            printf(
                                __( 'added by %1$s.', 'bpsp' ),
                                bp_core_get_userlink( $course->post_author )
                            );
                        ?>
                    </div>
                </td>
                <td>
                    <?php echo mysql2date( get_option('date_format'), $course->post_date ); ?>
                </td>
            </tr>
        <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>