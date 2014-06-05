<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Only load the component if BuddyPress is loaded and initialized.
 */
function bp_courseware_init() {
	// Because our loader file uses BP_Component, it requires BP 1.5 or greater.
	if ( version_compare( BP_VERSION, '1.5', '>=' ) ) {
		add_action( 'bp_loaded', 'bpsp_courseware_load_core_component' );
	}
}
add_action( 'bp_include', 'bp_courseware_init' );

/**
 * Loads component into the $bp global
 */
function bpsp_courseware_load_core_component() {
    global $bp;
	require_once BPSP_PLUGIN_DIR . '/component/component.class.php';
	$bp->courseware = new BPSP_Courseware_Component();
}

// if BuddyPress is not present, use Ian Dunn's loading procedure
if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {

	/**
	 * On plugins loaded, mimic the component
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

		// @todo maybe some classes like activity will only be loaded if bp is active b/c they're completely tied to it
	}
	add_action( 'init', 'bpsp_init', 6 );
	
	// register post types
	add_action( 'init', 'bpsp_registration' );

}
