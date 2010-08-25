<?php
/**
 * Port from P2
 */
function bpsp_media_buttons() {
    ob_start();
    do_action( 'media_buttons' );
    return bpsp_make_media_urls( ob_get_clean() );
}

/**
 * Make sure the URL is loaded from the same domain as the frontend
 */
function bpsp_url_filter( $url, $path = '' ) {
    $parsed = parse_url( $url );
    $host = $parsed['host'];
    if(!false === strpos('http', $url) )
        return preg_replace( '|https?://'.preg_quote( $host ).'|', get_bloginfo('url'), $url );
    return $url;
}

function bpsp_admin_url( $path ) {
    return bpsp_url_filter( admin_url( $path ) );
}

function bpsp_make_media_urls( $string ) {
    // This line does not work in .org
    return str_replace( 'media-upload.php?', bpsp_admin_url( 'media-upload.php?bpsp-upload=true&' ), $string );
}

function bpsp_load_editor_files() {
    include_once ABSPATH . '/wp-admin/includes/media.php' ;
    require_once ABSPATH . '/wp-admin/includes/post.php' ;
}

?>