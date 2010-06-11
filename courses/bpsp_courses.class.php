<?php
/**
 * BPSP Class for courses management
 */
class BPSP_Courses {
    /**
     * Courses capabilities
     */
    var $caps = array(
        'publish_courses',
        'manage_courses',
        'edit_courses',
        'delete_courses',
        'assign_courses'
    );
    
    /**
     * BPSP_Courses()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Courses() {
        add_action( 'wp', array( &$this, 'setup_nav' ), 2 );
        add_action( 'scholarpress_new_teacher_added', array( &$this, 'add_course_caps' ) );
        add_action( 'scholarpress_new_teacher_removed', array( &$this, 'remove_course_caps' ) );
        add_action( 'scholarpress_render_courses_screen', array( &$this, 'courses_screen_render' ) );
    }
    
    /**
     * register_post_types()
     *
     * Static function to register the courses post types, taxonomies and capabilities.
     */
    function register_post_types() {
        $course_post_def = array(
            'label'                 => __( 'Courses', 'bpsp' ),
            'singular_label'        => __( 'Course', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress LMS Courses', 'bpsp' ),
            'public'                => true, //TODO: set to false when stable
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => true, //TODO: set to false when stable
            'capability_type'       => 'course',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' )
        );
        if( !register_post_type( 'course', $course_post_def ) )
            wp_die( __( 'BuddyPress ScholarPress error while registering courses post type.', 'bpsp' ) );
        
        $course_rel_def = array(
            'public'        => true, //TODO: set to false when stable
            'show_ui'       => true, //TODO: set to false when stable
            'hierarchical'  => false,
            'label'         => __( 'Group ID', 'bpsp'),
            'query_var'     => false,
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_courses',
                'edit_terms'    => 'edit_courses',
                'delete_terms'  => 'delete_courses',
                'assign_terms'  => 'assign_courses'
                )
        );
        register_taxonomy( 'group_id', 'course', $course_rel_def );
        if( !get_taxonomy( 'group_id' ) )
            wp_die( __( 'BuddyPress ScholarPress error while registering courses taxonomies.', 'bpsp' ) );
    }
    
    /**
     * add_course_caps( $user_id )
     *
     * Adds course capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_course_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
    }
    
    /**
     * remove_course_caps( $user_id )
     *
     * Adds course capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_course_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * activate_component()
     *
     * Activates courses as a BuddyPress component
     */
    function activate_component() {
        global $bp;
        $bp->courses->id = 'courses';
        $bp->courses->slug = 'courses';
        $bp->active_components[$bp->courses->slug] = $bp->courses->id;
    }
    
    /**
     * setup_nav()
     *
     * Sets up the componenet navigation
     */
    function setup_nav() {
        global $bp;
        
	if ( $group_id = BP_Groups_Group::group_exists($bp->current_action) ) {
		$bp->is_single_item = true;
		$bp->groups->current_group = &new BP_Groups_Group( $group_id );
	}
        
        $groups_link = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/';        
        
        if ( $bp->is_single_item )
            bp_core_new_subnav_item( array( 
		'name' => __( 'ScholarPress', 'bpsp' ),
		'slug' => $bp->courses->slug,
		'parent_url' => $groups_link, 
		'parent_slug' => $bp->groups->slug, 
		'screen_function' => array( &$this, 'courses_screen' ),
		'position' => 35, 
		'user_has_access' => $bp->groups->current_group->user_has_access,
		'item_css_id' => 'scholarpress-courses'
            ) );
        
        do_action('scholarpress_courses_nav_setup');
    }
    
    /**
     * courses_screen()
     *
     * Renders the courses screen on group page
     */
    function courses_screen() {
        global $bp;
        if ( $bp->current_component == $bp->groups->slug && $bp->current_action == $bp->courses->slug ) {
            add_action( 'bp_before_group_body', array( &$this, 'courses_nav_options' ) );
            do_action( 'scholarpress_render_courses_screen', $bp->action_variables );
        }
        bp_core_load_template( apply_filters( 'bp_core_template_plugin' , 'groups/single/plugins' ) );
    }
    
    function courses_nav_options() {
        echo "<ul><li>TODO: Get a some secondary nav</li></ul>";
    }
    
    function courses_screen_render( $action_vars ) {
        if( $action_vars[0] == 'new' )
            add_action( 'bp_template_content', array( &$this, 'courses_add_new' ) );
        else
            add_action( 'bp_template_content', array( &$this, 'courses_list' ) );
    }
    
    function courses_add_new() {
        echo "TODO: Get a UI";
    }
    
    function courses_list() {
        echo "TODO: List all courses";
    }
}
?>