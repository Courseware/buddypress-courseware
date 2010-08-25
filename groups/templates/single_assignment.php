<?php setup_postdata( $assignment ); ?>
<div id="courseware-assignment">
    <div id="assignment-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Assignment Meta', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="assignment-due-date">
                <?php _e( 'Due date', 'bpsp' ); ?>:
                <strong><?php bpsp_date( $assignment->due_date ); ?></strong>
            </li>
            <?php if( isset( $user_grade ) ): ?>
            <li id="assignment-grade">
                <?php _e( 'Your Grade:' ); ?>
                <strong>
                    <?php if( !empty( $user_grade['format'] ) && 'percentage' == $user_grade['format'] ): ?>
                    <?php echo $user_grade['value']; ?>%
                    <?php else: ?>
                    <?php echo $user_grade['value']; ?>
                    <?php endif; ?>
                </strong>
            </li>
            <?php endif; ?>
            <li class="date">
            <?php
                printf(
                    __( 'Date: %1$s', 'bpsp' ),
                    bpsp_get_date( $assignment->post_date )
                );
            ?>
            </li>
            <li class="author">
            <?php
                printf(
                    __( 'Author: %1$s', 'bpsp' ),
                    bp_core_get_userlink( $assignment->post_author )
                );
            ?>
            </li>
            <li class="parent-course">
            <?php
                printf(
                    __( 'Course: %1$s', 'bpsp' ),
                    '<a href="' . $course_permalink . '" >' . $assignment->course->post_title . '</a>'
                );
            ?>
            </li>
            <?php if( !empty( $assignment->forum_link ) ): ?>
                <li id="assignment-forum-link">
                    <a href="<?php echo $assignment->forum_link ?>" class="action">
                        <?php _e( 'Visit Assignment Forum', 'bpsp' ); ?>
                    </a>
                </li>
            <?php elseif( isset( $assignment_e_forum_permalink ) && bp_group_is_admin() ): ?>
                <li id="assignment-enable-forum">
                    <form method="post" action="<?php echo $assignment_e_forum_permalink; ?>" class="standard-form" >
                        <input type="submit" class="safe" value="<?php _e( 'Enable Assignment Forum', 'bpsp' ); ?>" />
                        <?php echo $assignment_e_forum_nonce; ?>
                    </form>
                </li>
            <?php elseif( bp_group_is_admin() ): ?>
                <li id="assignment-forum-inactive">
                    <a href="#" class="action alert"><?php _e( 'You need forums enabled', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
            <?php if( count( $responses ) > 0 ): ?>
                <li id="responses">
                    <a href="#courseware-responses-list" class="action">
                        <?php _e( 'Responses', 'bpsp' ); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if( empty( $response ) && isset( $response_add_uri ) ): ?>
                <li id="assignment-response">
                    <a href="<?php echo $response_add_uri; ?>" class="action">
                        <?php _e( 'Add a Response', 'bpsp' ); ?>
                    </a>
                </li>
            <?php elseif( !empty( $response ) ): ?>
                <li id="assignment-solution-meta">
                    <a href="<?php echo $response_permalink . $response->post_name; ?>" class="action">
                        <?php _e( 'Your response', 'bpsp' ); ?>
                    </a>
                </li>
            <?php endif; ?>
            <?php if( isset( $has_gradebook_caps ) && $has_gradebook_caps ): ?>
                <li class="gradebook-link">
                    <a href="<?php echo $assignment_permalink . '/gradebook'; ?>" class="action"><?php _e( 'Gradebook', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
                <li class="show-bibs">
                    <a href="#courseware-bibs-list" class="action"><?php _e( 'Bibliography', 'bpsp' ); ?></a>
                </li>
            <?php if( $show_edit ): ?>
                <li class="add bib">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Bibliography', 'bpsp' ); ?></a>
                </li>
                <li class="add book">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Book', 'bpsp' ); ?></a>
                </li>
                <li class="add www">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Webpage', 'bpsp' ); ?></a>
                </li>
                <li class="edit-link">
                    <a href="<?php echo $assignment_edit_uri; ?>" class="action"><?php _e( 'Edit Assignment', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="assignment-content" class="courseware-content-wrapper">
        <h4 id="assignment-title" class="courseware-title">
            <?php echo get_the_title( $assignment->ID ); ?>
        </h4>
        <div id="assignment-body" class="courseware-content">
            <?php the_content(); ?>
        </div>
    </div>
</div>
<?php
    // Load responses
    bpsp_partial( $templates_path, '_responses', get_defined_vars() );
    // Load bibs
    bpsp_partial( $templates_path, '_bibs', get_defined_vars() );
?>