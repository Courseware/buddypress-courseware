<?php

/**
 * Creates a mock version of BuddyPress to simulate BuddyPress objects/functions when BuddyPress is not activated
 */

function mockbp_init() {
	global $bp;
	require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

	if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		$bp->groups->current_group = $bp->groups = $bp = new stdClass();

		// Setup global objects/variables that BPSP expects to exist, and assign mock values
		$bp->current_component = 'mock';
		$bp->groups->id = 0;
		$bp->action_variables = array();

		$current_user = (object) (array) wp_get_current_user();		// casting to stdClass to avoid __deprecated_argument() when accessing ->id
		$current_user->id = $current_user->ID;
		$bp->loggedin_user =  $current_user;

		// create stubs for bp functions using if function_exists
			// have to do insite mockbp init function, b/c bpsp loads before bp does
		if ( ! function_exists( 'bp_group_is_admin' ) ) {
			function bp_group_is_admin() {
				return true;	// @todo may need to be smarter than this, but see
			}
		}

		if ( ! function_exists( 'bp_core_admin_hook' ) ) {
			function bp_core_admin_hook() {
				return 'admin_menu';	// @todo assuming only single site activation for now
			}
		}

		if ( ! function_exists( 'bp_core_get_userlink' ) ) {
			function bp_core_get_userlink() {
				return '';	// @todo maybe look website field from wp user profile
			}
		}

		if ( ! function_exists( 'bp_get_group_permalink' ) ) {
			function bp_get_group_permalink() {
				return '';
			}
		}

		if ( ! function_exists( 'xprofile_insert_field_group' ) ) {
			function xprofile_insert_field_group() {
				return 1;
			}
		}

		if ( ! class_exists( 'BP_XProfile_Group' ) ) {
			class BP_XProfile_Group {
				static function get() {
					return array();
				}
			}
		}
	}
}
add_action( 'plugins_loaded', 'mockbp_init' );