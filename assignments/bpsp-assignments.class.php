<?php
/**
 * BPSP Class for assignments management
 */
class BPSP_Assignments {
    /**
     * Assignments capabilities
     */
    var $caps = array(
        'view_assignments',
        'publish_assignments',
        'manage_assignments',
        'edit_assignment',
        'edit_assignments',
        'delete_assignment',
        'assign_assignments',
        'manage_group_id',
        'manage_course_id',
        'upload_files',
        'edit_files',
    );
    
    /**
     * Current assignment id
     */
    var $current_assignment = null;
    
    /**
     * BPSP_Assignments()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Assignments() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_assignment_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_assignment_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
   }
    
    /**
     * register_post_types()
     *
     * Static function to register the assignments post types, taxonomies and capabilities.
     */
    function register_post_types() {
        $assignment_post_def = array(
            'label'                 => __( 'Assignments', 'bpsp' ),
            'singular_label'        => __( 'Assignment', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Assignments', 'bpsp' ),
            'public'                => true, //TODO: set to false when stable
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => true, //TODO: set to false when stable
            'capability_type'       => 'assignment',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' )
        );
        if( !register_post_type( 'assignment', $assignment_post_def ) )
            $this->error = __( 'BuddyPress Courseware error while registering assignment post type.', 'bpsp' );
        
        $assignment_rel_def = array(
            'public'        => true, //TODO: set to false when stable
            'show_ui'       => true, //TODO: set to false when stable
            'hierarchical'  => false,
            'label'         => __( 'Course ID', 'bpsp'),
            'query_var'     => true,
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_course_id',
                'edit_terms'    => 'manage_course_id',
                'delete_terms'  => 'manage_course_id',
                'assign_terms'  => 'edit_assignments'
                )
        );
        
        register_taxonomy( 'course_id', array( 'assignment' ), $assignment_rel_def );
        register_taxonomy_for_object_type( 'group_id', 'assignment' ); //append already registered group_id term
        
        if( !get_taxonomy( 'group_id' ) || !get_taxonomy( 'course_id' ) )
            wp_die( __( 'BuddyPress Courseware error while registering assignment taxonomies.', 'bpsp' ) );
    }
    
    /**
     * add_assignment_caps( $user_id )
     *
     * Adds assignment capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_assignment_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_assignments' ) )
                $user->add_cap( 'edit_others_assignments' );
    }
    
    /**
     * remove_assignment_caps( $user_id )
     *
     * Adds assignment capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_assignment_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_assignment_caps( $user_id )
     *
     * Checks if $user_id has assignment management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_assignment_caps( $user_id ) {
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_assignment_caps( $user_id );
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
     * Assignment screens handler.
     * Handles uris like groups/ID/courseware/assignments/args
     */
    function screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_assignment' ) {
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( &$this, 'new_assignment_screen' ) );
        }
        elseif ( $action_vars[0] == 'assignment' ) {
            if( isset ( $action_vars[1] ) && null != $this->is_assignment( $action_vars[1] ) )
                $this->current_assignment = $action_vars[1];
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( &$this, 'edit_assignment_screen' ) );
            }
            elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_assignment_screen' ) );
            }
            else {
                do_action( 'courseware_bibliography_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'single_assignment_screen' ) );
            }
        }
        elseif ( $action_vars[0] == 'assignments' ) {
            do_action( 'courseware_list_assignments_screen' );
            add_filter( 'courseware_group_template', array( &$this, 'list_assignments_screen' ) );
        }
    }
    
    /**
     * is_assignment( $assignment_identifier )
     *
     * Checks if a assignment with $assignment_identifier exists
     *
     * @param $assignment_identifier ID or Name of the assignment to be checked
     * @return Assignment object if assignment exists and null if not.
     */
    function is_assignment( $assignment_identifier = null ) {
        global $bp;
        
        if( !$assignment_identifier )
            $this->current_assignment;
        
        $assignment_query = array(
            'post_type' => 'assignment',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if( is_numeric( $assignment_identifier ) )
            $assignment_query['p'] = $assignment_identifier;
        else
            $assignment_query['name'] = $assignment_identifier;
        
        $assignment = get_posts( $assignment_query );
        
        if( !empty( $assignment[0] ) ) {
            $assignment[0]->due_date = get_post_meta( $assignment[0]->ID, 'due_date', true );
            $assignment[0]->group = wp_get_object_terms( $assignment[0]->ID, 'group_id' );
            $assignment_course = wp_get_object_terms( $assignment[0]->ID, 'course_id' );
            $assignment[0]->course = BPSP_Courses::is_course($assignment_course[0]->name );
            return $assignment[0];
        }
        else
            return null;
    }
    
    /**
     * add_nav_options()
     *
     * Adds assignment specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function add_nav_options( $options ) {
        global $bp;
        
        if( $this->has_assignment_caps( $bp->loggedin_user->id ) || is_super_admin() ) {
            // If there are no courses, there will be no assignments
            if( BPSP_Courses::has_courses( $bp->groups->current_group->id ) )
                $options[__( 'New Assignment', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_assignment';
        }
        
        $options[__( 'Assignments', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/assignments';
        return $options;
    }
    
    /**
     * new_assignment_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to add new assignments.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function new_assignment_screen( $vars ) {
        global $bp;
        $nonce_name = 'new_assignment';
        
        if( !$this->has_assignment_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new assignment.' );
            return $vars;
        }
        
        // Save new assignment
        if( isset( $_POST['assignment'] ) &&
            $_POST['assignment']['object'] == 'group' &&
            BPSP_Courses::is_course( $_POST['assignment']['course_id'] ) &&
            isset( $_POST['_wpnonce'] )
        ) {
            $new_assignment = $_POST['assignment'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce ) 
                $vars['error'] = __( 'Nonce Error while adding an assignment.', 'bpsp' );
            else
                if( isset( $new_assignment['title'] ) &&
                    isset( $new_assignment['content'] ) &&
                    isset( $new_assignment['group_id'] ) &&
                    strtotime( $new_assignment['due_date'] ) &&
                    is_numeric( $new_assignment['course_id'] )
                ) {
                    $new_assignment['title'] = strip_tags( $new_assignment['title'] );
                    $new_assignment_id =  wp_insert_post( array(
                        'post_author'   => $bp->loggedin_user->id,
                        'post_title'    => $new_assignment['title'],
                        'post_content'  => $new_assignment['content'],
                        'post_status'   => 'publish',
                        'post_type'     => 'assignment',
                    ));
                    if( $new_assignment_id ) {
                        wp_set_post_terms( $new_assignment_id, $new_assignment['group_id'], 'group_id' );
                        wp_set_post_terms( $new_assignment_id, $new_assignment['course_id'], 'course_id' );
                        add_post_meta( $new_assignment_id, 'due_date', $new_assignment['due_date'] );
                        $vars['message'] = __( 'New assignment was added.', 'bpsp' );
                        return $this->list_assignments_screen( $vars );
                    } else
                        $vars['error'] = __( 'New assignment could not be added.', 'bpsp' );
                }
        }
        
        $vars['courses'] = BPSP_Courses::has_courses( $bp->groups->current_group->id );
        $vars['name'] = 'new_assignment';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * list_assignments_screen( $vars )
     *
     * Hooks into assignments_screen_handler
     * Adds a UI to list assignments.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function list_assignments_screen( $vars ) {
        global $bp;
        $assignments = get_posts( array(
            'post_type' => 'assignment',
            'group_id' => $bp->groups->current_group->id,
            'numberposts' => get_option( 'posts_per_page', '10' ),
        ));
        
        $vars['name'] = 'list_assignments';
        $vars['assignments_hanlder_uri'] = $vars['current_uri'] . '/assignment/';
        $vars['assignments'] = $assignments;
        return $vars;
    }
    
    /**
     * single_assignment_screen( $vars )
     *
     * Hooks into assignments_screen_handler
     * Displays a single assignment screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_assignment_screen( $vars ) {
        global $bp;
        $assignment = $this->is_assignment( $this->current_assignment );
        
        if(  $assignment->post_author == $bp->loggedin_user->id || is_super_admin() )
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        $vars['name'] = 'single_assignment';
        $vars['assignment_permalink'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment;
        $vars['assignment_edit_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment . '/edit';
        $vars['course_permalink'] = $vars['current_uri'] . '/course/' . $assignment->course->ID;
        $vars['assignment'] = $assignment;
        return apply_filters( 'courseware_assignment', $vars );
    }
    
    /**
     * delete_assignment_screen( $vars )
     *
     * Hooks into assignments_screen_handler
     * Delete assignment screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function delete_assignment_screen( $vars ) {
        global $bp;
        $assignment = $this->is_assignment( $this->current_assignment );
        $nonce_name = 'delete_assignment';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        
        if( true != $is_nonce ) {
            $vars['die'] = __( 'Nonce Error while deleting the assignment.', 'bpsp' );
            return $vars;
        }
        
        if(  ( $assignment->post_author == $bp->loggedin_user->id ) || is_super_admin() ) {
            wp_delete_post( $assignment->ID );
        } else {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to delete the assignment.', 'bpsp' );
            return $vars;
        }
        
        $vars['message'] = __( 'Assignment deleted successfully.', 'bpsp' );
        return $this->list_assignments_screen( $vars );
    }
    
    /**
     * edit_assignment_screen( $vars )
     *
     * Hooks into assignments_screen_handler
     * Edit assignment screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_assignment_screen( $vars ) {
        global $bp;
        $nonce_name = 'edit_assignment';
        
        $old_assignment = $this->is_assignment( $this->current_assignment );
        
        if( !$this->has_assignment_caps( $bp->loggedin_user->id ) &&
            $bp->loggedin_user->id != $old_course->post_author &&
            $bp->groups->current_group->id != $old_course->group[0]->name &&
            !is_super_admin()
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to update the assignment.', 'bpsp' );
            return $vars;
        }
        
        // Update course
        if( isset( $_POST['assignment'] ) &&
            $_POST['assignment']['object'] == 'group' &&
            BPSP_Courses::is_course( $_POST['assignment']['course_id'] ) &&
            isset( $_POST['_wpnonce'] )
        ) {
            $updated_assignment = $_POST['assignment'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce )
                $vars['error'] = __( 'Nonce Error while editing the assignment.', 'bpsp' );
            else 
                if( isset( $updated_assignment['title'] ) &&
                    isset( $updated_assignment['content'] ) &&
                    isset( $updated_assignment['course_id'] ) &&
                    strtotime( $updated_assignment['due_date'] ) &&
                    is_numeric( $updated_assignment['group_id'] )
                ) {
                    $updated_assignment['title'] = strip_tags( $updated_assignment['title'] );
                    $updated_assignment_id =  wp_update_post( array(
                        'ID'            => $old_assignment->ID,
                        'post_title'    => $updated_assignment['title'],
                        'post_content'  => $updated_assignment['content'],
                    ));
                    
                    if( $updated_assignment_id ) {
                        wp_set_post_terms( $updated_assignment_id, $updated_assignment['course_id'], 'course_id' );
                        update_post_meta( $updated_assignment_id, 'due_date', $updated_assignment['due_date'], $old_assignment->due_date );
                        $vars['message'] = __( 'Assignment was updated.', 'bpsp' );
                    }
                    else
                        $vars['error'] = __( 'Assignment could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_assignment';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['courses'] = BPSP_Courses::has_courses( $bp->groups->current_group->id );
        $vars['assignment'] = $this->is_assignment( $updated_assignment_id );
        $vars['assignment_edit_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment . '/edit';
        $vars['assignment_delete_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment . '/delete';
        $vars['assignment_permalink'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_assignment' ), $vars['assignment_delete_uri'] );
        return $vars;
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        wp_enqueue_script( 'assignments' );
        wp_enqueue_style( 'datetimepicker' );
        wp_enqueue_script( 'post' );
        wp_enqueue_script( 'editor' );
        wp_enqueue_script( 'utils' );
        add_thickbox();
        $media_upload_js = '/wp-admin/js/media-upload.js';
        wp_enqueue_script('media-upload', get_bloginfo('wpurl') . $media_upload_js, array( 'thickbox' ), filemtime( ABSPATH . $media_upload_js) );
    }
}
?>