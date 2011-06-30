<div id="group-dashboard">
    <h4 class="meta padded"><?php _e( 'At a glance', 'bpsp' ); ?></h4>
    <div class="grid courseware-content">
        <div class="dp75">
            <ul class="details">
                <li>
                    <?php
                        printf(
                            __( '<strong>%s</strong> course class started by %s and managed by %d teacher(s) for this group', 'bpsp' ),
                            get_the_title( $group_course->ID ),
                            bp_core_get_userlink( $group_course->post_author ),
                            count( $teachers )
                        );
                    ?>
                    <?php if( $is_teacher ): ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/course/edit' ?>" class="alignright action">
                            <?php _e( 'Update Course  &raquo;', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/course' ?>" class="alignright action">
                            <?php _e( 'Course description  &raquo;', 'bpsp' );?>
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
                            <?php _e( 'Create an assignment &raquo;', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/assignments' ?>" class="alignright action">
                            <?php _e( 'All assignments  &raquo;', 'bpsp' );?>
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
                        <?php _e( 'All responses &raquo;', 'bpsp' );?>
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
                            <?php _e( 'Add a schedule &raquo;', 'bpsp' );?>
                        </a>
                    <?php else: ?>
                        <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )] . '/schedules' ?>" class="alignright action">
                            <?php _e( 'All schedules  &raquo;', 'bpsp' );?>
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
                            <?php _e( 'Manage bibliography &raquo;', 'bpsp' );?>
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
                            <?php _e( 'Visit forums &raquo;', 'bpsp' );?>
                        </a>
                    </li>
                <?php endif;?>
            </ul>
        </div>
        <div class="dp25">
            <?php get_calendar(); ?>
        </div>
        <div class="clear"></div>
    </div>
    
    <?php if( count( $assignments ) > 0 ): ?>
    <h4 class="meta padded">
        <?php _e( 'Latest assignments', 'bpsp' ); ?>
        <a href="<?php echo $nav_options[__( 'Assignments', 'bpsp' )] ?>" class="alignright action">
            <?php _e( 'All assignments  &raquo;', 'bpsp' );?>
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
    <h4 class="meta padded">
        <?php _e( ' Latest schedules', 'bpsp' ); ?>
        <a href="<?php echo $nav_options[__( 'Calendar', 'bpsp' )] ?>" class="alignright action">
            <?php _e( 'All schedules &raquo;', 'bpsp' );?>
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
    
    <h4 class="meta padded"><?php _e( 'Latest responses', 'bpsp' ); ?></h4>
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
    <h4 class="meta padded"><?php _e( 'Your progress based on received grades', 'bpsp' ); ?></h4>
    <div class="grid courseware-content">
        <div id="user-grades" >
            <?php echo implode( ',', $grades ); ?>
        </div>
        <div class="clear"></div>
    </div>
    <?php endif; ?>
</div>