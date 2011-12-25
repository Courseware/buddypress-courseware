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
        add_action( 'bp_after_group_header', array( &$this, 'course_group_header' ) );
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_course_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_course_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_action( 'groups_created_group', array( &$this, 'init_course' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
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
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'course',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' )
        );
        if( !register_post_type( 'course', $course_post_def ) )
            wp_die( __( 'BuddyPress Courseware error while registering courses post type.', 'bpsp' ) );
        
        $groups_rel_def = array(
            'public'        => BPSP_DEBUG,
            'show_ui'       => BPSP_DEBUG,
            'hierarchical'  => false,
            'label'         => __( 'Group ID', 'bpsp'),
            'query_var'     => 'group_id',
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_group_id',
                'edit_terms'    => 'manage_group_id',
                'delete_terms'  => 'manage_group_id',
                'assign_terms'  => 'edit_courses'
                )
        );
        register_taxonomy( 'group_id', array( 'course' ), $groups_rel_def );
        if( !get_taxonomy( 'group_id' ) )
            wp_die( __( 'BuddyPress Courseware error while registering group taxonomy.', 'bpsp' ) );
    }
    
    /**
     * Appends course information to group header
     */
    function course_group_header() {
        global $bp;
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) || !BPSP_Roles::can_teach( $bp->loggedin_user->id ) )
            return;
        
        $vars['name'] = '_no_course_group_header';
        $vars['echo'] = false;
        $this->current_uri = bp_get_group_permalink( $bp->groups->current_group ) . $bp->courseware->slug;
        $vars['init_course_link'] =  $this->current_uri . '/course/edit';
        echo BPSP_Groups::load_template( $vars );
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
        global $bp;
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_course_caps( $user_id );
        }
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $is_ok = false;
        
        if( !get_option( 'bpsp_allow_only_admins' ) )
            if( !bp_group_is_admin() )
                $is_ok = false;
        
        return $is_ok;
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Courses screens handler.
     * Handles uris like groups/ID/courseware/action/args
     */
    function screen_handler( $action_vars ) {
        if ( reset( $action_vars ) == 'course' ) {
            $course = $this->is_course( $this->current_course );
            
            if( !$course ) {
                $course = $this->is_course( $this->current_course );
            }
            
            if( isset ( $action_vars[1] ) && 'edit' == $action_vars[1] ) {
                // Hide excerpt from group header
                remove_action( 'bp_after_group_header', array( &$this, 'course_group_header' ) );
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( &$this, 'edit_course_screen' ) );
            } else {
                do_action( 'courseware_lectures_screen' );
                do_action( 'courseware_bibliography_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'single_course_screen' ) );
            }
        }
    }
    
    /**
     * is_course( $course_identifier )
     *
     * Checks if a course with $course_identifier exists
     *
     * @param $course_identifier ID or Name of the course to be checked
     * @return Course object if course exists and null if not.
     */
    function is_course( $course_identifier = null ) {
        global $bp;
        $courseware_uri = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/' ;
        
        if( is_object( $course_identifier ) && $course_identifier->post_type == "course" )
            if( $course_identifier->group[0]->name == $bp->groups->current_group->id )
                return $course_identifier;
            else
                return null;
        
        if( !$course_identifier && get_class( (object)$this->current_course ) == __CLASS__ )
            return $this->current_course;
        
        $course_query = array(
            'post_type' => 'course',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if ( $course_identifier != null ) {
            if( is_numeric( $course_identifier ) )
                $course_query['p'] = $course_identifier;
            else
                $course_query['name'] = $course_identifier;
        }
        $course = get_posts( $course_query );
        
        if( !empty( $course[0] ) ) {
            $course[0]->permalink = $courseware_uri . 'course/' . $course[0]->post_name;
            return $course[0];
        } else
            return null;
    }
    
    /**
     * has_courses()
     *
     * Checks if a group has courses
     * @param Int $group_id the ID of the group, default $bp->groups->current_group->id
     * @return null if no groups and Course object if has courses.
     */
    function has_courses( $group_id = null ) {
        global $bp;
        $course_ids = null;
        $courses = array();
        
        if( empty( $group_id ) )
            $group_id = $bp->groups->current_group->id;
        
        $term_id = get_term_by( 'slug', $group_id, 'group_id' );
        if( !empty( $term_id ) )
            $course_ids = get_objects_in_term( $term_id->term_id, 'group_id' );
        
        if( !empty( $course_ids ) )
            arsort( $course_ids ); // Get latest entries first
        else
            return null;
        
        foreach ( $course_ids as $cid )
            $courses[] = self::is_course( $cid );
        
        return array_filter( $courses );
    }
    
    /**
     * add_nav_options( $options )
     *
     * Adds courses specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function add_nav_options( $options ) {
        global $bp;
        $options[__( 'Course Description', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/course';
        return $options;
    }
    
    /**
     * init_course()
     * 
     * On initial group creation, assign a course to it
     * @param Int $group_id, the id of the created group
     */
    function init_course( $group_id ) {
        global $bp;
        $new_course_id =  wp_insert_post( array(
            'post_author'   => $bp->loggedin_user->id,
            'post_title'    => $bp->groups->current_group->name,
            'post_content'  => $bp->groups->current_group->description,
            'post_status'   => 'publish',
            'post_type'     => 'course',
        ));
        
        if( $new_course_id ) {
            wp_set_post_terms( $new_course_id, $bp->groups->current_group->id, 'group_id' );
            $this->current_course = $new_course_id;
            do_action( 'courseware_course_added', $new_course_id );
            bp_core_add_message( __( 'New course was added.', 'bpsp' ) );
        } else
            bp_core_add_message( __( 'New course could not be added.', 'bpsp' ) );
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
        global $bp;
        $course = $this->is_course( $this->current_course );
        
        if( $this->has_course_caps( $bp->loggedin_user->id ) || is_super_admin() )
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        if( !$course )
            $vars['die'] = __( 'BuddyPress Courseware Error! Cheatin\' Uh?', 'bpsp' );
        $vars['name'] = 'single_course';
        $vars['course_permalink'] = $vars['current_uri'] . '/course/';
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/edit';
        $vars['course'] = $course;
        $vars['trail'] = array(
            $course->post_title => $course->permalink
        );
        return apply_filters( 'courseware_course', &$vars );
    }
    
    /**
     * edit_course_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Edit course screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_course_screen( $vars ) {
        global $bp;
        $nonce_name = 'edit_course';
        $updated_course_id = false;
        
        $old_course = $this->is_course( $this->current_course );
        $old_course->terms = wp_get_object_terms($old_course->ID, 'group_id' );
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) || !is_super_admin() &&
            $bp->groups->current_group->id != $old_course->terms[0]->name
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to update the course.', 'bpsp' );
            return $vars;
        }
        
        // Update course
        if( isset( $_POST['course'] ) && $_POST['course']['object'] == 'group' && isset( $_POST['_wpnonce'] ) ) {
            $updated_course = $_POST['course'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce )
                $vars['message'] = __( 'Nonce Error while editing a course.', 'bpsp' );
            else 
                if( isset( $updated_course['title'] ) &&
                    isset( $updated_course['content'] ) &&
                    isset( $updated_course['group_id'] )
                ) {
                    $updated_course['title'] = strip_tags( $updated_course['title'] );
                    $updated_course_id =  wp_update_post( array(
                        'ID'            => $old_course->ID,
                        'post_title'    => $updated_course['title'],
                        'post_content'  => $updated_course['content'],
                    ));
                    
                    if( $updated_course_id )
                        $vars['message'] = __( 'New course was updated.', 'bpsp' );
                    else
                        $vars['error'] = __( 'New course could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_course';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['course'] = $this->is_course( $updated_course_id );
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/edit/';
        $vars['course_permalink'] = $vars['current_uri'] . '/course';
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['trail'] = array(
            __( 'Editing Course: ', 'bpsp' ) . $vars['course']->post_title => $vars['course']->permalink
        );
        return $vars;
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        do_action( 'courseware_editor' );
    }
}
?>
