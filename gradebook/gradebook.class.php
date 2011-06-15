<?php
/**
 * BPSP Class for gradebook management
 */
class BPSP_Gradebook {
    /**
     * Gradebook capabilities
     */
    var $caps = array(
        'view_gradebooks',
        'publish_gradebooks',
        'manage_gradebooks',
        'edit_gradebook',
        'edit_gradebooks',
        'delete_gradebook',
        'assign_gradebooks',
        'manage_group_id',
        'manage_assignment_id',
    );
    
    /**
     * Current assignment id
     */
    var $current_assignment = null;
    
    /**
     * BPSP_Gradebook()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Gradebook() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_grade_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_grade_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_action( 'courseware_assignment', array( &$this, 'student_screen' ) );
   }
    
    /**
     * register_post_types()
     *
     * Static function to register the assignments post types, taxonomies and capabilities.
     */
    function register_post_types() {
        $grade_post_def = array(
            'label'                 => __( 'Gradebooks', 'bpsp' ),
            'singular_label'        => __( 'Gradebook', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Gradebook', 'bpsp' ),
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'gradebook',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'author', 'custom-fields' )
        );
        if( !register_post_type( 'gradebook', $grade_post_def ) )
            wp_die( __( 'BuddyPress Courseware error while registering grade post type.', 'bpsp' ) );
        
        $grade_rel_def = array(
            'public'        => BPSP_DEBUG,
            'show_ui'       => BPSP_DEBUG,
            'hierarchical'  => false,
            'label'         => __( 'Assignment ID', 'bpsp'),
            'query_var'     => true,
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_assignment_id',
                'edit_terms'    => 'manage_assignment_id',
                'delete_terms'  => 'manage_assignment_id',
                'assign_terms'  => 'edit_gradebooks'
                )
        );
        
        register_taxonomy( 'assignment_id', array( 'gradebook' ), $grade_rel_def );
        register_taxonomy_for_object_type( 'group_id', 'gradebook' ); //append already registered group_id term
        
        if( !get_taxonomy( 'group_id' ) || !get_taxonomy( 'assignment_id' ) )
            wp_die( __( 'BuddyPress Courseware error while registering grade taxonomies.', 'bpsp' ) );
    }
    
    /**
     * add_grade_caps( $user_id )
     *
     * Adds grade capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_grade_caps( $user_id ) {
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
     * remove_grade_caps( $user_id )
     *
     * Adds grade capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_grade_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_grade_caps( $user_id )
     *
     * Checks if $user_id has grade management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_gradebook_caps( $user_id ) {
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_grade_caps( $user_id );
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
     * Gradebook screens handler.
     * Handles uris like groups/ID/courseware/assignment/args/gradebook
     */
    function screen_handler( $action_vars ) {
        if ( $action_vars[0] == 'assignment' ) {
            
            $current_assignment = BPSP_Assignments::is_assignment( $action_vars[1] );
            
            if( isset ( $action_vars[1] ) && null != $current_assignment )
                $this->current_assignment = $current_assignment;
            else
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            
            if( isset ( $action_vars[2] ) && 'gradebook' == $action_vars[2] && 'clear' == $action_vars[3] )
                add_filter( 'courseware_group_template', array( &$this, 'clear_gradebook_screen' ) );
            elseif( isset ( $action_vars[2] ) && 'gradebook' == $action_vars[2] && 'import' == $action_vars[3] )
                add_filter( 'courseware_group_template', array( &$this, 'import_gradebook_screen' ) );
            elseif( isset ( $action_vars[2] ) && 'gradebook' == $action_vars[2] ) {
                do_action( 'courseware_gradebook_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'gradebook_screen' ) );
            }
        }
    }
    
    /**
     * has_gradebook( $assignment_id = null, $force_creation = true )
     *
     * Checks if $assignment_id has a gradebook
     * 
     * @param String $assignment_id, is assignment identifier
     *  default is null which defaults to $this->current_assignment as $assignment_id
     * @param Bool $force_creation, to force the creation of a gradebook if none exists
     * @return Int the ID of the gradebook
     */
    function has_gradebook( $assignment_id = null, $force_creation = true ) {
        global $bp;
        $gradebook_id = null;
        
        if( !$assignment_id )
            $assignment_id = $this->current_assignment;
        
        $assignment = BPSP_Assignments::is_assignment( $assignment_id );
        if( $assignment ) {
            $gradebook = reset(
                get_children( array(
                    'post_parent' => $assignment->ID,
                    'post_type' => 'gradebook'
                ) )
            );
            if( !empty( $gradebook ) )
                $gradebook_id = $gradebook->ID;
        } else
            return null;
        
        if( !$gradebook_id && $force_creation ) {
            $gradebook_id = wp_insert_post( array(
                    'post_title' => ' ',
                    'post_type' => 'gradebook',
                    'post_status' => 'publish',
                    'post_parent' => $assignment->ID
            ) );
            if( $gradebook_id )
                wp_set_post_terms( $gradebook_id, $bp->groups->current_group->id, 'group_id' );
        }
        
        return $gradebook_id;
    }
    
    /**
     * gen_grade_shortcode( $data )
     *
     * Generates a gradebook entry
     * 
     * @param Mixed Array $data
     * @return String generated shortcode
     */
    function gen_grade_shortcode( $data ) {
        $entry = '[grade ';
        // Build shortcode
        foreach ( $data as $n => $v ) {
            $n = sanitize_key( $n );
            $v = sanitize_text_field( $v );
            $entry .= $n . '="' . $v . '" ';
        }
        $entry .= ']';
        return $entry;
    }
    
    /**
     * load_grades( $gradebook_id )
     *
     * Loads all the grades for a given gradebook
     *
     * @param Int $gradebook_id the ID of the gradebook to load
     * @param Bool $parse, if the results should be parsed using shortcode_parse_atts()
     * @return Mixed, a set of $grades
     */
    function load_grades( $gradebook_id, $parse = false ) {
        if( empty( $gradebook_id ) )
            return false;
        
        $grades = get_post_meta( $gradebook_id, 'grade' );
        
        if( !$parse )
            return $grades;
        else {
            $parsed_grades = array();
            foreach( $grades as $g ) {
                $grade = shortcode_parse_atts( $g );
                $parsed_grades[ $grade['uid'] ] = $grade;
            }
            return $parsed_grades;
        }
    }
    
    /**
     * load_grades( $gradebook_id )
     *
     * Loads all the grades for a given gradebook
     *
     * @param Int $gradebook_id the ID of the gradebook to load
     * @param Bool $parse, if the results should be parsed using shortcode_parse_atts()
     * @return Mixed, $user_id grade or null on failure
     */
    function load_grade_by_user_id( $assignment = null, $user_id = null ) {
        $user_grade = null;
        
        if( empty( $assignment ) )
            $assignment = $this->current_assignment;
        
        if( empty( $user_id ) ) {
            global $bp;
            $user_id = $bp->loggedin_user->id;
        }
        
        $gradebook_id = self::has_gradebook( $assignment, false );
        if( !$gradebook_id )
            return;
        
        $grades = get_post_meta( $gradebook_id, 'grade' );
        if( empty( $grades ) )
            return;
        
        foreach( $grades as $g ) {
            $grade = shortcode_parse_atts( $g );
            if( $grade['uid'] == $user_id )
                $user_grade = $grade;
        }
        
        return $user_grade;
    }
    
    /**
     * save_grade( $gradebook_id, $grade )
     *
     * Adds a new gradebook entry to gradebook
     *
     * @param Int $gradebook_id, the ID of the gradebook
     * @param Mixed $grade, information about grade
     * @return True on success and false on failure.
     */
    function save_grade( $gradebook_id, $grade ) {
        $grade_saved = false;
        
        if( empty( $gradebook_id ) )
            return false;
        
        $grade_shortcode = $this->gen_grade_shortcode( $grade );
        
        $grades = $this->load_grades( $gradebook_id );
        if( empty( $grades ) ) {
            add_post_meta( $gradebook_id, 'grade', $grade_shortcode );
            $grade_saved = true;
        }
        else {
            foreach( $grades as $g ) {
                $g_data = shortcode_parse_atts( $g );
                // Check if we need to update an existing grade
                if( $g_data['uid'] == $grade['uid'] ) {
                    $new_grade = array_merge( $g_data, $grade );
                    $new_grade = array_unique( $new_grade );
                    unset( $new_grade[0] ); // start of shortcode
                    unset( $new_grade[1] ); // end of shortcode
                    $new_grade_shortcode = $this->gen_grade_shortcode( $new_grade );
                    update_post_meta( $gradebook_id, 'grade', $new_grade_shortcode, $g );
                    $grade_saved = true;
                }
            }
            // If no previous entry was found, just add a new one
            if( !$grade_saved )
                add_post_meta( $gradebook_id, 'grade', $grade_shortcode );
        }
        
        return $grade_saved;
    }
    
    /**
     * gradebook_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to assignments for gradebook management.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function gradebook_screen( $vars ) {
        global $bp;
        $nonce_name = 'gradebook_nonce';
        $nonce_import_name = 'gradebook_import_nonce';
        $nonce_clear_name = 'gradebook_clear_nonce';
        
        if( !$this->has_gradebook_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to manage gradebook.', 'bpsp' );
            return $vars;
        }
        
        $students = BP_Groups_Member::get_all_for_group( $bp->groups->current_group->id );
        
        if( isset( $_POST['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
        
        if( isset( $_POST['_wpnonce'] ) && true != $is_nonce ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while updating gradebook.', 'bpsp' );
            return $vars;
        }
        
        $gradebook_id = $this->has_gradebook( $this->current_assignment );
        if( !$gradebook_id ) {
            $vars['die'] =  __( 'BuddyPress Courseware Error while creating gradebook.', 'bpsp' );
            return $vars;
        }
        
        if( !empty( $_POST['grade'] ) ){
            foreach( $_POST['grade'] as $grade )
                if( !empty( $grade ) && !empty( $grade['uid'] ) && !empty( $grade['value'] ) )
                    if( $this->save_grade( $gradebook_id, $grade ) ) {
                        $data = array(
                            'grade' => $grade,
                            'teacher' => $bp->loggedin_user->userdata,
                            'assignment' => $this->current_assignment,
                        );
                        do_action( 'courseware_grade_updated', $data );
                        $vars['message'] = __( 'Gradebook saved.', 'bpsp' );
                    }
        }
        
        $vars['name'] = 'gradebook';
        $vars['students'] = $students['members'];
        if( empty( $vars['grades'] ) )
            $vars['grades'] = $this->load_grades( $gradebook_id, true );
        $vars['bpsp_gradebook_format'] = get_option( 'bpsp_gradebook_format' );
        $vars['assignment'] = BPSP_Assignments::is_assignment( $this->current_assignment );
        $vars['gradebook_permalink'] = $vars['assignment_permalink'] . '/gradebook';
        $vars['clear_gradebook_permalink'] = add_query_arg( '_wpnonce', wp_create_nonce( $nonce_clear_name ), $vars['gradebook_permalink'] . '/clear' );
        $vars['import_gradebook_nonce'] = wp_nonce_field( $nonce_import_name, '_wpnonce', true, false );
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * clear_gradebook_screen( $vars )
     *
     * Hooks into screen_handler
     * Clears the Gradebook for a certain assignment
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function clear_gradebook_screen( $vars ){
        $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], 'gradebook_clear_nonce' );
        if( !$is_nonce ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while clearing gradebook.', 'bpsp' );
            return $vars;
        }
        
        $gradebook_id = $this->has_gradebook( $this->current_assignment );
        if( $gradebook_id ) {
            $grades = $this->load_grades( $gradebook_id );
            foreach ( $grades as $g )
                delete_post_meta( $gradebook_id, 'grade', $g );
            $vars['message'] = __( 'Gradebook was cleared', 'bpsp' );
        }
        return $this->gradebook_screen( $vars );
    }
    
    /**
     * import_gradebook_screen( $vars )
     *
     * Hooks into screen_handler
     * Imports a CSV file data into the gradebook_screen(). It doesn't save anything!
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function import_gradebook_screen( $vars ) {
        $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], 'gradebook_import_nonce' );
        if( !$is_nonce ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while importing gradebook.', 'bpsp' );
            return $this->gradebook_screen( $vars );
        }
        
        $grades = array();
        if( isset( $_FILES['csv_filename'] ) && !empty( $_FILES['csv_filename'] )) {
            require_once 'parseCSV.class.php'; // Load CSV parser
            $csv = new parseCSV();
            $csv->auto( $_FILES['csv_filename']['tmp_name'] );
            
            foreach ( $csv->data as $grade ) {
                $id = bp_core_get_userid_from_nicename( $grade['uid'] );
                if( $id )
                    $grades[$id] = $grade;
            }
            if( count( $csv->data ) == count( $grades ) )
                $vars['message'] = __( 'Data imported successfully, but it is not saved yet! Save this form changes to keep the data.', 'bpsp' );
            else
                $vars['error'] = __( 'File data contains error or entries from other gradebook. Please check again.', 'bpsp' );
        }
        
        $vars['grades'] = $grades;
        $vars['assignment_permalink'] = $vars['assignment_permalink'] . '/gradebook';
        unset( $_POST );
        return $this->gradebook_screen( $vars );
    }
    
    /**
     * gradebook_screen( $vars )
     *
     * Hooks into courseware_assignment
     * If a student is visiting assignment screen, his grade will be shown
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function student_screen( $vars ) {
        global $bp;
        $user_id = null;
        
        if( bp_group_is_member( $bp->current_group->id ) && !bp_group_is_admin() )
            $user_id = $bp->loggedin_user->id;
        
        if( $user_id )
            $vars['user_grade'] = $this->load_grade_by_user_id( $this->current_assignment, $user_id );
        
        $vars['has_gradebook_caps'] = $this->has_gradebook_caps( $bp->loggedin_user->id );
        return $vars;
    }
}
?>
