<?php
/**
 * Helpers for loading partials and rest of stuff
 */

/**
 * bpsp_partial( $path, $name, $vars )
 *
 * Function outputs a partial template file used inside another template
 * @param String $path, the path to the directory to search in, default empty
 * @param String $name, the name of the partial template to be loaded, default empty
 * @param Mixed $vars, a set of values you want to pass the $name partial can use, use get_defined_vars() for that, default null
 * @uses bpsp_get_partial()
 * @return String the resulted content
 */
function bpsp_partial( $path = '', $name = '', $vars = null ) {
    echo bpsp_get_partial( $path, $name, $vars );
}

/**
 * bpsp_get_partial( $path, $name, $vars )
 *
 * Function loads a partial template file inside another template
 * @param String $path, the path to the directory to search in, default empty
 * @param String $name, the name of the partial template to be loaded, default empty, DO NOT APPEND .php extension
 * @param Mixed $vars, a set of values you want to pass the $name partial can use, use get_defined_vars() for that, default null
 * @return String the resulted content
 */
function bpsp_get_partial( $path = '', $name = '', $vars = null ) {
    $content = '';
    
    if( empty( $path ) || empty( $name ) )
        return;
    
    $bpsp_partial_file = $path . $name . '.php';
    
    if( file_exists( $bpsp_partial_file ) ) {
        ob_start();
        if( !empty( $vars ) )
            extract( $vars );
        include_once $bpsp_partial_file;
        $content = ob_get_clean();
    }
    
    return apply_filters( 'bpsp_partial', $content );
}

/**
 * bpsp_date( $date )
 *
 * Outputs a formated date+time string using WordPress settings
 * @uses bpsp_get_date()
 */
function bpsp_date( $date ) {
    echo bpsp_get_date( $date );
}

/**
 * bpsp_get_date( $date )
 *
 * Returns a formated date+time string using WordPress settings
 */
function bpsp_get_date( $date ) {
    return mysql2date( get_option('date_format') . ", " . get_option('time_format'), $date );
}

