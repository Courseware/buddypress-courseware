<?php
bpsp_load_editor_files();
wp_tiny_mce();
setup_postdata( $parent_assignment );
?>
<div id="assignment-response-form">
    <form action="" method="post" class="standard-form" id="new-response-form">
        <h5><?php _e( 'Add your response to: ', 'bpsp' ); the_title(); ?></h5>
        <div id="new-response-content">
            <div id="new-response-content-title">
                <label for="response[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                    <input type="text" id="response-title" name="response[title]"/>
            </div>
            <div id="new-response-content-textarea">
                <div id="editor-toolbar">
                    <?php
                        echo bpsp_media_buttons();
                        the_editor( '', 'response[content]', 'response[title]', false );
                    ?>
                </div>
            </div>
            <div id="new-response-content-options">
                <input type="hidden" name="response[parent_id]" value="<?php echo $parent_assignment->ID ?>" />
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="new-response-content-submit">
                    <input type="submit" name="response[submit]" id="new-response-submit" value="<?php _e( 'Add my response', 'bpsp' ); ?>">
                </div>
            </div>
        </div>
    </form>
</div>