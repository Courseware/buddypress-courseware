<div class="item-list-tabs no-ajax" id="subnav">
<?php if( !empty( $nav_options ) ):?>
    <ul>
        <?php foreach( $nav_options as $title => $link ): ?>
            <?php $current = ( $link == $current_option )? 'current' : ''; ?>
            <li class="<?php echo $current; ?>">
                <a href="<?php echo $link; ?>"><?php echo $title; ?></a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>