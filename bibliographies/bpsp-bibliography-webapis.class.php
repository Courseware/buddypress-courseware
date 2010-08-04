<?php
/**
 * Class for handling third party Webservices API integration
 */
class BPSP_Bibliography_WebApis {
    /**
     * WordCat Api Key
     */
    var $worldcat_key;
    
    /**
     * ISBNdb Api Key
     */
    var $isbndb_key;
    
    /**
     * Query String to search
     */
    var $query;
    
    /**
     * Citation format
     */
    var $cformat = 'apa';
    
    /**
     * Number of results to return
     */
    var $count = 1;
    
    /**
     * Constructor
     *
     * @param Mixed $keys contains a set of API keys
     *  - 'worldcat' => 'for worldcat api'
     *  - 'isbndb' => 'for isbndb'
     *  ...
     */
    function BPSP_Bibliography_WebApis( $keys ) {
        if( isset( $keys['worldcat'] ) )
            $this->worldcat_key = $keys['worldcat'];
        
        if( isset( $keys['isbndb'] ) )
            $this->isbndb_key = $keys['isbndb'];
        
        $this->worldcat_uri = 'http://www.worldcat.org/webservices/catalog/search/worldcat/';
        $this->isbndb_uri = 'http://isbndb.com/api/';
    }
    
    /**
     * worldcat_opensearch( $query, $start = 1 )
     *
     * Performs an WorldCat search
     *
     * @tutorial http://worldcat.org/devnet/wiki/Code_PHP_OpenSearch
     * @param String $query to search
     * @param Int $start record position, default 1
     * @return Mixed and array with ( 'title', 'author', 'summary', 'content' )
     */
    function worldcat_opensearch( $query, $start = 1 ) {
	if( $this->worldcat_key )
            $key = $this->worldcat_key;
        else
            return;
        
        // citation format
        $cformat = $this->cformat;
        // results to return
        $count = $this->count;
        // format to query
        $format = 'atom';
	// response array
	$r = null;
        
	// construct worldcat opensearch request
	$url = $this->worldcat_uri;
	$url .= "opensearch?q=";
	$url .= urlencode($query);
	$url .= "&format=".$format;
	$url .= "&start=".$start;
	$url .= "&count=".$count;
	$url .= "&cformat=".$cformat;
	$url .= "&wskey=".$key;
	
	$response = fetch_feed( $url );
        if( empty( $response->errors ) )
            $results = $response->get_items();
	
	if ( !empty( $results ) ) {
            foreach ($results as $item) {
                $author = $item->get_author();
                
                $isbn_tag = $item->get_item_tags(SIMPLEPIE_NAMESPACE_DC_11, 'identifier');
                $isbn = explode( ':', $isbn_tag[0]['data'] );
                if( isset( $isbn[2] ) )
                   $isbn = $isbn[2];
                
                $entry = array(
                    "author"    => $author->name,
                    "title"     => $item->get_title(),
                    "citation"  => $item->get_content(),
                    "url"       => $item->get_permalink(),
                    "isbn"      => $isbn,
                );
                
                $r[] = $entry;
	    }
        }
        
        return $r;
    }
    
    /**
     * get_book_cover( $isbn, $size )
     * 
     * Generates a book cover using OpenLibrary Covers API
     * @param String $isbn the ISBN id to use
     * @param Char $size, the size of the image: default S, can be M or L
     * @return String the generated URL
     */
    function get_book_cover( $isbn, $size = 'S' ) {
        if( empty( $isbn ) )
            return BPSP_Static::get_image( "blank_book.png", false, false );
        
        $openlibrary_uri = 'http://covers.openlibrary.org/b/isbn/%s-%s.jpg';
        return sprintf( $openlibrary_uri, $isbn, $size );
    }
    
    /**
     * get_www_cover()
     * 
     * Returns a webpage dummy icon
     * @return String the generated URL
     */
    function get_www_cover() {
        if( empty( $isbn ) )
            return BPSP_Static::get_image( "web.png", false, false );
    }
    
    /**
     * isbndb_query( $isbn, $start = 1 )
     *
     * Performs an ISBNdb query
     *
     * @tutorial http://isbndb.com/docs/api/index.html
     * @param String $isbn to search
     * @param Int $start record position, default 1
     * @return Mixed and array with ( 'title', 'author', 'summary', 'content' )
     */
    function isbndb_query( $isbn, $start = 1 ) {
	if( $this->isbndb_key )
            $key = $this->isbndb_key;
        else
            return;
        
        // Strip '-'
        $isbn = str_replace( '-', '', $isbn );
        
        // results array
	$entry = null;
        
	// construct isbndb api request
	$url = $this->isbndb_uri;
	$url .= "books.xml?index1=isbn&value1=";
	$url .= urlencode($isbn);
	$url .= "&access_key=".$key;
	
        // load xml
        $xml = @file_get_contents( $url );
        if( $xml != null ) {
            $parser = xml_parser_create();
            xml_parse_into_struct( $parser, $xml, $tags );
            xml_parser_free( $parser );
        } else {
            return null;
        }
        
        foreach( $tags as $t ) {
            if( strtolower( $t['tag'] ) == 'titlelong' )
                $entry['title'] = $t['value'];
            if( strtolower( $t['tag'] ) == 'authorstext' )
                $entry['author'] = $t['value'];
            if( strtolower( $t['tag'] ) == 'publishertext' )
                $entry['pub'] = $t['value'];
        }
        $entry['isbn'] = $isbn;
        
        return array( $entry );
    }
}
?>