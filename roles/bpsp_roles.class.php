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
        add_filter( 'bp_get_the_profile_field_options_radio', array( &$this, 'profile_screen_admin' ) );
        add_filter( 'bp_get_the_profile_field_options_radio', array( &$this, 'profile_screen_hide_roles' ) );
        add_action( 'xprofile_profile_field_data_updated', array( &$this, 'profile_screen_update' ), 10, 2 );
    }
    
    /**
     * profile_screen_update()
     * 
     * Action to notify site_admin that a new request has been sent
     */
    function profile_screen_update( $field_id, $field_value ) {
        
        //required to search for superadmins
        require_once( ABSPATH . 'wp-admin/includes/user.php');
        
        global $bp;
        if( $field_value == __( 'Apply for Teacher', BPSP_TD ) ) {
            $users_search = new WP_User_Search( null, null, 'administrator' );
            $superadmins = $users_search->get_results();
            $content = $this->request_message( $bp->loggedin_user->id, true );
            $subject = $this->request_message( $bp->loggedin_user->id, false, true );
            messages_new_message(
                array(
                    'recipients' => $superadmins,
                    'subject' => $subject,
                    'content' => $content,
                )
            );
        }
        if( $field_value == __( 'Teacher', BPSP_TD ) && !is_super_admin() )
            wp_die( __( 'BuddyPress ScholarPress error, you are not allowed to assign Teachers.' ) );
    }
    
    /**
     * request_message($user_id, $body = false, $subject = false )
     * 
     * Generated content for new request messages.
     * 
     * @param $user_id the user id of the sender and request author
     * @param bool $body if true generates the message body content
     * @param bool $subject if true generates the message subject content
     * @return String $content the generated message content
     */
    function request_message( $user_id, $body = false, $subject = false ) {
        $userdata = bp_core_get_core_userdata( $user_id );
        $content = null;
        if( $subject )
            $content = $userdata->user_nicename . __( ' applied to become a teacher. Please review.', BPSP_TD );
        if( $body ) {
            $fields_group_id = $this->field_group_id_from_name( __( 'ScholarPress LMS', BPSP_TD ) );
            $admin_url = $userdata->user_url . 'profile/edit/group/' . $fields_group_id;
            $content = $userdata->user_nicename;
            $content.= __( ' applied to become a teacher. To review his profile, please follow the link below.', BPSP_TD );
            $content.= "\n";
            $content.= __( 'Profile review link: ', BPSP_TD );
            $content.= $admin_url;
        }
        return $content;
    }
    
    /**
     * profile_screen_hide_roles()
     * 
     * Filters intermediate roles like 'Applied for Teacher'
     * if a user has teaching role assigned already.
     */
    function profile_screen_hide_roles( $content ) {
        global $bp;
        $user_field_data = xprofile_get_field_data( __( 'Role'), $bp->loggedin_user->id );
        if( !is_super_admin() &&
            $user_field_data == __( 'Teacher', BPSP_TD ) &&
            stristr( $content, 'value="' . __( 'Apply for Teacher', BPSP_TD ) )
        )
            $content = '';
        
        return $content;
    }
    
    /**
     * profile_screen_admin()
     * 
     * Filters option for 'Teacher', only admins are allowed to access it.
     */
    function profile_screen_admin( $content ) {
        if( !is_super_admin() ) {
            if( stristr( $content, 'value="' . __( 'Teacher', BPSP_TD ) ) &&
                !stristr( $content, 'checked="checked"' )
            )
                $content = '';
        }
        return $content;
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
        
        if( $this->field_group_id_from_name( __( 'ScholarPress LMS', BPSP_TD ) ) )
            return false;
        
        $bpsp_group_id = xprofile_insert_field_group(
            array(
                name        => __( 'ScholarPress LMS', BPSP_TD ),
                description => __( 'Students and Teachers fields. Do not delete as long as you use BuddyPress ScholarPress!', BPSP_TD ),
                can_delete  => false
            )
        );
        if( !$bpsp_group_id )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile group.', BPSP_TD ) );
        
        /* Create the radio buttons */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                name            => __( 'Role', BPSP_TD ),
                can_delete      => false,
                description     => __( 'You role when using ScholarPress. Every request requires moderation. Please be patient untill an administrator reviews it.', BPSP_TD ),
                is_required     => false,
                type            => 'radio'
            )
        );
        $bpsp_field_id = xprofile_get_field_id_from_name( __( 'Role', BPSP_TD ) );
        if( !$bpsp_field_id )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile field.', BPSP_TD ) );
            
        /* Create the radio options */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                parent_id       => $bpsp_field_id,
                name            => __( 'Teacher', BPSP_TD ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        xprofile_insert_field(
            array (
                field_group_id      => $bpsp_group_id,
                parent_id           => $bpsp_field_id,
                name                => __( 'Student', BPSP_TD ),
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
                name            => __( 'Apply for Teacher', BPSP_TD ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        if( !xprofile_get_field_id_from_name( __( 'Teacher' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Student' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Apply for Teacher', BPSP_TD ) )
        )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile field options.', BPSP_TD ) );
            
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
}

?>