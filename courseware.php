<?php
/*
Plugin Name: BuddyPress ScholarPress Courseware
Plugin URI: http://scholarpress.net/
Description: A LMS for BuddyPress.
Author: Stas Sușcov
Version: 0.1alfa
License: GNU/GPL 2
Requires at least: WordPress 3.0, BuddyPress 1.2.4.1
Tested up to: WordPress 2.9.2 / BuddyPress 1.2.4.1
Author URI: http://stas.nerd.ro/
*/

define( 'BPSP_VERSION', '0.1' );
define( 'BPSP_PLUGIN_DIR', dirname( __FILE__ ) );
define( 'BPSP_WEB_URI', WP_PLUGIN_URL . '/courseware' ); //hardcoded cause of symlinks
//define( 'BPSP_WEB_URI', WP_PLUGIN_URL . '/' . basename(BPSP_PLUGIN_DIR) ); //correct path

/* Load the components */
require_once BPSP_PLUGIN_DIR . '/wordpress/bpsp-wordpress.class.php';
require_once BPSP_PLUGIN_DIR . '/roles/bpsp-roles.class.php';
require_once BPSP_PLUGIN_DIR . '/courses/bpsp-courses.class.php';
require_once BPSP_PLUGIN_DIR . '/courses/bpsp-courses.us.class.php';
require_once BPSP_PLUGIN_DIR . '/assignments/bpsp-assignments.class.php';
//require_once BPSP_PLUGIN_DIR . '/schedules/bpsp-schedules.class.php';
require_once BPSP_PLUGIN_DIR . '/groups/bpsp-groups.class.php';
require_once BPSP_PLUGIN_DIR . '/static/bpsp-static.class.php';

/**
 * i18n
 */
function bpsp_textdomain() {
    load_plugin_textdomain( 'bpsp', false, BPSP_DIR . '/i18n' );
}
add_action( 'init', 'bpsp_textdomain' );

/**
 * Register post types and taxonomies
 */
function bpsp_registration() {
    BPSP_Courses::register_post_types();
    BPSP_Assignments::register_post_types();
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
    //new BPSP_Schedules();
    new BPSP_Groups();
    new BPSP_Static();
}
add_action( 'bp_init', 'bpsp_init' );

/* Activate the components */
function bpsp_activation() {
    BPSP_Roles::register_profile_fields();
}
register_activation_hook( 'courseware/courseware.php', 'bpsp_activation' );

/** TEMPORARY HELPERS **/
function _d($stuff) {
    wp_die( '<pre>' . var_dump( $stuff ) . '</pre>');
}
?>