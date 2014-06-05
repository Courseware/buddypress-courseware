<?php

if ( !defined( 'ABSPATH' ) ) { exit; }

class BPSP_Courseware_Component extends BP_Component {
    function __construct() {
        global $bp;

        parent::start(
            'courseware',
            __( 'Courseware', 'bpsp' ),
            BPSP_PLUGIN_DIR
        );

        $bp->active_components[$this->id] = '1';

		add_action( 'init', array( &$this, 'bpsp_registration' ) );

		add_action( 'bp_init', array( &$this, 'bpsp_init' ), 6 );
    }
	
	/* Initiate the componenets */
	function bpsp_init() {
		new BPSP_WordPress();
		new BPSP_Roles();
		new BPSP_Groups();
		new BPSP_Courses();
		new BPSP_Lectures();
		new BPSP_Assignments();
		new BPSP_Responses();
		new BPSP_Gradebook();
		new BPSP_Bibliography();
		new BPSP_Schedules();
		new BPSP_Dashboards();
		new BPSP_Static();
		new BPSP_Activity();
		new BPSP_Notifications();
	}

	/**
	 * Register post types and taxonomies
	 */
	function bpsp_registration() {
		BPSP_Courses::register_post_types();
		BPSP_Lectures::register_post_types();
		BPSP_Assignments::register_post_types();
		BPSP_Responses::register_post_types();
		BPSP_Gradebook::register_post_types();
		BPSP_Bibliography::register_post_types();
		BPSP_Schedules::register_post_types();
	}
}