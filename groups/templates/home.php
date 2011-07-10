<div id="group-dashboard">
    <h4 class="meta padded general"><span class="icon"></span><?php _e( 'At a glance', 'bpsp' ); ?></h4>
    <div class="grid courseware-content">
        <div class="dp75">
            <ul class="details">
                <li>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/course/edit' ?>" class="alignright action">
                            <?php _e( 'Update Course', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/course' ?>" class="alignright action">
                            <?php _e( 'Course description', 'bpsp' );?>
                        </a>
                    <?php endif; ?>
                    <?php
                        printf(
                            __( '<strong>%s</strong> course class started by %s and managed by %d teacher(s) for this group', 'bpsp' ),
                            get_the_title( $group_course->ID ),
                            bp_core_get_userlink( $group_course->post_author ),
                            count( $teachers )
                        );
                    ?>
                </li>
                <li>
                    <?php
                        printf(
                            __( '<em>%s</em> Lecture(s)', 'bpsp' ),
                            count( $lectures )
                        );
                    ?>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/new_lecture' ?>" class="alignright action">
                            <?php _e( 'Add a new lecture', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/course' ?>" class="alignright action">
                            <?php _e( 'View course lectures', 'bpsp' );?>
                        </a>
                    <?php endif; ?>
                </li>
                <li>
                    <?php
                        printf(
                            __( '<em>%s</em> Assignment(s)', 'bpsp' ),
                            count( $assignments )
                        );
                    ?>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/new_assignment' ?>" class="alignright action">
                            <?php _e( 'Create an assignment', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/assignments' ?>" class="alignright action">
                            <?php _e( 'All assignments', 'bpsp' );?>
                        </a>
                    <?php endif; ?>
                </li>
                <li>
                    <?php
                        printf(
                            __( '<em>%s</em> Response(s)', 'bpsp' ),
                            $responses_count
                        );
                    ?>
                    <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/assignments' ?>" class="alignright action">
                        <?php _e( 'All responses', 'bpsp' );?>
                    </a>
                </li>
                <li>
                    <?php
                        printf(
                            __( '<em>%s</em> Schedule(s)', 'bpsp' ),
                            count( $schedules )
                        );
                    ?>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/new_schedule' ?>" class="alignright action">
                            <?php _e( 'Add a schedule', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/schedules' ?>" class="alignright action">
                            <?php _e( 'All schedules', 'bpsp' );?>
                        </a>
                    <?php endif; ?>
                </li>
                <li>
                    <?php
                        printf(
                            __( '<em>%s</em> Bibliography entries', 'bpsp' ),
                            $bibliography_count
                        );
                    ?>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/new_bibliography' ?>" class="alignright action">
                            <?php _e( 'Manage bibliography', 'bpsp' );?>
                        </a>
                    <?php endif; ?>
                </li>
                <?php if( bp_group_is_forum_enabled() ): ?>
                    <li>
                        <?php
                            printf(
                                __( '<em>%d</em> Assignment discussions started', 'bpsp' ),
                                $assignment_topics_count
                            );
                        ?>
                        <a href="<?php bp_group_forum_permalink(); ?>" class="alignright action">
                            <?php _e( 'Visit forums', 'bpsp' );?>
                        </a>
                    </li>
                <?php endif;?>
            </ul>
        </div>
        <div class="dp25">
            <div id="user-progress">
                <?php echo $assignments_count - $own_responses_count; ?>,<?php echo $own_responses_count; ?>
            </div>
            <div id="progress-title"><?php _e( 'Your progress so far:', 'bpsp' );?></div>
            <div id="progress-count"><?php echo ( $own_responses_count / $assignments_count ) * 100; ?>%</div>
            <hr />
            <?php if( $user_bookmark ) : ?>
                <a href="<?php echo $user_bookmark->permalink ?>" class="alignleft action"><?php _e( 'Your last bookmark &rarr;', 'bpsp' );?></a>
            <?php else: ?>
                <em><?php _e( "You didn't bookmark any lectures so far.", 'bpsp' );?></em>
            <?php endif; ?>
            <hr />
            <em><?php _e( "Today is: ", 'bpsp' );?></em><code><?php echo bpsp_get_date( date( 'now' ) ); ?></code>
        </div>
        <div class="clear"></div>
    </div>
    
    <?php if( count( $assignments ) > 0 ): ?>
    <h4 class="meta padded assignments">
        <span class="icon"></span>
        <?php _e( 'Latest assignments', 'bpsp' ); ?>
        <a href="<?php echo $nav_options[__( 'Assignments', 'bpsp' )] ?>" class="alignright action">
            <?php _e( 'All assignments', 'bpsp' );?>
        </a>
    </h4>
    <div class="grid courseware-content">
        <div class="dp100">
            <ul class="details marked">
                <?php foreach ( array_slice( $assignments, 0, $items_limit ) as $a ): ?>
                    <li>
                        <a href="<?php echo $a->permalink ?>"><?php echo get_the_title( $a->ID ); ?></a>
                        <span class="alignright meta">
                            <?php
                                printf(
                                    __( 'By %s, on %s', 'bpsp' ),
                                    bp_core_get_userlink( $a->post_author ),
                                    bpsp_get_date( $a->post_date )
                                );
                            ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
    
    <?php if( count( $schedules ) > 0 ): ?>
    <h4 class="meta padded schedules">
        <span class="icon"></span>
        <?php _e( ' Latest schedules', 'bpsp' ); ?>
        <a href="<?php echo $nav_options[__( 'Schedule', 'bpsp' )] ?>" class="alignright action">
            <?php _e( 'All schedules', 'bpsp' );?>
        </a>
    </h4>
    <div class="grid courseware-content">
        <div class="dp100">
            <ul class="details marked">
                <?php foreach ( array_slice( $schedules, 0, $items_limit ) as $s ): ?>
                    <li>
                        <a href="<?php echo $s->permalink ?>"><?php echo bp_create_excerpt( $s->post_content, 20 ); ?></a>
                        <span class="alignright meta">
                            <?php
                                printf(
                                    __( 'By %s, on %s', 'bpsp' ),
                                    bp_core_get_userlink( $s->post_author ),
                                    bpsp_get_date( $s->post_date )
                                );
                            ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
    
    <h4 class="meta padded responses"><span class="icon"></span><?php _e( 'Latest responses', 'bpsp' ); ?></h4>
    <div class="grid courseware-content">
        <div class="dp100">
            <ul class="details marked">
                    <?php
                        $no_responses = true;
                        $count = $items_limit;
                        if( !empty( $assignments ) )
                        foreach ( $assignments as $a )
                            if( !empty( $a->responses ) && $count > 0 )
                                foreach ( $a->responses as $r ) :
                                    $no_responses = false;
                                    $count--;
                    ?>
                    <li>
                        <a href="<?php echo $a->permalink . '/response/' . $r->ID ?>">
                            <?php echo get_the_title( $r->ID ); ?>
                        </a>
                        <span class="alignright meta">
                            <?php
                                printf(
                                    __( 'By %s, on %s', 'bpsp' ),
                                    bp_core_get_userlink( $r->post_author ),
                                    bpsp_get_date( $r->post_date )
                                );
                            ?>
                        </span>
                    </li>
                <?php endforeach; ?>
                <?php if( $no_responses ): ?>
                    <li><?php _e( 'No responses yet.', 'bpsp' ); ?></li>
                <?php endif; ?>
            </ul>
        </div>
        <div class="clear"></div>
    </div>
    
    <?php if( !empty( $grades ) ): ?>
    <h4 class="meta padded grades"><span class="icon"></span><?php _e( 'Your progress based on received grades', 'bpsp' ); ?></h4>
    <div class="grid courseware-content">
        <div id="user-grades" >
            <?php echo implode( ',', $grades ); ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>