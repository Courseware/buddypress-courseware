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
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_course_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_course_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'courses_screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'courses_add_nav_options' ) );
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
            'description'           => __( 'BuddyPress ScholarPress Courseware Courses', 'bpsp' ),
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
            wp_die( __( 'BuddyPress Courseware error while registering courses post type.', 'bpsp' ) );
        
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
            wp_die( __( 'BuddyPress Courseware error while registering courses taxonomies.', 'bpsp' ) );
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
     * courses_screen_handler( $action_vars )
     *
     * Courses screens handler.
     * Handles uris like groups/ID/courses/action
     */
    function courses_screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_course' )
            add_filter( 'courseware_group_template', array( &$this, 'courses_new_screen' ) );
        elseif ( $action_vars[0] == 'all' )
            add_filter( 'courseware_group_template', array( &$this, 'courses_list_screen' ) );
        else
            add_filter( 'courseware_group_template', array( &$this, 'courses_home_screen' ) );
    }
    
    /**
     * courses_add_nav_options()
     *
     * Adds courses specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function courses_add_nav_options( $options ) {
        $options[__( 'New Course' )] = $options[__( 'Home' )] . '/new_course';
        $options[__( 'Courses' )] = $options[__( 'Home' )] . '/all';
        return $options;
    }
    
    /**
     * courses_add_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Adds a UI to add new courses.
     */
    function courses_new_screen( $vars ) {
        $vars['name'] = 'new';
        return $vars;
    }
    
    /**
     * courses_add_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Adds a dashboard UI
     */
    function courses_home_screen( $vars ) {
        $vars['name'] = 'home';
        return $vars;
    }
    
    /**
     * courses_list_screen()
     *
     * Hooks into courses_screen_handler
     * Adds a UI to list courses.
     */
    function courses_list_screen( $vars ) {
        $vars['name'] = 'list';
        return $vars;
    }
}
?>