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
        add_option( 'bpsp_curriculum' ); //Initialize our option
    }
    
    /**
     * menus()
     *
     * Adds menus to admin area
     */
    function menus() {
        if ( is_super_admin() )
            //add_options_page( __( 'Courseware', 'bpsp' ), __( 'Courseware', 'bpsp' ), 3, "courseware", array(&$this, "screen"));
            add_submenu_page( 'bp-general-settings', __( 'Courseware', 'bpsp' ), __( 'Courseware', 'bpsp' ), 'manage_options', 'bp-courseware', array(&$this, "screen") );
    }
    
    /** screen()
     *
     * Handles the wp-admin screen
     */
    function screen() {
        $vars = array();
       
        if( isset( $_POST['bpsp_curriculum'] ) ) {
            if( update_option( 'bpsp_curriculum', strtolower( $_POST['bpsp_curriculum'] ) ) )
                $vars['flash'] = __( 'Courseware option was updated.' );
            else
                $vars['flash'] = __( 'Some error! Courseware option was not updated.' );
        }
        
        $current_option = get_option( 'bpsp_curriculum' );
        if( $current_option == 'us' )
            $vars['us'] = $current_option;
        elseif ( $current_option == 'eu' )
            $vars['eu'] = $current_option;
        
        //Load the template
        ob_start();
        extract( $vars );
        include( BPSP_PLUGIN_DIR . '/wordpress/templates/admin.php' );
        echo ob_get_clean();
    }
}
?>