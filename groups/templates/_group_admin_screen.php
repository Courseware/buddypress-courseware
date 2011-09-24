<h4><?php _e( 'Courseware Status', 'bpsp' ); ?></h4>
<div class="info">
    <p>
        <?php _e( 'This option will enable or disable Courseware for current group.', 'bpsp' ) ?>
        <?php _e( 'There\'s also a default global option on Courseware Settings page.', 'bpsp' ); ?>
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

<h4><?php _e( 'Private Responses', 'bpsp' ); ?></h4>
<div class="info">
    <p>
        <?php _e( 'This option will enable or disable private responses for current group.', 'bpsp' ) ?>
        <?php _e( 'There\'s also a default global option on Courseware Settings page.', 'bpsp' ); ?>
    </p>
</div>
<div class="radio">
    <label>
        <input type="radio" name="responses_courseware_status" value="true" <?php checked( 'true', $current_responses_status ); ?>/>
        <?php _e( 'Enabled', 'bpsp' ); ?>
    </label>
    <label>
        <input type="radio" name="responses_courseware_status" value="false" <?php checked( 'false', $current_responses_status ); ?>/>
        <?php _e( 'Disabled', 'bpsp' ); ?>
    </label>
</div>
<input type="submit" name="save" value="Save" />
<?php echo $form_nonce; ?>