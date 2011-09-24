<?php
/**
 * BPSP Class for activity notifications
 */
class BPSP_Activity {
    
    /**
     * BPSP_Activity()
     *
     * Constructor, adds hooks to existing actions
     */
    function BPSP_Activity() {
        add_action( 'courseware_assignment_activity', array( &$this, 'activity_for_assignment' ) );
        add_action( 'courseware_lecture_activity', array( &$this, 'activity_for_lecture' ) );
        add_action( 'courseware_response_added', array( &$this, 'activity_for_response' ) );
        add_action( 'courseware_schedule_activity', array( &$this, 'activity_for_schedule' ) );
        add_action( 'bp_register_activity_actions', array( &$this, 'register_activity_types' ) );
        add_action( 'bp_group_activity_filter_options', array( &$this, 'register_filter_options' ) );
        add_action( 'bp_member_activity_filter_options', array( &$this, 'register_filter_options' ) );
    }
    
    /**
     * register_activity_types()
     *
     * Function registers the activity type for Courseware components
     */
    function register_activity_types() {
        global $bp;
        bp_activity_set_action( $bp->groups->id, 'assignment_add', __( 'New assignment', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'lecture_add', __( 'New lecture', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'response_add', __( 'New response', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'schedule_add', __( 'New Schedule', 'bpsp' ) );
    }
    
    /**
     * register_filter_options()
     *
     * Function adds filtering options for activity types for Courseware components
     */
    function register_filter_options() { ?>
        <option value="lecture_add"><?php _e( 'New Lectures', 'bpsp' ) ?></option>
        <option value="assignment_add"><?php _e( 'New Assignments', 'bpsp' ) ?></option>
        <option value="schedule_add"><?php _e( 'Schedule Updates', 'bpsp' ) ?></option>
        <option value="response_add"><?php _e( 'New Responses', 'bpsp' ) ?></option>
    <?php }
    
    /**
     * activity_for_assignment( $assignment, $type = "add" )
     *
     * Function generates activity updates on assignment actions
     * @param Object $assignment of type assignment
     * @param String $type, the type of action: add - default, on assignment creations
     */
    function activity_for_assignment( $assignment, $type = "add" ){
        global $bp;
        
        $activity_action = sprintf(
            __( '%s changed the assignment %s in %s Courseware:', 'bpsp' ),
            bp_core_get_userlink( $bp->loggedin_user->id ),
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/assignment/' . $assignment->post_name .'/">' . attribute_escape( $assignment->post_title ) . '</a>',
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
        );
        $activity_content = bp_create_excerpt( $assignment->post_content );
        $primary_link = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/assignment/' . $assignment->post_name . '/';
        
        groups_record_activity(
            array(
                'action' => apply_filters( 'courseware_assignment_activity_action', $activity_action, $assignment->ID, $assignment->post_content, &$assignment ),
                'content' => apply_filters( 'courseware_assignment_activity_content', $activity_content, $assignment->ID, $assignment->post_content, &$assignment ),
                'primary_link' => apply_filters( 'courseware_assignment_activity_primary_link', "{$primary_link}#post-{$assignment->ID}" ),
                'type' => "assignment_$type",
                'item_id' => $bp->groups->current_group->id
            )
        );
    }
    
    /**
     * activity_for_lecture( $lecture, $type = "add" )
     *
     * Function generates activity updates on lecture actions
     * @param Object $course of type course
     * @param String $type, the type of action: add - default, on new lectures
     */
    function activity_for_lecture( $lecture, $type = "add" ){
        global $bp;
        
        $activity_action = sprintf(
            __( '%s changed the lecture %s in %s Courseware:', 'bpsp' ),
            bp_core_get_userlink( $bp->loggedin_user->id ),
            '<a href="' . $lecture->permalink .'/">' . attribute_escape( $lecture->post_title ) . '</a>',
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
        );
        $activity_content = bp_create_excerpt( $lecture->post_content );
        $primary_link = $lecture->permalink;
        
        groups_record_activity(
            array(
                'action' => apply_filters( 'courseware_course_activity_action', $activity_action, $lecture->ID, $lecture->post_content, &$lecture ),
                'content' => apply_filters( 'courseware_course_activity_content', $activity_content, $lecture->ID, $lecture->post_content, &$lecture ),
                'primary_link' => apply_filters( 'courseware_course_activity_primary_link', "{$primary_link}#post-{$lecture->ID}" ),
                'type' => "lecture_$type",
                'item_id' => $bp->groups->current_group->id
            )
        );
    }
    
    /**
     * activity_for_response( $response_data, $type = "add" )
     *
     * Function generates activity updates on response actions
     * @param Mixed $response_data, response details
     * @param String $type, the type of action: add - default, on response creations
     */
    function activity_for_response( $response_data, $type = "add" ){
        global $bp;
        
        if( !isset( $response_data['public'] ) || !$response_data['public'] )
            return;
        
        $response = $response_data['response'];
        
        $activity_action = sprintf(
            __( '%s added a response %s to %s Courseware Assignment:', 'bpsp' ),
            bp_core_get_userlink( $bp->loggedin_user->id ),
            '<a href="' . $response_data['assignment_permalink'] . '/response/' . $response->post_name .'/">' . attribute_escape( $response->post_title ) . '</a>',
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
        );
        $activity_content = bp_create_excerpt( $response->post_content );
        $primary_link = $response_data['assignment_permalink'] . '/response/' . $response->post_name . '/';
        
        groups_record_activity(
            array(
                'action' => apply_filters( 'courseware_response_activity_action', $activity_action, $response->ID, $response->post_content, &$response ),
                'content' => apply_filters( 'courseware_response_activity_content', $activity_content, $response->ID, $response->post_content, &$response ),
                'primary_link' => apply_filters( 'courseware_response_activity_primary_link', "{$primary_link}#post-{$response->ID}" ),
                'type' => "response_$type",
                'item_id' => $bp->groups->current_group->id
            )
        );
    }
    
    /**
     * activity_for_schedule( $schedule_count, $type = "add" )
     *
     * Function generates activity updates on schedule actions
     * @param Int $schedule_count, nr of added schedules
     * @param String $type, the type of action: add - default, on schedule creations
     */
    function activity_for_schedule( $schedule_count, $type = "add" ){
        global $bp;
        
        $activity_action = sprintf(
            __( '%s updated %s Courseware schedule.', 'bpsp' ),
            bp_core_get_userlink( $bp->loggedin_user->id ),
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
        );
        $primary_link = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/schedules/';
        
        groups_record_activity(
            array(
                'action' => apply_filters( 'courseware_schedule_activity_action', $activity_action ),
                'primary_link' => apply_filters( 'courseware_schedule_activity_primary_link', $primary_link ),
                'type' => "schedule_$type",
                'item_id' => $bp->groups->current_group->id
            )
        );
    }
}
?>
