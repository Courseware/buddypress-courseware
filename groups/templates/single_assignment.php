<?php setup_postdata( $assignment ); ?>
<div id="courseware-assignment">
    <div class="assignment-content">
        <h4 id="assignment-title"><?php echo $assignment->post_title; ?></h4>
        <h5 id="assignment-due-date">
            <?php echo $due_date_label ?>: <?php echo mysql2date( get_option('date_format'), $assignment->due_date ); ?>
        </h5>
        <div id="assignment-body">
            <?php the_content(); ?>
        </div>
    </div>
    <div class="assignment-meta">
        <span class="meta">
        <?php
            printf(
                $assignment_meta_title,
                mysql2date( get_option('date_format'), $assignment->post_date ),
                bp_core_get_userlink( $assignment->post_author ),
                '<a href="' . $course_permalink . '" >' . $assignment->course->post_title . '</a>'
            );
        ?>
        </span>
        <?php if( $show_edit ): ?>
            <span class="edit-link">
                <a href="<?php echo $assignment_edit_uri; ?>"><?php echo $show_edit ?></a>
            </span>
        <?php endif; ?>
    </div>
</div>