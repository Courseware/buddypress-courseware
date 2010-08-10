<?php if( !empty( $error ) ): ?>
<div id="message" class="error">
    <p><?php echo $error; ?></p>
</div>
<?php elseif( !empty( $message ) ): ?>
<div id="message" class="updated">
    <p>
        <?php echo $message; ?>
        <?php if( isset( $redirect_to ) ): ?>
            <a href="<?php echo $redirect_to; ?>"><?php _e( 'Go back', 'bpsp' ); ?>.</a>
        <?php endif; ?>
    </p>
</div>
<?php endif; ?>