<?php
/**
 * BPSP Class for courses management
 */
class BPSP_Courses {
    /**
     * Courses capabilities
     */
    var $caps = array(
        'view_courses',
        'publish_courses',
        'manage_courses',
        'edit_course',
        'edit_courses',
        'delete_course',
        'assign_courses',
        'manage_group_id',
        'upload_files',
        'edit_files',
    );
    
    /**
     * Current course id
     */
    var $current_course = null;
    
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
                'manage_terms'  => 'manage_group_id',
                'edit_terms'    => 'manage_group_id',
                'delete_terms'  => 'manage_group_id',
                'assign_terms'  => 'edit_courses'
                )
        );
        register_taxonomy( 'group_id', array( 'course' ), $course_rel_def );
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
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_courses' ) )
                $user->add_cap( 'edit_others_courses' );
    }
    
    /**
     * remove_course_caps( $user_id )
     *
     * Adds course capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_course_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
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
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_course_caps( $user_id );
        }
        
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
            add_filter( 'courseware_group_template', array( &$this, 'new_course_screen' ) );
        }
        elseif ( $action_vars[0] == 'course' ) {
            if( isset ( $action_vars[1] ) && $this->is_course( $action_vars[1] ) )
                $this->current_course = $action_vars[1];
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] )
                add_filter( 'courseware_group_template', array( &$this, 'edit_course_screen' ) );
            else
                add_filter( 'courseware_group_template', array( &$this, 'single_course_screen' ) );
        }
        elseif ( $action_vars[0] == 'courses' )
            add_filter( 'courseware_group_template', array( &$this, 'list_courses_screen' ) );
    }
    
    /**
     * is_course( $course_identifier )
     *
     * Checks if a course with $course_identifier exists
     *
     * @param $course_identifier ID or Name of the course to be checked
     * @return Bool True if course exists and False is not.
     */
    function is_course( $course_identifier ) {
        global $bp;
        $course_query = array(
            'post_type' => 'course',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if( is_numeric( $course_identifier ) )
            $course_query['p'] = $course_identifier;
        else
            $course_query['name'] = $course_identifier;
        
        $course = get_posts( $course_query );
        
        if( isset( $course[0] ) )
            return true;
        else
            return false;
        
        return true;
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
        
        if( $this->has_course_caps( $bp->loggedin_user->id ) || is_super_admin() )
            $options[__( 'New Course' )] = $options[__( 'Home' )] . '/new_course';
        
        $options[__( 'Courses' )] = $options[__( 'Home' )] . '/courses';
        return $options;
    }
    
    /**
     * new_course_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Adds a UI to add new courses.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function new_course_screen( $vars ) {
        global $bp;
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) && !is_super_admin() )
            wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to add a new course.' ) );
        
        // Save new course
        if( isset( $_POST['course'] ) && $_POST['course']['object'] == 'group' ) {
            $new_course = $_POST['course'];
            if( isset( $new_course['title'] ) && isset( $new_course['content'] ) && isset( $new_course['group_id'] ) ) {
                $new_course['title'] = strip_tags( $new_course['title'] );
                $new_course_id =  wp_insert_post( array(
                    'post_author'   => $bp->loggedin_user->id,
                    'post_title'    => $new_course['title'],
                    'post_content'  => $new_course['content'],
                    'post_status'   => 'publish',
                    'post_type'     => 'course',
                ));
                if( $new_course_id ) {
                    wp_set_post_terms( $new_course_id, $new_course['group_id'], 'group_id' );
                    $vars['message'] = __( 'New course was added.' );
                }
            }
        }
        
        $vars['name'] = 'new_course';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['form_title'] = __( 'Add a new course' );
        $vars['submit_title'] = __( 'Add a new course' );
        return $vars;
    }
    
    /**
     * list_courses_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Adds a UI to list courses.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function list_courses_screen( $vars ) {
        $courses = get_posts( array(
            'post_type' => 'course',
            'tag' => $bp->groups->current_group->id,
            'numberposts' => get_option( 'posts_per_page', '10' ),
        ));
        
        $vars['name'] = 'list_courses';
        $vars['courses_hanlder_uri'] = $vars['current_uri'] . '/course/';
        $vars['courses'] = $courses;
        return $vars;
    }
    
    /**
     * single_course_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Displays a single course screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_course_screen( $vars ) {
        $course_query = array(
            'post_type' => 'course',
            'tag' => $bp->groups->current_group->id,
        );
        if( is_numeric( $this->current_course ))
            $course_query['p'] = $this->current_course;
        else
            $course_query['name'] = $this->current_course;
        
        $course = get_posts( $course_query );
        
        $vars['name'] = 'single_course';
        $vars['course_uri'] = $vars['current_uri'] . '/course/';
        if( isset( $course[0] ))
            $vars['course'] = $course[0];
        else
            $vars['course'] = null;
        return $vars;
    }
    
    /**
     * edit_course_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Displays a single course screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_course_screen( $vars ) {
        $course_query = array(
            'post_type' => 'course',
            'tag' => $bp->groups->current_group->id,
        );
        if( is_numeric( $this->current_course ))
            $course_query['p'] = $this->current_course;
        else
            $course_query['name'] = $this->current_course;
        
        $course = get_posts( $course_query );
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        wp_enqueue_script( 'post' );
        wp_enqueue_script( 'editor' );
        wp_enqueue_script( 'utils' );
        add_thickbox();
        $media_upload_js = '/wp-admin/js/media-upload.js';
        wp_enqueue_script('media-upload', get_bloginfo('wpurl') . $media_upload_js, array( 'thickbox' ), filemtime( ABSPATH . $media_upload_js) );
    }
}
?>