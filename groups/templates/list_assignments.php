<div id="courseware-courses-list">
    <table class="datatables">
        <thead>
            <tr>
                <th><?php _e( 'Description', 'bpsp' ); ?></th>
                <th><?php _e( 'Due Date', 'bpsp' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        if( empty( $assignments ) ):
        ?>
            <tr>
                <td>
                    <?php _e( 'No assignments were added.', 'bpsp' ); ?>
                </td>
                <td></td>
            </tr>
        <?php
        else:
            foreach ( $assignments as $assignment ):
        ?>
            <tr>
                <td>
                    <a href="<?php echo $assignments_hanlder_uri . $assignment->post_name; ?>"><?php echo $assignment->post_title; ?></a>
                    <div class="assignment-meta">
                        <?php
                            printf(
                                __( 'added on %1$s by %2$s.', 'bpsp' ),
                                bpsp_get_date( $assignment->post_date ),
                                bp_core_get_userlink( $assignment->post_author )
                            );
                        ?>
                    </div>
                </td>
                <td>
                    <?php
                        $due_date = get_post_meta( $assignment->ID, 'due_date' );
                        if( !empty( $due_date ) )
                            bpsp_date( end( $due_date ) );
                    ?>
                </td>
            </tr>
        <?php
            endforeach;
        endif;
        ?>
        </tbody>
    </table>
</div>