<?php
/**
 * BPSP Lectures class
 */
class BPSP_Lectures {
    /**
     * Lectures capabilities
     */
    var $caps = array(
        'view_lectures',
        'publish_lectures',
        'manage_lectures',
        'edit_lecture',
        'edit_lectures',
        'delete_lecture',
        'assign_lectures',
        'manage_group_id',
        'manage_course_id',
        'upload_files',
    );
    
    /**
     * Current lecture id
     */
    var $current_lecture = null;
    
    /**
     * BPSP_Lectures()
     *
     * Constructor. Loads the hooks and actions.
     */
    function BPSP_Lectures() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_caps' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
   }
   
   /**
     * register_post_types()
     *
     * Static function to register the lecture post type, taxonomies and capabilities.
     */
    function register_post_types() {
        $lecture_post_def = array(
            'label'                 => __( 'Lecture', 'bpsp' ),
            'singular_label'        => __( 'Lecture', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Lectures', 'bpsp' ),
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'lecture',
            'hierarchical'          => true,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'page-attributes' )
        );
        if( !register_post_type( 'lecture', $lecture_post_def ) )
            $this->error = __( 'BuddyPress Courseware error while registering lecture post type.', 'bpsp' );
        
        $lecture_rel_def = array(
            'public'        => BPSP_DEBUG,
            'show_ui'       => BPSP_DEBUG,
            'hierarchical'  => false,
            'label'         => __( 'Course ID', 'bpsp'),
            'query_var'     => true,
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_course_id',
                'edit_terms'    => 'manage_course_id',
                'delete_terms'  => 'manage_course_id',
                'assign_terms'  => 'edit_lectures'
                )
        );
        
        register_taxonomy( 'course_id', array( 'lecture' ), $lecture_rel_def );
        register_taxonomy_for_object_type( 'group_id', 'lecture' ); //append already registered group_id term
        
        if( !get_taxonomy( 'group_id' ) || !get_taxonomy( 'course_id' ) )
            wp_die( __( 'BuddyPress Courseware error while registering lecture taxonomies.', 'bpsp' ) );
    }
    
    /**
     * add_caps( $user_id )
     *
     * Adds lecture capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_lectures' ) )
                $user->add_cap( 'edit_others_lectures' );
    }
    
    /**
     * remove_caps( $user_id )
     *
     * Adds lecture capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_lecture_caps( $user_id )
     *
     * Checks if $user_id has lecture management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_lecture_caps( $user_id ) {
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_caps( $user_id );
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
     * Lecture screens handler.
     * Handles uris like groups/ID/courseware/lecture/args
     */
    function screen_handler( $action_vars ) {
        
        if( $action_vars[0] == 'new_lecture' ) {
            //Load editor
            add_action( 'bp_head', array( &$this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( &$this, 'new_lecture_screen' ) );
        }
        elseif( $action_vars[0] == 'lecture' ) {
            $current_lecture = BPSP_Lectures::is_lecture( $action_vars[1] );
            
            if( isset ( $action_vars[1] ) && null != $current_lecture )
                $this->current_lecture = $current_lecture;
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                add_action( 'bp_head', array( &$this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( &$this, 'edit_lecture_screen' ) );
            } elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( &$this, 'delete_lecture_screen' ) );
            } else {
                do_action( 'courseware_lecture_screen' );
                add_filter( 'courseware_group_template', array( &$this, 'single_lecture_screen' ) );
            }
            do_action( 'courseware_lecture_screen_handler', $action_vars );
        }
    }
    
    /**
     * is_lecture( $lecture_identifier = null  )
     *
     * Checks if a lecture with $lecture_identifier exists
     *
     * @param $lecture_identifier ID or Name of the lecture to be checked
     * @return Lecture object if lecture exists and null if not.
     */
    function is_lecture( $lecture_identifier = null ) {
        global $bp;
        $courseware_uri = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/' ;
        
        if( is_object( $lecture_identifier ) && $lecture_identifier->post_type == "lecture" )
            return $lecture_identifier;
        
        if( !$lecture_identifier )
            $lecture_identifier = $this->current_lecture;
        
        $lecture_query = array(
            'post_type' => 'lecture',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if( is_numeric( $lecture_identifier ) )
            $lecture_query['p'] = $lecture_identifier;
        else
            $lecture_query['name'] = $lecture_identifier;
        
        $lecture = get_posts( $lecture_query );
        
        if( !empty( $lecture[0] ) ) {
            $lecture[0]->group = wp_get_object_terms( $lecture[0]->ID, 'group_id' );
            $lecture_course = wp_get_object_terms( $lecture[0]->ID, 'course_id' );
            $lecture[0]->course = BPSP_Courses::is_course($lecture_course[0]->name );
            $lecture[0]->permalink = $courseware_uri . 'lecture/' . $lecture[0]->post_name;
            return $lecture[0];
        } else
            return null;
    }
    
    /**
     * has_lectures( $group_id = null )
     *
     * Checks if a $group_id has lectures
     *
     * @param Int $group_id of the group to be checked
     * @return Mixed Lecture objects if lectures exist and null if not.
     */
    function has_lectures( $group_id = null ) {
        global $bp;
        $lecture_ids = null;
        $lectures = array();
        
        if( empty( $group_id ) )
            $group_id = $bp->groups->current_group->id;
        
        $term_id = get_term_by( 'slug', $group_id, 'group_id' );
        if( !empty( $term_id ) )
            $lecture_ids = get_objects_in_term( $term_id->term_id, 'group_id' );
        
        if( !empty( $lecture_ids ) )
            arsort( $lecture_ids ); // Get latest entries first
        else
            return null;
        
        foreach ( $lecture_ids as $aid )
            $lectures[] = self::is_lecture( $aid );
        
        return array_filter( $lectures );
    }
    
    /**
     * add_nav_options()
     *
     * Adds lecture specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function add_nav_options( $options ) {
        global $bp;
        
        if( $this->has_lecture_caps( $bp->loggedin_user->id ) || is_super_admin() ) {
            // If there are no courses, there will be no lectures
            if( BPSP_Courses::has_courses( $bp->groups->current_group->id ) )
                $options[__( 'New Lecture', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_lecture';
        }
        
        return $options;
    }
    
    /**
     * new_lecture_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to add new lectures.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function new_lecture_screen( $vars ) {
        global $bp;
        $nonce_name = 'new_lecture';
        
        if( !$this->has_lecture_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new lecture.' );
            return $vars;
        }
        
        // Save new lecture
        if( isset( $_POST['lecture'] ) &&
            $_POST['lecture']['object'] == 'group' &&
            BPSP_Courses::is_course( $_POST['lecture']['course_id'] ) &&
            isset( $_POST['_wpnonce'] )
        ) {
            $new_lecture = $_POST['lecture'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce ) 
                $vars['error'] = __( 'Nonce Error while adding a lecture.', 'bpsp' );
            else
                if( isset( $new_lecture['title'] ) &&
                    isset( $new_lecture['content'] ) &&
                    isset( $new_lecture['group_id'] )
                ) {
                    $new_lecture['title'] = strip_tags( $new_lecture['title'] );
                    $new_lecture_id =  wp_insert_post( array(
                        'post_author'   => $bp->loggedin_user->id,
                        'post_title'    => $new_lecture['title'],
                        'post_content'  => $new_lecture['content'],
                        'post_parent'   => isset( $new_lecture['parent'] ) ? intval( $new_lecture['parent'] ) : 0,
                        'menu_order'    => isset( $new_lecture['order'] ) ? intval( $new_lecture['order'] ) : 0,
                        'post_status'   => 'publish',
                        'post_type'     => 'lecture',
                    ));
                    if( $new_lecture_id ) {
                        wp_set_post_terms( $new_lecture_id, $new_lecture['group_id'], 'group_id' );
                        wp_set_post_terms( $new_lecture_id, $new_lecture['course_id'], 'course_id' );
                        $this->current_lecture = $this->is_lecture( $new_lecture_id );
                        
                        $vars['message'] = __( 'New lecture was added.', 'bpsp' );
                        do_action( 'courseware_lecture_added', $this->current_lecture );
                        return $this->single_lecture_screen( $vars );
                    } else
                        $vars['error'] = __( 'New lecture could not be added.', 'bpsp' );
                } else
                    $vars['error'] = __( 'Please fill in all the fields.', 'bpsp' );
        }
        
        $vars['posted_data'] = $_POST['lecture'];
        $vars['course'] = reset( BPSP_Courses::has_courses( $bp->groups->current_group->id ) );
        $vars['lectures'] = $this->has_lectures( $bp->groups->current_group->id );
        $vars['name'] = 'new_lecture';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * blank_lecture_screen( $vars )
     *
     * Hooks into screen_handler
     * Just a blank screen to show off deletion confirmation
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function blank_lecture_screen( $vars ) {
        global $bp;
        return $vars;
    }
    
    /**
     * list_lectures_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to list lectures.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function lectures_screen( $vars ) {
        global $bp;
        $lectures = get_posts( array(
            'numberposts'   => '-1',
            'post_type'     => 'lecture',
            'group_id'      => $bp->groups->current_group->id,
            'orderby'       => 'menu_order',
            'hierarchical'  => true
        ));
        
        $vars['name'] = '_list_lectures';
        $vars['lectures_hanlder_uri'] = $vars['current_uri'] . '/lectures/';
        $vars['lectures'] = $lectures;
        return $vars;
    }
    
    /**
     * single_lecture_screen( $vars )
     *
     * Hooks into screen_handler
     * Displays a single lecture screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function single_lecture_screen( $vars ) {
        global $bp;
        
        $lecture = $this->is_lecture( $this->current_lecture );
        
        if(  $this->has_lecture_caps( $bp->loggedin_user->id ) || is_super_admin() )
            $vars['show_edit'] = true;
        else
            $vars['show_edit'] = null;
        
        $vars['name'] = 'single_lecture';
        $vars['lecture_permalink'] = $vars['current_uri'] . '/lecture/' . $this->current_lecture->post_name;
        $vars['lecture_edit_uri'] = $vars['current_uri'] . '/lecture/' . $this->current_lecture->post_name . '/edit';
        $vars['lecture'] = $lecture;
        $vars['next'] = $this->next_lecture( $lecture );
        $vars['prev'] = $this->prev_lecture( $lecture );
        
        return apply_filters( 'courseware_lecture', $vars );
    }
    
    /**
     * delete_lecture_screen( $vars )
     *
     * Hooks into screen_handler
     * Delete lecture screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function delete_lecture_screen( $vars ) {
        global $bp;
        $lecture = $this->is_lecture( $this->current_lecture );
        $nonce_name = 'delete_lecture';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) )
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        
        if( true != $is_nonce ) {
            $vars['die'] = __( 'Nonce Error while deleting the lecture.', 'bpsp' );
            return $vars;
        }
        
        if(  ( $lecture->post_author == $bp->loggedin_user->id ) || is_super_admin() ) {
            wp_delete_post( $lecture->ID );
        } else {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to delete the lecture.', 'bpsp' );
            return $vars;
        }
        
        $vars['message'] = __( 'Lecture deleted successfully.', 'bpsp' );
        return $this->blank_lecture_screen( $vars );
    }
    
    /**
     * edit_lecture_screen( $vars )
     *
     * Hooks into screen_handler
     * Edit lecture screen
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function edit_lecture_screen( $vars ) {
        global $bp;
        $nonce_name = 'edit_lecture';
        $updated_lecture_id = $this->current_lecture;
        
        $old_lecture = $this->is_lecture( $this->current_lecture );
        
        if( !$this->has_lecture_caps( $bp->loggedin_user->id ) &&
            $bp->loggedin_user->id != $old_lecture->post_author &&
            $bp->groups->current_group->id != $old_lecture->group[0]->name &&
            !is_super_admin()
        ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to update the lecture.', 'bpsp' );
            return $vars;
        }
        
        // Update lecture
        if( isset( $_POST['lecture'] ) &&
            $_POST['lecture']['object'] == 'group' &&
            isset( $_POST['_wpnonce'] )
        ) {
            $updated_lecture = $_POST['lecture'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            if( true != $is_nonce )
                $vars['error'] = __( 'Nonce Error while editing the lecture.', 'bpsp' );
            else 
                if( isset( $updated_lecture['title'] ) &&
                    isset( $updated_lecture['content'] ) &&
                    is_numeric( $updated_lecture['group_id'] )
                ) {
                    $updated_lecture['title'] = strip_tags( $updated_lecture['title'] );
                    $updated_lecture_id =  wp_update_post( array(
                        'ID'            => $old_lecture->ID,
                        'post_title'    => $updated_lecture['title'],
                        'post_content'  => $updated_lecture['content'],
                        'post_parent'   => intval( $updated_lecture['parent'] ),
                        'menu_order'    => intval( $updated_lecture['order'] )
                    ));
                    
                    if( $updated_lecture_id ) {
                        $vars['message'] = __( 'Lecture was updated.', 'bpsp' );
                        do_action( 'courseware_lecture_activity', $this->is_lecture( $updated_lecture_id ), 'update' );
                    }
                    else
                        $vars['error'] = __( 'Lecture could not be updated.', 'bpsp' );
                }
        }
        
        $vars['name'] = 'edit_lecture';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['lecture'] = $this->is_lecture( $updated_lecture_id );
        $vars['lectures'] = $this->has_lectures( $bp->groups->current_group->id );
        $vars['lecture_edit_uri'] = $vars['current_uri'] . '/lecture/' . $this->current_lecture->post_name . '/edit';
        $vars['lecture_delete_uri'] = $vars['current_uri'] . '/lecture/' . $this->current_lecture->post_name . '/delete';
        $vars['lecture_permalink'] = $vars['current_uri'] . '/lecture/' . $this->current_lecture->post_name;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_lecture' ), $vars['lecture_delete_uri'] );
        return $vars;
    }
    
    /**
     * Comparer for post objects, to be used as `usort` callback
     */
    function cmp_menu_order( $a, $b ) {
        if( $a->menu_order == $b->menu_order )
            return 0;
        return ( $a->menu_order > $b->menu_order ) ? +1 : -1;
    }
    
    /**
     * next_lecture( $lecture )
     * Get the next lecture
     *
     * @param Mixed $lecture object, current lecture
     * @return Mixed the next $lecture object
     */
    function next_lecture( $lecture ) {
        global $bp;
        $next = false;
        
        // Get lectures
        $lectures = BPSP_Lectures::has_lectures( $bp->groups->current_group->id );
        if( empty( $lectures ) )
            return null;
        
        // Try to sort them by menu_order
        usort( $lectures, array( 'BPSP_Lectures', 'cmp_menu_order' ) );
        // Use WordPress's hierarchical algorithm
        $hierarchy = get_page_hierarchy( $lectures, 0 );
        // Find current position in hierarchy
        if( reset( $hierarchy ) != $lecture->post_name ) {
            while( next( $hierarchy ) )
                if( current( $hierarchy ) == $lecture->post_name )
                    break;
            $next = next( $hierarchy );
        } else
            $next = next( $hierarchy );
        
        if( $next )
            return BPSP_Lectures::is_lecture( $next );
        else
            return null;
    }
    
    /**
     * prev_lecture( $lecture )
     * Get the previous lecture
     *
     * @param Mixed $lecture object, current lecture
     * @return Mixed the previous $lecture object
     */
    function prev_lecture( $lecture ) {
        global $bp;
        // Get lectures
        $lectures = BPSP_Lectures::has_lectures( $bp->groups->current_group->id );
        if( empty( $lectures ) )
            return null;
        
        // Try to sort them by menu_order
        usort( $lectures, array( 'BPSP_Lectures', 'cmp_menu_order' ) );
        // Use WordPress's hierarchical algorithm
        $hierarchy = get_page_hierarchy( $lectures, 0 );
        // Find current position in hierarchy
        while( next( $hierarchy ) )
            if( current( $hierarchy ) == $lecture->post_name )
                break;
        
        $prev = prev( $hierarchy );
        if( $prev )
            return BPSP_Lectures::is_lecture( $prev );
        else
            return null;
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        do_action( 'courseware_editor' );
        wp_enqueue_style( 'datetimepicker' );
        wp_enqueue_script( 'post' );
        wp_enqueue_script( 'editor' );
        wp_enqueue_script( 'utils' );
        add_thickbox();
        $media_upload_js = '/wp-admin/js/media-upload.js';
        wp_enqueue_script('media-upload', get_bloginfo('wpurl') . $media_upload_js, array( 'thickbox' ), filemtime( ABSPATH . $media_upload_js) );
    }
}
?>