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
    static $current_lecture = null;
    
    /**
     * __constructor()
     *
     * Constructor. Loads the hooks and actions.
     */
    function __construct() {
        add_action( 'courseware_new_teacher_added', array( $this, 'add_caps' ) );
        add_action( 'courseware_new_teacher_removed', array( $this, 'remove_caps' ) );
        add_action( 'courseware_group_screen_handler', array( $this, 'screen_handler' ) );
        add_filter( 'courseware_course', array( $this, 'lectures_screen' ) );
        add_filter( 'post_type_link', array( __CLASS__, 'get_permalink' ), 10, 2 );
        add_filter( 'page_css_class', array( __CLASS__, 'css_class' ), 10, 2 );
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
        foreach( $this->caps as $c ) {
            if ( !$user->has_cap( $c ) ) {
                $user->add_cap( $c );
			}
		}
        
        //Treat super admins
        if( is_super_admin( $user_id ) ) {
            if ( !$user->has_cap( 'edit_others_lectures' ) ) {
                $user->add_cap( 'edit_others_lectures' );
			}
		}
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
        if( is_super_admin( $user_id) ) {
            return;
		}
        
        $user = new WP_User( $user_id );
        foreach( $this->caps as $c ) {
            if ( $user->has_cap( $c ) ) {
                $user->remove_cap( $c );
			}
		}
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
        foreach( $this->caps as $c ) {
            if ( !$user->has_cap( $c ) ) {
                $is_ok = false;
			}
		}
        
        if( !get_option( 'bpsp_allow_only_admins' ) ) {
            if( !bp_group_is_admin() ) {
                $is_ok = false;
			}
		}
        
        return $is_ok;
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Lecture screens handler.
     * Handles uris like groups/ID/courseware/lecture/args
     */
    function screen_handler( $action_vars ) {
        
        if( reset( $action_vars ) == 'new_lecture' ) {
            //Load editor
            add_action( 'bp_head', array( $this, 'load_editor' ) );
            add_filter( 'courseware_group_template', array( $this, 'new_lecture_screen' ) );
        }
        elseif( reset( $action_vars ) == 'lecture' ) {
            $current_lecture = BPSP_Lectures::is_lecture( $action_vars[1] );
            
            if( isset ( $action_vars[1] ) && null != $current_lecture ) {
                self::$current_lecture = $current_lecture;
			} else {
                wp_redirect( wp_redirect( get_option( 'siteurl' ) ) );
            }
            
            if( isset ( $action_vars[2] ) && 'edit' == $action_vars[2] ) {
                add_action( 'bp_head', array( $this, 'load_editor' ) );
                add_filter( 'courseware_group_template', array( $this, 'edit_lecture_screen' ) );
            } elseif( isset ( $action_vars[2] ) && 'delete' == $action_vars[2] ) {
                add_filter( 'courseware_group_template', array( $this, 'delete_lecture_screen' ) );
            } else {
                do_action( 'courseware_lecture_screen' );
                add_filter( 'courseware_group_template', array( $this, 'single_lecture_screen' ) );
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
        
        if( is_object( $lecture_identifier ) && $lecture_identifier->post_type == "lecture" ) {
            if( $lecture_identifier->group[0]->name == $bp->groups->current_group->id ) {
                return $lecture_identifier;
			} else {
                return null;
			}
		}
        
        if(	
        	!$lecture_identifier &&
        	isset( self::$current_lecture ) && 
        	!is_null( self::$current_lecture ) && 
        	get_class( (object) self::$current_lecture ) == __CLASS__ 
        ) {
            return self::$current_lecture;
		}
        
        $lecture_query = array(
            'post_type' => 'lecture',
            'group_id' => $bp->groups->current_group->id,
        );
        
        if ( $lecture_identifier != null ) {
            if( is_numeric( $lecture_identifier ) ) {
                $lecture_query['p'] = $lecture_identifier;
			} else {
                $lecture_query['name'] = $lecture_identifier;
			}
        }
        $lecture = get_posts( $lecture_query );
        
        if( reset( $lecture ) ) {
            $lecture[0]->group = wp_get_object_terms( $lecture[0]->ID, 'group_id' );
            $lecture_course = wp_get_object_terms( $lecture[0]->ID, 'course_id' );
            $lecture[0]->course = !empty( $lecture_course ) ? BPSP_Courses::is_course( reset( $lecture_course )->name ) : null;
            $lecture[0]->permalink = $courseware_uri . 'lecture/' . $lecture[0]->post_name;
            return $lecture[0];
        } else {
            return null;
		}
    }
    
    /**
     * has_lectures( $group_id = null )
     *
     * Checks if a $group_id has lectures
     *
     * @param Int $group_id of the group to be checked
     * @return Mixed Lecture objects if lectures exist and null if not.
     */
    public static function has_lectures( $group_id = null ) {
        global $bp;
        $lecture_ids = null;
        $lectures = array();
        
        if( empty( $group_id ) ) {
            $group_id = $bp->groups->current_group->id;
		}
        
        $term_id = get_term_by( 'slug', $group_id, 'group_id' );
        if( !empty( $term_id ) ) {
            $lecture_ids = get_objects_in_term( $term_id->term_id, 'group_id' );
		}
        
        if( !empty( $lecture_ids ) ) {
            arsort( $lecture_ids ); // Get latest entries first
		} else {
            return null;
		}
        
        foreach ( $lecture_ids as $aid ) {
            $lectures[] = self::is_lecture( $aid );
		}
        
        return array_filter( $lectures );
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
            $vars['die'] = __( 'BuddyPress Courseware Error while forbidden user tried to add a new lecture.', 'bpsp' );
            return $vars;
        }
        
        // Save new lecture
        $course = reset( BPSP_Courses::has_courses( $bp->groups->current_group->id ) );
        if( isset( $_POST['lecture'] ) && $_POST['lecture']['object'] == 'group' && isset( $_POST['_wpnonce'] ) ) {
            $new_lecture = $_POST['lecture'];
            $is_nonce = wp_verify_nonce( $_POST['_wpnonce'], $nonce_name );
            
			if( true != $is_nonce ) {
                $vars['error'] = __( 'Nonce Error while adding a lecture.', 'bpsp' );
			} else {
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
                        wp_set_post_terms( $new_lecture_id, $course->ID, 'course_id' );
                        self::$current_lecture = $this->is_lecture( $new_lecture_id );
                        
                        $vars['message'] = __( 'New lecture was added.', 'bpsp' );
                        do_action( 'courseware_lecture_added', self::$current_lecture );
                        do_action( 'courseware_lecture_activity', self::$current_lecture, 'add' );
                        return $this->single_lecture_screen( $vars );
                    } else {
                        $vars['error'] = __( 'New lecture could not be added.', 'bpsp' );
					}
                } else {
                    $vars['error'] = __( 'Please fill in all the fields.', 'bpsp' );
				}
			}
        }
        
        $vars['posted_data'] = isset( $_POST['lecture'] ) ? $_POST['lecture'] : false;
        $vars['course'] = $course;
        $vars['lectures'] = $this->has_lectures( $bp->groups->current_group->id );
        $vars['name'] = 'new_lecture';
        $vars['group_id'] = $bp->groups->current_group->id;
        $vars['user_id'] = $bp->loggedin_user->id;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['trail'] = array(
            $vars['course']->post_title => $vars['course']->permalink,
            __( 'New Lecture' ) => '',
        );
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
        // global $bp;
        return $vars;
    }
    
    /**
     * lectures_screen( $vars )
     *
     * Hooks into screen_handler
     * Adds a UI to list lectures.
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function lectures_screen( $vars ) {
        global $bp;
        $args = array(
            'numberposts'   => '-1',
            'post_type'     => 'lecture',
            'group_id'      => $bp->groups->current_group->id,
            'orderby'       => 'menu_order, post_title',
            'link_before'   => '',
            'link_after'   => ''
        );
        $lectures = get_posts( $args );
        
        $vars['lectures_hanlder_uri'] = $vars['current_uri'] . '/lectures/';
        $vars['lectures'] = walk_page_tree( $lectures, 0, 0, $args );
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
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) ) {
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], 'bookmark' );
		}
        
        $lecture = $this->is_lecture( self::$current_lecture );
        
        if( $is_nonce ) {
            update_user_meta( get_current_user_id(), 'bookmark_' . bp_get_group_id(), $lecture->ID );
		}
        
        if(  $this->has_lecture_caps( $bp->loggedin_user->id ) || is_super_admin() ) {
            $vars['show_edit'] = true;
		} else {
            $vars['show_edit'] = null;
		}
        
        if( !$lecture ) {
            $vars['die'] = __( 'BuddyPress Courseware Error! Cheatin\' Uh?', 'bpsp' );
		}

        $vars['name'] = 'single_lecture';
        $vars['lecture_permalink'] = self::$current_lecture->permalink;
        $vars['lecture_edit_uri'] = self::$current_lecture->permalink . '/edit';
        $vars['lecture_bookmark_uri'] = add_query_arg( '_wpnonce', wp_create_nonce( 'bookmark' ), self::$current_lecture->permalink );
        $vars['bookmarked'] = get_user_meta( get_current_user_id(), 'bookmark_' . bp_get_group_id(), true );
        $vars['lecture'] = $lecture;
        $vars['next'] = $this->next_lecture( $lecture );
        $vars['prev'] = $this->prev_lecture( $lecture );
        
        $vars['trail'] = array(
            self::$current_lecture->course->post_title =>
            self::$current_lecture->course->permalink,
            self::$current_lecture->post_title => self::$current_lecture->permalink,
        );
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
        $lecture = $this->is_lecture( self::$current_lecture );
        $nonce_name = 'delete_lecture';
        $is_nonce = false;
        
        if( isset( $_GET['_wpnonce'] ) ) {
            $is_nonce = wp_verify_nonce( $_GET['_wpnonce'], $nonce_name );
		}
        
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
        $updated_lecture_id = self::$current_lecture;
        
        $old_lecture = $this->is_lecture( self::$current_lecture );
        
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
        $vars['lecture_edit_uri'] = $vars['current_uri'] . '/lecture/' . self::$current_lecture->post_name . '/edit/';
        $vars['lecture_delete_uri'] = $vars['current_uri'] . '/lecture/' . self::$current_lecture->post_name . '/delete/';
        $vars['lecture_permalink'] = $vars['current_uri'] . '/lecture/' . self::$current_lecture->post_name;
        $vars['nonce'] = wp_nonce_field( $nonce_name, '_wpnonce', true, false );
        $vars['delete_nonce'] = add_query_arg( '_wpnonce', wp_create_nonce( 'delete_lecture' ), $vars['lecture_delete_uri'] );
        $vars['trail'] = array(
            self::$current_lecture->course->post_title => self::$current_lecture->course->permalink,
            __( 'Editing Lecture: ', 'bpsp' ) . self::$current_lecture->post_title => self::$current_lecture->permalink,
        );
        return $vars;
    }
    
    /**
     * Comparer for post objects, to be used as `usort` callback
     */
    function cmp_menu_order( $a, $b ) {
        if( $a->menu_order == $b->menu_order ) {
            return 0;
		}
		
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
        if( empty( $lectures ) ) {
            return null;
		}
        
        // Try to sort them by menu_order
        usort( $lectures, array( 'BPSP_Lectures', 'cmp_menu_order' ) );
		
        // Use WordPress's hierarchical algorithm
        $hierarchy = get_page_hierarchy( $lectures, 0 );
		
        // Find current position in hierarchy
        if( reset( $hierarchy ) != $lecture->post_name ) {
            while( next( $hierarchy ) ) {
                if( current( $hierarchy ) == $lecture->post_name ) {
                    break;
				}
			}
            $next = next( $hierarchy );
        } else {
            $next = next( $hierarchy );
		}
        
        if( $next ) {
            return BPSP_Lectures::is_lecture( $next );
		} else {
            return null;
		}
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
        
		if( empty( $lectures ) ) {
            return null;
		}
        
        // Try to sort them by menu_order
        usort( $lectures, array( 'BPSP_Lectures', 'cmp_menu_order' ) );
        // Use WordPress's hierarchical algorithm
        $hierarchy = get_page_hierarchy( $lectures, 0 );
        // Find current position in hierarchy
        while( next( $hierarchy ) ) {
            if( current( $hierarchy ) == $lecture->post_name ) {
                break;
			}
		}
        
        $prev = prev( $hierarchy );
        if( $prev ) {
            return BPSP_Lectures::is_lecture( $prev );
		} else {
            return null;
		}
    }
    
    /**
     * get_permalink( $permalink, $lecture )
     * Permalink generator for courseware lectures
     *
     * @param String $permalink, the WordPress generated guid
     * @param Mixed $lecture, WordPress post object
     * @return String, the new courseware permalink
     */
    function get_permalink( $permalink, $lecture ) {
        global $bp;
        
        if( is_object( $lecture ) && $lecture->post_type == 'lecture' && is_plugin_active( 'buddypress/bp-loader.php' ) ) {
            $courseware_uri = bp_get_group_permalink( $bp->groups->current_group ) . 'courseware/lecture/' ;
            return $courseware_uri . $lecture->post_name;
        } else {
            return $permalink;
		}
    }
    
    /**
     * css_class( $css_classes, $lecture )
     * CSS classes generator to append menu order of the lecture, required for js sorting
     *
     * @param Mixed $css_classes, existing css classes array
     * @param Mixed $lecture, the WordPress object
     * @return Mixed, the extended $css_classes array
     */
    function css_class( $css_classes, $lecture ) {
        global $bp;
        
        if( is_object( $lecture ) && $lecture->post_type == 'lecture' ) {
            $css_classes[] = $lecture->menu_order . "-order";
            return $css_classes;
        } else {
            return $css_classes;
		}
    }
    
    /**
     * load_editor()
     *
     * Loads editor scripts and styles
     */
    function load_editor() {
        do_action( 'courseware_editor' );
        wp_enqueue_style( 'datetimepicker' );
    }
   
   /**
     * register_post_types()
     *
     * Static function to register the lecture post type, taxonomies and capabilities.
     */
    public static function register_post_types() {
        $lecture_post_def = array(
            'label'                 => __( 'Lecture', 'bpsp' ),
            'singular_label'        => __( 'Lecture', 'bpsp' ),
            'description'           => __( 'BuddyPress Courseware Lectures', 'bpsp' ),
            'public'                => BPSP_DEBUG,
            'publicly_queryable'    => false,
            'exclude_from_search'   => true,
            'show_ui'               => BPSP_DEBUG,
            'capability_type'       => 'lecture',
            'hierarchical'          => true,
            'rewrite'               => false,
            'query_var'             => false,
            'supports'              => array( 'title', 'editor', 'author', 'page-attributes' ),
            'taxonomies'            => array( 'group_id' )
        );
        if( !register_post_type( 'lecture', $lecture_post_def ) ) {
            wp_die( __( 'BuddyPress Courseware error while registering lecture post type.', 'bpsp' ) );
		}
        
        $course_rel_def = array(
            'public'        => BPSP_DEBUG,
            'show_ui'       => BPSP_DEBUG,
            'hierarchical'  => false,
            'label'         => __( 'Course ID', 'bpsp'),
            'query_var'     => 'course_id',
            'rewrite'       => false,
            'capabilities'  => array(
                'manage_terms'  => 'manage_course_id',
                'edit_terms'    => 'manage_course_id',
                'delete_terms'  => 'manage_course_id',
                'assign_terms'  => 'edit_courses'
                )
        );
        register_taxonomy( 'course_id', array( 'lecture' ), $course_rel_def );
        
        if( !get_taxonomy( 'course_id' ) ) {
            wp_die( __( 'BuddyPress Courseware error while registering course taxonomy.', 'bpsp' ) );
		}
    }
}
