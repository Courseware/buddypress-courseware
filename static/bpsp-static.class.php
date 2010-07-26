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
        // Custom jQuery UI
        wp_register_script( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/js/jquery-ui-custom/jquery-ui-1.8.2.custom.min.js', array( 'jquery' ), '1.8.2' );
        
        // jQuery UI Date & Time picker
        wp_register_script( 'datetimepicker', BPSP_WEB_URI . '/static/js/datetimepicker/jquery-ui-timepicker-addon-0.5.min.js', array( 'jquery-ui-courseware-custom' ), '0.5' );
        wp_localize_script( 'datetimepicker', 'dtpLanguage', $this->datetimepicker_l10n() );
        
        // Flexselect
        wp_register_script( 'liquidmetal', BPSP_WEB_URI . '/static/js/flexselect/liquidmetal.js', null, '0.1' );
        wp_register_script( 'flexselect', BPSP_WEB_URI . '/static/js/flexselect/jquery.flexselect.js', array( 'jquery', 'liquidmetal' ), '0.2' );
        
        // DataTables
        wp_register_script( 'datatables', BPSP_WEB_URI . '/static/js/datatables/jquery.dataTables.min.js', array( 'jquery' ), '1.6.2' );
        // Localize datatables
        wp_localize_script( 'datatables', 'oLanguage', $this->datatables_l10n() );
        
        // Loaders
        wp_register_script( 'assignments', BPSP_WEB_URI . '/static/js/assignments.js', array( 'datetimepicker' ), BPSP_VERSION, true );
        wp_register_script( 'bibliographies', BPSP_WEB_URI . '/static/js/bibliographies.js', array( 'flexselect' ), BPSP_VERSION, true );
        wp_register_script( 'gradebook', BPSP_WEB_URI . '/static/js/gradebook.js', array( 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'schedules', BPSP_WEB_URI . '/static/js/schedules.js', array( 'datetimepicker' ), BPSP_VERSION, true );
        
        // Styles
        wp_register_style( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/css/jquery-ui-custom/theme/smoothness/jquery-ui-1.8.2.custom.css', '1.8.2' );
        wp_register_style( 'datatables', BPSP_WEB_URI . '/static/css/datatables/jquery.datatables.css', null, '1.6.2' );
        wp_register_style( 'datetimepicker', BPSP_WEB_URI . '/static/css/datetimepicker/jquery.timepicker.css', array( 'jquery-ui-courseware-custom' ), '0.5' );
        wp_register_style( 'flexselect', BPSP_WEB_URI . '/static/css/flexselect/jquery.flexselect.css', null, '0.2' );
    }
    
    function bibs_enqueues() {
        wp_enqueue_script( 'bibliographies' );
        wp_enqueue_style( 'flexselect' );
        wp_enqueue_style( 'datatables' );
    }
    
    function gradebook_enqueues() {
        wp_enqueue_script( 'gradebook' );
        wp_enqueue_style( 'datatables' );
    }
    
    function bibs_enqueues_deregister() {
        wp_deregister_script( 'bibliographies' );
        wp_deregister_style( 'flexselect' );
    }
    
    function schedules_enqueues() {
        wp_enqueue_style( 'datetimepicker' );
        wp_enqueue_script( 'schedules' );
    }
    
    /**
     * datatables_l10n()
     *
     * Helper to get datatables messages localized
     */
    function datatables_l10n() {
        return array(
            "sLengthMenu" => __( "Display _MENU_ records per page", 'bpsp' ),
            "sZeroRecords" => __( "Nothing found - sorry", 'bpsp' ),
            "sInfo" => __( "Showing _START_ to _END_ of _TOTAL_ records", 'bpsp' ),
            "sInfoEmpty" => __( "Showing 0 to 0 of 0 records", 'bpsp' ),
            "sInfoFiltered" => __( "(filtered from _MAX_ total records)", 'bpsp' )
        );
    }
    
    /**
     * datetimepicker_l10n()
     *
     * Helpers to get datetimepicker messages localized
     */
    function datetimepicker_l10n() {
        return array(
            "closeText" => __( 'Done', 'bpsp' ),
            "prevText" => __( 'Prev', 'bpsp' ),
            "nextText" => __( 'Next', 'bpsp' ),
            "currentText" => __( 'Today', 'bpsp' ),
            "monthNames" => $this->datetimepicker_months_l10n(),
            "dayNamesMin" => $this->datetimepicker_days_l10n(),
            "firstDay" => __( '1', 'bpsp' ),
            "isRTL" => __( 'false', 'bpsp' ),
            "showMonthAfterYear" => __( 'false', 'bpsp' ),
        );
    }
    function datetimepicker_months_l10n() {
        return implode( ',',
            array(
                __( 'January', 'bpsp' ),
                __( 'February', 'bpsp' ),
                __( 'March', 'bpsp' ),
                __( 'April', 'bpsp' ),
                __( 'May', 'bpsp' ),
                __( 'June', 'bpsp' ),
                __( 'July', 'bpsp' ),
                __( 'August', 'bpsp' ),
                __( 'September', 'bpsp' ),
                __( 'October', 'bpsp' ),
                __( 'November', 'bpsp' ),
                __( 'December', 'bpsp' ),
            )
        );
    }
    function datetimepicker_days_l10n() {
        return implode( ',',
            array(
                __( 'Su', 'bpsp' ),
                __( 'Mo', 'bpsp' ),
                __( 'Tu', 'bpsp' ),
                __( 'We', 'bpsp' ),
                __( 'Th', 'bpsp' ),
                __( 'Fr', 'bpsp' ),
                __( 'Sa', 'bpsp' ),
            )
        );
    }
}
?>