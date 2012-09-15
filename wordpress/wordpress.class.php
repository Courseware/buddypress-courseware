<?php
/**
 * Class to handle all WordPress admin backend stuff
 */
class BPSP_WordPress {
    /**
     * BPSP_WordPress()
     *
     * Constructor, loads all the required hooks
     */
    function BPSP_WordPress() {
        // Add our screen to BuddyPress menu
        add_action(
            bp_core_admin_hook(),
            array( __CLASS__, 'menus')
        );

        // Ensure compatibility
        add_action('admin_notices', 'bpsp_check' );
        // Help Screen
        add_action('admin_head', array( __CLASS__, 'screen_help') );
        // Support link
        add_filter( 'plugin_row_meta', array( __CLASS__, 'support_link' ), 10, 2 );
        // Settings link
        add_action( 'plugin_action_links_' . BPSP_PLUGIN_FILE, array( __CLASS__, 'action_link' ), 10, 4 );

        // Initialize our options
        add_option( 'bpsp_allow_only_admins' );
        add_option( 'bpsp_global_status' );
        add_option( 'bpsp_gradebook_format' );
        add_option( 'bpsp_private_responses' );
        add_option( 'bpsp_worldcat_key' );
        add_option( 'bpsp_isbndb_key' );
        add_option( 'bpsp_load_css' );
    }

    /**
     * menus()
     *
     * Adds menus to admin area
     */
    function menus() {
        if ( is_super_admin() )
            add_submenu_page(
                'bp-general-settings',
                __( 'Courseware', 'bpsp' ),
                __( 'Courseware', 'bpsp' ),
                'manage_options',
                'bp-courseware',
                array( __CLASS__, "screen")
            );
    }

    /**
     * screen_help()
     *
     * Handles the screen() help
     */
    function screen_help() {
        global $current_screen;

        // If it's not Courseware Screen
        if( !stristr( $current_screen->id, 'courseware' ) )
            return;

        $vars['name'] = 'contextual_help';
        get_current_screen()->add_help_tab( array( 'id'=> 'Help', 'title'=> 'Help', 'content' => self::load_template( $vars )) );
    }

    /**
     * screen()
     *
     * Handles the wp-admin screen
     */
    function screen() {
        $nonce_name = 'courseware_options';
        $vars = array();
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $is_nonce = false;

        if( isset( $_POST['_wpnonce'] ) )
            check_admin_referer( $nonce_name );

        // Courseware Global Status
        if( isset( $_POST['bpsp_global_status'] ) )
            if( update_option( 'bpsp_global_status', strtolower( $_POST['bpsp_global_status'] ) ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );
        if( !isset( $_POST['bpsp_global_status'] ) && isset( $_POST['bpsp_global_status_check'] ) )
            if( update_option( 'bpsp_global_status', '' ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );

        // Courseware Collaborative Settings
        if( isset( $_POST['bpsp_allow_only_admins'] ) )
            if( update_option( 'bpsp_allow_only_admins', strtolower( $_POST['bpsp_allow_only_admins'] ) ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );
        if( !isset( $_POST['bpsp_allow_only_admins'] ) && isset( $_POST['bpsp_allow_only_admins_check'] ) )
            if( update_option( 'bpsp_allow_only_admins', '' ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );

        // Courseware Private Responses
        if( isset( $_POST['bpsp_private_responses_check'] ) )
            if( update_option( 'bpsp_private_responses', strtolower( $_POST['bpsp_private_responses'] ) ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );
        if( isset( $_POST['bpsp_private_responses_check'] ) && !isset( $_POST['bpsp_private_responses'] ) )
            if( update_option( 'bpsp_private_responses', '' ) )
                $vars['flash'][] = __( 'Courseware option was updated.', 'bpsp' );

        // Courseware Default Gradebook Format
        if( isset( $_POST['bpsp_gradebook_format_check'] ) && isset( $_POST['bpsp_gradebook_format'] ) )
            if( update_option( 'bpsp_gradebook_format', strtolower( $_POST['bpsp_gradebook_format'] ) ) )
                $vars['flash'][] = __( 'Courseware gradebook format option was updated.', 'bpsp' );

        // Courseware Bibliography Webservices Integration
        if( isset( $_POST['worldcat_key'] ) && !empty( $_POST['worldcat_key'] ) )
            if( update_option( 'bpsp_worldcat_key', $_POST['worldcat_key'] ) )
                $vars['flash'][] = __( 'WorldCat option was updated.', 'bpsp' );
        if( isset( $_POST['isbndb_key'] ) && !empty( $_POST['isbndb_key'] ) )
            if( update_option( 'bpsp_isbndb_key', $_POST['isbndb_key'] ) )
                $vars['flash'][] = __( 'ISBNdb option was updated.', 'bpsp' );

        // Courseware Custom CSS
        if( isset( $_POST['bpsp_load_css_check'] ) && isset( $_POST['bpsp_load_css'] ) )
            if( update_option( 'bpsp_load_css', strtolower( $_POST['bpsp_load_css'] ) ) )
                $vars['flash'][] = __( 'Courseware customization options updated.', 'bpsp' );
        if( isset( $_POST['bpsp_load_css_check'] ) && !isset( $_POST['bpsp_load_css'] ) )
            if( update_option( 'bpsp_load_css', '' ) )
                $vars['flash'][] = __( 'Courseware customization options updated.', 'bpsp' );

        $vars['name'] = 'admin';
        $vars['echo'] = 'true';
        $vars['bpsp_private_responses'] = get_option( 'bpsp_private_responses' );
        $vars['bpsp_gradebook_format'] = get_option( 'bpsp_gradebook_format' );
        $vars['bpsp_allow_only_admins'] = get_option( 'bpsp_allow_only_admins' );
        $vars['bpsp_global_status'] = get_option( 'bpsp_global_status' );
        $vars['worldcat_key'] = get_option( 'bpsp_worldcat_key' );
        $vars['isbndb_key'] = get_option( 'bpsp_isbndb_key' );
        $vars['bpsp_load_css'] = get_option( 'bpsp_load_css' );

        //Load the template
        self::load_template( $vars );
    }

    /**
     * action_link( $links )
     * Adds a new entry link under plugin description
     *
     * @param Mixed $links, initial links
     * @param String $file, the plugin filename
     * @return Mixed, modified set of $links
     */
    function support_link( $links, $file ) {
        if ( $file == BPSP_PLUGIN_FILE ) {
            $links[] = '<a href="http://buddypress.org/community/groups/buddypress-courseware/forum/">' . __( 'Support', 'bpsp' ) . '</a>';
        }
        return $links;
    }

    /**
     * action_link( $links )
     * Adds a new action link to plugin entry
     *
     * @param Mixed $links, initial links
     * @return Mixed, modified set of $links
     */
    function action_link( $links ) {
        $action_link = '<a href="' . admin_url( 'admin.php?page=bp-courseware' ) . '">' . __( 'Settings', 'bpsp' ) .'</a>';
        array_unshift( $links, $action_link );
        return $links;
    }

    /**
     * load_template( $vars )
     *
     * Loads a template for displaying group screens
     *
     * @param Array $vars of options
     * @return template $content if $vars['echo'] == false
     */
    function load_template( $vars ) {
        ob_start();
        extract( $vars );
        if( file_exists( BPSP_PLUGIN_DIR . '/wordpress/templates/' . $name . '.php' ) )
            include( BPSP_PLUGIN_DIR . '/wordpress/templates/' . $name . '.php' );

        if( isset( $echo ) && $echo )
            echo ob_get_clean();
        else
            return ob_get_clean();
    }

    /**
     * get_posts( $terms, $post_types, $s )
     *
     * A hack to query multiple custom terms
     * Left for backwards compatibility, on the removal list!
     *
     * @param Mixed $terms, a set of term slugs as keys and taxonomies as values
     * @param Mixed $post_types, a set of post types to query
     * @param String $s to search for
     * @return Mixed $posts, a set of queried posts
     */
    function get_posts( $terms, $post_types = null, $s = null ){
        if( !$post_types )
            $post_types = array( 'post' );
        $term_ids = array();
        $post_ids = array();
        $posts = array();

        // Get term ids
        // TODO: Here's something wrong, totally!!!
        foreach ( $terms as $term => $taxonomy ) {
            $t = get_term_by( 'slug', $taxonomy, $term );
            if( !empty( $t ) )
                $term_ids[ $t->term_id ] = $term;
        }
        // Get term's objects
        if( !empty( $term_ids ) )
        foreach( $term_ids as $term_id => $taxonomy )
            $post_ids[] = get_objects_in_term( $term_id, $taxonomy );
        // Get common objects
        if( !empty( $post_ids ) ) {
            for( $i = 1; $i < count( $post_ids ); $i++ )
                $post_ids[0] = array_intersect( $post_ids[0], $post_ids[$i] );
            // return the final array
            $post_ids = reset( $post_ids );
        }
        // Get object data's of one type
        if( !empty( $post_ids ) && is_array( $post_ids ) )
            return get_posts( array(
                'post__in' => $post_ids,
                'post_type' => $post_types,
                's' => $s,
            ) );
        else
            return null;
    }
}
?>
