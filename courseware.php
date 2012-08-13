<?php
/*
Plugin Name: BuddyPress Courseware
Plugin URI: http://buddypress.coursewa.re/
Description: A LMS for BuddyPress.
Author: Stas Sușcov
Version: 0.9.6
License: GNU/GPL 2
Requires at least: WordPress 3.2, BuddyPress 1.5
Tested up to: WordPress 3.3 / BuddyPress 1.6
Author URI: https://github.com/Courseware/buddypress-courseware/contributors
*/

define( 'BPSP_VERSION', '0.9.6' );
define( 'BPSP_DEBUG', (bool) WP_DEBUG ); // This will allow you to see post types in wp-admin
define( 'BPSP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'BPSP_WEB_URI', WP_PLUGIN_URL . '/' . basename( BPSP_PLUGIN_DIR ) );
define( 'BPSP_PLUGIN_FILE', basename( BPSP_PLUGIN_DIR ) . '/' . basename( __FILE__ ) );

/* Load the components */
require_once BPSP_PLUGIN_DIR . '/wordpress/wordpress.class.php';
require_once BPSP_PLUGIN_DIR . '/roles/roles.class.php';
require_once BPSP_PLUGIN_DIR . '/courses/courses.class.php';
require_once BPSP_PLUGIN_DIR . '/lectures/lectures.class.php';
require_once BPSP_PLUGIN_DIR . '/assignments/assignments.class.php';
require_once BPSP_PLUGIN_DIR . '/responses/responses.class.php';
require_once BPSP_PLUGIN_DIR . '/gradebook/gradebook.class.php';
require_once BPSP_PLUGIN_DIR . '/bibliography/bibliography.class.php';
require_once BPSP_PLUGIN_DIR . '/bibliography/webapis.class.php';
require_once BPSP_PLUGIN_DIR . '/schedules/schedules.class.php';
require_once BPSP_PLUGIN_DIR . '/groups/groups.class.php';
require_once BPSP_PLUGIN_DIR . '/dashboards/dashboards.class.php';
require_once BPSP_PLUGIN_DIR . '/static/static.class.php';
require_once BPSP_PLUGIN_DIR . '/activity/activity.class.php';
require_once BPSP_PLUGIN_DIR . '/notifications/notifications.class.php';

/**
 * i18n
 */
function bpsp_textdomain() {
    load_plugin_textdomain( 'bpsp', false, basename( BPSP_PLUGIN_DIR ) . '/languages' );
}
add_action( 'init', 'bpsp_textdomain' );

/**
 * Register post types and taxonomies
 */
function bpsp_registration() {
    BPSP_Courses::register_post_types();
    BPSP_Lectures::register_post_types();
    BPSP_Assignments::register_post_types();
    BPSP_Responses::register_post_types();
    BPSP_Gradebook::register_post_types();
    BPSP_Bibliography::register_post_types();
    BPSP_Schedules::register_post_types();
}
add_action( 'init', 'bpsp_registration' );

/**
 * On plugins load
 */
function bpsp_on_plugins_load() {
    BPSP_Groups::activate_component();
}
add_action( 'plugins_loaded', 'bpsp_on_plugins_load', 5 );

/* Initiate the componenets */
function bpsp_init() {
    new BPSP_WordPress();
    new BPSP_Roles();
    new BPSP_Groups();
    new BPSP_Courses();
    new BPSP_Lectures();
    new BPSP_Assignments();
    new BPSP_Responses();
    new BPSP_Gradebook();
    new BPSP_Bibliography();
    new BPSP_Schedules();
    new BPSP_Dashboards();
    new BPSP_Static();
    new BPSP_Activity();
    new BPSP_Notifications();
}
add_action( 'bp_init', 'bpsp_init', 6 );

/**
 * bpsp_check()
 * Will check for Courseware dependencies and active components
 *
 * @return True on errors
 * @uses `admin_notices`
 */
function bpsp_check() {
    $messages = array();

    if ( defined( 'BP_VERSION' ) ) {
        foreach( array( 'groups', 'activity', 'xprofile', 'forums', 'messages' ) as $c )
            if( !bp_is_active( $c ) )
                $messages[] = sprintf(
                    __( 'BuddyPress Courseware dependency error: <a href="%1$s">%2$s has to be activated</a>!', 'bpsp' ),
                    admin_url( 'admin.php?page=bp-general-settings' ),
                    $c
                );
    } else {
        $messages[] = sprintf(
            __( 'BuddyPress Courseware dependency error: Please <a href="%1$s">install BuddyPress</a>!', 'bpsp' ),
            admin_url( 'plugins.php' )
        );
    }

    if( !empty( $messages ) ) {
        echo '<div id="message" class="error fade">';
            foreach ( $messages as $m )
                echo "<p>{$m}</p>";
        echo '</div>';
        return false;
    }

    return true;
}

/* Activate the components */
function bpsp_activation() {
    if( !bpsp_check() )
        exit(1);
    BPSP_Roles::register_profile_fields();
}
register_activation_hook( BPSP_PLUGIN_FILE, 'bpsp_activation' );
?>
