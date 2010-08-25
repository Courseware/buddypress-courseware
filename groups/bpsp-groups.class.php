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
	add_filter( 'groups_get_groups', array( &$this, 'extend_search' ), 10, 2 );
	add_action( 'groups_admin_tabs', array( &$this, 'group_admin_tab' ), 10, 2 );
	add_action( 'wp', array( &$this, 'group_admin_screen' ), 4 );
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
     * courseware_status( $group_id = null )
     *
     * Checks if courseware is enabled for current group
     * @param Int $group_id, the id of the group, default is null and global $bp will be used
     * @return Bool, true on success, and false on failure.
     */
    function courseware_status( $group_id = null ) {
	if( !$group_id ) {
	    global $bp;
	    $group_id = $bp->groups->current_group->id;
	}
	
	$global_status = get_option( 'bpsp_global_status' );
	$group_status = groups_get_groupmeta( $group_id, 'courseware' );
	
	if( 'true' == $group_status )
	    return true;
	elseif ( 'false' == $group_status )
	    return false;
	elseif( !empty( $global_status ) )
	    return true;
	else
	    return false;
    }
    
    /**
     * set_nav()
     *
     * Sets up the component navigation
     */
    function set_nav() {
        global $bp;
        
	if( !$this->courseware_status( $bp->groups->current_group->id ) )
	    return;
	
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
	    $this->nav_options[__( 'Home', 'bpsp' )] = $groups_link . $bp->courseware->slug;
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
	
        if( !$this->courseware_status( $bp->groups->current_group->id ) )
	    return;
	
	if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->courseware->slug ) {
	    $this->current_nav_option =  $this->nav_options[__( 'Home', 'bpsp' )];
	    
	    if( $bp->action_variables[0] )
		$this->current_nav_option .= '/' . $bp->action_variables[0];
	    
            add_action( 'bp_before_group_body', array( &$this, 'nav_options' ) );
            do_action( 'courseware_group_screen_handler', $bp->action_variables );
	    add_action( 'bp_template_content', array( &$this, 'load_template' ) );
        }
	groups_update_last_activity( $bp->groups->current_group->id );
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
	    'name' => '_nav',
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
     * @return template $content if $vars['echo'] == false
     */
    function load_template( $vars = '' ) {
	$content = '';
	if( empty( $vars ) || !isset( $vars['name'] ) )
	    $vars = array(
		'name' => 'home',
		'nav_options' => $this->nav_options,
		'current_uri' => $this->nav_options[__( 'Home', 'bpsp' )],
		'current_option' => $this->current_nav_option,
		'echo' => true,
	    );
	
	if( !isset( $vars['echo'] ) )
	    $vars['echo'] = true;
	
	$templates_path = BPSP_PLUGIN_DIR . '/groups/templates/';
	$vars['templates_path'] = $templates_path;
	
	// Load helpers
	foreach ( glob( $templates_path . "helpers/*.php" ) as $helper )
	    include_once $helper;
	
	//Exclude internal templates like navigation, starts with an underscore
	if(  substr( $vars['name'], 0, 1) != '_' )
	    apply_filters( 'courseware_group_template', &$vars );
	
	if( file_exists( $templates_path . $vars['name']. '.php' ) ) {
	    ob_start();
	    extract( $vars );
	    if( !empty( $die ) ) {
		$error = $die;
		include( $templates_path . '_message.php' ); // Template for errors
	    }
	    else {
		include( $templates_path . '_message.php' ); // Template for messages    
		include( $templates_path . $name . '.php' );
	    }
	    $content = ob_get_clean();
	}
	
	if( $vars['echo'] )
	    echo $content;
	else
	    return $content;
    }
    
    /**
     * extend_search( $groups, $params )
     *
     * Hooks into groups_get_groups filter and extends search to include Courseware used post types
     */
    function extend_search( $groups, $params ) {
	// A hack to make WordPress believe the taxonomy is registered
	if( !taxonomy_exists( 'group_id' ) ) {
	    global $wp_taxonomies;
	    $wp_taxonomies['group_id'] = '';
	}
	$all_groups = BP_Groups_Group::get_alphabetically();
	foreach( $all_groups['groups'] as $group ) {
	    // Search posts from current $group
	    $results = BPSP_WordPress::get_posts(
		array(
		    'group_id' => $group->id
		),
		array(
		    'assignment',
		    'course',
		    'schedule',
		),
		$params['search_terms']
	    );
	    
	    // Merge posts to $groups if new found
	    if( !empty( $results ) ) {
		if( !in_array( $group, $groups['groups'] ) ) {
		    $groups['groups'][] = $group;
		    $groups['total']++;
		}
	    }
	}
	return $groups;
    }
    
    /**
     * group_admin_tab( $current_tab, $group_slug )
     *
     * Hooks into groups_admin_tabs, and adds the courseware options tab
     */
    function group_admin_tab( $current_tab, $group_slug ) {
	global $bp;
	
	$tab_content = '<li ';
	if ( 'courseware' == $current_tab )
	    $tab_content .= 'class="current"';
	
	$tab_content .= '><a href="' . $bp->root_domain . '/' . $bp->groups->slug;
	$tab_content .= '/' . $group_slug . '/admin/courseware">';
	$tab_content .= __( 'Courseware', 'buddypress' ) . '</a></li>';
	
	echo $tab_content;
    }
    
    /**
     * group_admin_screen()
     *
     * Hooks into wp, adds a new screen to group Admin screens
     */
    function group_admin_screen() {
	global $bp;
	
	if ( $bp->current_component == $bp->groups->slug && 'courseware' == $bp->action_variables[0] ) {
	    if ( $bp->is_item_admin || $bp->is_item_mod  ) {
		add_action( 'bp_before_group_admin_content', array( &$this, 'group_admin_content' ) );
		bp_core_load_template( apply_filters( 'groups_template_group_admin', 'groups/single/home' ) );
	    }
	}
    }
    
    /**
     * group_admin_content()
     *
     * Hooks into bp_before_group_admin_content(), adds Courseware group options
     */
    function group_admin_content() {
	global $bp;
	$nonce_name = 'courseware_group_option';
	
	if ( isset( $_POST['save'] ) && wp_verify_nonce( $_POST['_wpnonce'], $nonce_name ) ) {
	    if( isset( $_POST['group_courseware_status'] ) && !empty( $_POST['group_courseware_status'] ) ) {
		$post_value = sanitize_key( $_POST['group_courseware_status'] );
		
		if( groups_update_groupmeta( $bp->groups->current_group->id, 'courseware', $post_value ) )
		    $vars['message'] = __( 'Group Courseware settings were successfully updated.', 'bpsp' );
		else
		    $vars['error'] = __( 'Group Courseware settings were not updated.', 'bpsp' );
	    }
	}
	
	$vars['name'] = '_group_admin_screen';
	$vars['form_action'] = $bp->root_domain . '/' . $bp->groups->slug . '/' . $group_slug . '/admin/courseware';
	$vars['form_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
	$vars['current_status'] = groups_get_groupmeta( $bp->groups->current_group->id, 'courseware' );
	$this->load_template( $vars );
    }
}
?>