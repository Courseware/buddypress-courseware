<?php
/**
 * BPSP Class for student/teacher roles management
 */

class BPSP_Roles {
    /**
     * BPSP_Roles()
     * Loads all the filters and actions
     */
    function BPSP_Roles() {
        add_filter( 'bp_get_the_profile_field_options_radio', array( &$this, 'profile_screen_admin' ) );
        add_action( 'xprofile_profile_field_data_updated', array( &$this, 'profile_screen_update' ), 10, 2 );
    }
    
    /**
     * profile_screen_update()
     * Trigger to notify site_admin that a new request has been sent
     */
    function profile_screen_update( $field_id, $field_value ) {
        if( $field_value == __( 'Apply for Teacher' ) ) {
            //TODO: Get the list of site_admins and send them a message!
            /*
            messages_new_message(
                array(
                    
                )
            );
            */
            wp_die( _dd($field_value) );
        }
    }
    
    /**
     * profile_screen_admin()
     * Filters option for 'Teacher', only admins are allowed to access it.
     */
    function profile_screen_admin( $content ) {
        if( !is_super_admin() ) {
            if( stristr( $content, __( 'Teacher' ) ) )
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
        $registered = false;
        
        //HACK: to allow options
        global $bp;
        (array)$bp->profile->field_types[] = 'option';
        
        $groups = BP_XProfile_Group::get();
        foreach( $groups as $g ) {
            if( $g->name == 'BuddyPress ScholarPress LMS' )
                $registered = true;
        }
        
        if($registered)
            return;
        
        $bpsp_group_id = xprofile_insert_field_group(
            array(
                name        => 'BuddyPress ScholarPress LMS',
                description => __( 'Students and Teachers fields. Do not delete as long as you use BuddyPress ScholarPress! '),
                can_delete  => false
            )
        );
        if( !$bpsp_group_id )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile group.' ) );
        
        /* Create the radio buttons */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                name            => __( 'Role' ),
                can_delete      => false,
                description     => __( 'You role when using ScholarPress.' ),
                is_required     => false,
                type            => 'radio'
            )
        );
        $bpsp_field_id = xprofile_get_field_id_from_name( __( 'Role' ) );
        if( !$bpsp_field_id )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile field.' ) );
            
        /* Create the radio options */
        xprofile_insert_field(
            array (
                field_group_id  => $bpsp_group_id,
                parent_id       => $bpsp_field_id,
                name            => __( 'Teacher' ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        xprofile_insert_field(
            array (
                field_group_id      => $bpsp_group_id,
                parent_id           => $bpsp_field_id,
                name                => __( 'Student' ),
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
                name            => __( 'Apply for Teacher' ),
                can_delete      => false,
                is_required     => false,
                type            => 'option'
            )
        );
        
        if( !xprofile_get_field_id_from_name( __( 'Teacher' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Student' ) ) ||
            !xprofile_get_field_id_from_name( __( 'Apply for Teacher' ) )
        )
            wp_die( __( 'BuddyPress ScholarPress error when saving xProfile field options.' ) );
        else
            $registered = true;
            
        return $registered;
    }
}

?>