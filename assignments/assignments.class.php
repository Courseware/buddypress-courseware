<?php
// Load the formbuilder
require_once( dirname(__FILE__) . '/formbuilder.class.php' );

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
    );
    
    /**
     * Current assignment id
     */
    var $current_assignment = null;
    
    /**
     * Current course id
     */
    var $current_course = null;
    
    /**
     * FormBuilder instance
     */
    var $frmb = null;
    
    /**
     * BPSP_Assignments()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Assignments() {
        // Initialize our form builder
        $this->frmb = new FormBuilder();
        
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
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'assignment',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'custom-fields' )
        );
        if( !register_post_type( 'assignment', $assignment_post_def ) )
            $this->error = __( 'BuddyPress Courseware error while registering assignment post type.', 'bpsp' );
        
        $assignment_rel_def = array(
            'public'        => BPSP_DEBUG,
            'show_ui'       => BPSP_DEBUG,
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
            $this->current_course = BPSP_Courses::is_course();
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            do_action( 'courseware_list_assignments_screen' );
            do_action( 'courseware_new_assignment_screen' );
            add_filter( 'courseware_group_template', array( &$this, 'new_assignment_screen' ) );
        }
        elseif( $action_vars[0] == 'assignment' ) {
            $current_assignment = BPSP_Assignments::is_assignment( $action_vars[1] );
            
            if( isset ( $action_vars[1] ) && null != $current_assignment )
                $this->current_assignment = $current_assignment;
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                // Try to serve the form data
                if( isset( $_GET['get_form_data'] ) )
                    $this->get_form_data();
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                do_action( 'courseware_edit_assignment_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'edit_assignment_screen' ) );
            }
            elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_assignment_screen' ) );
            }
            elseif( isset ( $action_vars[2] ) && 'enable_forum' == $action_vars[2] ) {
                do_action( 'courseware_bibliography_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'enable_forum_assignment_screen' ) );
            }
            else {
                do_action( 'courseware_bibliography_screen' );
                do_action( 'courseware_assignment_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'single_assignment_screen' ) );
            }
            
            do_action( 'courseware_assignment_screen_handler', $action_vars );
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
        $courseware_uri = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/' ;
        
        if( is_object( $assignment_identifier ) && $assignment_identifier->post_type == "assignment" )
            if( $assignment_identifier->group[0]->name == $bp->groups->current_group->id )
                return $assignment_identifier;
            else
                return null;
        
        if( !$assignment_identifier )
            $assignment_identifier = $this->current_assignment;
        
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
            $assignment[0]->course = BPSP_Courses::is_course( $assignment_course[0]->name );
            $assignment_lecture = get_post_meta( $assignment[0]->ID, 'lecture_id', true );
            $assignment[0]->lecture = $assignment_lecture ? BPSP_Lectures::is_lecture( $assignment_lecture ) : null;
            $assignment[0]->forum_link = get_post_meta( $assignment[0]->ID, 'topic_link', true );
            $assignment[0]->responded_author = get_post_meta( $assignment[0]->ID, 'responded_author' );
            $assignment[0]->form_data = get_post_meta( $assignment[0]->ID, 'form_data', true );
            // If Assignment has form, render it first
            if( !empty( $assignment[0]->form_data ) ) {
                if( !isset( $this ) || !isset( $this->frmb ) )
                    $frmb = new FormBuilder();
                else
                    $frmb = $this->frmb;
                
                $frmb->set_data( $assignment[0]->form_data );
                $assignment[0]->form = $frmb->render();
            }
            $assignment[0]->permalink = $courseware_uri . 'assignment/' . $assignment[0]->post_name;
            return $assignment[0];
        } else
            return null;
    }
    
    /**
     * has_assignments( $group_id = null )
     *
     * Checks if a $group_id has assignments
     *
     * @param Int $group_id of the group to be checked
     * @return Mixed Assignment objects if assignments exist and null if not.
     */
    function has_assignments( $group_id = null ) {
        global $bp;
        $assignment_ids = null;
        $assignments = array();
        
        if( empty( $group_id ) )
            $group_id = $bp->groups->current_group->id;
        
        $term_id = get_term_by( 'slug', $group_id, 'group_id' );
        if( !empty( $term_id ) )
            $assignment_ids = get_objects_in_term( $term_id->term_id, 'group_id' );
        
        if( !empty( $assignment_ids ) )
            arsort( $assignment_ids ); // Get latest entries first
        else
            return null;
        
        foreach ( $assignment_ids as $aid )
            $assignments[] = self::is_assignment( $aid );
        
        return array_filter( $assignments );
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
            BPSP_Lectures::is_lecture( $_POST['assignment']['lecture_id'] ) &&
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
                        
                        if( isset( $new_assignment['lecture_id'] ) )
                            add_post_meta( $new_assignment_id, 'lecture_id', $new_assignment['lecture_id'] );
                        
                        if( strtotime( $new_assignment['due_date'] ) )
                            add_post_meta( $new_assignment_id, 'due_date', $new_assignment['due_date'] );
                        // Save the formbuilder
                        if( $new_assignment['form'] ) {
                            $this->frmb->load_serialized( $new_assignment['form'] );
                            if( $this->frmb->get_data() )
                                add_post_meta( $new_assignment_id, 'form_data', $this->frmb->get_data() );
                        }
                        $vars['message'] = __( 'New assignment was added.', 'bpsp' );
                        do_action( 'courseware_assignment_added', $this->is_assignment( $new_assignment_id ) );
                        return $this->list_assignments_screen( $vars );
                    } else
                        $vars['error'] = __( 'New assignment could not be added.', 'bpsp' );
                } else
                    $vars['error'] = __( 'Please fill in all the fields.', 'bpsp' );
        }
        
        $vars['posted_data'] = $_POST['assignment'];
        $vars['lectures'] = BPSP_Lectures::has_lectures( $bp->groups->current_group->id );
        $vars['name'] = 'new_assignment';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['course_id'] = $this->current_course->ID;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * list_assignments_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to list assignments.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function list_assignments_screen( $vars ) {
        $assignments = $this->has_assignments();
        
        $vars['name'] = 'list_assignments';
        $vars['assignments_hanlder_uri'] = $vars['current_uri'] . '/assignment/';
        $vars['assignments'] = $assignments;
        return $vars;
    }
    
    /**
     * single_assignment_screen( $vars )
     *
     * Hooks into screen_handler
     * Displays a single assignment screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_assignment_screen( $vars ) {
        global $bp;
        $e_forum_nonce = 'assignment_enable_forum';
        
        $assignment = $this->is_assignment( $this->current_assignment );
        
        if(  $this->has_assignment_caps( $bp->loggedin_user->id ) || is_super_admin() )
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        if( !$assignment )
            $vars['die'] = __( 'BuddyPress Courseware Error! Cheatin\' Uh?' );
        $vars['name'] = 'single_assignment';
        $vars['assignment_permalink'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name;
        $vars['assignment_edit_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name . '/edit';
        
        $vars['assignment'] = $assignment;
        
        //TODO: find why the forum_link is not showing up instantly
        if( empty( $assignment->forum_link ) && isset( $vars['forum_link'] ) )
            $assignment->forum_link = $vars['forum_link'];
        
        // Check if forums are available and show the option
        if( bp_group_is_forum_enabled() ) {
            $vars['assignment_e_forum_permalink'] = $vars['assignment_permalink'] . '/enable_forum';
            $vars['assignment_e_forum_nonce'] = wp_nonce_field( $e_forum_nonce, '_wpnonce', true, false );
        }
        
        return apply_filters( 'courseware_assignment', $vars );
    }
    
    /**
     * enable_forum_assignment_screen( $vars )
     *
     * Hooks into screen_handler
     * If forum is active for group, creates a thread for assignment
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function enable_forum_assignment_screen( $vars ) {
        global $bp;
        
        $e_forum_nonce = 'assignment_enable_forum';
        $is_nonce = false;
        
        if( isset( $_POST['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $e_forum_nonce );
        
        // Nonce will take care of dublicates
        if( $is_nonce && bp_group_is_forum_enabled() ) {
            $assignment = $this->is_assignment( $this->current_assignment );
            $assignment_forum_id = groups_get_groupmeta( $bp->groups->current_group->id, 'forum_id' );
            
            // Append assignment link to the content
            $assignment->post_content =
                $assignment->post_content . "\n\n" .
                "<a href=\"" . $vars['current_uri'] . '/assignment/' . $assignment->post_name . "\">" .
                __( 'Courseware Assignment Link', 'bpsp' ). "</a>";
            
            // Create tags from title and append 'assignment' to it
            $assignment->tags = str_replace( '-', ', ', $assignment->post_name ) . __( ", assignment" , 'bpsp' );
            
            // Create a topic for current assignment
            $topic = groups_new_group_forum_topic(
                $assignment->post_title,
                $assignment->post_content,
                $assignment->tags,
                $assignment_forum_id
            );
            
            // Create topic for assignment and save in post_meta topic link
            if( $topic ) {
                $topic_permalink = bp_get_group_permalink( $bp->groups->current_group ) . 'forum/topic/' . $topic->topic_slug;
                
                if( update_post_meta( $assignment->ID, 'topic_link', $topic_permalink ) )
                    $vars['message'] = __( 'Assignment forum created.', 'bpsp' );
                
                // Force saving the new permalink to $vars, since it doesn't show up
                $vars['forum_link'] = $topic_permalink;
            }
        } else
            $vars['error'] = __( 'Forum was not created.', 'bpsp' );
        
        return $this->single_assignment_screen( $vars );
    }
    
    /**
     * delete_assignment_screen( $vars )
     *
     * Hooks into screen_handler
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
     * Hooks into screen_handler
     * Edit assignment screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_assignment_screen( $vars ) {
        global $bp;
        $nonce_name = 'edit_assignment';
        $updated_assignment_id = $this->current_assignment;
        
        $old_assignment = $this->is_assignment( $this->current_assignment );
        
        if( !$this->has_assignment_caps( $bp->loggedin_user->id ) &&
            $bp->loggedin_user->id != $old_assignment->post_author &&
            $bp->groups->current_group->id != $old_assignment->group[0]->name &&
            !is_super_admin()
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to update the assignment.', 'bpsp' );
            return $vars;
        }
        
        // Update course
        if( isset( $_POST['assignment'] ) &&
            $_POST['assignment']['object'] == 'group' &&
            BPSP_Lectures::is_lecture( $_POST['assignment']['lecture_id'] ) &&
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
                        if( strtotime( $updated_assignment['due_date'] ) )
                            update_post_meta( $updated_assignment_id, 'due_date', $updated_assignment['due_date'], $old_assignment->due_date );
                        
                        if( isset( $updated_assignment['lecture_id'] ) )
                            update_post_meta( $updated_assignment_id, 'lecture_id', $updated_assignment['lecture_id'] );
                        
                        // Save the formbuilder
                        if( isset( $updated_assignment['form'] ) && !empty( $updated_assignment['form'] ) ) {
                            $this->frmb->load_serialized( $updated_assignment['form'] );
                            if( $this->frmb->get_data() )
                                update_post_meta( $updated_assignment_id, 'form_data', $this->frmb->get_data(), $old_assignment->form_data );
                        }
                        $vars['message'] = __( 'Assignment was updated.', 'bpsp' );
                        do_action( 'courseware_assignment_activity', $this->is_assignment( $updated_assignment_id ), 'update' );
                    }
                    else
                        $vars['error'] = __( 'Assignment could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_assignment';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['lecture_id'] = get_post_meta( $updated_assignment_id, 'lecture_id', true );
        $vars['lectures'] = BPSP_Lectures::has_lectures( $bp->groups->current_group->id );
        $vars['assignment'] = $this->is_assignment( $updated_assignment_id );
        $vars['assignment_edit_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name . '/edit';
        $vars['assignment_delete_uri'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name . '/delete';
        $vars['assignment_permalink'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_assignment' ), $vars['assignment_delete_uri'] );
        return $vars;
    }
    
    /**
     * get_form_data()
     * Loads current assignment form data and serves it json-ified
     * @uses exit()
     */
    function get_form_data() {
        if( $this->has_assignment_caps( $bp->loggedin_user->id ) || is_super_admin() ) {
            header('HTTP/1.1 200 OK');
            header( "Content-Type: application/json" );
            $this->frmb->set_data( $this->current_assignment->form_data );
            echo $this->frmb->get_json();
            exit( 0 );
        }
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        do_action( 'courseware_editor' );
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