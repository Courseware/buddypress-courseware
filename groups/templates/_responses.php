<?php if( empty( $responses ) ) return; ?>
<div id="courseware-responses-list">
    <h4 class="meta"><?php _e( 'List of responses', 'bpsp' ); ?></h4>
    <ul>
        <?php foreach ( $responses as $r ): setup_postdata( $r ); ?>
            <li>
                <span class="response-title">
                    <a href="<?php echo $response_permalink . $r->post_name; ?>"><?php echo get_the_title( $r->ID ); ?></a>
                </span>
                &mdash; <?php _e( 'By ', 'bpsp' ); ?>
                <span class="response-author"><?php the_author_link(); ?>.</span>
                <?php _e( 'On ', 'bpsp' ); ?>
                <span class="response-date"><?php the_date(); ?>, <?php the_time(); ?></span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>