<?php
/**
 * BPSP Dashboards handles user dashboard
 */
class BPSP_Dashboards {
    
    /**
     * BPSP_Dashboards()
     *
     * Constructor, loads the hooks
     */
    function BPSP_Dashboards() {
        add_action( 'courseware_group_screen_handler', array( &$this, 'screen_handler' ) );
		add_shortcode( 'courseware_dashboard', array( &$this, 'shortcode_courseware_dashboard' ) );
    }
    
    /**
     * screen_handler( $action_vars )
     *
     * Dashboard screen handler.
     * Handles uris like groups/ID/courseware/home
     */
    function screen_handler( $action_vars ) {
        if( !isset( $action_vars[0] ) || $action_vars[0] == 'home' ) {
            do_action( 'courseware_group_dashboard' );
            add_filter( 'courseware_group_template', array( &$this, 'group_dashboard' ) );
        }
    }
    
    function get_group_courseware( $group_id = null ) {
        global $bp;
        if( !$group_id )
            $group_id = $bp->groups->current_group->id;
        
        $group_data['bibliography'] = array();
        $group_data['assignments_count'] = 0;
        $group_data['responses_count'] = 0;
        $group_data['own_responses_count'] = 0;
        $group_data['assignment_topics_count'] = 0;
        $group_data['user_grades'] = array();
        $group_data['user_bookmark'] = null;
        $group_data['courses'] = ( array )BPSP_Courses::has_courses( $group_id );
        $group_data['lectures'] = ( array )BPSP_Lectures::has_lectures( $group_id );
        $group_data['assignments'] = ( array )BPSP_Assignments::has_assignments( $group_id );
        $group_data['schedules'] = BPSP_Schedules::has_schedules( $group_id );
        $group_data['lectures'] = BPSP_Lectures::has_lectures( $group_id );
        $posts = array_merge( $group_data['courses'], $group_data['assignments'] );
        
        if( $posts )
        foreach ( $posts as &$post ) {
            // Get group bibs
            $group_data['bibliography'] = array_merge( $group_data['bibliography'], BPSP_Bibliography::get_bibs( $post->ID ) );
            // Get group responses
            if( $post->post_type == 'assignment' ) {
                // Forum threads
                if( get_post_meta( $post->ID, 'topic_link', true ) != '' )
                    $group_data['assignment_topics_count'] += 1;
                // Responses
                $post->responses = get_children( array(
                    'post_parent' => $post->ID,
                    'post_type' => 'response'
                ) );
                
                foreach( $post->responses as $pr ) {
                    if( $pr->post_author == get_current_user_id() )
                        $group_data['own_responses_count']++;
                    $group_data['responses_count'] ++;
                }
                
                // Gradebook
                $group_data['user_grades'][] = BPSP_Gradebook::load_grade_by_user_id( $post->ID, $bp->loggedin_user->id );
            }
        }
        
        $bookmark = get_user_meta( get_current_user_id(), 'bookmark_' . bp_get_group_id(), true );
        if( $bookmark )
            $group_data['user_bookmark'] = BPSP_Lectures::is_lecture( $bookmark );
        
        $group_data['assignments_count'] = count( $group_data['assignments'] );
        $group_data['bibliography_count'] = count( $group_data['bibliography'] );
        
        return $group_data;
    }
    
    /**
     * group_dashboard( $vars )
     *
     * Hooks into screen_handler
     * Displays group dashboard
     *
     * @param Array $vars a set of variables received for this screen template
     * @return Array $vars a set of variable passed to this screen template
     */
    function group_dashboard( $vars ) {
        global $bp;
        $group_data = $this->get_group_courseware( $bp->groups->current_group->id );
        
        $vars['grades'] = array();
        foreach ( $group_data['user_grades'] as $grade )
            if( is_numeric( $grade['value'] ) )
                $vars['grades'][] = $grade['value'];
        
        $vars['founder'] = $bp->groups->current_group->creator_id;
        $vars['teachers'] = BPSP_Roles::get_teachers( $bp->groups->current_group->id );
        $vars['is_teacher'] = BPSP_Roles::can_teach( $bp->loggedin_user->id );
        $vars['group_course'] = reset( $group_data['courses'] );
        $vars = array_merge( $vars, $group_data );
        $vars['items_limit'] = 5;
        $vars['name'] = 'dashboard';
        $vars['trail'] = array(
            ( $vars['is_teacher'] ) ? __( 'Welcome Teacher', 'bpsp' ) : __( 'Welcome Student', 'bpsp' ) => ''
        );

        return $vars;
    }

	/*
	 *
	 */
	function shortcode_courseware_dashboard( $attributes ) {
		global $bp;

		$course = false;
		if ( isset( $_GET['course_id'] ) ) {
			$course = BPSP_Courses::is_course( (int) $_GET['course_id'] );
		}

		// @todo not pulling in # of lectures, assignments, etc

		if ( $course ) {
			$bp->groups->current_group->creator_id = 1;	// @todo set to current user? no, to author of course post?
			$vars = array_merge(
				$this->group_dashboard( array() ),
				array(
					'nav_options' => array( __( 'Home', 'bpsp' ) => '' ),
					'group_course' => $course,

					// @todo update link urls
				)
			);

			BPSP_Groups::load_template( $vars );
		} else {
			echo '<p>Invalid course ID</p>';
		}
	}
}
?>