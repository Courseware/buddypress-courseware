<?php
/**
 * BPSP Class for dashboard/screens management on groups
 *
 * This class will set up the group navigation
 * Also it introduces a new action and filter:
 *  - courseware_group_screen_handler
 *  - courseware_group_nav_options
 * Both are pluggable and can be used to add new content or handle screens
 * for actions like groups/ID/courseware/action/action_var
 */
class BPSP_Groups {
    /**
     * Holds the navigation options for component
     */
    var $nav_options = array();
    
    /**
     * Holds the current navigation option for component
     */
    var $current_nav_option = null;
    
    /**
     * BPSP_Groups()
     *
     * Constructor. Loads filters and actions.
     */
    function BPSP_Groups() {
        add_action( 'bp_setup_nav', array( &$this, 'set_nav' ) );
        add_filter( 'groups_get_groups', array( &$this, 'extend_search' ), 10, 2 );
        add_action( 'groups_admin_tabs', array( &$this, 'group_admin_tab' ), 10, 2 );
        add_action( 'wp', array( &$this, 'group_admin_screen' ), 4 );
        add_filter( 'media_upload_form_url', array( __CLASS__, 'bpsp_media_library_tab' ) );
    }
    
    /**
     * activate_component()
     *
     * Activates Courseware as a BuddyPress component for groups
     */
    function activate_component() {
        global $bp;
        $bp->courseware->id = 'courseware';
        $bp->courseware->slug = 'courseware';
        $bp->active_components[$bp->courseware->slug] = $bp->courseware->id;
    }
    
    /**
     * courseware_status( $group_id = null )
     *
     * Checks if courseware is enabled for current group
     * @param Int $group_id, the id of the group, default is null and global $bp will be used
     * @return Bool, true on success, and false on failure.
     */
    function courseware_status( $group_id = null ) {
        if( !$group_id ) {
            global $bp;
            $group_id = $bp->groups->current_group->id;
        }
        
        $global_status = get_option( 'bpsp_global_status' );
        $group_status = groups_get_groupmeta( $group_id, 'courseware' );
        
        if( 'true' == $group_status || !empty( $global_status ) )
            return true;
        else
            return false;
    }
    
    /**
     * set_nav()
     *
     * Sets up the component navigation
     */
    function set_nav() {
        global $bp;
        
        if( !$this->courseware_status( $bp->groups->current_group->id ) )
            return;
        
        $group_permalink = bp_get_group_permalink( $bp->groups->current_group );
        
        bp_core_new_subnav_item( array( 
            'name' => __( 'Courseware', 'bpsp' ),
            'slug' => $bp->courseware->slug,
            'parent_url' => $group_permalink, 
            'parent_slug' => $bp->groups->current_group->slug, 
            'screen_function' => array( &$this, 'screen_handler' ),
            'position' => 35, 
            'user_has_access' => $bp->groups->current_group->user_has_access,
            'item_css_id' => 'courseware-group'
        ) );
        
        $this->nav_options[__( 'Home', 'bpsp' )] = $group_permalink . $bp->courseware->slug;
        
        do_action( 'courseware_group_set_nav' );
    }
    
    /**
     * screen_handler()
     *
     * Courseware action for handling the screens on group pages
     */
    function screen_handler() {
        global $bp;
        
        if( !$this->courseware_status( $bp->groups->current_group->id ) )
            return;
        
        if ( $bp->current_component == $bp->groups->id && $bp->current_action == $bp->courseware->slug ) {
            $this->current_nav_option =  $this->nav_options[__( 'Home', 'bpsp' )];
            
            if( $bp->action_variables[0] )
                $this->current_nav_option .= '/' . $bp->action_variables[0];
            
            add_action( 'bp_before_group_body', array( &$this, 'nav_options' ) );
            do_action( 'courseware_group_screen_handler', $bp->action_variables );
            add_action( 'bp_template_content', array( &$this, 'load_template' ) );
        }
        groups_update_last_activity( $bp->groups->current_group->id );
        bp_core_load_template( apply_filters( 'bp_core_template_plugin' , 'groups/single/plugins' ) );
    }
    
    /**
     * nav_options()
     *
     * Courseware action to manage group navigation options
     */
    function nav_options() {
        apply_filters( 'courseware_group_nav_options', &$this->nav_options );
        $this->load_template( array(
            'name' => '_nav',
            'nav_options' => $this->nav_options,
            'current_option' => $this->current_nav_option
        ));
    }
    
    /**
     * load_template( $vars )
     *
     * Loads a template for displaying group screens
     *
     * @param Array $vars of options
     * @return template $content if $vars['echo'] == false
     */
    function load_template( $vars = '' ) {
        $content = '';
        if( empty( $vars ) || !isset( $vars['name'] ) )
            $vars = array(
                'name' => 'home',
                'nav_options' => $this->nav_options,
                'current_uri' => $this->nav_options[__( 'Home', 'bpsp' )],
                'current_option' => $this->current_nav_option,
                'echo' => true,
            );
    
        if( !isset( $vars['echo'] ) )
            $vars['echo'] = true;
        
        $templates_path = BPSP_PLUGIN_DIR . '/groups/templates/';
        $vars['templates_path'] = $templates_path;
        
        // Load helpers
        foreach ( glob( $templates_path . "helpers/*.php" ) as $helper )
            include_once $helper;
        
        //Exclude internal templates like navigation, starts with an underscore
        if(  substr( $vars['name'], 0, 1) != '_' )
            apply_filters( 'courseware_group_template', &$vars );
        else
            apply_filters( 'courseware_template_vars', &$vars );
        
        if( file_exists( $templates_path . $vars['name']. '.php' ) ) {
            ob_start();
            extract( $vars );
            if( !empty( $die ) ) {
                $error = $die;
                include( $templates_path . '_message.php' ); // Template for errors
            } else {
                if ( isset( $trail ) )
                    include_once $templates_path . '_trail.php'; // Template for crumbs
                include( $templates_path . '_message.php' ); // Template for messages
                include( $templates_path . $name . '.php' );
            }
            do_action( 'courseware_post_template', $vars );
            $content = ob_get_clean();
        }
        
        if( $vars['echo'] )
            echo $content;
        else
            return $content;
    }
    
    /**
     * extend_search( $groups, $params )
     *
     * Hooks into groups_get_groups filter and extends search to include Courseware used post types
     */
    function extend_search( $groups, $params ) {
        // Don't bother searching if nothing queried
        if( empty( $params['search_terms'] ) )
            return $groups;
        
        // A hack to make WordPress believe the taxonomy is registered
        if( !taxonomy_exists( 'group_id' ) ) {
            global $wp_taxonomies;
            $wp_taxonomies['group_id'] = '';
        }
        
        $all_groups = BP_Groups_Group::get_alphabetically();
        foreach( $all_groups['groups'] as $group ) {
            // Search posts from current $group
            $results = BPSP_WordPress::get_posts(
                array(
                    'group_id' => $group->id
                ),
                array(
                    'assignment',
                    'course',
                    'schedule',
                ),
                $params['search_terms']
            );
            
            // Merge posts to $groups if new found
            if( !empty( $results ) ) {
                if( !in_array( $group, $groups['groups'] ) ) {
                    $groups['groups'][] = $group;
                    $groups['total']++;
                }
            }
        }
        return $groups;
    }
    
    /**
     * group_admin_tab( $current_tab, $group_slug )
     *
     * Hooks into groups_admin_tabs, and adds the courseware options tab
     */
    function group_admin_tab( $current_tab, $group_slug ) {
        global $bp;
        
        $tab_content = '<li ';
        if ( 'courseware' == $current_tab )
            $tab_content .= 'class="current"';
        
        $tab_content .= '><a href="' . bp_get_group_admin_permalink() . '/courseware">';
        $tab_content .= __( 'Courseware', 'bpsp' ) . '</a></li>';
        
        echo $tab_content;
    }
    
    /**
     * group_admin_screen()
     *
     * Hooks into wp, adds a new screen to group Admin screens
     */
    function group_admin_screen() {
        global $bp;
        
        if ( $bp->current_component == $bp->groups->id && 'courseware' == $bp->action_variables[0] ) {
            if ( $bp->is_item_admin || $bp->is_item_mod  ) {
                add_action( 'bp_before_group_admin_content', array( &$this, 'group_admin_content' ) );
                bp_core_load_template( apply_filters( 'groups_template_group_admin', 'groups/single/home' ) );
            }
        }
    }
    
    /**
     * group_admin_content()
     *
     * Hooks into bp_before_group_admin_content(), adds Courseware group options
     */
    function group_admin_content() {
        global $bp;
        $nonce_name = 'courseware_group_option';
        
        if ( isset( $_POST['save'] ) && wp_verify_nonce( $_POST['_wpnonce'], $nonce_name ) ) {
            if( isset( $_POST['group_courseware_status'] ) && !empty( $_POST['group_courseware_status'] ) ) {
                $post_value = sanitize_key( $_POST['group_courseware_status'] );
            
                if( groups_update_groupmeta( $bp->groups->current_group->id, 'courseware', $post_value ) )
                    $vars['message'] = __( 'Group Courseware settings were successfully updated.', 'bpsp' );
            }
            
            if( isset( $_POST['responses_courseware_status'] ) && !empty( $_POST['responses_courseware_status'] ) ) {
                $post_value = sanitize_key( $_POST['responses_courseware_status'] );
            
                if( groups_update_groupmeta( $bp->groups->current_group->id, 'courseware_responses', $post_value ) )
                    $vars['message'] = __( 'Group Courseware responses settings were successfully updated.', 'bpsp' );
            }
        }
        
        $vars['name'] = '_group_admin_screen';
        $vars['form_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['current_status'] = groups_get_groupmeta( $bp->groups->current_group->id, 'courseware' );
        $vars['current_responses_status'] = groups_get_groupmeta( $bp->groups->current_group->id, 'courseware_responses' );
        $this->load_template( $vars );
    }
    
    /**
     * Will hook into get_children() if the upload form media library is accessed,
     * and will add to the current query currently logged in author ID
     */
    function bpsp_restrict_uploads( $wp_the_query ) {
        // Check if current user is admin or sort of
        if ( !current_user_can( 'manage_options' ) )
            // If not, he will only see his own attachments
            $wp_the_query->query_vars['author'] = get_current_user_id();
    }
    
    /**
     * Media library displays all the uploads,
     * bpsp_media_library_tab() will do some checks and try to hide
     * attachments that are not owned by current user
     */
    function bpsp_media_library_tab( $action_url ) {
        // Try to catch Courseware uploads page
        if ( isset( $_REQUEST['bpsp-upload'] ) && isset( $_REQUEST['tab'] ) )
            // Check if the user is on the current tab
            if ( $_REQUEST['tab'] == 'library' )
                // Do some checks before displaying attachments
                add_action( 'pre_get_posts', array( __CLASS__, 'bpsp_restrict_uploads' ) );
        
        return $action_url;
    }
}
?>