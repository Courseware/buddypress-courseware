<?php
/**
 * BPSP Class for bibliographies management on groups
 *
 * Will hook into both
 *  - courseware_below_courses
 *  - courseware_below_assignments
 */
class BPSP_Bibliographies {
    /**
     * Capabilities required to edit/add bibliographies
     */
    var $caps = array(
        'view_bibs',
        'manage_bibs',
        'edit_bib',
        'edit_bibs'
    );
    
    /**
     * Bibliographies post identifier
     */
    var $DBID = 'BIBSDB';
    
    /**
     * Current parent post_id
     */
    var $current_parent = null;
    
    /**
     * Courseware home uri
     */
    var $home_uri;
    
    /**
     * Post meta identifier
     */
    var $bid = 'bibliography';
    
    /**
     * BPSP_Bibliographies()
     *
     * Constructor. Loads all the hooks.
     */
    function BPSP_Bibliographies() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_bib_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_bib_caps' ) );
        add_filter( 'courseware_course', array( &$this, 'bibs_screen' ) );
        add_filter( 'courseware_assignment', array( &$this, 'bibs_screen' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
    }
    
/**
     * register_post_types()
     *
     * Static function to register the bibliography post types and capabilities.
     */
    function register_post_types() {
        $bib_post_def = array(
            'label'                 => __( 'Bibliographies', 'bpsp' ),
            'singular_label'        => __( 'Bibliography', 'bpsp' ),
            'description'           => __( 'BuddyPress ScholarPress Courseware Bibliographies', 'bpsp' ),
            'public'                => true, //TODO: set to false when stable
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => true, //TODO: set to false when stable
            'capability_type'       => 'bib',
            'hierarchical'          => false,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'custom-fields' )
        );
        if( !register_post_type( 'bib', $bib_post_def ) )
            wp_die( __( 'BuddyPress Courseware error while registering bibliography post type.', 'bpsp' ) );
        
        /**
        * Dummy post definition for storing the bibs entries as custom-fields
        */
        $bibdb_def = array(
           'post_title'    => 'BIBSDB',
           'post_status'   => 'draft',
           'post_type'     => 'bib',
        );
        
        if( !get_posts( $bibdb_def ) ) {
            $bibs_id =  wp_insert_post( $bibdb_def );
            if( !$bibs_id )
                wp_die( 'BuddyPress Courseware error while creating bibliography database.', 'bpsp' );
        }
    }
    
    /**
     * add_bib_caps( $user_id )
     *
     * Adds bibliography capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function add_bib_caps( $user_id ) {
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $user->add_cap( $c );
        
        //Treat super admins
        if( is_super_admin( $user_id ) )
            if ( !$user->has_cap( 'edit_others_courses' ) )
                $user->add_cap( 'edit_others_courses' );
    }
    
    /**
     * remove_bib_caps( $user_id )
     *
     * Adds bibliography capabilities to new $user_id
     *
     * @param Int $user_id ID of the user capabilities to be removed from
     */
    function remove_bib_caps( $user_id ) {
        //Treat super admins
        if( is_super_admin( $user_id) )
            return;
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( $user->has_cap( $c ) )
                $user->remove_cap( $c );
    }
    
    /**
     * has_bib_caps( $user_id )
     *
     * Checks if $user_id has bibliography management capabilities
     *
     * @param Int $user_id ID of the user capabilities to be checked
     * @return True if $user_id is eligible and False if not.
     */
    function has_bib_caps( $user_id ) {
        $is_ok = true;
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            $this->add_bib_caps( $user_id );
        }
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c )
            if ( !$user->has_cap( $c ) )
                $is_ok = false;
        
        return $is_ok;
    }
    
    /**
     * has_bibs()
     *
     * Loads all the course bibliography entries
     *
     * @param Int $post_id the id of the course
     * @return Mixed a set of entries, or null else
     */
    function has_bibs( $post_id = null ) {
        if( $post_id == null )
            $post_id = $this->current_parent;
        
        return get_post_meta( $post_id, $this->bid );
    }
    
    /**
     * load_bibs()
     *
     * Loads all bibliography database
     * @return Mixed Array of get_post_meta results, or null else
     */
    function load_bibs() {
        $bibdb_def = array(
            'post_title'    => 'BIBSDB',
            'post_status'   => 'draft',
            'post_type'     => 'bib',
        );
        $bibdb = get_posts( $bibdb_def );
        return get_post_meta( $bibdb[0]->ID, $this->bid );
    }
    
    /**
     * gen_bib_shortcode( $data )
     *
     * Generates a bibliography entry
     * @param Mixed Array $data
     * @return String generated shortcode
     */
    function gen_bib_shortcode( $data ) {
        $entry = '[' . $this->bid . ' ';
        // Build shortcode
        foreach ( $data as $n => $v ) {
            $n = sanitize_key( $n );
            $v = sanitize_text_field( $v );
            $entry .= $n . '="' . $v . '" ';
        }
        $entry .= ']';
        return $entry;
    }
    
    /**
     * add_bib( $data )
     *
     * Stores a bibliography entry to db or post_id if set
     * @param Mixed $data
     * @return Bool true on success and false on failure
     */
    function add_bib( $data ) {
        $post_id = null;
        if( isset( $data['post_id'] ) ) {
            $post_id = $data['post_id'];
            unset( $data['post_id'] );
        } else {
            // Get bibdb post_id
            $bibdb_def = array(
                'post_title'    => 'BIBSDB',
                'post_status'   => 'draft',
                'post_type'     => 'bib'
            );
            $bibdb = get_posts( $bibdb_def );
            $post_id = $bibdb[0]->ID;
        }
        
        $entry = $this->gen_bib_shortcode( $data );
        add_post_meta( $post_id, $this->bid, $entry );
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Bibliographies screens handler.
     * Handles uris like groups/ID/courseware/new_bibliography
     */
    function screen_handler( $action_vars ) {
        if( $action_vars[0] == 'new_bibliography' ) {
            add_filter( 'courseware_group_template', array( &$this, 'new_bib_screen' ) );
        }
        elseif( $action_vars[0] == 'import_bibliographies' ) {
            add_filter( 'courseware_group_template', array( &$this, 'import_bibs_screen' ) );
        }
        elseif ( $action_vars[0] == 'edit_bibliography' ) {   
            if( isset ( $action_vars[1] ) && null != $this->is_bib( $action_vars[1] ) ) {
                $this->current_parent = $action_vars[1];
                add_filter( 'courseware_group_template', array( &$this, 'edit_bib_screen' ) );
            }
            else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
        }
        elseif( isset ( $action_vars[2] ) && 'delete_bibliography' == $action_vars[2] )
            add_filter( 'courseware_group_template', array( &$this, 'delete_bib_screen' ) );
    }

    /**
     * add_nav_options()
     *
     * Adds bibliography specific navigations options
     *
     * @param Array $options A set of current nav options
     * @return Array containing new nav options
     */
    function add_nav_options( $options ) {
        global $bp;
        
        $this->home_uri = $options[__( 'Home', 'bpsp' )];
        
        if( $this->has_bib_caps( $bp->loggedin_user->id ) || is_super_admin() ) {
            $options[__( 'New Bibliography', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_bibliography';
        }
        
        return $options;
    }
    
    /**
     * new_bib_screen( $vars )
     *
     * Handles the screen for adding new bibliographies
     * @param Array $vars, an array of variables
     * @return Array $vars of modified variables
     */
    function new_bib_screen( $vars ) {
        global $bp;
        if( isset( $_POST['bib'] ) )
            $this->add_bib( $_POST['bib'] );
        $vars['name'] = 'new_bibliography';
        $vars['import_uri'] = $this->home_uri . '/import_bibliographies';
        return $vars;
    }
    
    /**
     * import_bibs_screen( $vars )
     *
     * Handles the screen for importing new bibliographies
     * @param Array $vars, an array of variables
     * @return Array $vars of modified variables
     */
    function import_bibs_screen( $vars ) {
        if( !class_exists( 'BibTeX_Parser') )
            include_once 'bibtex-parser.class.php';
        
        if( isset( $_POST['bib'] ) )
            $to_parse = $_POST['bib']['source'];
        
        $parser = new BibTeX_Parser( null, $to_parse );
        
        $vars['name'] = 'import_bibliographies';
        return $vars;
    }
    
    /**
     * bibs_screen()
     *
     * Hooks into courseware_below_* for handling bibs screen
     */
    function bibs_screen( $vars ) {
        global $bp;
        $nonce_name = 'bibs';
        
        $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
        
        if( $is_nonce && isset( $_POST['bib'] ) ) {
            
            if( !$this->has_bib_caps( $bp->loggedin_user->id ) )
                wp_die( __( 'BuddyPress Courseware Error while forbidden user tried to add bibliography entries.', 'bpsp' ) );
                
            //Add a new book
            if( isset( $_POST['bib']['book'] ) )
                if( $this->add_book( $_POST['bib']['book'] ) )
                    $vars['message'] = __( 'Book added', 'bpsp' );
                else
                    $vars['message'] = __( 'Book could not be added', 'bpsp' );
            // Add a new www entry
            elseif ( isset( $_POST['bib']['www'] ) )
                if( !$this->add_www( $_POST['bib']['www'] ) )
                    $vars['message'] = __( 'Entry added', 'bpsp' );
                else
                    $vars['message'] = __( 'Entry could not be added', 'bpsp' );
            /// Add a new wiki entry
            elseif ( isset( $_POST['bib']['wiki'] ) )
                if( $this->add_wiki( $_POST['bib']['wiki'] ) )
                    $vars['message'] = __( 'Entry added', 'bpsp' );
                else
                    $vars['message'] = __( 'Entry could not be added', 'bpsp' );
            else
                $vars['message'] = __( 'No bibliography entry could be added.', 'bpsp' );
        }
        
        if( isset( $vars['course'] ) && $vars['course']->ID )
            $this->current_parent = $vars['course']->ID;
        
        if( isset( $vars['assignment'] ) && $vars['assignment']->ID )
            $this->current_parent = $vars['assignment']->ID;
        
        $vars['has_bibs'] = true;
        $vars['bibs'] = $this->has_bibs( $this->current_parent );
        $vars['bibdb'] = $this->load_bibs();
        $vars['bibs_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * add_book()
     *
     * Adds a book to $this->current_parent
     *
     * @param Mixed $entry that contains information about current entry
     * @return True if added and false if failed
     */
    function add_book( $entry ) {
        _d( $entry );
        return true;
    }
    
    /**
     * add_www()
     *
     * Adds a web entry to $this->current_parent
     *
     * @param Mixed $entry that contains information about current entry
     * @return True if added and false if failed
     */
    function add_www( $entry ) {
        _d( $entry );
        return true;
    }
    
    /**
     * add_wiki()
     *
     * Adds a book to $this->current_parent
     *
     * @param Mixed $entry that contains information about current entry
     * @return True if added and false if failed
     */
    function add_wiki( $entry ) {
        _d( $entry );
        return true;
    }
}
?>