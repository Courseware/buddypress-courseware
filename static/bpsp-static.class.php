<?php
/**
 * Class for handling javascripts and css enqueues
 */
class BPSP_Static {
    /**
     * BPSP_Static()
     *
     * Constructor, registers enqueues.
     */
    function BPSP_Static() {
        if( !defined( BPSP_VERSION ) )
            define( BPSP_VERSION, '' );
        
        // Scripts
        wp_register_script( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/js/jquery-ui-custom/jquery-ui-1.8.2.custom.min.js', array( 'jquery' ), '1.8.2' );
        wp_register_script( 'datetimepicker', BPSP_WEB_URI . '/static/js/datetimepicker/jquery-ui-timepicker-addon-0.5.min.js', array( 'jquery-ui-courseware-custom' ), '0.5' );
        wp_register_script( 'liquidmetal', BPSP_WEB_URI . '/static/js/flexselect/liquidmetal.js', null, '0.1' );
        wp_register_script( 'flexselect', BPSP_WEB_URI . '/static/js/flexselect/jquery.flexselect.js', array( 'jquery', 'liquidmetal' ), '0.2' );
        wp_register_script( 'assignments', BPSP_WEB_URI . '/static/js/assignments.js', array( 'datetimepicker' ), BPSP_VERSION, true );
        wp_register_script( 'bibliographies', BPSP_WEB_URI . '/static/js/bibliographies.js', array( 'flexselect' ), BPSP_VERSION, true );
        wp_register_script( 'schedules', BPSP_WEB_URI . '/static/js/schedules.js', array( 'datetimepicker' ), BPSP_VERSION, true );
        // Styles
        wp_register_style( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/css/jquery-ui-custom/theme/smoothness/jquery-ui-1.8.2.custom.css', '1.8.2' );
        wp_register_style( 'datetimepicker', BPSP_WEB_URI . '/static/css/datetimepicker/jquery.timepicker.css', array( 'jquery-ui-courseware-custom' ), '0.5' );
        wp_register_style( 'flexselect', BPSP_WEB_URI . '/static/css/flexselect/jquery.flexselect.css', null, '0.2' );
    }
    
    function bibs_enqueues() {
        wp_enqueue_script( 'bibliographies' );
        wp_enqueue_style( 'flexselect' );
    }
    
    function schedules_enqueues() {
        wp_enqueue_style( 'datetimepicker' );
        wp_enqueue_script( 'schedules' );
    }
}
?>