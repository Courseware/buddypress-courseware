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
        // Custom jQuery UI components
        wp_register_script( 'jquery-ui-datepicker', BPSP_WEB_URI . '/static/js/jquery-ui/jquery.ui.datepicker.js', array( 'jquery-ui-mouse' ), '1.8.12', true );
        wp_register_script( 'jquery-ui-slider', BPSP_WEB_URI . '/static/js/jquery-ui/jquery.ui.slider.js', array( 'jquery-ui-mouse' ), '1.8.12', true );
        
        // FullCalendar
        wp_register_script( 'fullcalendar', BPSP_WEB_URI . '/static/js/fullcalendar/fullcalendar.min.js', array( 'jquery-ui-droppable', 'jquery-ui-resizable' ), '1.4.7' );
        wp_localize_script( 'fullcalendar', 'fcLanguage', $this->fullcalendar_l10n() );
        
        // jQuery UI Date & Time picker
        wp_register_script( 'datetimepicker', BPSP_WEB_URI . '/static/js/datetimepicker/jquery-ui-timepicker-addon-0.7.min.js', array( 'jquery-ui-datepicker', 'jquery-ui-slider' ), '0.7', true );
        wp_localize_script( 'datetimepicker', 'dtpLanguage', $this->datetimepicker_l10n() );
        
        // Flexselect
        wp_register_script( 'liquidmetal', BPSP_WEB_URI . '/static/js/flexselect/liquidmetal.js', null, '0.1' );
        wp_register_script( 'flexselect', BPSP_WEB_URI . '/static/js/flexselect/jquery.flexselect.js', array( 'jquery', 'liquidmetal' ), '0.2' );
        
        // DataTables
        wp_register_script( 'datatables', BPSP_WEB_URI . '/static/js/datatables/jquery.dataTables.min.js', array( 'jquery' ), '1.6.2' );
        // Localize datatables
        wp_localize_script( 'datatables', 'oLanguage', $this->datatables_l10n() );
        
        // InputHint
        wp_register_script( 'inputhint', BPSP_WEB_URI . '/static/js/inputhint/jquery.inputhint.js', array( 'jquery' ), '1.0' );
        
        // Sprakline
        wp_register_script( 'sparkline', BPSP_WEB_URI . '/static/js/sparkline/jquery.sparkline.min.js', array( 'jquery' ), '1.5.1' );
        
        // jsTree
        wp_register_script( 'jstree', BPSP_WEB_URI . '/static/js/jstree/jquery.jstree.js', array( 'jquery' ), '1.0-rc3' );
        
        // formBuilder
        wp_register_script( 'formbuilder', BPSP_WEB_URI . '/static/js/formbuilder/jquery.formbuilder.js', array( 'jquery', 'jquery-ui-sortable' ), '0.3-beta' );
        wp_localize_script( 'formbuilder', 'fbLanguage', $this->formbuilder_l10n() );
        
        // Loaders
        wp_register_script( 'courseware-editor', BPSP_WEB_URI . '/static/js/courseware-editor.js', array( 'inputhint' ), BPSP_VERSION, true );
        wp_register_script( 'list-assignments', BPSP_WEB_URI . '/static/js/list-assignments.js', array( 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'single-assignment', BPSP_WEB_URI . '/static/js/single-assignment.js', array( 'jquery' ), BPSP_VERSION, true );
        wp_register_script( 'assignments', BPSP_WEB_URI . '/static/js/assignments.js', array( 'datetimepicker' ), BPSP_VERSION, true );
        wp_register_script( 'bibliographies', BPSP_WEB_URI . '/static/js/bibliographies.js', array( 'flexselect', 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'new-bibliograpy', BPSP_WEB_URI . '/static/js/new-bibliograpy.js', array( 'inputhint' ), BPSP_VERSION, true );
        wp_register_script( 'edit-bibliograpy', BPSP_WEB_URI . '/static/js/edit-bibliograpy.js', array( 'inputhint' ), BPSP_VERSION, true );
        wp_register_script( 'gradebook', BPSP_WEB_URI . '/static/js/gradebook.js', array( 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'schedules', BPSP_WEB_URI . '/static/js/schedules.js', array( 'datatables', 'datetimepicker', 'inputhint' ), BPSP_VERSION, true );
        wp_register_script( 'list-schedules', BPSP_WEB_URI . '/static/js/list-schedules.js', array( 'fullcalendar', 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'delete-schedule', BPSP_WEB_URI . '/static/js/delete-schedule.js', array( 'datatables' ), BPSP_VERSION, true );
        wp_register_script( 'group-dashboard', BPSP_WEB_URI . '/static/js/group-dashboard.js', array( 'sparkline' ), BPSP_VERSION, true );
        wp_register_script( 'lectures', BPSP_WEB_URI . '/static/js/lectures.js', array( 'jstree' ), BPSP_VERSION, true );
        wp_register_script( 'new-assignment', BPSP_WEB_URI . '/static/js/new-assignment.js', array( 'courseware-editor', 'formbuilder' ), BPSP_VERSION, true );
        wp_register_script( 'edit-assignment', BPSP_WEB_URI . '/static/js/edit-assignment.js', array( 'courseware-editor', 'formbuilder' ), BPSP_VERSION, true );
        
        // Styles
        wp_register_style( 'courseware', BPSP_WEB_URI . '/static/css/courseware.css', null, BPSP_VERSION );
        wp_register_style( 'courseware-editor', BPSP_WEB_URI . '/static/css/courseware-editor.css', null, BPSP_VERSION );
        wp_register_style( 'jquery-ui-courseware-custom', BPSP_WEB_URI . '/static/css/jquery-ui-custom/theme/smoothness/jquery-ui-custom.css', '1.8.2' );
        wp_register_style( 'fullcalendar', BPSP_WEB_URI . '/static/css/fullcalendar/jquery.fullcalendar.css', null, '1.4.7' );
        wp_register_style( 'datatables', BPSP_WEB_URI . '/static/css/datatables/jquery.datatables.css', null, '1.6.2' );
        wp_register_style( 'datetimepicker', BPSP_WEB_URI . '/static/css/datetimepicker/jquery.timepicker.css', array( 'jquery-ui-courseware-custom' ), '0.5' );
        wp_register_style( 'flexselect', BPSP_WEB_URI . '/static/css/flexselect/jquery.flexselect.css', null, '0.2' );
        wp_register_style( 'formbuilder', BPSP_WEB_URI . '/static/css/formbuilder/jquery.formbuilder.css', null, '0.3-beta' );
        
        // Hooks
        add_action( 'bp_head', array( &$this, 'load_courseware_css' ) );
        add_action( 'courseware_editor', array( &$this, 'courseware_editor_enqueues' ) );
        add_action( 'courseware_list_schedules_screen', array( &$this, 'list_schedules_enqueues' ) );
        add_action( 'courseware_list_assignments_screen', array( &$this, 'list_assignments_enqueues' ) );
        add_action( 'courseware_assignment_screen', array( &$this, 'assignment_enqueues' ) );
        add_action( 'courseware_bibliography_screen', array( &$this, 'bibs_enqueues' ) );
        add_action( 'courseware_new_bibliography_screen', array( &$this, 'new_bib_enqueues' ) );
        add_action( 'courseware_edit_bibliography_screen', array( &$this, 'edit_bib_enqueues' ) );
        add_action( 'courseware_new_schedule_screen', array( &$this, 'schedules_enqueues' ) );
        add_action( 'courseware_edit_schedule_screen', array( &$this, 'schedules_enqueues' ));
        add_action( 'courseware_delete_schedule_screen', array( &$this, 'delete_schedule_enqueues' ) );
        add_action( 'courseware_gradebook_screen', array( &$this, 'gradebook_enqueues' ) );
        add_action( 'courseware_group_dashboard', array( &$this, 'group_dashboard_enqueues' ) );
        add_action( 'courseware_lectures_screen', array( &$this, 'lectures_enqueues' ) );
        add_action( 'courseware_new_assignment_screen', array( &$this, 'new_assignment_enqueues' ) );
        add_action( 'courseware_edit_assignment_screen', array( &$this, 'edit_assignment_enqueues' ) );
    }
    
    /**
     * courseware_editor_enqueues()
     * Loads the screen editor styles and scripts
     */
    function courseware_editor_enqueues() {
        wp_enqueue_script( 'courseware-editor' );
        wp_enqueue_style( 'courseware-editor' );
    }
    
    /**
     * load_courseware_css()
     *
     * Function will check if courseware.css option is checked
     * and will load the file if this exists
     * Also a default courseware.css is loaded
     */
    function load_courseware_css() {
        // Load default css
        wp_enqueue_style( 'courseware' );
        // Load customized css
        $to_load = get_option( 'bpsp_load_css' );
        $css_name = '/courseware.css';
        
        if( empty( $to_load ) )
            return;
        
        if( file_exists( STYLESHEETPATH . $css_name ) ) {
            $web_uri = get_bloginfo( 'stylesheet_directory' );    
            wp_enqueue_style( 'custom-courseware-css', $web_uri . $css_name, null, BPSP_VERSION );
        }
    }
    
    function bibs_enqueues() {
        wp_enqueue_script( 'bibliographies' );
        wp_enqueue_style( 'flexselect' );
        wp_enqueue_style( 'datatables' );
    }
    
    function lectures_enqueues() {
        wp_enqueue_script( 'lectures' );
    }
    
    function edit_assignment_enqueues() {
        wp_enqueue_script( 'edit-assignment' );
        wp_enqueue_style( 'formbuilder' );
    }
    
    function new_assignment_enqueues() {
        wp_enqueue_script( 'new-assignment' );
        wp_enqueue_style( 'formbuilder' );
    }
    
    function new_bib_enqueues() {
        wp_enqueue_script( 'new-bibliograpy' );
    }
    
    function edit_bib_enqueues() {
        wp_enqueue_script( 'edit-bibliograpy' );
    }
    
    function gradebook_enqueues() {
        wp_enqueue_script( 'gradebook' );
        wp_enqueue_style( 'datatables' );
    }
    
    function schedules_enqueues() {
        wp_enqueue_style( 'datetimepicker' );
        wp_enqueue_script( 'schedules' );
    }   

    function list_assignments_enqueues() {
        wp_enqueue_script( 'list-assignments' );
        wp_enqueue_style( 'datatables' );
    }
    
    function assignment_enqueues() {
        wp_enqueue_script( 'single-assignment' );
    }
    
    function list_schedules_enqueues() {
        wp_enqueue_style( 'fullcalendar' );
        wp_enqueue_style( 'datatables' );
        wp_enqueue_script( 'list-schedules' );
    }

    function delete_schedule_enqueues() {
        wp_enqueue_style( 'datatables' );
        wp_enqueue_script( 'delete-schedule' );
        wp_enqueue_style( 'datatables' );
    }
    
    function group_dashboard_enqueues() {
        wp_enqueue_script( 'group-dashboard' );
    }
    
    /**
     * formbuilder_l10n()
     *
     * Helper to get formbuilder messages localized/options set
     */
    function formbuilder_l10n() {
        return array(
            "save"              => __( "Save", 'bpsp' ),
            "add_new_field"     => __( "Add New Field...", 'bpsp' ),
            "text"              => __( "Text Field", 'bpsp' ),
            "title"             => __( "Title", 'bpsp' ),
            "paragraph"         => __( "Paragraph", 'bpsp' ),
            "checkboxes"        => __( "Checkboxes", 'bpsp' ),
            "radio"             => __( "Radio", 'bpsp' ),
            "select"            => __( "Select List", 'bpsp' ),
            "text_field"        => __( "Text Field", 'bpsp' ),
            "label"             => __( "Label", 'bpsp' ),
            "paragraph_field"   => __( "Paragraph Field", 'bpsp' ),
            "select_options"    => __( "Select Options", 'bpsp' ),
            "add"               => __( "Add", 'bpsp' ),
            "checkbox_group"    => __( "Checkbox Group", 'bpsp' ),
            "remove_message"    => __( "Are you sure you want to remove this element?", 'bpsp' ),
            "remove"            => __( "Remove", 'bpsp' ),
            "radio_group"       => __( "Radio Group", 'bpsp' ),
            "selections_message"=> __( "Allow Multiple Selections", 'bpsp' ),
            "hide"              => __( "Hide", 'bpsp' ),
            "required"          => __( "Required", 'bpsp' ),
            "show"              => __( "Show", 'bpsp' )
        );
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
            "sInfoPostFix"      => __( " ", 'bpsp' ),
            "sSearch"           => __( "Search", 'bpsp' ),
        );
    }
    
    /**
     * fullcalendar_l10n()
     *
     * Helpers to get fullcalendar messages localized
     */
    function fullcalendar_l10n() {
        $buttonText = array(
            "today" => __( 'today', 'bpsp' ),
            "month" => __( 'month', 'bpsp' ),
            "week"  => __( 'week', 'bpsp' ),
            "day"   => __( 'day', 'bpsp' )
        );
        
        return array(
            "timeFormat"        => __( 'H(:mm)', 'bpsp' ), // 'H(:mm)' for 24-hour clock
            "buttonTextKeys"    => implode( ',' , array_keys( $buttonText ) ),
            "buttonTextVals"    => implode( ',' , array_values( $buttonText ) ),
            "monthNames"        => implode( ',', $this->months_l10n() ),
            "monthNamesShort"   => implode( ',', $this->months_short_l10n() ),
            "dayNames"          => implode( ',', $this->days_l10n() ),
            "dayNamesShort"     => implode( ',', $this->days_short_l10n() ),
            "firstDay"          => __( '1', 'bpsp' ),
            "ical_img"          => self::get_image( 'calendar_link.png', false, false )
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
            "dayNamesMin"           => implode( ',', $this->days_shorter_l10n() ),
            "firstDay"              => __( '1', 'bpsp' ),
            "isRTL"                 => __( 'false', 'bpsp' ),
            "showMonthAfterYear"    => __( 'false', 'bpsp' ),
        );
    }
    function months_l10n() {
        return array(
            'January'   => __( 'January', 'bpsp' ),
            'February'  => __( 'February', 'bpsp' ),
            'March'     => __( 'March', 'bpsp' ),
            'April'     => __( 'April', 'bpsp' ),
            'May'       => __( 'May', 'bpsp' ),
            'June'      => __( 'June', 'bpsp' ),
            'July'      => __( 'July', 'bpsp' ),
            'August'    => __( 'August', 'bpsp' ),
            'September' => __( 'September', 'bpsp' ),
            'October'   => __( 'October', 'bpsp' ),
            'November'  => __( 'November', 'bpsp' ),
            'December'  => __( 'December', 'bpsp' ),
        );
    }
    function months_short_l10n() {
        return array(
            'Jan'  => __( 'Jan', 'bpsp' ),
            'Feb'  => __( 'Feb', 'bpsp' ),
            'Mar'  => __( 'Mar', 'bpsp' ),
            'Apr'  => __( 'Apr', 'bpsp' ),
            'May'  => __( 'May', 'bpsp' ),
            'Jun'  => __( 'Jun', 'bpsp' ),
            'Jul'  => __( 'Jul', 'bpsp' ),
            'Aug'  => __( 'Aug', 'bpsp' ),
            'Sep'  => __( 'Sep', 'bpsp' ),
            'Oct'  => __( 'Oct', 'bpsp' ),
            'Nov'  => __( 'Nov', 'bpsp' ),
            'Dec'  => __( 'Dec', 'bpsp' ),
        );
    }
    function days_l10n() {
        return array(
            'Sunday'    => __( 'Sunday', 'bpsp' ),
            'Monday'    => __( 'Monday', 'bpsp' ),
            'Tuesday'   => __( 'Tuesday', 'bpsp' ),
            'Wednesday' => __( 'Wednesday', 'bpsp' ),
            'Thursday'  => __( 'Thursday', 'bpsp' ),
            'Friday'    => __( 'Friday', 'bpsp' ),
            'Saturday'  => __( 'Saturday', 'bpsp' ),
        );
    }
    function days_short_l10n() {
        return array(
            'Sun'  => __( 'Sun', 'bpsp' ),
            'Mon'  => __( 'Mon', 'bpsp' ),
            'Tue'  => __( 'Tue', 'bpsp' ),
            'Wed'  => __( 'Wed', 'bpsp' ),
            'Thu'  => __( 'Thu', 'bpsp' ),
            'Fri'  => __( 'Fri', 'bpsp' ),
            'Sat'  => __( 'Sat', 'bpsp' ),
        );
    }
    function days_shorter_l10n() {
        return array(
            'Su'  => __( 'Su', 'bpsp' ),
            'Mo'  => __( 'Mo', 'bpsp' ),
            'Tu'  => __( 'Tu', 'bpsp' ),
            'We'  => __( 'We', 'bpsp' ),
            'Th'  => __( 'Th', 'bpsp' ),
            'Fr'  => __( 'Fr', 'bpsp' ),
            'Sa'  => __( 'Sa', 'bpsp' ),
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
            self::get_image( 'map_go.png', false ) .
            '</a>';
        
        if( $echo )
            return printf( $link_template, $string );
        else
            sprintf( $link_template, $string );
    }
}
?>