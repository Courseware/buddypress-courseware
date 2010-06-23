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
        wp_register_script( 'assignments', BPSP_WEB_URI . '/static/js/assignments.js', array( 'jquery', 'dtpicker' ), '0.1alfa', true );
        wp_register_script( 'dtpicker', BPSP_WEB_URI . '/static/js/dtpicker/jquery.dtpicker.min.js', array( 'jquery' ), '1.0a6' );
        wp_register_style( 'dtpicker', BPSP_WEB_URI . '/static/js/dtpicker/jquery.dtpicker.css', '1.0a6');
    }
}
?>