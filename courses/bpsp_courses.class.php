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
        add_action( 'scholarpress_new_teacher_added', array( &$this, 'add_course_caps' ) );
        add_action( 'scholarpress_new_teacher_removed', array( &$this, 'remove_course_caps' ) );
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
            'publicly_queryable'    => true,
            'exclude_from_search'   => false,
            'show_ui'               => true, //TODO: set to false when stable
            'capability_type'       => 'course',
            'hierarchical'          => false,
            'rewrite'               => array( 'slug' => 'course', 'with_front' => false ),
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
            'query_var'     => true,
            'rewrite'       => array( 'slug' => 'class', 'with_front' => false ),
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
     * Adds Course Capabilities to new $user_id
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
     * Adds Course Capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_course_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
}
?>