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

    }
	
}