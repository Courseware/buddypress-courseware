<?php
/**
 * Class to handle all WordPress admin backend stuff
 */
class BPSP_WordPress {
    /**
     * BPSP_WordPress()
     *
     * Constructor, loads all the required hooks
     */
    function BPSP_WordPress() {
        add_action('admin_menu', array(&$this, 'menus'));
        //Initialize our options
        add_option( 'bpsp_curriculum' );
        add_option( 'bpsp_worldcat_key' );
        add_option( 'bpsp_isbndb_key' );
    }
    
    /**
     * menus()
     *
     * Adds menus to admin area
     */
    function menus() {
        if ( is_super_admin() )
            add_submenu_page(
                'bp-general-settings',
                __( 'Courseware', 'bpsp' ),
                __( 'Courseware', 'bpsp' ),
                'manage_options',
                'bp-courseware',
                array(&$this, "screen")
            );
    }
    
    /** screen()
     *
     * Handles the wp-admin screen
     */
    function screen() {
        $nonce_name = 'courseware_options';
        $vars = array();
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $is_nonce = false;
        
        if( isset( $_POST['_wpnonce'] ) )
            check_admin_referer( $nonce_name );
        
        if( isset( $_POST['bpsp_curriculum'] ) )
            if( update_option( 'bpsp_curriculum', strtolower( $_POST['bpsp_curriculum'] ) ) )
                $vars['flash'][] = __( 'Courseware option was updated.' );
        if( isset( $_POST['worldcat_key'] ) && !empty( $_POST['worldcat_key'] ) )
            if( update_option( 'bpsp_worldcat_key', $_POST['worldcat_key'] ) )
                $vars['flash'][] = __( 'WorldCat option was updated.' );
        if( isset( $_POST['isbndb_key'] ) && !empty( $_POST['isbndb_key'] ) )
            if( update_option( 'bpsp_isbndb_key', $_POST['isbndb_key'] ) )
                $vars['flash'][] = __( 'ISBNdb option was updated.' );
        
        $current_option = get_option( 'bpsp_curriculum' );
        if( $current_option == 'us' )
            $vars['us'] = $current_option;
        elseif ( $current_option == 'eu' )
            $vars['eu'] = $current_option;
        
        $vars['worldcat_key'] = get_option( 'bpsp_worldcat_key' );
        $vars['isbndb_key'] = get_option( 'bpsp_isbndb_key' );
        
        //Load the template
        ob_start();
        extract( $vars );
        include( BPSP_PLUGIN_DIR . '/wordpress/templates/admin.php' );
        echo ob_get_clean();
    }
}
?>