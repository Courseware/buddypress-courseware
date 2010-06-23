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
            'query_var'     => true,
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
     * Handles uris like groups/ID/courseware/action/args
     */
    function courses_screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_course' ) {
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( &$this, 'new_course_screen' ) );
        }
        elseif ( $action_vars[0] == 'course' ) {
            if( isset ( $action_vars[1] ) && null != $this->is_course( $action_vars[1] ) )
                $this->current_course = $action_vars[1];
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( &$this, 'edit_course_screen' ) );
            }
            elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_course_screen' ) );
            }
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
     * @return Course object if course exists and null if not.
     */
    function is_course( $course_identifier = null ) {
        global $bp;
        
        if( !$course_identifier )
            $this->current_course;
        
        $course_query = array(
            'post_type' => 'course',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if( is_numeric( $course_identifier ) )
            $course_query['p'] = $course_identifier;
        else
            $course_query['name'] = $course_identifier;
        
        $course = get_posts( $course_query );
        
        if( !empty( $course[0] ) )
            return $course[0];
        else
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
        if( !$group_id ) {
            global $bp;
            $group_id = $bp->groups->current_group->id;
        }
        
        $course_query = array(
            'post_type' => 'course',
            'group_id' => $group_id,
        );
        
        $course = get_posts( $course_query );
        
        if( !empty( $course ) )
            return $course;
        else
            return null;
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
            $options[__( 'New Course', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_course';
        
        $options[__( 'Courses', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/courses';
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
        $nonce_name = 'new_course';
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) && !is_super_admin() )
            wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to add a new course.' ) );
        
        // Save new course
        if( isset( $_POST['course'] ) && $_POST['course']['object'] == 'group' && isset( $_POST['_wpnonce'] ) ) {
            $new_course = $_POST['course'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce ) 
                $vars['message'] = __( 'Nonce Error while adding a course.', 'bpsp' );
            else
                if( isset( $new_course['title'] ) && isset( $new_course['content'] ) && isset( $new_course['group_id'] ) && $is_nonce ) {
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
                        $vars['message'] = __( 'New course was added.', 'bpsp' );
                        return $this->list_courses_screen( $vars );
                    } else
                        $vars['message'] = __( 'New course could not be added.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'new_course';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['label_title'] = __( 'Title', 'bpsp' );
        $vars['form_title'] = __( 'Add a new course', 'bpsp' );
        $vars['submit_title'] = __( 'Add a new course', 'bpsp' );
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
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
        global $bp;
        $courses = get_posts( array(
            'post_type' => 'course',
            'group_id' => $bp->groups->current_group->id,
            'numberposts' => get_option( 'posts_per_page', '10' ),
        ));
        
        $vars['name'] = 'list_courses';
        $vars['no_courses_title'] = __( 'No courses were added.', 'bpsp' );
        $vars['courses_meta_title'] = __( 'added on %1$s by %2$s.', 'bpsp' );
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
        global $bp;
        $course = $this->is_course( $this->current_course );
        
        if(  $course->post_author == $bp->loggedin_user->id || is_super_admin() )
            $vars['show_edit'] = __( 'Edit Course', 'bpsp' );
        else
            $vars['show_edit'] = null;
        
        $vars['name'] = 'single_course';
        $vars['course_meta_title'] = __( 'added on %1$s by %2$s.', 'bpsp' );
        $vars['course_permalink'] = $vars['current_uri'] . '/course/' . $this->current_course;
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/' . $this->current_course . '/edit';
        $vars['course'] = $course;
        return $vars;
    }
    
    /**
     * delete_course_screen( $vars )
     *
     * Hooks into courses_screen_handler
     * Delete course screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function delete_course_screen( $vars ) {
        global $bp;
        $course = $this->is_course( $this->current_course );
        $nonce_name = 'delete_course';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        
        if( true != $is_nonce ) {
            $vars['message'] = __( 'Nonce Error while deleting the course.', 'bpsp' );
            return $this->list_courses_screen( $vars );
        }
        
        if(  ( $course->post_author == $bp->loggedin_user->id ) || is_super_admin() ) {
            wp_delete_post( $course->ID );
        } else
            wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to delete the course.', 'bpsp' ) );
        
        $vars['message'] = __( 'Course deleted successfully.', 'bpsp' );
        return $this->list_courses_screen( $vars );
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
        
        $old_course = $this->is_course( $this->current_course );
        $old_course->terms = wp_get_object_terms($old_course->ID, 'group_id' );
        
        if( !$this->has_course_caps( $bp->loggedin_user->id ) &&
            $bp->loggedin_user->id != $old_course->post_author &&
            $bp->groups->current_group->id != $old_course->terms[0]->name &&
            !is_super_admin()
        )
            wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to update the course.', 'bpsp' ) );
        
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
                        $vars['message'] = __( 'New course could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_course';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['label_preview'] = __( 'Preview', 'bpsp' );
        $vars['label_title'] = __( 'Title', 'bpsp' );
        $vars['form_title'] = __( 'Edit course', 'bpsp' );
        $vars['submit_title'] = __( 'Update course', 'bpsp' );
        $vars['course'] = $this->is_course( $updated_course_id );
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/' . $this->current_course . '/edit';
        $vars['course_delete_uri'] = $vars['current_uri'] . '/course/' . $this->current_course . '/delete';
        $vars['course_delete_title'] = __( 'Delete Course', 'bpsp' );
        $vars['course_permalink'] = $vars['current_uri'] . '/course/' . $this->current_course;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_course' ), $vars['course_delete_uri'] );
        return $vars;
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