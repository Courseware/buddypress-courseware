<?php if( !empty( $error ) ): ?>
<div id="message" class="error">
    <p><?php echo $error; ?></p>
</div>
<?php elseif( !empty( $message ) ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>