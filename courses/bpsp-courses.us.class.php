<?php
/**
 * BPSP Class for courses management as in US
 */
class BPSP_USCourses extends BPSP_Courses {
    /**
     * BPSP_USCourses()
     *
     * Constructor. Loads all the hooks.
     */
    function BPSP_USCourses() {
        add_action( 'bp_after_group_header', array( &$this, 'course_group_header' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'courses_screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'courses_add_nav_options' ) );
    }
    
    /**
     * Appends course information to group header
     */
    function course_group_header() {
        global $bp;
        $c = $this->has_courses();
        if( !empty( $c[0] ) ) {
            $this->current_course = $c[0]->ID;
            $vars['name'] = '_course_group_header';
            $vars['echo'] = false;
            $vars['course'] = $c[0];
        } else {
            $vars['name'] = '_no_course_group_header';
            $vars['echo'] = false;
            $this->current_uri = $bp->root_domain . '/' . $bp->groups->slug . '/' . $bp->groups->current_group->slug . '/' . $bp->courseware->slug;
            $vars['init_course_link'] =  $this->current_uri . '/course/edit';
        }
        
        echo BPSP_Groups::load_template($vars);
    }
    
    /**
     * courses_screen_handler( $action_vars )
     *
     * Courses screens handler.
     * Handles uris like groups/ID/courseware/action/args
     */
    function courses_screen_handler( $action_vars ) {
        if ( $action_vars[0] == 'course' ) {
            $course = $this->is_course( $this->current_course );
            
            if( !$course ) {
                bp_core_add_message( $this->init_course() );
                $course = $this->is_course( $this->current_course );
            }
            
            if( isset ( $action_vars[1] ) && 'edit' == $action_vars[1] ) {
                // Hide excerpt from group header
                remove_action( 'bp_after_group_header', array( &$this, 'course_group_header' ) );
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( &$this, 'edit_course_screen' ) );
            }
            elseif( isset ( $action_vars[1] ) && 'delete' == $action_vars[1] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_course_screen' ) );
            }
            else
                add_filter( 'courseware_group_template', array( &$this, 'single_course_screen' ) );
        }
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
        $options[__( 'Course Description', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/course';
        return $options;
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
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        $vars['name'] = 'single_course';
        $vars['course_permalink'] = $vars['current_uri'] . '/course/';
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/edit';
        $vars['course'] = $course;
        return apply_filters( 'courseware_course', &$vars );
    }
    
    /**
     * init_course()
     * 
     * On initial group creation, assign a course to it
     * @return String a message with creation results
     */
    function init_course() {
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
            return __( 'New course was added.', 'bpsp' );
        } else
            return __( 'New course could not be added.', 'bpsp' );
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
        $vars['course'] = $this->is_course( $updated_course_id );
        $vars['course_edit_uri'] = $vars['current_uri'] . '/course/edit';
        $vars['course_permalink'] = $vars['current_uri'] . '/course';
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
}
?>