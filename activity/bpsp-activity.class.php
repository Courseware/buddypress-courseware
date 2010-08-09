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
        bp_activity_set_action( $bp->groups->id, 'assignment_add', __( 'Assignment adds', 'bpsp' ) );
        bp_activity_set_action( $bp->groups->id, 'assignment_update', __( 'Assignment updates', 'bpsp' ) );
    }
    
    /**
     * register_filter_options()
     *
     * Function adds filtering options for activity types for Courseware components
     */
    function register_filter_options() { ?>
        <option value="assignment_add"><?php _e( 'Show Assignment Adds', 'bpsp' ) ?></option>
        <option value="assignment_update"><?php _e( 'Show Assignment Updates', 'bpsp' ) ?></option>
    <?php }
    
    /**
     * activity_for_assignment( $assignment, $type = "add" )
     *
     * Function generates activity updates on assignment actions
     * @param Object $assignment of type assignment
     * @param String $type, the type of action: add - default, on assignment creations, update - on assignment edits
     */
    function activity_for_assignment( $assignment, $type ){
        global $bp;
        
        if( $type == "update" )
            $activity_action = sprintf(
                __( '%s updated assignment %s in %s Courseware:', 'bp'),
                bp_core_get_userlink( $bp->loggedin_user->id ),
                '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/assignment/' . $assignment->post_name .'/">' . attribute_escape( $assignment->post_title ) . '</a>',
                '<a href="' . bp_get_group_permalink( $bp->groups->current_group ) . '">' . attribute_escape( $bp->groups->current_group->name ) . '</a>'
            );
        else
            $activity_action = sprintf(
                __( '%s created assignment %s in %s Courseware:', 'bp'),
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
}
?>