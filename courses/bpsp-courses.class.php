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
                'publish_terms' => 'publish_courses',
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
     * has_course_caps( $user_id )
     *
     * Checks if $user_id has course management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_course_caps( $user_id ) {
        $is_ok = true;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $is_ok = false;
        
        return $is_ok;
    }
    
    /**
     * courses_screen_handler( $action_vars )
     *
     * Courses screens handler.
     * Handles uris like groups/ID/courses/action
     */
    function courses_screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_course' ) {
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( &$this, 'courses_new_screen' ) );
        }
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
        global $bp;
        
        if( $this->has_course_caps( $bp->loggedin_user->id ) )
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
        global $bp;
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) )
            wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to add a new course.' ) );
        
        //TODO: Add logic
        
        $vars['name'] = 'new_course';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['form_title'] = __( 'Add a new course' );
        $vars['submit_title'] = __( 'Add a new course' );
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
    
    function load_editor() {
        wp_enqueue_script('post');
        wp_enqueue_script( 'editor' );
        wp_enqueue_script( 'utils' );
        add_thickbox();
        $media_upload_js = '/wp-admin/js/media-upload.js';
        wp_enqueue_script('media-upload', get_bloginfo('wpurl') . $media_upload_js, array( 'thickbox' ), filemtime( ABSPATH . $media_upload_js) );
    }
}
?>