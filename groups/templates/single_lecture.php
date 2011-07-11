<?php setup_postdata( $lecture ); ?>
<div id="courseware-lecture">
    <div id="course-meta" class="courseware-sidebar">
        <h4 class="meta lectures"><span class="icon"></span><?php _e( 'Lecture Meta', 'bpsp' ) ?></h4>
        <ul class="courseware-meta">
            <li class="date">
            <?php
                printf(
                    __( 'Date: %1$s', 'bpsp' ),
                    bpsp_get_date( $lecture->post_date )
                );
            ?>
            </li>
            <li class="author">
            <?php
                printf(
                    __( 'Author: %1$s', 'bpsp' ),
                    bp_core_get_userlink( $lecture->post_author )
                );
            ?>
            </li>
            <?php if( $next ): ?>
            <li class="next">
                <a href="<?php echo $next->permalink; ?>" class="action"><?php echo get_the_title( $next->ID ); ?> &rarr;</a>
            </li>
            <?php endif; ?>
            <?php if( $prev ): ?>
            <li class="next">
                <a href="<?php echo $prev->permalink; ?>" class="action">&larr; <?php echo get_the_title( $prev->ID ); ?></a>
            </li>
            <?php endif; ?>
            <?php if( $lecture_bookmark_uri && $bookmarked != $lecture->ID ): ?>
                <li class="bookmark-link">
                    <a href="<?php echo $lecture_bookmark_uri; ?>" class="action"><?php _e( 'Bookmark this lecture', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
            <?php if( $show_edit ): ?>
                <li class="edit-link">
                    <a href="<?php echo $lecture_edit_uri; ?>" class="action"><?php _e( 'Edit Lecture', 'bpsp' ); ?></a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
    <div id="lecture-content" class="courseware-content-wrapper">
        <h4 id="lecture-title" class="courseware-title">
            <?php echo get_the_title( $lecture->ID ); ?>
        </h4>
        <div id="lecture-body" class="courseware-content">
            <?php the_content(); ?>
        </div>
    </div>
</div>