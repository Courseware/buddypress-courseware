<?php setup_postdata( $assignment ); ?>
<div id="courseware-assignment">
    <div class="assignment-content">
        <div id="assignment-forum-meta" style="float: right">
            <?php if( !empty( $assignment->forum_link ) ): ?>
                <div id="assignment-forum-link">
                    <a href="<?php echo $assignment->forum_link ?>">
                        <?php _e( 'Visit Assignment Forum', 'bpsp' ); ?>
                    </a>
                </div>
            <?php elseif( isset( $assignment_e_forum_permalink ) ): ?>
                <div id="assignment-enable-forum">
                    <form method="post" action="<?php echo $assignment_e_forum_permalink; ?>" class="standard-form" >
                        <input type="submit" value="<?php _e( 'Enable Assignment Forum', 'bpsp' ); ?>" />
                        <?php echo $assignment_e_forum_nonce; ?>
                    </form>
                </div>
            <?php else: ?>
                <div id="assignment-forum-inactive">
                    <form method="post" class="standard-form" >
                        <input type="submit" value="<?php _e( 'Enable forums to allow forum integration.', 'bpsp' ); ?>" disabled="true" />
                    </form>
                </div>
            <?php endif; ?>
        </div>
        <h4 id="assignment-title"><?php echo $assignment->post_title; ?></h4>
        <div id="assignment-due-date">
            <?php _e( 'Due date' ); ?>: <?php echo mysql2date( get_option('date_format'), $assignment->due_date ); ?>
        </div>
        <?php if( isset( $user_grade ) ): ?>
            <div id="assignment-grade">
                <em><?php _e( 'Your grade for this assignment was:' ); ?></em>
                <strong>
                    <?php if( !empty( $user_grade['format'] ) && 'percentage' == $user_grade['format'] ): ?>
                    <?php echo $user_grade['value']; ?>%
                    <?php else: ?>
                    <?php echo $user_grade['value']; ?>
                    <?php endif; ?>
                </strong>
            </div>
        <?php endif; ?>
        <div id="assignment-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="assignment-meta">
        <span class="meta">
        <?php
            printf(
                __( 'added on %1$s by %2$s for %3$s.', 'bpsp' ),
                mysql2date( get_option('date_format'), $assignment->post_date ),
                bp_core_get_userlink( $assignment->post_author ),
                '<a href="' . $course_permalink . '" >' . $assignment->course->post_title . '</a>'
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $assignment_edit_uri; ?>"><?php _e( 'Edit Assignment', 'bpsp' ); ?></a>
            </span>
        <?php endif; ?>
        <span class="gradebook-link">
            <?php if( isset( $has_gradebook_caps ) && $has_gradebook_caps ): ?>
                <a href="<?php echo $assignment_permalink . '/gradebook'; ?>"><?php _e( 'Gradebook', 'bpsp' ); ?></a>
            <?php endif; ?>
        </span>
    </div>
</div>
<?php
    require_once BPSP_PLUGIN_DIR . '/groups/templates/_bibs.php';
?>