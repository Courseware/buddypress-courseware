<?php setup_postdata( $schedule ); ?>
<div id="courseware-schedule">
    <div class="schedule-meta courseware-sidebar">
        <h4 class="meta"><?php _e( 'Schedule Meta', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <!-- Ponintless--li class="date">
            <?php
                printf(
                    __( 'Date: %1$s', 'bpsp' ),
                    bpsp_get_date( $schedule->post_date )
                );
            ?>
            </li-->
            <li id="schedule-startdate">
                <?php
                    printf(
                        __( 'Start Date: %1$s', 'bpsp' ),
                        bpsp_get_date( $schedule->start_date )
                    );
                ?>
            </li>
            <?php if( !empty( $schedule->end_date ) ) : ?>
                <li id="schedule-enddate">
                    <?php
                        printf(
                            __( 'End Date: %1$s', 'bpsp' ),
                            bpsp_get_date( $schedule->end_date )
                        );
                    ?>
                </li>
            <?php endif; ?>
            <?php if( $schedule->location ): ?>
                <li id="schedule-location">
                    <?php
                        printf(
                            __( 'Location: %1$s', 'bpsp' ),
                            $schedule->location
                        );
                    ?>
                </li>
            <?php endif; ?>
            <li class="author">
            <?php
                printf(
                    __( 'Author: %1$s', 'bpsp' ),
                    bp_core_get_userlink( $schedule->post_author )
                );
            ?>
            </li>
            <?php if( isset( $schedule->lecture ) ): ?>
                <li id="schedule-lecture">
                    <?php _e( 'Linked Lecture:', 'bpsp' ); ?>
                    <a href="<?php echo $schedule->lecture->permalink; ?>">
                        <?php echo $schedule->lecture->post_title; ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if( $show_edit ): ?>
                <li class="edit-link">
                    <a href="<?php echo $schedule_edit_uri; ?>" class="action"><?php _e( 'Edit Schedule', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="schedule-content courseware-content-wrapper">
        <h4 id="course-title" class="courseware-title">
            <?php echo get_the_title( $schedule->ID ); ?>
        </h4>
        <div id="schedule-desc" class="courseware-content">
            <?php the_content(); ?>
        </div>
    </div>
</div>