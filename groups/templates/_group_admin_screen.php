<form action="<?php echo $form_action ?>" method="post" class="standard-form">
    <h4><?php _e( 'Courseware', 'bpsp' ); ?></h4>
    <div class="info">
        <p>
            <?php _e( 'This option will enable or disable Courseware for current group.', 'bpsp' ) ?>
            <?php _e( 'There\'s a default global option on Courseware Settings page.', 'bpsp' ); ?>
        </p>
    </div>
    <div class="radio">
        <label>
            <input type="radio" name="group_courseware_status" value="true" <?php checked( 'true', $current_status ); ?>/>
            <?php _e( 'Enabled', 'bpsp' ); ?>
        </label>
        <label>
            <input type="radio" name="group_courseware_status" value="false" <?php checked( 'false', $current_status ); ?>/>
            <?php _e( 'Disabled', 'bpsp' ); ?>
        </label>
    </div>
    <input type="submit" name="save" value="Save" />
    <?php echo $form_nonce; ?>
</form>