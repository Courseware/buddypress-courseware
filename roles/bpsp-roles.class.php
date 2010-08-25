<?php
/**
 * BPSP Class for student/teacher roles management
 */
class BPSP_Roles {    
    /**
     * BPSP_Roles()
     * 
     * Constructor. Loads all the filters and actions.
     */
    function BPSP_Roles() {
        add_filter( 'bp_xprofile_field_get_children', array( &$this, 'profile_screen_admin' ) );
        add_filter( 'bp_xprofile_field_get_children', array( &$this, 'profile_screen_hide_roles' ) );
        add_action( 'xprofile_profile_field_data_updated', array( &$this, 'profile_screen_new_request' ), 10, 2 );
    }
    
    /**
     * profile_screen_new_request()
     * 
     * Action to notify site_admin that a new request has been sent
     */
    function profile_screen_new_request( $field_id, $field_value ) {
        //required to search for superadmins
        require_once ABSPATH . 'wp-admin/includes/user.php';
        
        global $bp;
        if( $field_value == __( 'Apply for Teacher', 'bpsp' ) ) {
            $users_search = new WP_User_Search( null, null, 'administrator' );
            $superadmins = $users_search->get_results();
            $content = $this->request_message( $bp->loggedin_user->userdata, true );
            $subject = $this->request_message( $bp->loggedin_user->userdata, false, true );
            if( !is_super_admin() )
                messages_new_message(
                    array(
                        'recipients' => $superadmins,
                        'subject' => $subject,
                        'content' => $content,
                    )
                );
        }
        if( $field_value == __( 'Teacher', 'bpsp' ) && !is_super_admin() )
            wp_die( __( 'BuddyPress Courseware error, you are not allowed to assign Teachers.' ) );
        
        // Add an action every time a new teacher is added
        if( $field_value == __( 'Teacher', 'bpsp' ) && is_super_admin() )
            do_action( 'courseware_new_teacher_added', $bp->displayed_user->id );
        
        // Add an action every time a teacher is removed
        if( $field_value != __( 'Teacher', 'bpsp' ) )
            do_action( 'courseware_new_teacher_removed', $bp->displayed_user->id );
    }
    
    /**
     * request_message($user_id, $body = false, $subject = false )
     * 
     * Generated content for new request messages.
     * 
     * @param Mixed $userdata the user object of the sender and request author
     * @param bool $body if true generates the message body content
     * @param bool $subject if true generates the message subject content
     * @return String $content the generated message content
     */
    function request_message( $userdata, $body = false, $subject = false ) {
        $content = null;
        if( $subject )
            $content = sprintf( __( 'Please review %s application to become a teacher.', 'bpsp' ), $userdata->user_nicename );
        if( $body ) {
            $fields_group_id = $this->field_group_id_from_name( __( 'Courseware', 'bpsp' ) );
            $admin_url = $userdata->user_url . 'profile/edit/group/' . $fields_group_id;
            $content = $userdata->user_nicename;
            $content.= __( " applied to become a teacher.", 'bpsp' );
            $content.= "\n";
            $content.= __( "To review the profile, follow the link below.", 'bpsp' );
            $content.= "\n";
            $content.= $admin_url . print_r($userdata ) ;
        }
        return $content;
    }
    
    /**
     * profile_screen_hide_roles()
     * 
     * Filters intermediate roles like 'Applied for Teacher'
     * if a user has teaching role assigned already.
     */
    function profile_screen_hide_roles( $options ) {
        global $bp;
        $user_field_data = xprofile_get_field_data( __( 'Role'), $bp->loggedin_user->id );
        for( $i = 0; $i < count( $options ); $i++ ) {
            if( !is_super_admin() &&
                $user_field_data == __( 'Teacher', 'bpsp' ) &&
                $options[$i]->name == __( 'Apply for Teacher', 'bpsp' ) )
                unset( $options[$i] );
        }
        return array_merge( $options );
    }
    
    /**
     * profile_screen_admin()
     * 
     * Filters option for 'Teacher', only admins are allowed to access it.
     */
    function profile_screen_admin( $options ) {
        for( $i = 0; $i < count( $options ); $i++ ) {
            if( !is_super_admin() &&
                $options[$i]->name == __( 'Teacher', 'bpsp' ) &&
                BP_XProfile_ProfileData::get_value_byid($options[$i]->parent_id) != __( 'Teacher', 'bpsp' ) ) 
                unset( $options[$i] );
        }
        return array_merge( $options );
    }
    
    /**
     * profile_fields_screen()
     *
     * Adds extra field to profile screen
     */
    function register_profile_fields() {
        //HACK: to allow options
        global $bp;
        (array)$bp->profile->field_types[] = 'option';
        
        if( BPSP_Roles::field_group_id_from_name( __( 'Courseware', 'bpsp' ) ) )
            return false;
        
        $bpsp_group_id = xprofile_insert_field_group(
            array(
                name        => __( 'Courseware', 'bpsp' ),
                description => __( 'Students and Teachers fields. Do not delete as long as you use BuddyPress ScholarPress Courseware!', 'bpsp' ),
                can_delete  => false
            )
        );
        if( !$bpsp_group_id )
            wp_die( __( 'BuddyPress Courseware error when saving xProfile group.', 'bpsp' ) );
        
        /* Create the radio buttons */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                name            => __( 'Role', 'bpsp' ),
                can_delete      => false,
                description     => __( 'You role when using Courseware. Every request requires moderation. Please be patient untill an administrator reviews it.', 'bpsp' ),
                is_required     => false,
                type            => 'radio'
            )
        );
        $bpsp_field_id = xprofile_get_field_id_from_name( __( 'Role', 'bpsp' ) );
        if( !$bpsp_field_id )
            wp_die( __( 'BuddyPress Courseware error when saving xProfile field.', 'bpsp' ) );
            
        /* Create the radio options */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                parent_id       => $bpsp_field_id,
                name            => __( 'Teacher', 'bpsp' ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        xprofile_insert_field(
            array (
                field_group_id      => $bpsp_group_id,
                parent_id           => $bpsp_field_id,
                name                => __( 'Student', 'bpsp' ),
                can_delete          => false,
                is_required         => false,
                type                => 'option',
                is_default_option   => true
            )
        );
        
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                parent_id       => $bpsp_field_id,
                name            => __( 'Apply for Teacher', 'bpsp' ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        if( !xprofile_get_field_id_from_name( __( 'Teacher' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Student' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Apply for Teacher', 'bpsp' ) )
        )
            wp_die( __( 'BuddyPress Courseware error when saving xProfile field options.', 'bpsp' ) );
            
        return true;
    }
    
    /**
     * field_group_id_from_name( $group_name )
     *
     * Searches for a profile field group by it's name
     *
     * @param String $group_name the name of the field group to be found.
     * @return Int $group_id the id of the found field group
     */
    function field_group_id_from_name( $group_name ) {
        $group_id = null;
        $groups = BP_XProfile_Group::get();
        foreach( $groups as $g )
            if( $g->name == $group_name )
                $group_id = $g->id;
        
        return $group_id;
    }
    
    /**
     * is_teacher( $user_id )
     *
     * Checks if $user_id is a teacher
     * @param Int $user_id, user ID to check for
     * @return Bool, true if is a teacher and false on failure
     */
    function is_teacher( $user_id ) {
        if( __( 'Teacher', 'bpsp') == xprofile_get_field_data( __( 'Role'), $user_id ) )
            return true;
        else
            return false;
    }
    
    /**
     * get_teachers( $group_id )
     *
     * Returns an array with group teachers user_id
     * @param Int $group_id, the group ID to check for
     * @return Mixed, an array of user_id's
     */
    function get_teachers( $group_id ) {
        $teachers = array();
        
        $group_admins = groups_get_group_admins( $group_id );
        foreach ( $group_admins as $admin )
            if( self::is_teacher( $admin->user_id ) )
                $teachers[] = $admin->user_id;
        
        return $teachers;
    }
}

?>