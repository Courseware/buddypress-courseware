<?php
/**
 * BPSP Class for schedules management
 */
class BPSP_Schedules {
    /**
     * Schedules capabilities
     */
    var $caps = array(
        'view_schedules',
        'publish_schedules',
        'manage_schedules',
        'edit_schedule',
        'edit_schedules',
        'delete_schedule',
        'assign_schedules',
        'manage_group_id',
        'manage_course_id',
    );
    
    /**
     * Current course id
     */
    var $current_schedule = null;
    
    /**
     * BPSP_Courses()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Schedules() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_schedule_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_schedule_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
   }
    
    /**
     * register_post_types()
     *
     * Static function to register the schedules post types, taxonomies and capabilities.
     */
    function register_post_types() {
        $schedule_post_def = array(
            'label'                 => __( 'Schedules', 'bpsp' ),
            'singular_label'        => __( 'Schedule', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Schedules', 'bpsp' ),
            'public'                => true, //TODO: set to false when stable
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => true, //TODO: set to false when stable
            'capability_type'       => 'schedule',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'author', 'custom-fields' )
        );
        if( !register_post_type( 'schedule', $schedule_post_def ) )
            wp_die( __( 'BuddyPress Courseware error while registering schedule post type.', 'bpsp' ) );
        
        register_taxonomy_for_object_type( 'group_id', 'schedule' ); //append already registered group_id term
        register_taxonomy_for_object_type( 'course_id', 'schedule' ); //append already registered course_id term
        
        if( !get_taxonomy( 'group_id' ) || !get_taxonomy( 'course_id' ) )
            wp_die( __( 'BuddyPress Courseware error while registering schedule taxonomies.', 'bpsp' ) );
    }
    
    /**
     * add_schedule_caps( $user_id )
     *
     * Adds schedule capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_schedule_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_schedules' ) )
                $user->add_cap( 'edit_others_schedules' );
    }
    
    /**
     * remove_schedule_caps( $user_id )
     *
     * Adds schedule capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_schedule_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_schedule_caps( $user_id )
     *
     * Checks if $user_id has schedule management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_schedule_caps( $user_id ) {
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_schedule_caps( $user_id );
        }
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $is_ok = false;
        
        if( get_option( 'bpsp_allow_only_admins' ) )
            if( !bp_group_is_admin() )
                $is_ok = false;
        
        return $is_ok;
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Schedules screens handler.
     * Handles uris like groups/ID/courseware/schedule/args
     */
    function screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_schedule' ) {
            add_action( 'bp_head', array( BPSP_Static, schedules_enqueues ));
            add_filter( 'courseware_group_template', array( &$this, 'new_schedule_screen' ) );
        }
        
        elseif ( $action_vars[0] == 'schedule' ) {
            if( isset ( $action_vars[1] ) && null != $this->is_schedule( $action_vars[1] ) )
                $this->current_schedule = $action_vars[1];
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                add_action( 'bp_head', array( BPSP_Static, schedules_enqueues ));
                add_filter( 'courseware_group_template', array( &$this, 'edit_schedule_screen' ) );
            }
            elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_schedule_screen' ) );
            }
            else
                add_filter( 'courseware_group_template', array( &$this, 'single_schedule_screen' ) );
        }
        elseif ( $action_vars[0] == 'schedules' )
            add_filter( 'courseware_group_template', array( &$this, 'list_schedules_screen' ) );
    }
    
    /**
     * is_schedule( $schedule_identifier )
     *
     * Checks if a schedule with $schedule_identifier exists
     *
     * @param $course_identifier ID or Name of the schedule to be checked
     * @return Course object if schedule exists and null if not.
     */
    function is_schedule( $schedule_identifier = null ) {
        global $bp;
        
        if( !$schedule_identifier )
            $this->current_schedule;
        
        $schedule_query = array(
            'post_type' => 'schedule',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if( is_numeric( $schedule_identifier ) )
            $schedule_query['p'] = $schedule_identifier;
        else
            $schedule_query['name'] = $schedule_identifier;
        
        $schedule = get_posts( $schedule_query );
        
        if( !empty( $schedule[0] ) )
            $schedule = $schedule[0];
        else
            return null;
        
        $schedule->start_date = get_post_meta( $schedule->ID, 'start_date', true );
        $schedule->end_date = get_post_meta( $schedule->ID, 'end_date', true );
        $schedule->location = get_post_meta( $schedule->ID, 'location', true );
        $course_id = wp_get_object_terms( $schedule->ID, 'course_id' );
        if( !empty( $course_id ) )
            $schedule->course = BPSP_Courses::is_course( $course_id[0]->name );
        
        return $schedule;
    }
    
    /**
     * add_nav_options()
     *
     * Adds schedule specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function add_nav_options( $options ) {
        global $bp;
        
        if( $this->has_schedule_caps( $bp->loggedin_user->id ) || is_super_admin() )
            $options[__( 'New Schedule', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_schedule';
        
        $options[__( 'Schedules', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/schedules';
        return $options;
    }
    
    /**
     * datecheck( $starttime, $endtime, $repetition = null )
     *
     * Performs some verifications to ensure its a period
     * @param String $starttime a formated date string
     * @param String $endtime a formated date string
     * @param Mixed $repetition, default is null, an array (interval (day, week, month), count (Int))
     * @return Bool true on success and false on failure, Mixed set of intervals on $repetition
     */
    function datecheck( $starttime, $endtime, $repetition = null ){
        $starttime_t = strtotime( $starttime );
        $endtime_t = strtotime( $endtime );
        $rep_t = 0;
        $periods = null;
        $date_format = "Y-m-d H:i:s";
        
        // timestamps are valid
        if( $starttime_t == 0 || $endtime_t == 0 )
            return false;
        // timestamp creates a period
        if( $endtime_t < $starttime_t )
            return false;
        // generate periods
        if( is_array( $repetition ) ) {
            if( !in_array( $repetition['interval'], array( 'day', 'week', 'month' )) ||
                !is_numeric( $repetition['count'])
            )
                return false;
            else
                $interval = $repetition['interval'];
            
            for( $i = 1; $i <= $repetition['count']; $i++ )
                $periods[] = array(
                    'start_date' => date( $date_format, strtotime( "+$i $interval", $starttime_t )),
                    'end_date' => date( $date_format, strtotime( "+$i $interval", $endtime_t )),
                );
        }
        
        if( !empty( $periods ) )
            return $periods;
        else
            return true;
    }
    
    /**
     * new_schedule_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to add new schedules.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function new_schedule_screen( $vars ) {
        global $bp;
        $nonce_name = 'new_schedule';
        $repeats = null;
        
        if( !$this->has_schedule_caps( $bp->loggedin_user->id ) || !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new course.' );
            return $vars;
        }
        
        // Save new schedule
        if( isset( $_POST['schedule'] ) && $_POST['schedule']['object'] == 'group' && isset( $_POST['_wpnonce'] ) ) {
            $new_schedule = $_POST['schedule'];
            
            if( !empty( $new_schedule['repetition'] ) && !empty( $new_schedule['repetition_times'] ) )
                $repeats = array(
                    'interval' => $new_schedule['repetition'],
                    'count' => $new_schedule['repetition_times']
                );
            else
                $repeats = null;
                
            $valid_dates = $this->datecheck( $new_schedule['start_date'], $new_schedule['end_date'], $repeats );
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce ) 
                $vars['error'] = __( 'Nonce Error while adding a schedule.', 'bpsp' );
            else
                if( !empty( $new_schedule['desc'] ) &&
                    !empty( $new_schedule['group_id'] ) &&
                    $valid_dates &&
                    $is_nonce
                ) {
                    // create a template
                    $first_schedule = array(
                        'post_author'   => $bp->loggedin_user->id,
                        'post_title'    => sanitize_text_field( $new_schedule['desc'] ),
                        'post_status'   => 'publish',
                        'post_type'     => 'schedule',
                        'cw_group_id'   => $new_schedule['group_id'],
                        'cw_start_date' => $new_schedule['start_date'],
                        'cw_end_date'   => $new_schedule['end_date'],
                        'cw_location'   => sanitize_text_field( $new_schedule['location'] ),
                    );
                    if( BPSP_Courses::is_course( $new_schedule['course_id'] ) )
                        $first_schedule['cw_course_id'] = $new_schedule['course_id'];
                    
                    // store first schedule
                    $new_schedules[] = array_filter( $first_schedule );
                    
                    // generate repeated events
                    if( is_array( $valid_dates ) ) {
                        foreach( $valid_dates as $p ) {
                            $schedule_copy = reset( $new_schedules );
                            $schedule_copy['post_title'] .= __( ' for ', 'bpsp' ) . $p['start_date'];
                            $schedule_copy['cw_start_date'] = $p['start_date'];
                            $schedule_copy['cw_end_date'] = $p['end_date'];
                            $new_schedules[] = $schedule_copy;
                        }
                    }
                    // Add all schedules
                    foreach( $new_schedules as $newschedule ) {
                        $newschedule_id = wp_insert_post( $newschedule );
                        
                        if( $newschedule_id ) {
                            wp_set_post_terms( $newschedule_id, $newschedule['cw_group_id'], 'group_id' );
                            if( $newschedule['cw_course_id'] )
                                wp_set_post_terms( $newschedule_id, $newschedule['cw_course_id'], 'course_id' );
                            add_post_meta( $newschedule_id, 'start_date', $newschedule['cw_start_date'] );
                            add_post_meta( $newschedule_id, 'end_date', $newschedule['cw_end_date'] );
                            if( $newschedule['cw_location'] )
                                add_post_meta( $newschedule_id, 'location', $newschedule['cw_location'] );
                        }
                    }
                    
                    $vars['message'] = __( 'New schedule was added.', 'bpsp' );
                    return $this->list_schedules_screen( $vars );
                } else
                    $vars['error'] = __( 'New schedule could not be added.', 'bpsp' );
            }
        
        $vars['name'] = 'new_schedule';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['courses'] = BPSP_Courses::has_courses( $bp->groups->current_group->id );
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * list_schedules_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to list schedules.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function list_schedules_screen( $vars ) {
        global $bp;
        $schedules = get_posts( array(
            'post_type' => 'schedule',
            'group_id' => $bp->groups->current_group->id,
            'numberposts' => get_option( 'posts_per_page', '10' ),
        ));
        
        $vars['name'] = 'list_schedules';
        $vars['schedules_hanlder_uri'] = $vars['current_uri'] . '/schedule/';
        $vars['schedules'] = $schedules;
        return $vars;
    }
    
    /**
     * single_schedule_screen( $vars )
     *
     * Hooks into screen_handler
     * Displays a single schedule screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_schedule_screen( $vars ) {
        global $bp;
        $schedule = $this->is_schedule( $this->current_schedule );
        
        if(  $schedule->post_author == $bp->loggedin_user->id || is_super_admin() )
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        $vars['name'] = 'single_schedule';
        $vars['schedule_permalink'] = $vars['current_uri'] . '/schedule/' . $this->current_schedule;
        $vars['schedule_edit_uri'] = $vars['current_uri'] . '/schedule/' . $this->current_schedule . '/edit';
        $vars['schedule'] = $schedule;
        return $vars;
    }
    
    /**
     * delete_schedule_screen( $vars )
     *
     * Hooks into screen_handler
     * Delete schedule screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function delete_schedule_screen( $vars ) {
        global $bp;
        $schedule = $this->is_schedule( $this->current_schedule );
        $nonce_name = 'delete_schedule';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        
        if( true != $is_nonce ) {
            $vars['error'] = __( 'BuddyPress Courseware Nonce Error while deleting a schedule.', 'bpsp' );
            return $this->list_schedules_screen( $vars );
        }
        
        if(  ( $schedule->post_author == $bp->loggedin_user->id ) || is_super_admin() ) {
            wp_delete_post( $schedule->ID );
        } else {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to delete a schedule.', 'bpsp' );
            return $vars;
        }
        
        $vars['message'] = __( 'Schedule deleted successfully.', 'bpsp' );
        return $this->list_schedules_screen( $vars );
    }
    
    /**
     * edit_schedule_screen( $vars )
     *
     * Hooks into screen_handler
     * Edit schedule screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_schedule_screen( $vars ) {
        global $bp;
        $nonce_name = 'edit_schedule';
        
        $old_schedule = $this->is_schedule( $this->current_schedule );
        $old_schedule->terms = wp_get_object_terms($old_course->ID, 'group_id' );
        
        if( !$this->has_schedule_caps( $bp->loggedin_user->id ) &&
            $bp->loggedin_user->id != $old_schedule->post_author &&
            $bp->groups->current_group->id != $old_schedule->terms[0]->name &&
            !is_super_admin()
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to update the schedule.', 'bpsp' );
            return $vars;
        }
        
        // Update schedule
        if( isset( $_POST['schedule'] ) && $_POST['schedule']['object'] == 'group' && isset( $_POST['_wpnonce'] ) ) {
            $updated_schedule = $_POST['schedule'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce )
                $vars['error'] = __( 'Nonce Error while editing a schedule.', 'bpsp' );
            else 
                if( !empty( $updated_schedule['desc'] ) &&
                    !empty( $updated_schedule['start_date'] ) &&
                    !empty( $updated_schedule['end_date'] ) &&
                    !empty( $updated_schedule['group_id'] ) &&
                    $this->datecheck( $updated_schedule['start_date'], $updated_schedule['end_date'] )
                ) {
                    $updated_schedule_id =  wp_update_post( array(
                        'ID'            => $old_schedule->ID,
                        'post_title'    => sanitize_text_field( $updated_schedule['desc'] ),
                    ));
                    
                    if( $updated_schedule_id ) {
                        if( !empty( $updated_schedule['course_id'] ) && BPSP_Courses::is_course( $updated_schedule['course_id'] ) )
                            wp_set_post_terms( $updated_schedule_id, $updated_schedule['course_id'], 'course_id' );
                        elseif( empty( $updated_schedule['course_id'] ) )
                            wp_set_post_terms( $updated_schedule_id, '', 'course_id' );
                        update_post_meta( $updated_schedule_id, 'start_date', $updated_schedule['start_date'], $old_schedule->start_date );
                        update_post_meta( $updated_schedule_id, 'end_date', $updated_schedule['end_date'], $old_schedule->end_date );
                        if( !empty( $updated_schedule['location'] ) )
                            if( $old_schedule->location )
                                update_post_meta( $updated_schedule_id, 'location', $updated_schedule['location'], $old_schedule->location );
                            else
                                add_post_meta( $updated_schedule_id, 'location', $updated_schedule['location'] );
                        $vars['message'] = __( 'Schedule was updated.', 'bpsp' );
                    }
                    else
                        $vars['error'] = __( 'Schedule could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_schedule';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['courses'] = BPSP_Courses::has_courses( $bp->groups->current_group->id );
        $vars['schedule'] = $this->is_schedule( $old_schedule->ID );
        $vars['schedule_edit_uri'] = $vars['current_uri'] . '/schedule/' . $this->current_schedule . '/edit';
        $vars['schedule_delete_uri'] = $vars['current_uri'] . '/schedule/' . $this->current_schedule . '/delete';
        $vars['schedule_delete_title'] = __( 'Delete Course', 'bpsp' );
        $vars['schedule_permalink'] = $vars['current_uri'] . '/schedule/' . $this->current_schedule;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_schedule' ), $vars['schedule_delete_uri'] );
        return $vars;
    }
}
?>