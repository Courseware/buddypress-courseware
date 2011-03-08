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
        add_action( 'courseware_course_activity', array( &$this, 'activity_for_course' ) );
        add_action( 'courseware_response_added', array( &$this, 'activity_for_response' ) );
        add_action( 'courseware_schedule_activity', array( &$this, 'activity_for_schedule' ) );
        add_action( 'bp_register_activity_actions', array( &$this, 'register_activity_types' ) );
        add_action( 'bp_group_activity_filter_options', array( &$this, 'register_filter_options' ) );
    }
    
    /**
     * register_activity_types()
     *
     * Function registers the activity type for Courseware components
     */
    function register_activity_types() {
        global $bp;
        bp_activity_set_action( $bp->groups->id, 'assignment_add', __( 'New assignment', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'course_add', __( 'New course', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'response_add', __( 'New response', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'schedule_add', __( 'New Schedule', 'bpsp' ) );
    }
    
    /**
     * register_filter_options()
     *
     * Function adds filtering options for activity types for Courseware components
     */
    function register_filter_options() { ?>
        <option value="course_add"><?php _e( 'Show New Courses', 'bpsp' ) ?></option>
        <option value="assignment_add"><?php _e( 'Show New Assignments', 'bpsp' ) ?></option>
        <option value="schedule_add"><?php _e( 'Show Schedule Updates', 'bpsp' ) ?></option>
        <option value="response_add"><?php _e( 'Show New Responses', 'bpsp' ) ?></option>
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
            __( '%s created the assignment %s in %s Courseware:', 'bp'),
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
     * activity_for_course( $course, $type = "add" )
     *
     * Function generates activity updates on course actions
     * @param Object $course of type course
     * @param String $type, the type of action: add - default, on course creations
     */
    function activity_for_course( $course, $type = "add" ){
        global $bp;
        
        $activity_action = sprintf(
            __( '%s created the course %s in %s Courseware:', 'bp'),
            bp_core_get_userlink( $bp->loggedin_user->id ),
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/course/' . $course->post_name .'/">' . attribute_escape( $course->post_title ) . '</a>',
            '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
        );
        $activity_content = bp_create_excerpt( $course->post_content );
        $primary_link = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/assignment/' . $course->post_name . '/';
        
        groups_record_activity(
            array(
                'action' => apply_filters( 'courseware_course_activity_action', $activity_action, $course->ID, $course->post_content, &$course ),
                'content' => apply_filters( 'courseware_course_activity_content', $activity_content, $course->ID, $course->post_content, &$course ),
                'primary_link' => apply_filters( 'courseware_course_activity_primary_link', "{$primary_link}#post-{$course->ID}" ),
                'type' => "course_$type",
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
        
        $response = $response_data['response'];
        
        $activity_action = sprintf(
            __( '%s added a response %s to %s Courseware Assignment:', 'bp'),
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
            __( '%s updated %s Courseware schedule.', 'bp'),
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
