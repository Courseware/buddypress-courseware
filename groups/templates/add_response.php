<?php
bpsp_load_editor_files();
wp_tiny_mce();
?>
<div id="assignment-response-form">
    <form action="" method="post" class="standard-form" id="new-response-form">
        <div id="new-response-content">
            <div id="new-response-content-title">
                <input type="text" id="response-title" name="response[title]" class="long" value="<?php _e( 'My response for: ', 'bpsp' ); echo get_the_title( $parent_assignment->ID ); ?>" />
            </div>
            <div id="new-response-content-textarea">
                <div id="editor-toolbar">
                    <div id="media-toolbar">
                        <?php echo bpsp_media_buttons(); ?>
                    </div>
                    <?php the_editor( '', 'response[content]', 'response[title]', false ); ?>
                </div>
            </div>
            <div id="new-response-content-options">
                <input type="hidden" name="response[parent_id]" value="<?php echo $parent_assignment->ID ?>" />
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="new-response-content-submit">
                    <input type="submit" name="response[submit]" id="new-response-submit" value="<?php _e( 'Add my response', 'bpsp' ); ?>">
                    <div class="alignright submits">
                        <a href="<?php echo $parent_assignment->permalink ?>" class="action safe"><?php _e( 'Cancel/Go Back', 'bpsp' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>
