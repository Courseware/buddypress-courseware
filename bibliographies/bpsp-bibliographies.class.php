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
     * Capabilities required to edit/add assignments
     */
    var $caps = array(
        'add_bibliographies',
        'delete_bibliographies'
    );
    
    /**
     * Current parent post_id
     */
    var $current_parent = null;
    
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
        
        $bibs = get_post_meta( $post_id, $this->bid );
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