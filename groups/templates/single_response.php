<?php setup_postdata( $response ); ?>
<div id="courseware-course">
    <div class="course-meta courseware-sidebar">
        <h4 class="meta"><?php _e( 'Response Meta', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li class="date">
                <?php
                    printf(
                        __( 'Date: %1$s', 'bpsp' ),
                        bpsp_get_date( $response->post_date )
                    );
                ?>
            </li>
            <li class="author">
                <?php
                    printf(
                        __( 'Author: %1$s', 'bpsp' ),
                        bp_core_get_userlink( get_the_author() )
                    );
                ?>
            </li>
            <li class="assignment-link">
                <a href="<?php echo $assignment_permalink; ?>" class="action"><?php _e( 'Assignment page', 'bpsp' ); ?></a>
            </li>
            <?php if( isset( $response_delete_uri ) ): ?>
                <li class="delete-link">
                    <a href="<?php echo $response_delete_uri; ?>" class="action alert"><?php _e( 'Delete Response', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="course-content courseware-content-wrapper">
        <h4 id="response-title" class="courseware-title"><?php echo get_the_title( $response->ID ); ?></h4>
        <div id="response-body" class="courseware-content">
            <?php the_content(); ?>
            <?php if( isset( $response->form_values ) ) : ?>
                <strong><?php _e( 'Wrong answers', 'bpsp' ); ?>:</strong>
                <ol>
                    <?php foreach( $response->form_values as $q => $a ): ?>
                        <?php if( in_array( $q, array( 'total', 'correct' ) ) ) continue; ?>
                        <li>
                            <em><?php echo $q ?></em>
                            <ul class="answers">
                                <?php foreach ($a as $k => $e): ?>
                                    <li class="<?php echo ( ( $k % 2 ) == 0 ) ? 'wrong' : 'correct'; ?>">
                                        <?php echo $e; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </li>
                    <?php endforeach; ?>
                </ol>
            <?php endif; ?>
        </div>
    </div>
</div>