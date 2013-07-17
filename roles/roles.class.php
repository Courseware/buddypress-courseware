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
     * profile_screen_new_request( $field_id, $field_value )
     * 
     * Action to notify site_admin that a new request has been sent
     * @param Int $field_id, the id of the xprofile field
     * @param String $field_value, the value of the field
     */
    function profile_screen_new_request( $field_id, $field_value ) {
        global $bp;
        if( $field_value == __( 'Apply for Teacher', 'bpsp' ) ) {
            $superadmins = self::get_admins();
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
            wp_die( __( 'BuddyPress Courseware error, you are not allowed to assign Teachers.', 'bpsp' ) );
        
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
            if( empty( $userdata->user_url ) )
                $userdata->user_url = bp_core_get_userlink( $userdata->ID, false, true );
            $fields_group_id = $this->field_group_id_from_name( __( 'Courseware', 'bpsp' ) );
            $admin_url = $userdata->user_url . 'profile/edit/group/' . $fields_group_id;
            $content = $userdata->user_nicename;
            $content.= __( " applied to become a teacher.", 'bpsp' );
            $content.= "\n";
            $content.= __( "To review the profile, follow the link below.", 'bpsp' );
            $content.= "\n";
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
    function profile_screen_hide_roles( $options ) {
        global $bp;
        $user_field_data = xprofile_get_field_data( __( 'Role', 'bpsp' ), $bp->loggedin_user->id );
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
                description     => __( 'Your role when using Courseware. Every request requires moderation. Please be patient until an administrator reviews it.', 'bpsp' ),
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
        
        if( !xprofile_get_field_id_from_name( __( 'Teacher', 'bpsp' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Student', 'bpsp' ) ) ||
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
        if( __( 'Teacher', 'bpsp') == xprofile_get_field_data( __( 'Role', 'bpsp' ), $user_id ) )
            return true;
        else
            return false;
    }
    
    /**
     * can_teach( $user_id = null )
     *
     * This will check if current user is allowed to manage courseware for current group
     * @param Int $user_id, the id of the user to check
     * @return Bool, true or false
     */
    function can_teach( $user_id = null ) {
        global $bp;
        
        if( !$user_id )
            $user_id = $bp->loggedin_user->id;
        
        $is_admin = false;
        
        if( !BPSP_Groups::courseware_status() )
            return false;
        
        if( is_super_admin( $user_id ) )
            return true;
        
        if( self::is_teacher( $user_id) )
            $is_admin = true;
        
        if( !get_option( 'bpsp_allow_only_admins' ) && !bp_group_is_admin() )
            $is_admin = false;
            
        return $is_admin;
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
        $group_members = groups_get_group_members( $group_id );
        $group_members = array_merge( $group_admins, (array)$group_members['members'] );

        foreach ( $group_members as $member )
            if( self::can_teach( $member->user_id ) )
                $teachers[] = $member->user_id;

        return $teachers;
    }
    
    /**
     * get_admins()
     *
     * A wrapper for WordPress 3.1 `get_users()` with backcompat
     * @return Mixed, an array of objects
     */
    function get_admins() {
        $admins = array();
        
        if( function_exists( 'get_users' ) ) {
            $superadmins = get_users( array( 'role' => 'administrator' ) );
            if( $superadmins )
                foreach( $superadmins as $su )
                    $admins[] = $su->ID;
                
        } else {
            //required to search for superadmins
            require_once ABSPATH . 'wp-admin/includes/user.php';
            $users_search = new WP_User_Search( null, null, 'administrator' );
            $admins = $users_search->get_results();
        }
        
        return $admins;
    }
}

?>
