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
        
        // FullCalendar
        wp_register_script( 'fullcalendar', BPSP_WEB_URI . '/static/js/fullcalendar/fullcalendar.min.js', array( 'jquery-ui-courseware-custom' ), '1.4.7' );        
        // TODO: find a way to call multiple times wp_localize_script()
        //wp_localize_script( 'fullcalendar', 'fc_months', $this->months_l10n() );
        //wp_localize_script( 'fullcalendar', 'fc_days', $this->days_l10n() );
        
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
        wp_register_script( 'list-schedules', BPSP_WEB_URI . '/static/js/list-schedules.js', array( 'fullcalendar' ), BPSP_VERSION, true );
        
        // Styles
        wp_register_style( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/css/jquery-ui-custom/theme/smoothness/jquery-ui-1.8.2.custom.css', '1.8.2' );
        wp_register_style( 'fullcalendar', BPSP_WEB_URI . '/static/css/fullcalendar/jquery.fullcalendar.css', null, '1.4.7' );
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
    
    function list_schedules_enqueues() {
        wp_enqueue_style( 'fullcalendar' );
        wp_enqueue_script( 'list-schedules' );
    }
    
    /**
     * datatables_l10n()
     *
     * Helper to get datatables messages localized
     */
    function datatables_l10n() {
        return array(
            "sProcessing"       => __( "Processing...", 'bpsp' ),
            "sLengthMenu"       => __( "Display _MENU_ records per page", 'bpsp' ),
            "sZeroRecords"      => __( "Nothing found - sorry", 'bpsp' ),
            "sInfo"             => __( "Showing _START_ to _END_ of _TOTAL_ records", 'bpsp' ),
            "sInfoEmpty"        => __( "Showing 0 to 0 of 0 records", 'bpsp' ),
            "sInfoFiltered"     => __( "(filtered from _MAX_ total records)", 'bpsp' ),
            "sInfoPostFix"      => __( "", 'bpsp' ),
            "sSearch"           => __( "Search", 'bpsp' ),
        );
    }
    
    /**
     * datetimepicker_l10n()
     *
     * Helpers to get datetimepicker messages localized
     */
    function datetimepicker_l10n() {
        return array(
            "closeText"             => __( 'Done', 'bpsp' ),
            "prevText"              => __( 'Prev', 'bpsp' ),
            "nextText"              => __( 'Next', 'bpsp' ),
            "currentText"           => __( 'Today', 'bpsp' ),
            "monthNames"            => implode( ',', $this->months_l10n() ),
            "dayNamesMin"           => implode( ',', $this->days_l10n() ),
            "firstDay"              => __( '1', 'bpsp' ),
            "isRTL"                 => __( 'false', 'bpsp' ),
            "showMonthAfterYear"    => __( 'false', 'bpsp' ),
        );
    }
    function months_l10n() {
        return array(
            __( 'January', 'bpsp' ),
            __( 'February', 'bpsp' ),
            __( 'March', 'bpsp' ),
            __( 'April', 'bpsp' ),
            __( 'May', 'bpsp' ),
            __( 'June', 'bpsp' ),
            __( 'Julya', 'bpsp' ),
            __( 'August', 'bpsp' ),
            __( 'September', 'bpsp' ),
            __( 'October', 'bpsp' ),
            __( 'November', 'bpsp' ),
            __( 'December', 'bpsp' ),
            );
    }
    function days_l10n() {
        return array(
            __( 'Su', 'bpsp' ),
            __( 'Moa', 'bpsp' ),
            __( 'Tu', 'bpsp' ),
            __( 'We', 'bpsp' ),
            __( 'Th', 'bpsp' ),
            __( 'Fr', 'bpsp' ),
            __( 'Sa', 'bpsp' ),
        );
    }
    
    /**
     * get_image( $name = null )
     *
     * Get the URI for a image with $name
     * @param String $name of the image
     * @param Bool $html if true will return an HTML formatted string
     * @return String the URI of the image, or a blank image URI if image not found
     */
    function get_image( $name = null, $echo = true, $html = true ) {
        $images_folder = BPSP_PLUGIN_DIR . '/static/images/';
        $images_web_folder = BPSP_WEB_URI . '/static/images/';
        $image_uri = null;
        $html_template = '<img src="%s" alt="%s" class="%s"/>';
        
        if( file_exists( $images_folder . $name ) )
            $image_uri = $images_web_folder . $name;
        
        if( $html )
            $image_uri = sprintf( $html_template, $image_uri, $name, $name );
        
        if( $echo )
            echo $image_uri;
        
        return $image_uri;
    }
    
    /**
     * gmaps_link( $string, $echo = true )
     *
     * Generates a Google Maps link for $string
     * @param String $string to use in URI
     * @param Bool $echo, is false will return the result, else will echo it, default true
     * @return String, HTML formatted URI for $string if $echo, else null
     */
    function gmaps_link( $string, $echo = true ) {
        $string = urlencode( esc_html( $string ) );
        
        if( empty( $string ) )
            return;
        
        $link_template =
            '<a href="http://maps.google.com/maps?q=%s">' .
            self::get_image( 'info_button_16.png', false ) .
            '</a>';
        
        if( $echo )
            return printf( $link_template, $string );
        else
            sprintf( $link_template, $string );
    }
}
?>