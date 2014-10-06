<?php
/**
 * BPSP Class for responses management
 */
class BPSP_Responses {
    /**
     * Response capabilities
     */
    var $caps = array(
        'view_responses',
        'publish_responses',
        'manage_responses',
        'edit_response',
        'edit_responses',
        'delete_response',
        'assign_responses',
        'upload_files',
    );
    
    /**
     * Response capabilities for students
     */
    var $students_caps = array(
        'view_responses',
        'publish_responses',
        'edit_response',
        'upload_files'
    );
    
    /**
     * Current assignment id
     */
    var $current_assignment = null;
    
    /**
     * Current response id
     */
    var $current_response = null;
    
    /**
     * BPSP_Responses()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Responses() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_response_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_response_caps' ) );
        add_action( 'courseware_assignment_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_assignment', array( &$this, 'populate_responses' ) );
   }
    
    /**
     * register_post_types()
     *
     * Static function to register the responses post types, taxonomies and capabilities.
     */
    static function register_post_types() {
        $response_post_def = array(
            'label'                 => __( 'Responses', 'bpsp' ),
            'singular_label'        => __( 'Response', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Responses', 'bpsp' ),
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'response',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author' )
        );
        if( !register_post_type( 'response', $response_post_def ) )
            wp_die( __( 'BuddyPress Courseware error while registering response post type.', 'bpsp' ) );
    }
    
    /**
     * add_response_caps( $user_id )
     *
     * Adds response capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_response_caps( $user_id ) {
        global $bp;
        $is_teacher = false;
        
        if( __( 'Teacher', 'bpsp') == xprofile_get_field_data( __( 'Role', 'bpsp' ), $user_id ) )
            $is_teacher = true;
        elseif ( is_super_admin( $user_id ) )
            $is_teacher = true;
        
        $user = new WP_User( $user_id );
        // Treat teachers with default set of caps
        if( $is_teacher )
            foreach( $this->caps as $c )
                if ( !$user->has_cap( $c ) )
                    $user->add_cap( $c );
        // Treat ordinary users as students that will get caps for adding responses
        else
            foreach( $this->students_caps as $c )
                    if ( !$user->has_cap( $c ) )
                        $user->add_cap( $c );
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_responses' ) )
                $user->add_cap( 'edit_others_responses' );
    }
    
    /**
     * remove_response_caps( $user_id )
     *
     * Adds response capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_response_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_response_caps( $user_id )
     *
     * Checks if $user_id has response management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_response_caps( $user_id = null ) {
        $is_ok = true;
        
        if( !$user_id ){
            global $bp;
            $user_id = $bp->loggedin_user->id;
        }
        
        // Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_response_caps( $user_id );
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
     * has_student_caps( $user_id )
     *
     * Checks if $user_id has response management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked, default null
     * @return True if $user_id is eligible and False if not.
     */
    function has_student_caps( $user_id = null ) {
        global $bp;
        
        if( !$user_id )
            $user_id = $bp->loggedin_user->id;
        
        $user_role = xprofile_get_field_data( __( 'Role'), $user_id );
        // Go away teacher
        if( __( 'Student', 'bpsp' ) != $user_role && !empty( $user_role ) )
            return false;
        
        // Treat super admins
        if( is_super_admin( $user_id ) )
            $this->add_response_caps( $user_id );
        
        $user = new WP_User( $user_id );
        foreach( $this->students_caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
        
        return true;
    }
    
    /**
     * group_responses_status( $gid )
     *
     * Will check if groups responses are public or private
     * @param Int $gid, group id to check, default is current group
     * @return Bool true if private flag is set or false if responses are public
     */
    function group_responses_status( $gid = null ) {
        if( !$gid ) {
            global $bp;
            $gid = $bp->groups->current_group->id;
        }
        
        $global_status = get_option( 'bpsp_private_responses' );
        $group_status = groups_get_groupmeta( $bp->groups->current_group->id, 'courseware_responses' );
        
        if( 'true' == $group_status )
            return true;

        if( $global_status != '' )
            return true;
        
        return false;
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Response screens handler.
     * Handles uris like groups/ID/courseware/response/args
     */
    function screen_handler( $action_vars ) {
        $this->current_assignment = BPSP_Assignments::is_assignment( $action_vars[1] );
        
        // Check if we got a valid parent assignment
        if( !$this->current_assignment )
            wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
        
        if( isset ( $action_vars[2] ) && $action_vars[2] == 'add_response' ) {
            do_action( 'courseware_add_response' );
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( &$this, 'new_response_screen' ) );
        }
        elseif ( isset ( $action_vars[2] ) && $action_vars[2] == 'response' ) {
            $current_response = $this->is_response( $action_vars[3] );
            if( isset ( $action_vars[3] ) && !empty( $current_response ) )
                $this->current_response = $current_response;
            else
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            
            if( isset ( $action_vars[4] ) && 'delete' == $action_vars[4] )
                add_filter( 'courseware_group_template', array( &$this, 'delete_response_screen' ) );
            else {
                do_action( 'courseware_single_response' );
                add_filter( 'courseware_group_template', array( &$this, 'single_response_screen' ) );
            }
        }
    }
    
    /**
     * is_response( $response_identifier )
     *
     * Checks if a response with $response_identifier exists
     *
     * @param $response_identifier ID or Name of the response to be checked
     * @return Response object if response exists and null if not.
     */
    function is_response( $response_identifier = null ) {
        
        if( is_object( $response_identifier ) && $response_identifier->post_type == "response" )
            return $response_identifier;
        
        if( !$response_identifier && get_class( (object)$this->current_response ) == __CLASS__ )
            return $this->current_response;
        
        $response_query = array(
            'post_status' => 'publish',
            'post_type' => 'response',
            'post_parent' => $this->current_assignment->ID,
        );
        
        if ( $response_identifier != null ) {
            if( is_numeric( $response_identifier ) )
                $response_query['p'] = $response_identifier;
            else
                $response_query['name'] = $response_identifier;
        }
        $response = get_posts( $response_query );
        
        if( !empty( $response ) )
            $response = reset( $response );
        else
            return null;
        
        if( isset( $this->current_assignment->form ) )
            $response->form_values = get_post_meta( $response->ID, 'form_values', true );
        
        $response->form = isset( $this->current_assignment->form ) ? $this->current_assignment->form : false;
        
        return $response;
    }
    
    /**
     * has_response( $assignment_id, $author_id)
     *
     * This will check if $author_id has responses to $assignment_id
     * @param Int $assignment_id, the id of the assignment to check
     * @param Int $author_id, the id of the author to check
     * @return Mixed, the response post object, or null on failure
     */
    function has_response( $assignment_id = null, $author_id = null ) {
        global $bp;
        $has_responded = false;
        $responses = null;
        
        if( empty( $assignment_id ) )
            $assignment_id = $this->current_assignment->ID;
        
        if( empty( $author_id ) )
            $author_id = $bp->loggedin_user->id;
        
        $response_authors = get_post_meta( $assignment_id, 'responded_author');
        if( in_array( $author_id, $response_authors ) )
            $has_responded = true;
        
        if( $has_responded ) {
            $responses = get_posts(
                array(
                    'numberposts'   => '-1',
                    'post_parent' => $assignment_id,
                    'author' => $author_id,
                    'post_type' => 'response',
                    'post_status' => 'publish'
                )
            );
            
            $response = reset( $responses );
            if( !empty( $response ) ) {
                $response->form_values = get_post_meta( $response->ID, 'form_values', true );
                return $response;
            }
        }
        return false;
    }
    
    /**
     * check_quiz( $answers, $questions )
     * Verifies user submitted form values and returns a set of wrong answers
     *
     * @param Mixed $answers, an array of form data
     * @param Mixed $questions, an array of question to check
     * @return Mixed, an array of wrong answers
     */
    function check_quiz( $answers, $questions ) {
        $results = array();
        $results['total'] = 0;
        $results['correct'] = 0;
        $answers = stripslashes_deep( $answers );
        $answers = str_replace( '\\', "&#92;", $answers );
        foreach( $questions as $question ) {
            // Find the initial correct question and answer
            if( !is_array( $question['values'] ) ) {
                // The answer is after last ?
                $q_and_a = preg_split( "/\?(?!.*\?)/", $question['values'] );
                $a = trim( end( $q_and_a ) );
                $q = trim( reset( $q_and_a ) );
                $results[ $q ] = array();
                // Find the name of the form
                $name = md5( $q );
                // Correct answers should be counted
                if( !empty( $a ) )
                    $results['total']++;
                // Find the user answer and compare
                if( isset( $answers[ $name ] ) && !empty( $answers[ $name ] ) ) {
                    // Save the wrong answers
                    if( trim( strtolower( $a ) ) != strtolower( $answers[ $name ] ) ) {
                        $results[ $q ][] = esc_html( $answers[ $name ] );
                        $results[ $q ][] = $a;
                    }
                    else
                        $results['correct']++;
                } else {
                    // Save the wrong answer even if no answer was given
                    if( !isset( $answers[ $name ] ) || empty( $answers[ $name ] ) ) {
                        $results[ $q ][] = __( '(No answer)', 'bpsp' );
                        $results[ $q ][] = $a;
                    }
                }
            } else {
                $q = $question['title'];
                $results[ $q ] = array();
                // Find the name of the form
                $name = md5( $q );
                if( $question['cssClass'] == 'checkbox' ) {
                    foreach( $question['values'] as $a ) {
                        // Correct answers should be counted
                        if( $a['baseline'] != 'undefined' )
                            $results['total']++;
                        
                        $cb_name = md5( $q . $a['value'] );
                        if( isset( $answers[ $cb_name ] ) ) {
                            // Save the wrong answer
                            if( $a['baseline'] == 'undefined' ) {
                                $results[ $q ][] = esc_html( $a['value'] );
                                $results[ $q ][] = esc_html( $a['value'] ) . ' ' . __( '(wrong)', 'bpsp' );
                            }
                            else
                                $results['correct']++;
                        } else {
                            // Save the wrong answer even if no answer was given
                            if( $a['baseline'] != 'undefined' ) {
                                $results[ $q ][] = __( '(No answer)', 'bpsp' );
                                $results[ $q ][] = esc_html( $a['value'] );
                            }
                        }
                    }
                } else { // Radios | Select
                    foreach( $question['values'] as $a ) {
                        // Correct answers should be counted
                        if( $a['baseline'] != 'undefined' )
                            $results['total']++;
                        // Check if the answer exists
                        $r_a = false;
                        if( isset( $answers[ $name ] ) )
                            $r_a = $answers[ $name ];
                        
                        // Save the wrong answer if any
                        if( $a['baseline'] != 'undefined' && $question['cssClass'] == 'radio' ) {
                            if( trim( strtolower( $a['value'] ) ) != trim( strtolower( $r_a ) ) ) {
                                $results[ $q ][] = isset( $r_a ) ? $r_a : __( '(No answer)', 'bpsp' );
                                $results[ $q ][] = esc_html( $a['value'] );
                            } else 
                                $results['correct']++;
                        } elseif ( $a['baseline'] != 'undefined' ) { // Select
                            if ( md5( $q . $a['value'] ) != $r_a ) {
                                $results[ $q ][] = __( '(Correct answer below)', 'bpsp' );
                                $results[ $q ][] = esc_html( $a['value'] );
                            } else 
                                $results['correct']++;
                        }
                    }
                }
            }
        }
        $results = array_filter( $results );
        return apply_filters( 'courseware_quiz_results', $results, $answers, $this->current_assignment );
    }
    
    /**
     * new_response_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to add new responses.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function new_response_screen( $vars ) {
        global $bp;
        $nonce_name = 'add_response';
        $form_results = null;
        
        if( !$this->has_student_caps( $bp->loggedin_user->id ) && !is_super_admin() ||
           !bp_group_is_member( $bp->groups->current_group )
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new response.', 'bpsp' );
            return $vars;
        }
        
        // Save new response
        if( isset( $_POST['response'] ) &&
            $this->current_assignment->ID == $_POST['response']['parent_id'] &&
            isset( $_POST['_wpnonce'] )
        ) {
            $new_response = $_POST['response'];
            $new_response_quiz = !empty( $_POST['frmb'] ) ? $_POST['frmb'] : null;
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            $response = $this->has_response();
            if( true != $is_nonce ) 
                $vars['error'] = __( 'Nonce Error while adding a response.', 'bpsp' );
            
            if( !empty( $response ) ) {
                $vars['response'] = $response;
                $vars['error'] = __( 'You already sent your response.', 'bpsp' );
                $this->single_response_screen( $vars );
            } else {
                if( $new_response_quiz ) {
                    $new_response['title'] = bp_core_get_username( $bp->loggedin_user->id )
                        . __( ' on ', 'bpsp' ) . $this->current_assignment->post_title;
                    $new_response['content'] = __( 'Your quiz results: ', 'bpsp' );
                    $form_results = $this->check_quiz( $new_response_quiz, $this->current_assignment->form_data );
                    $new_response['content'] .= ( isset( $form_results['correct'] ) ? $form_results['correct'] : 0 )
                        . '/' . $form_results['total'];
                }
                if( isset( $new_response['title'] ) && isset( $new_response['content'] ) ) {
                    $new_response['title'] = strip_tags( $new_response['title'] );
                    $new_response_id =  wp_insert_post( array(
                        'post_author'   => $bp->loggedin_user->id,
                        'post_title'    => $new_response['title'],
                        'post_content'  => $new_response['content'],
                        'post_status'   => 'publish',
                        'post_type'     => 'response',
                        'post_parent'   => $this->current_assignment->ID
                    ));
                    if( $new_response_id ) {
                        // Save author id in assignment post_meta so we don't have to query it all over
                        add_post_meta( $this->current_assignment->ID, 'responded_author', $bp->loggedin_user->id );
                        add_post_meta( $new_response_id, 'form_values', $form_results );
                        $vars = $this->single_response_screen( $vars );
                        // Leave this for imediate results preview
                        $vars['response']->form_values = $form_results;
                        if( !$this->group_responses_status() )
                            $vars['public'] = true;
                        
                        do_action( 'courseware_response_added', $vars );
                        
                        $vars['message'] = __( 'New response was added.', 'bpsp' );
                        return $vars;
                    } else
                        $vars['error'] = __( 'New response could not be added (fill the title/content).', 'bpsp' );
                }
            }
        }
        
        $vars['name'] = 'add_response';
        $vars['parent_assignment'] = $this->current_assignment;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['trail'] = array(
            $this->current_assignment->lecture->post_title => $this->current_assignment->lecture->permalink,
            $this->current_assignment->post_title => $this->current_assignment->permalink,
            __( 'New Response', 'bpsp' ) => ''
        );
        return $vars;
    }
    
    /**
     * populate_responses( $vars )
     *
     * Hooks into single_assignment_screen
     * Populates $vars with responses
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function populate_responses( $vars ) {
        global $bp;
        
        if( $this->has_student_caps() && bp_group_is_member( $bp->groups->current_group ) )
            $vars['response_add_uri'] = $vars['assignment_permalink'] . '/add_response';
        
        $vars['response'] = $this->has_response();
        $vars['response_permalink'] = $vars['assignment_permalink'] . '/response/';
        
        if( !$this->group_responses_status() || $this->has_response_caps() )
            $vars['responses'] = get_posts(
                array(
                    'numberposts'   => '-1',
                    'post_type' => 'response',
                    'post_status' => 'publish',
                    'post_parent' => $this->current_assignment->ID,
                )
            );
        
        return $vars;
    }
    
    /**
     * single_response_screen( $vars )
     *
     * Hooks into screen_handler
     * Displays a single response screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_response_screen( $vars ) {
        global $bp;
        $nonce_delete_name = 'response_delete';
        $response = null;
        
        if( !empty( $this->current_response ) )
            $response = $this->current_response;
        else
            $response = $this->has_response();
        
        if( $this->group_responses_status() && !$this->has_response_caps() &&
           ( $bp->loggedin_user->id != $response->post_author )
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to access a private response.', 'bpsp' );
            return $vars;
        }
        
        $vars['name'] = 'single_response';
        $vars['assignment_permalink'] = $vars['current_uri'] . '/assignment/' . $this->current_assignment->post_name;
        $vars['assignment'] = $this->current_assignment;
        $vars['response'] = $response; 
        if(  is_super_admin() || $this->has_response_caps() ) {
            $vars['response_delete_permalink'] = $vars['assignment_permalink'] . '/response/' . $response->post_name . '/delete';
            $vars['response_delete_uri'] = add_query_arg( '_wpnonce', wp_create_nonce( $nonce_delete_name ), $vars['response_delete_permalink'] );
        }
        
        $vars['trail'] = array(
            $this->current_assignment->lecture->post_title => $this->current_assignment->lecture->permalink,
            $this->current_assignment->post_title => $this->current_assignment->permalink,
            $response->post_title => ''
        );
        
        return apply_filters( 'courseware_response', $vars );
    }
    
    /**
     * delete_response_screen( $vars )
     *
     * Hooks into screen_handler
     * Delete response screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function delete_response_screen( $vars ) {
        if( is_object( $this->current_response ) )
            $response = $this->current_response;
        else
            $response = $this->is_response( $this->current_response );
        
        $nonce_name = 'response_delete';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        
        if( true != $is_nonce ) {
            $vars['die'] = __( 'Nonce Error while deleting the response.', 'bpsp' );
            return $vars;
        }
        
        if( $this->has_response_caps() || is_super_admin() ) {
            wp_delete_post( $response->ID );
            delete_post_meta( $this->current_assignment->ID, 'responded_author', $response->post_author );
            if( isset( $vars['assignment'] ) )
                $vars = $this->populate_responses( $vars );
        } else {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to delete the response.', 'bpsp' );
            return $vars;
        }
        
        $vars['name'] = 'single_assignment';
        $vars['message'] = __( 'Response deleted successfully.', 'bpsp' );
        return $vars;
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
    }
}
?>
