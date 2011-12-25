<?php
/**
 * Wrapper to overcome recent and any futher changes in WP editor API
 * 
 * @see wp_editor()
 * @param String $content, the content to be edited
 * @param String $target, target HTML element to be used on load
 * @param Mixed $settings, a bunch of settings to be used
 */
function courseware_editor( $content, $target, $settings = array() ) {
    $settings = array_merge( $settings, array(
            'dfw' => true
        )
    );
    wp_editor( $content, $target, $settings );
}
?>