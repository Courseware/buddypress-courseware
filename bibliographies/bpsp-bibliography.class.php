<?php
/**
 * BPSP Class for bibliographies management on groups
 *
 * Will hook into both
 *  - courseware_below_courses
 *  - courseware_below_assignments
 */
class BPSP_Bibliography {
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
     * BPSP_Bibliography()
     *
     * Constructor. Loads all the hooks.
     */
    function BPSP_Bibliography() {
        add_action( 'courseware_new_teacher_added', array( &$this, 'add_bib_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( &$this, 'remove_bib_caps' ) );
        add_filter( 'courseware_course', array( &$this, 'bibs_screen' ) );
        add_filter( 'courseware_assignment', array( &$this, 'bibs_screen' ) );
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
        add_filter( 'courseware_group_nav_options', array( &$this, 'add_nav_options' ) );
        // Load api keys
        $this->worldcat_key = get_option( 'bpsp_worldcat_key' );
        $this->isbndb_key = get_option( 'bpsp_isbndb_key' );
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
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => false,
            'show_ui'               => BPSP_DEBUG,
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
        
        if( !get_option( 'bpsp_allow_only_admins' ) )
            if( !bp_group_is_admin() )
                $is_ok = false;
        
        return $is_ok;
    }
    
    /**
     * has_bibs( $post_id = null )
     *
     * Loads all the course bibliography entries
     *
     * @param Int $post_id the id of the course, default null
     * @return Mixed a set of entries, or null else
     */
    function has_bibs( $post_id = null ) {
        if( $post_id == null )
            $post_id = $this->current_parent;
        
        $bibs = get_post_meta( $post_id, $this->bid );
        if( empty( $bibs ) )
            return;
        
        foreach( $bibs as &$b ) {
            $b = $this->format( $b );
        }
        return $bibs;
    }
    
    /**
     * get_bibs( $post_id = null )
     *
     * Gets all the bibliography entries, static method
     *
     * @see BPSP_Bibliography::has_bibs()
     * @param Int $post_id the id of the post, default null
     * @return Mixed a set of entries, or null else
     */
    function get_bibs( $post_id = null ) {
        if( $post_id == null )
            return;
        
        return get_post_meta( $post_id, 'bibliography' );
    }
    
    /**
     * load_bibs( $formated = false )
     *
     * Loads all bibliography database
     * @param Bool $formated if you want formated data in html or plain
     * @return Mixed Array of get_post_meta results, or null else
     */
    function load_bibs( $formated = false ) {
        $bibdb_def = array(
            'post_title'    => 'BIBSDB',
            'post_status'   => 'draft',
            'post_type'     => 'bib',
        );
        $bibdb_post = get_posts( $bibdb_def );
        $bibdb = get_post_meta( $bibdb_post[0]->ID, $this->bid );
        $bibs = array();
        foreach( $bibdb as $b )
            //hash our bib for easier identification
            $bibs[ md5( $b ) ] = $formated ? $this->format( $b ) : $b;
        return $bibs;
    }
    
    /**
     * get_bib( $hash, $post_id = null )
     *
     * Get bibliography by hash
     * @param String $hash md5 hash of the entry
     * @param Int $post_id the id of the entry, if null, load from all bibs
     * @return String bib entry, or null else
     */
    function get_bib( $hash, $post_id = null ) {
        if( $post_id ) {
            $bibs = $this->has_bibs( $post_id );
            if( !empty( $bibs ) )
                foreach( $bibs as $b )
                    if( $b['hash'] == $hash )
                        return $b['raw'];
            
            return null;
        }
        else {
            $bibs = $this->load_bibs();
            return isset( $bibs[$hash] ) ? $bibs[$hash] : null;
        }
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
     * add_bib( $data, $shortcode = true. $post_id = null )
     *
     * Stores a bibliography entry to db or post_id if set
     * @param Mixed $data
     * @param Bool $shortcode if you want data to be shortcoded, default true
     * @param Int $post_id the id of the post to store, if null use post_id of the database
     * @return Bool true on success and false on failure
     */
    function add_bib( $data, $shortcode = true, $post_id = null ) {
        if( !$post_id ) {
            // Get bibdb post_id
            $bibdb_def = array(
                'post_title'    => 'BIBSDB',
                'post_status'   => 'draft',
                'post_type'     => 'bib'
            );
            $bibdb = get_posts( $bibdb_def );
            $post_id = $bibdb[0]->ID;
        }
        
        if( !isset( $data['type'] ) )
            $data['type'] = 'misc';
        else
            $data['type'] = strtolower( $data['type'] );
        
        if( count( $data ) > 1 && $shortcode )
            $entry = $this->gen_bib_shortcode( array_filter( $data ) );
        elseif( count( $data ) < 1 )
            $post_id = null; // force add_post_meta to fail
        else
            $entry = $data; // shortcode if false
        
        // Check if entry doesn't exist yet
        if( $this->get_bib( md5( $entry ), $post_id ) )
            return false;
        else
            return add_post_meta( $post_id, $this->bid, $entry );
    }
    
/**
     * update_bib( $data, $shortcode = true, $post_id =null , $old_shcode )
     *
     * Stores a bibliography entry to db or post_id if set
     * @param Mixed $data
     * @param Bool $shortcode if you want data to be shortcoded, default true
     * @param Int $post_id the id of the post to store, if null use post_id of the database
     * @param String $old_shcode shortcode of the old entry
     * @return Bool true on success and false on failure
     */
    function update_bib( $data, $shortcode = true, $post_id = null, $old_shcode ) {
        if( !$post_id ) {
            // Get bibdb post_id
            $bibdb_def = array(
                'post_title'    => 'BIBSDB',
                'post_status'   => 'draft',
                'post_type'     => 'bib'
            );
            $bibdb = get_posts( $bibdb_def );
            $post_id = $bibdb[0]->ID;
        }
        
        if( count( $data ) > 1 && $shortcode )
            $entry = $this->gen_bib_shortcode( array_filter( $data ) );
        elseif( count( $data ) < 1 )
            $post_id = null; // force add_post_meta to fail
        else
            $entry = $data; // shortcode if false
        
        // Check if entry doesn't exist yet
        if( $this->get_bib( md5( $entry ), $post_id ) )
            return false;
        else
            if( update_post_meta( $post_id, $this->bid, $entry, $old_shcode ) )
                return md5( $entry );
    }
    
    /**
     * format( $raw_bib )
     *
     * Formats a bibliography entry using its type;
     * @param String $raw_bib shortcode
     * @return String formated paragraph
     */
    function format( $raw_bib ) {
        $bib = array_filter( shortcode_parse_atts( $raw_bib ) );
        $content['raw'] = $raw_bib;
        $content['hash'] = md5( $raw_bib );
        
        if( isset( $bib['type'] ) ) {
            if( $bib['type'] == 'www' || $bib['type'] == 'misc' ) {
                $content['html'] = '<a href="' . $bib['url'] . '">' . $bib['title'] . '</a>';
                $content['plain'] = $bib['title'] . ' &mdash; ' . $bib['url'];
                $content['cover'] = BPSP_Bibliography_WebApis::get_www_cover();
            } else {
                $authors = $bib['author_lname'] . ' ' . $bib['author_fname'];
                if( '' != trim( $authors ) )
                    $authors = ' &mdash; ' . $authors;
                $content['plain'] = $bib['title'] . $authors;
                
                if( isset( $bib['url'] ) )
                    if( isset( $bib['citation'] ) )
                        $content['html'] = '<a href="' . $bib['url'] . '">' . $bib['citation'] . '</a>';
                    else
                        $content['html'] = '<a href="' . $bib['url'] . '">' . $content['plain'] . '</a>';
                else
                    if( isset( $bib['citation'] ) )
                        $content['html'] = $bib['citation'];
                    else
                        $content['html'] = $content['plain'];
                if( !isset( $bib['isbn'] ) )
                    $bib['isbn'] = '';
                $content['cover'] = BPSP_Bibliography_WebApis::get_book_cover( $bib['isbn'] );
                $content['data'] = $bib;
            }
        }
        return $content;
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Bibliographies screens handler.
     * Handles uris like groups/ID/courseware/new_bibliography
     */
    function screen_handler( $action_vars ) {
        if( isset ( $action_vars[0] ) && 'new_bibliography' == $action_vars[0] ) {
            do_action( 'courseware_bibliography_screen' );
            do_action( 'courseware_new_bibliography_screen' );
            add_filter( 'courseware_group_template', array( &$this, 'new_bib_screen' ) );
        }
        elseif( isset ( $action_vars[0] ) && 'import_bibliographies' == $action_vars[0] ) {
            add_filter( 'courseware_group_template', array( &$this, 'import_bibs_screen' ) );
        }
        elseif ( isset ( $action_vars[0] ) && 'edit_bibliography' == $action_vars[0] ) {
            do_action( 'courseware_edit_bibliography_screen' );
            add_filter( 'courseware_group_template', array( &$this, 'edit_bib_screen' ) );
        }
        elseif( isset ( $action_vars[0] ) && 'delete_bibliography' == $action_vars[0] )
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
            $options[__( 'Bibliography', 'bpsp' )] = $options[__( 'Home', 'bpsp' )] . '/new_bibliography';
        }
        
        return $options;
    }
    
    /**
     * delete_bib_screen( $vars )
     *
     * Handles the screen for deleting bibliographies
     * @param Array $vars, an array of variables
     * @return Array $vars modified
     */
    function delete_bib_screen( $vars ) {
        $nonce_name = 'delete_bib';
        $to_redirect = true;
        
        if( !$this->has_bib_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to delete bibliography.', 'bpsp' );
            return $vars;
        }
        
        $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
        if( !$is_nonce && isset( $_GET['bhash'] ) ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while deleting a post.', 'bpsp' );
            return $vars;
        }
        
        $data = explode( ',', sanitize_text_field( $_GET['bhash'] ) );
        $bhash = $data[0];
        $post_id = null;
        if( isset( $data[1] ) && !empty( $data[1] ) )
            $post_id = $data[1];
        else {
            $to_redirect = false;
            $bibdb_def = array(
                'post_title'    => 'BIBSDB',
                'post_status'   => 'draft',
                'post_type'     => 'bib',
            );
            $bibdb_post = get_posts( $bibdb_def );
            $post_id = $bibdb_post[0]->ID;
        }
        
        if( $post_id ) {
            $bib = $this->get_bib( $bhash, $post_id );
            if( $bib != null ) {
                if( delete_post_meta( $post_id, $this->bid, $bib ) )
                    $vars['message'] = __( 'Entry deleted.', 'bpsp' );
                else
                    $vars['error'] = __( 'Entry could not be deleted.', 'bpsp' );
            } else
                $vars['error'] = __( 'Entry could not be found.', 'bpsp' );
        }
        else
            $vars['error'] = __( 'No Bibliography database was created.', 'bpsp' );
        
        if( $to_redirect )
            $vars['redirect_to'] = $_SERVER['HTTP_REFERER'];
        
        return $this->new_bib_screen( $vars );
    }
    
    /**
     * edit_bib_screen( $vars )
     *
     * Handles screen for editing the bibliographies
     * @param Array $vars, an array of variables
     * @return Array $vars modified
     */
    function edit_bib_screen( $vars ) {
        $nonce_name = 'edit_bib';
        $nonce_delete_name = 'delete_bib';
        $nonce_edit_name = $nonce_name;
        
        if( !$this->has_bib_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to edit bibliography.', 'bpsp' );
            return $vars;
        }
        
        if( isset( $_POST['_wpnonce'] ) && isset( $_GET['bhash'] ) )
            if( !wp_verify_nonce( $_POST['_wpnonce'], $nonce_name ) ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while editing bibliography.', 'bpsp' );
            return $vars;
        }
        
        $data = explode( ',', sanitize_text_field( $_GET['bhash'] ) );
        $bhash = $data[0];
        $new_bhash = null;
        $post_id = null;
        
        if( isset( $data[1] ) && !empty( $data[1] ) ) {
            $post_id = $data[1];
            // Get the permalink for parent
            if( BPSP_Assignments::is_assignment( $post_id ) )
                $vars['back_uri'] = $vars['nav_options'][ __( 'Home', 'bpsp' ) ] . '/assignment/' . $post_id;
            
            if( BPSP_Courses::is_course( $post_id ) )
                $vars['back_uri'] = $vars['nav_options'][ __( 'Home', 'bpsp' ) ] . '/course/' . $post_id;
        }
        else {
            $bibdb_def = array(
                'post_title'    => 'BIBSDB',
                'post_status'   => 'draft',
                'post_type'     => 'bib',
            );
            $bibdb_post = get_posts( $bibdb_def );
            $post_id = $bibdb_post[0]->ID;
        }
        
        if( $post_id ) {
            $old_bib = $this->get_bib( $bhash, $post_id );
            if( $old_bib != null && isset( $_POST['bib'] ) ) {
                $new_bhash = $this->update_bib( $_POST['bib'], true, $post_id, $old_bib );
                if( null != $new_bhash ) {
                    $bhash = $new_bhash; // Update for the next query
                    $vars['message'] = __( 'Entry updated.', 'bpsp' );
                }
                else {
                    $vars['error'] = __( 'Entry could not be updated. Or nothing changed.', 'bpsp' );
                }
            } elseif( !$old_bib && isset( $_POST['bib'] ) )
                $vars['error'] = __( 'Entry could not be found.', 'bpsp' );
        }
        else
            $vars['error'] = __( 'No Bibliography database was created.', 'bpsp' );
        
        $vars['name'] = 'edit_bibliography';
        $vars['bib'] = shortcode_parse_atts( $this->get_bib( $bhash, $post_id ) );
        $vars['has_bibs'] = true;
        $vars['post_id'] = null;
        $vars['has_bib_caps'] = $this->has_bib_caps( $bp->loggedin_user->id );
        $vars['bibs'] = $this->load_bibs( true );
        $vars['bibs_delete_permalink'] = $vars['current_uri'] . '/delete_bibliography';
        $vars['bibs_delete_uri'] = add_query_arg( '_wpnonce', wp_create_nonce( $nonce_delete_name ), $vars['bibs_delete_permalink'] );
        $vars['bibs_edit_uri'] = $vars['current_uri'] . '/edit_bibliography';
        $vars['bibs_form_uri'] = add_query_arg( 'bhash', $bhash . ',' . $post_id, $vars['bibs_edit_uri'] );
        $vars['bibs_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        return $vars;
    }
    
    /**
     * new_bib_screen( $vars )
     *
     * Handles the screen for adding new bibliographies
     * @param Array $vars, an array of variables
     * @return Array $vars modified
     */
    function new_bib_screen( $vars ) {
        global $bp;
        $nonce_name = 'bibs';
        $nonce_delete_name = 'delete_bib';
        $nonce_edit_name = 'edit_bib';
        
        if( !$this->has_bib_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new bibliography.', 'bpsp' );
            return $vars;
        }
        
        $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
        if( !$is_nonce && isset( $_POST['bib'] ) ) {
            $vars['die'] = __( 'BuddyPress Courseware Nonce Error while adding bibliography.', 'bpsp' );
            return $vars;
        }
        
        if( isset( $_POST['bib'] ) && $_POST['bib']['type'] ) {
            $data = array_filter( $_POST['bib'] );
            if( count( $data ) < 2 )
                $data = null; // force failure
            if( $this->add_bib( $data ) )
                $vars['message'] = __( 'Entry added.', 'bpsp' );
            else
                $vars['error'] = __( 'Entry could not be added', 'bpsp' );
        }
        // Add a new www entry
        elseif ( !empty( $_POST['bib']['www']['title'] ) && !empty( $_POST['bib']['www']['url'] ) )
            if( $this->add_www( $_POST['bib']['www'], $post_id ) )
                $vars['message'] = __( 'Entry added', 'bpsp' );
            else
                $vars['error'] = __( 'Entry could not be added', 'bpsp' );
        //Add a new book
        elseif( !empty( $_POST['bib']['book'] ) )
            if( $this->add_book( $_POST['bib']['book'], $post_id ) )
                $vars['message'] = __( 'Book added', 'bpsp' );
            else
                $vars['error'] = __( 'Book could not be added', 'bpsp' );
        
        $vars['name'] = 'new_bibliography';
        $vars['import_uri'] = $this->home_uri . '/import_bibliographies';
        $vars['has_bibs'] = true;
        $vars['hide_existing'] = true;
        $vars['post_id'] = null;
        $vars['has_bib_caps'] = $this->has_bib_caps( $bp->loggedin_user->id );
        $vars['bibs'] = $this->load_bibs( true );
        $vars['bibs_delete_permalink'] = $vars['current_uri'] . '/delete_bibliography';
        $vars['bibs_delete_uri'] = add_query_arg( '_wpnonce', wp_create_nonce( $nonce_delete_name ), $vars['bibs_delete_permalink'] );
        $vars['bibs_edit_uri'] = $vars['current_uri'] . '/edit_bibliography';
        $vars['bibs_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
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
        
        if( !$this->has_bib_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new bibliography.', 'bpsp' );
            return $vars;
        }
        
        if( isset( $_POST['bib'] ) )
            $to_parse = $_POST['bib']['source'];
        
        $parsed = new BibTeX_Parser( null, $to_parse );
        
        for( $i = 0; $i <= $parsed->count; $i++ ) {
            preg_match( '/@(.*?){/', $parsed->items['raw'][$i], $entry_type );
            if( isset( $entry_type[1] ) )
                $entry_type = $entry_type[1];
            
            if($entry_type == 'book') {
                $type = 'monograph';
            }
            elseif($entry_type == 'phdthesis' || $entry_type == 'mastersthesis') {
                $type = 'unpublished';
            }
            elseif($entry_type == 'inbook' || $entry_type == 'incollection') {
                $type = 'volumechapter';
            }
            else {
                $type = $entry_type;
            }
            
            $author = explode( ' ', $parsed->items['author'][$i], 2 );
            if ($author[1]) {
                $author_last = $author[1];
                $author_first = $author[0];
            } else {
                $author_last = $author[0];
                $author_first = "";
            }
            
            $url = str_replace( '\url', '', $parsed->items['url'][$i] );
            
            $new_bib = array(
                'author_lname'  => $author_last,
                'author_fname'  => $author_first,
                'title'         => $parsed->items['title'][$i],
                'jtitle'        => $parsed->items['journal'][$i],
                'vol'           => $parsed->items['volume'][$i],
                'pub'           => $parsed->items['publisher'][$i],
                'pubdate'       => $parsed->items['year'][$i],
                'url'           => $url,
                'pages'         => $parsed->items['pages'][$i],
                'desc'          => $parsed->items['abstract'][$i],
                'type'          => $type
            );
            
            if( $this->add_bib( $new_bib ) )
                $vars['message'] = $parsed->count + 1 . ' ' .
                    __( 'entries were imported.', 'bpsp' );
            else
                $vars['message'] = $parsed->count + 1 . ' ' .
                    __( 'entries were not imported (caused by duplication or misformat).', 'bpsp' );
        }
        
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
        $nonce_delete_name = 'delete_bib';
        $nonce_edit_name = 'edit_bib';
        
        // Are we dealing with courses or assignments?
        if( isset( $vars['assignment'] ) )
            $post_id = $vars['assignment']->ID;
        elseif( isset( $vars['course'] ) )
            $post_id = $vars['course']->ID;
        else
            $post_id = null;
            
        if( $post_id )
            $this->current_parent = $post_id;
        
        $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
        if( $is_nonce && isset( $_POST['bib'] ) ) {
            
            if( !$this->has_bib_caps( $bp->loggedin_user->id ) && !is_super_admin() ) {
                $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add bibliography entries.', 'bpsp' );
                return $vars;
            }
            
            // Add an existing bib
            if( isset( $_POST['bib']['existing'] ) && !empty( $_POST['bib']['existing'] ) ) {
                $data = $this->get_bib( $_POST['bib']['existing'] );
                if( $this->add_bib( $data, false, $post_id ) )
                    $vars['message'] = __( 'Bibliography added', 'bpsp' );
                else
                    $vars['error'] = __( 'Bibliography could not be added', 'bpsp' );
            }
            // Add a new www entry
            elseif ( !empty( $_POST['bib']['www']['title'] ) && !empty( $_POST['bib']['www']['url'] ) )
                if( $this->add_www( $_POST['bib']['www'], $post_id ) )
                    $vars['message'] = __( 'Entry added', 'bpsp' );
                else
                    $vars['error'] = __( 'Entry could not be added', 'bpsp' );
            //Add a new book
            elseif( !empty( $_POST['bib']['book'] ) )
                if( $this->add_book( $_POST['bib']['book'], $post_id ) )
                    $vars['message'] = __( 'Book added', 'bpsp' );
                else
                    $vars['error'] = __( 'Book could not be added', 'bpsp' );
            else
                $vars['error'] = __( 'No bibliography entry could be added.', 'bpsp' );
        }
        
        if( isset( $vars['course'] ) && $vars['course']->ID )
            $this->current_parent = $vars['course']->ID;
        
        if( isset( $vars['assignment'] ) && $vars['assignment']->ID )
            $this->current_parent = $vars['assignment']->ID;
        
        $vars['has_bibs'] = true;
        $vars['post_id'] = $this->current_parent;
        $vars['has_bib_caps'] = $this->has_bib_caps( $bp->loggedin_user->id );
        $vars['bibs'] = $this->has_bibs( $this->current_parent );
        $vars['bibdb'] = $this->load_bibs( true );
        $vars['bibs_nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['bibs_delete_permalink'] = $vars['current_uri'] . '/delete_bibliography';
        $vars['bibs_edit_permalink'] = $vars['current_uri'] . '/edit_bibliography';
        $vars['bibs_delete_uri'] = add_query_arg( '_wpnonce', wp_create_nonce( $nonce_delete_name ), $vars['bibs_delete_permalink'] );
        $vars['bibs_edit_uri'] = $vars['current_uri'] . '/edit_bibliography';
        return $vars;
    }
    
    /**
     * add_book( $entry, $post_id = null )
     *
     * Adds a book to $this->current_parent
     *
     * @param Mixed $entry that contains information about current entry
     * @param Int $post_id the id of the post to assign to, default null which defaults to bibdb post_id
     * @return True if added and false if failed
     */
    function add_book( $entry, $post_id = null ) {
        if( isset( $entry['title'] ) && $entry['title'] != '' ) {
            $api = new BPSP_Bibliography_WebApis( array( 'worldcat' => $this->worldcat_key ) );
            $item = $api->worldcat_opensearch( $entry['title'] );
            if( !empty( $item ) )  {
                $item[0]['type'] = 'book';
                if( !empty( $entry['desc'] ) )
                    $item[0]['desc'] = $entry['desc'];
                
                // add to global database
                if( $post_id == null )
                    return $this->add_bib( $item[0] );
                elseif( $this->add_bib( $item[0], true, $post_id ) ) { // add to post_id
                    $this->add_bib( $item[0] ); // also try to add it to global database
                    return true;
                }
            }
        } elseif ( isset( $entry['isbn'] ) && $entry['isbn'] != '' ) {
            $api = new BPSP_Bibliography_WebApis( array( 'isbndb' => $this->isbndb_key ) );
            $item = $api->isbndb_query( $entry['isbn'] );
            if( !empty( $item ) )  {
                $item[0]['type'] = 'book';
                if( !empty( $entry['desc'] ) )
                    $item[0]['desc'] = $entry['desc'];
                
                // add to global database
                if( $post_id == null )
                    return $this->add_bib( $item[0] );
                elseif( $this->add_bib( $item[0], true, $post_id ) ) { // add to post_id
                    $this->add_bib( $item[0] ); // also try to add it to global database
                    return true;
                }
            }
        }
        return false;
    }
    
    /**
     * add_www( $entry, $post_id = null )
     *
     * Adds a web entry to $this->current_parent
     *
     * @param Mixed $entry that contains information about current entry
     * @param Int $post_id the id of the post to assign to, default null which defaults to bibdb post_id
     * @return True if added and false if failed
     */
    function add_www( $entry, $post_id = null ) {
        if( !empty( $entry['title'] ) &&
            !empty( $entry['url'] )
        ) {
            $item['type'] = 'misc';
            $item['title'] = $entry['title'];
            $item['url'] = $entry['url'];
            
            // add to global database
            if( $post_id == null )
                return $this->add_bib( $item );
            elseif( $this->add_bib( $item, true, $post_id ) ) { // add to post_id
                $this->add_bib( $item ); // also try to add it to global database
                return true;
            }
        }
        return false;
    }
}
?>