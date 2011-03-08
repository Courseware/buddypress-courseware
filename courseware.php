<?php
/*
Plugin Name: BuddyPress ScholarPress Courseware
Plugin URI: http://scholarpress.github.com/buddypress-courseware/
Description: A LMS for BuddyPress.
Author: ScholarPress Dev Crew
Version: 0.1.6
License: GNU/GPL 2
Requires at least: WordPress 3.0, BuddyPress 1.2.5
Tested up to: WordPress 3.1 / BuddyPress 1.2.8
Author URI: http://github.com/scholarpress/
*/

define( 'BPSP_VERSION', '0.1.6' );
define( 'BPSP_DEBUG', false ); // This will allow you to see post types in wp-admin
define( 'BPSP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'BPSP_WEB_URI', WP_PLUGIN_URL . '/' . basename( BPSP_PLUGIN_DIR ) );

/* Load the components */
require_once BPSP_PLUGIN_DIR . '/wordpress/bpsp-wordpress.class.php';
require_once BPSP_PLUGIN_DIR . '/roles/bpsp-roles.class.php';
require_once BPSP_PLUGIN_DIR . '/courses/bpsp-courses.class.php';
require_once BPSP_PLUGIN_DIR . '/courses/bpsp-courses.us.class.php';
require_once BPSP_PLUGIN_DIR . '/assignments/bpsp-assignments.class.php';
require_once BPSP_PLUGIN_DIR . '/responses/bpsp-responses.class.php';
require_once BPSP_PLUGIN_DIR . '/gradebook/bpsp-gradebook.class.php';
require_once BPSP_PLUGIN_DIR . '/bibliographies/bpsp-bibliography.class.php';
require_once BPSP_PLUGIN_DIR . '/bibliographies/bpsp-bibliography-webapis.class.php';
require_once BPSP_PLUGIN_DIR . '/schedules/bpsp-schedules.class.php';
require_once BPSP_PLUGIN_DIR . '/groups/bpsp-groups.class.php';
require_once BPSP_PLUGIN_DIR . '/dashboards/bpsp-dashboards.class.php';
require_once BPSP_PLUGIN_DIR . '/static/bpsp-static.class.php';
require_once BPSP_PLUGIN_DIR . '/activity/bpsp-activity.class.php';
require_once BPSP_PLUGIN_DIR . '/notifications/bpsp-notifications.class.php';

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
    // Load Courseware behaviour
    if( get_option( 'bpsp_curriculum' ) != 'eu' )
        new BPSP_USCourses();
    else
        new BPSP_Courses();

    new BPSP_Assignments();
    new BPSP_Responses();
    new BPSP_Gradebook();
    new BPSP_Bibliography();
    new BPSP_Schedules();
    new BPSP_Dashboards();
    new BPSP_Groups();
    new BPSP_Static();
    new BPSP_Activity();
    new BPSP_Notifications();
}
add_action( 'bp_init', 'bpsp_init' );

/* Activate the components */
function bpsp_activation() {
    BPSP_Roles::register_profile_fields();
}
register_activation_hook( 'buddypress-courseware/courseware.php', 'bpsp_activation' );

?>
