<!-- groups/templates/list_assignments.php -->
<div id="courseware-assignments-list">
<h4 class="meta assignments"><span class="icon"></span><?php _e( 'Assignments', 'bpsp' ) ?></h4>
    <table class="datatables">
        <thead>
            <tr>
                <th><?php _e( 'Description', 'bpsp' ); ?></th>
                <th><?php _e( 'Type', 'bpsp' ); ?></th>
                <th><?php _e( 'Responses', 'bpsp' ); ?></th>
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
                <td></td>
                <td></td>
            </tr>
        <?php
        else:
            foreach ( $assignments as $assignment ):
        ?>
            <tr>
                <td>
                    <a href="<?php echo $assignments_hanlder_uri . $assignment->post_name; ?>"><?php echo get_the_title( $assignment->ID ); ?></a>
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
                        if( !empty( $assignment->form_data ) )
                            _e( 'Quiz', 'bpsp' );
                        else
                            _e( 'Task', 'bpsp' );
                    ?>
                </td>
                <td>
                    <?php
                        echo count( $assignment->responded_author );
                    ?>
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