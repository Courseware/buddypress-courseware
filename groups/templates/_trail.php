<!-- groups/templates/_trail.php -->
<div id="courseware-trail" class="<?php echo $name ?>" >
    <ul>
        <li>
            <a href="<?php echo $nav_options[__( 'Home', 'bpsp' )]; ?>"><?php _e( 'Courseware Dashboard', 'bpsp' ) ?></a>
        </li>
        <?php if( !empty( $trail ) ): $last_item = end( $trail ); ?>
            <?php foreach( $trail as $title => $link ): ?>
                <li>
                    <?php if( $last_item == $link ): ?>
                        <?php echo $title; ?>
                    <?php else: ?>
                        <a href="<?php echo $link; ?>"><?php echo $title; ?></a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>
</div>