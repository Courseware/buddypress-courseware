<?php

/**
 * Creates a mock version of BuddyPress to simulate BuddyPress objects/functions when BuddyPress is not activated
 */

function mockbp_init() {
	global $bp;

	if ( ! is_plugin_active( 'buddypress/bp-loader.php' ) ) {
		$bp->groups->current_group = $bp->groups = $bp = new stdClass();

		// Setup global objects/variables that BPSP expects to exist, and assign mock values
		$bp->current_component = 'mock';
		$bp->groups->id = 0;
		$bp->action_variables = array();

		$current_user = (object) (array) wp_get_current_user();		// casting to stdClass to avoid __deprecated_argument() when accessing ->id
		$current_user->id = $current_user->ID;
		$bp->loggedin_user =  $current_user;


		// Create stubs for BuddyPress functions
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

		if ( ! function_exists( 'bp_get_group_id' ) ) {
			function bp_get_group_id() {
				return 1;	// @todo group id of current course?
			}
		}

		if ( ! function_exists( 'groups_get_group_admins' ) ) {
			function groups_get_group_admins( $group_id ) {
				$admin = new stdClass();
				$admin->user_id = 1;
				return array( $admin );	// @todo user id of course teacher? no, probably just all users. maybe call group_members to keep it DRY
			}
		}

		if ( ! function_exists( 'groups_get_group_members' ) ) {
			function groups_get_group_members( $group_id ) {
				$member = new stdClass();
				$member->user_id = 2;

				$members = array();
				$members['members'][] = $member;

				return $members;	// @todo user ids of course students? no, probably just all users.
			}
		}

		if ( ! function_exists( 'groups_get_groupmeta' ) ) {
			function groups_get_groupmeta( $group_id, $meta_key ) {
				return 'true';	// @todo maybe need to be smarter
			}
		}

		if ( ! function_exists( 'bp_group_is_forum_enabled' ) ) {
			function bp_group_is_forum_enabled( $group = false ) {
				return true;	// @todo smarter?
			}
		}

		if ( ! function_exists( 'xprofile_get_field_data' ) ) {
			function xprofile_get_field_data( $field, $user_id = 0, $multi_format = 'array' ) {
				if ( 1 == $user_id ) {
					return __( 'Teacher', 'bpsp' );
				} else {
					return __( 'Student', 'bpsp' );
				}

				// @todo needs to be smarter
			}
		}

		if ( ! function_exists( 'bp_group_forum_permalink' ) ) {
			function bp_group_forum_permalink( $group = false ) {
				return '';	// @todo
			}
		}

		// Create stubs for BuddyPress classes
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
