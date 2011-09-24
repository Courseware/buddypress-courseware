<?php
/**
 * BibTex Parser 
 */
class BibTeX_Parser
{
    var $count;
    var $items;
    var $types;
    var $filename;
    var $inputdata;
    
    /**
     * BibTeX_Parser( $file, $data )
     *
     * Constructor
     * @param String $file if filename is used
     * @param String $data if input is a string
     */
    function BibTeX_Parser( $file = null, $data = null ) {
        $this->items = array(
            'note' => array(),
            'abstract' => array(),
            'year' => array(),
            'group' => array(),
            'publisher' => array(),
            'page-start' => array(),
            'page-end' => array(),
            'pages' => array(),
            'address' => array(),
            'url' => array(),
            'volume' => array(),
            'chapter' => array(),
            'journal' => array(),
            'author' => array(),
            'raw' => array(),
            'title' => array(),
            'booktitle' => array(),
            'folder' => array(),
            'type' => array(),
            'linebegin' => array(),
            'lineend' => array()
        );
        
        if( $file )
            $this->filename = $file;
        elseif( $data )
            $this->inputdata = $data;
        
        // Oh, what the heck!
        $this->parse();
    }

    /**
     * parse()
     *
     * Main method that parses the data.
     */
    function parse() {
        $value = array();
        $var = array();
        $this->count = -1;
        $lineindex = 0;
        $fieldcount = -1;
        if( $this->filename )
            $lines = file($this->filename);
        else
            $lines = preg_split( '/\n/', $this->inputdata );
    
        if (!$lines)
            return;
    
        foreach($lines as $line) {
            $lineindex++;
            $this->items['lineend'][$this->count] = $lineindex;
            $line = trim($line);
            $raw_line = $line + '\n';
            $line=str_replace("'","`",$line);
            $seg=str_replace("\"","`",$line);
            $ps=strpos($seg,'=');
            $segtest=strtolower($seg);
    
            // some funny comment string
            if (strpos($segtest,'@string')!==false)
                continue;
    
            // pybliographer comments
            if (strpos($segtest,'@comment')!==false)
                continue;
    
            // normal TeX style comment
            if (strpos($seg,'%%')!==false)
                continue;
    
            /* ok when there is nothing to see, skip it! */
            if (!strlen($seg))
                continue;
    
            if ("@" == $seg[0]) {
                $this->count++;
                $this->items['raw'][$this->count] = $line . "\r\n";
                
                $ps=strpos($seg,'@');
                $pe=strpos($seg,'{');
                $this->types[$this->count]=trim(substr($seg, 1,$pe-1));
                $fieldcount=-1;
                $this->items['linebegin'][$this->count] = $lineindex;
            } // #of item increase
            elseif ($ps!==false ) { // one field begins
                $this->items['raw'][$this->count] .= $line . "\r\n";
                $ps=strpos($seg,'=');
                $fieldcount++;
                $var[$fieldcount]=strtolower(trim(substr($seg,0,$ps)));
    
                if ($var[$fieldcount]=='pages') {
                    $ps=strpos($seg,'=');
                    $pm=strpos($seg,'--');
                    $pe=strpos($seg,'},');
                    $pagefrom[$this->count] = substr($seg,$ps,$pm-$ps);
                    $pageto[$this->count]=substr($seg,$pm,$pe-$pm);
                    $bp=str_replace('=','',$pagefrom[$this->count]); $bp=str_replace('{','',$bp);$bp=str_replace('}','',$bp);$bp=trim(str_replace('-','',$bp));
                    $ep=str_replace('=','',$pageto[$this->count]); $bp=str_replace('{','',$bp);$bp=str_replace('}','',$bp);;$ep=trim(str_replace('-','',$ep));
                }
                $pe=strpos($seg,'},');
                
                if ($pe===false)
                    $value[$fieldcount]=strstr($seg,'=');
                else
                    $value[$fieldcount]=substr($seg,$ps,$pe);
            } else {
                $this->items['raw'][$this->count] .= $line . "\r\n";
                $pe=strpos($seg,'},');
                
                if ($fieldcount > -1) {
                    if ($pe===false)
                        $value[$fieldcount].=' '.strstr($seg,' ');
                    else
                        $value[$fieldcount] .=' '.substr($seg,$ps,$pe);
                }
            }
            
            if ($fieldcount > -1) {
                $v = $value[$fieldcount];
                $v=str_replace('=','',$v);
                $v=str_replace('{','',$v);
                $v=str_replace('}','',$v);
                $v=str_replace(',',' ',$v);
                $v=str_replace('\'',' ',$v);
                $v=str_replace('\"',' ',$v);
                // test!
                $v=str_replace('`',' ',$v);
                $v=trim($v);
                $this->items["$var[$fieldcount]"][$this->count]="$v";
            }
        }
    }
}
?>