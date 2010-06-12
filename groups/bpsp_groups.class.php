<?php
/**
 * BPSP Class for dashboard/screens management on groups
 *
 * This class will set up the group navigation
 * Also it introduces a new action and filter:
 *  - courseware_group_screen_handler
 *  - courseware_group_nav_options
 * Both are pluggable and can be used to add new content or handle screens
 * for actions like groups/ID/courseware/action/action_var
 */
class BPSP_Groups {
    /**
     * $nav_options
     *
     * Holds the navigation options for component
     */
    var $nav_options = array();
    
    /**
     * $current_nav_option
     *
     * Holds the current navigation option for component
     */
    var $current_nav_option = null;
    
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
     * Activates Courseware as a BuddyPress component for groups
     */
    function activate_component() {
        global $bp;
        $bp->courseware->id = 'courseware';
        $bp->courseware->slug = 'courseware';
        $bp->active_components[$bp->courseware->slug] = $bp->courseware->id;
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
        
        if ( $bp->is_single_item ) {
            bp_core_new_subnav_item( array( 
		'name' => __( 'Courseware', 'bpsp' ),
		'slug' => $bp->courseware->slug,
		'parent_url' => $groups_link, 
		'parent_slug' => $bp->groups->slug, 
		'screen_function' => array( &$this, 'screen_handler' ),
		'position' => 35, 
		'user_has_access' => $bp->groups->current_group->user_has_access,
		'item_css_id' => 'courseware-group'
            ) );
	    $this->nav_options[__( 'Home' )] = $groups_link . $bp->courseware->slug;
	}
        do_action( 'courseware_group_set_nav' );
    }
    
    /**
     * screen_handler()
     *
     * Courseware action for handling the screens on group pages
     */
    function screen_handler() {
        global $bp;
	
        if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->courseware->slug ) {
	    $this->current_nav_option =  $this->nav_options[__( 'Home' )];
	    
	    if( $bp->action_variables[0] )
		$this->current_nav_option .= '/' . $bp->action_variables[0];
	    
            add_action( 'bp_before_group_body', array( &$this, 'nav_options' ) );
            do_action( 'courseware_group_screen_handler', $bp->action_variables );
	    add_action( 'bp_template_content', array( &$this, 'load_template' ) );
        }
        bp_core_load_template( apply_filters( 'bp_core_template_plugin' , 'groups/single/plugins' ) );
    }
    
    /**
     * nav_options()
     *
     * Courseware action to manage group navigation options
     */
    function nav_options() {
        apply_filters( 'courseware_group_nav_options', &$this->nav_options );
	$this->load_template( array(
	    'name' => 'nav',
	    'nav_options' => $this->nav_options,
	    'current_option' => $this->current_nav_option
	));
    }
    
    /**
     * load_template( $vars )
     *
     * Loads a template for displaying group screens
     *
     * @param Array $vars of options
     */
    function load_template( $vars = '' ) {	
	if( empty( $vars ) )
	    $vars = array(
	    'name' => '',
	    'nav_options' => $this->nav_options,
	    'current_option' => $this->current_nav_option
	    );
	    
	$templates_path = BPSP_PLUGIN_DIR . '/groups/templates/';
	
	//Exclude internal templates like navigation
	if( !$vars['name'] == 'nav' )
	    apply_filters( 'courseware_group_template', &$vars );
	
	if( file_exists( $templates_path . $vars['name']. '.php' ) ) {
	    ob_start();
	    extract( $vars );
	    include_once( $templates_path . $name . '.php' );
	    echo ob_get_clean();
	}
    }
}
?>