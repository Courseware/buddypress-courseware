<?php
/**
 * BPSP Class for dashboard/screens management on groups
 *
 * This class will set up the group navigation
 * Also it introduces two new actions:
 *  - scholarpress_group_screen_handler
 *  - scholarpress_group_nav_options
 * Both are pluggable and can be used to add new content or handle screens
 * for actions like scholarpress/action/action_var
 */
class BPSP_Groups {
    /**
     * BPSP_Groups()
     *
     * Constructor. Loads filters and actions.
     */
    function BPSP_Groups() {
        add_action( 'wp', array( &$this, 'set_nav' ), 2 );
    }
    
    /**
     * activate_component()
     *
     * Activates ScholarPress as a BuddyPress component for groups
     */
    function activate_component() {
        global $bp;
        $bp->scholarpress->id = 'scholarpress';
        $bp->scholarpress->slug = 'scholarpress';
        $bp->active_components[$bp->scholarpress->slug] = $bp->scholarpress->id;
    }
    
    /**
     * set_nav()
     *
     * Sets up the component navigation
     */
    function set_nav() {
        global $bp;
        
	if ( $group_id = BP_Groups_Group::group_exists($bp->current_action) ) {
		$bp->is_single_item = true;
		$bp->groups->current_group = &new BP_Groups_Group( $group_id );
	}
        
        $groups_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';        
        
        if ( $bp->is_single_item )
            bp_core_new_subnav_item( array( 
		'name' => __( 'ScholarPress', 'bpsp' ),
		'slug' => $bp->scholarpress->slug,
		'parent_url' => $groups_link, 
		'parent_slug' => $bp->groups->slug, 
		'screen_function' => array( &$this, 'screen_handler' ),
		'position' => 35, 
		'user_has_access' => $bp->groups->current_group->user_has_access,
		'item_css_id' => 'scholarpress-group'
            ) );
        
        do_action('scholarpress_group_set_nav');
    }
    
    /**
     * screen_handler()
     *
     * ScholarPress action for handling the screens on group pages
     */
    function screen_handler() {
        global $bp;
        if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->scholarpress->slug ) {
            add_action( 'bp_before_group_body', array( &$this, 'nav_options' ) );
            do_action( 'scholarpress_group_screen_handler', $bp->action_variables );
        }
        bp_core_load_template( apply_filters( 'bp_core_template_plugin' , 'groups/single/plugins' ) );
    }
    
    /**
     * nav_options()
     *
     * ScholarPress action to manage group navigation options
     */
    function nav_options() {
        do_action( 'scholarpress_group_nav_options' );
    }
    
}
?>