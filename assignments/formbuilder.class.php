<?php
/**
 * FormBuilder rewritten class
 * original work by Michael Botsko on jquery.formbuilder plugin
 */
class FormBuilder {
    /**
     * The original string that has to be parsed
     */
    var $serialized = null;
    
    /**
     * The parsed object of current form
     */
    var $unserialized = array();
    
    /**
     * The generated form content
     */
    var $generated_form = null;
    
    /**
     * The generated form names prefix
     */
    var $name_prefix = 'frmb';
    
    /**
     * FormBuilder()
     * Constructor, just creates an instance
     */
    function __construct(){
    }
    
    /**
     * load_serialized( $data )
     * Parses the serialized string
     *
     * @param String $data, a serialized data string to process
     */
    function load_serialized( $data ) {
        $this->serialized = $data;
		
        if( !empty( $data ) ) {
            parse_str( $data, $this->unserialized );
		}
		
        $this->unserialized = reset( $this->unserialized );
		
        // Sanitization
        foreach ( $this->unserialized as $k => $q ) {
            if ( !is_array( $q['values'] ) ) {
                $this->unserialized[$k]['values'] = apply_filters( 'content_save_pre', $q['values'] );
                $this->unserialized[$k]['values'] = str_replace( '\\', "&#92;", $q['values'] );
            } else {
                $this->unserialized[$k]['title'] = apply_filters( 'content_save_pre', $q['title'] );
                $this->unserialized[$k]['title'] = str_replace( '\\', "&#92;", $q['title'] );
                foreach ( $this->unserialized[$k]['values'] as $i => $a ) {
                    $this->unserialized[$k]['values'][$i]['value'] = apply_filters( 'content_save_pre', $a['value'] );
                    $this->unserialized[$k]['values'][$i]['value'] = str_replace( '\\', "&#92;", $a['value'] );
                }
            }
        }
    }
    
    /**
     * get_json( $data )
     * Compiles an unserialized string into a json object
     *
     * @param String $data, an unserialized data string to process
     * @return Mixed, the compiled json object
     */
    function get_json() {
        $data = $this->get_data();
        return json_encode( $data );
    }
    
    /**
     * set_data()
     * Setter for unserialized object
     *
     * @param Mixed $data, form data
     */
    function set_data( $data ) {
        $this->unserialized = $data;
    }
    
    /**
     * get_data()
     * Getter for unserialized object
     *
     * @return Mixed, form data
     */
    function get_data() {
        return $this->unserialized;
    }
    
    /**
     * render( $data = null )
     * Renders the form data in HTML
     *
     * @param Mixed $data, form data to render
     * @return String, rendered data string
     */
    function render( $data = null ) {
        $html = array();
        
        if( !$data ) {
            $data = $this->get_data();
		}
        
        if( $data ) {
            foreach( $data as $field ) {
                $html[] = $this->load_field( $field );
			}
		}
        
        return $html;
    }
    
    /**
     * Loads a new field based on its type
     *
     * @param Mixed $field, field data
     * @return String, rendered field data
     */
    function load_field( $field ) {
        // Apply some filters
        if ( !is_array( $field['values'] ) ) {
            $field['values'] = reset( preg_split( "/\?(?!.*\?)/", $field['values'] ) );
            $field['rendered_title'] = apply_filters( 'the_content', $field['values'] );
        } else {
            $field['rendered_title'] = apply_filters( 'the_content', $field['title'] );
            foreach ( $field['values'] as $i => $a )
                $field['values'][$i]['rendered_value'] = apply_filters( 'the_content', $a['value'] );
        }
        
        if( is_array( $field ) && isset( $field['cssClass'] ) ) {
            switch( $field['cssClass'] ) {
                case 'input_text':
                    return $this->load_text( $field );
                    break;
                case 'textarea':
                    return $this->load_textarea( $field );
                    break;
                case 'checkbox':
                    return $this->load_checkboxes( $field );
                    break;
                case 'radio':
                    return $this->load_radios( $field );
                    break;
                case 'select':
                    return $this->load_select( $field );
                    break;
            }
        }
		
        return false;
    }
    
    /**
     * load_text( $field )
     * Returns html for an input type="text"
     * 
     * @param Mixed $field, field values
     * @return String, resulted html
     */
    function load_text( $field ) {
        $field['required'] = $field['required'] == 'true' ? ' required' : false;
        $field_name = trim( $field['values'] );
        $field_id = sanitize_title( $field_name );
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            sanitize_title( $field['cssClass'] ),
            $field['required'],
            $field_id
        );
        $html .= sprintf(
            '<label for="%s">%s</label>' . "\n",
            $field_id,
            $field['rendered_title']
        );
        $html .= sprintf(
            '<input type="text" id="%s" name="%s" value="" />' . "\n",
            $field_id,
            $this->name_prefix . '[' . md5( $field_name ) . ']'
        );
        $html .= '</li>' . "\n";
        
        return $html;
    }
    
    /**
     * load_textarea( $field )
     * Returns html for a <textarea>
     *
     * @param Mixed $field, field values
     * @return String, resulted html
     */
    function load_textarea( $field ) {
        $field['required'] = $field['required'] == 'true' ? ' required' : false;
        $field_name = trim( $field['values'] );
        $field_id = sanitize_title( $field_name );
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            sanitize_title( $field['cssClass'] ),
            $field['required'],
            $field_id
        );
        $html .= sprintf(
            '<label for="%s">%s</label>' . "\n",
            $field_id,
            $field['rendered_title']
        );
        $html .= sprintf(
            '<textarea id="%s" name="%s" rows="5" cols="50"></textarea>' . "\n",
            $field_id,
            $this->name_prefix . '[' . md5( $field_name ) . ']'
        );
        $html .= '</li>' . "\n";
        
        return $html;
    }
    
    /**
     * load_checkboxes( $field )
     * Returns html for an <input type="checkbox"
     *
     * @param Mixed $field, field values
     * @return String, resulted html
     */
    function load_checkboxes( $field ){
        $field['required'] = $field['required'] == 'true' ? ' required' : false;
        $field_title = sanitize_title( $field['title'] );
        
        $html = sprintf(
            '<li class="%s" id="fld-%s">' . "\n",
            sanitize_title( $field['cssClass'] ),
            $field_title
        );
        
        if( isset( $field['title'] ) && !empty( $field['title'] ) ) {
            $html .= sprintf(
                '<label>%s</label>' . "\n",
                $field['rendered_title']
            );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $html .= '<span class="multi-row clearall">' . "\n";
            
            foreach( $field['values'] as $item ){
                // set the default checked value
                $checked = $item['baseline'] == 'true' ? true : false;
                $field_id = $field_title . '-' . sanitize_title( $item['value'] );
                
                $checkbox = '<span class="row clearall">';
                $checkbox .= '<input type="checkbox" id="%s" name="%s" value="%s" />';
                $checkbox .= '<label for="%1$s">%s</label></span>' . "\n";
                $html .= sprintf(
                    $checkbox,
                    $field_id,
                    $this->name_prefix . '[' . md5( $field['title'] . $item['value'] ) . ']',
                    $item['value'],
                    $item['rendered_value']
                );
            }
            $html .= '</span>' . "\n";
        }
        $html .= '</li>' . "\n";
        
        return $html;
    }
    
    /**
     * load_radios( $field )
     * Returns html for an <input type="radio"
     * 
     * @param Mixed $field, field values
     * @return String, resulted html
     */
    function load_radios( $field ) {
        $field['required'] = $field['required'] == 'true' ? ' required' : false;
        $field_id = sanitize_title( $field['cssClass'] );
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            $field_id,
            $field['required'],
            sanitize_title( $field['title'] )
        );
        
        if( isset( $field['title'] ) && !empty( $field['title'] ) ) {
            $html .= sprintf( '<label>%s</label>' . "\n", $field['rendered_title'] );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $html .= '<span class="multi-row">' . "\n";
            
            foreach( $field['values'] as $item ) {
                $field_id = sanitize_title( $field['title'] . ' ' . $item['value'] );
                $radio = '<span class="row clearall">';
                $radio .= '<input type="radio" id="%s" name="%s" value="%s" />';
                $radio .= '<label for="%1$s">%s</label>';
                $radio .= '</span>' . "\n";
                $html .= sprintf(
                    $radio,
                    $field_id,
                    $this->name_prefix . '[' . md5( $field['title'] ) . ']',
                    $item['value'],
                    $item['rendered_value']
                );
            }
            $html .= '</span>' . "\n";
        }
        $html .= '</li>' . "\n";
    
        return $html;
    }
    
    /**
    * load_select( $field )
    * Returns html for a <select>
    * 
     * @param Mixed $field, field values
     * @return String, resulted html
    */
    function load_select( $field ) {
        $field['required'] = $field['required'] == 'true' ? ' required' : false;
        $field_id = sanitize_title( $field['cssClass'] );
        $field_title = sanitize_title( $field['title'] );
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            $field_id,
            $field['required'],
            $field_title
        );
        
        if( isset( $field['title'] ) && !empty( $field['title'] ) ) {
            $html .= sprintf(
                '<label for="%s">%s</label>' . "\n",
                $field_title,
                $field['rendered_title']
            );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $multiple = $field['multiple'] == "true" ? ' multiple="multiple"' : '';
            $html .= sprintf(
                '<select name="%s" id="%s"%s>' . "\n",
                $this->name_prefix . '[' . md5( $field['title'] . $item['value'] ) . ']',
                $field_title,
                $multiple
            );
            
            foreach( $field['values'] as $item ) {
                $option = '<option value="%s">%s</option>' . "\n";
                $html .= sprintf(
                    $option,
                    md5( $field['title'] . $item['value'] ),
                    $item['rendered_value']
                );
            }
            
            $html .= '</select>' . "\n";
            $html .= '</li>' . "\n";
        }
        
        return $html;
    }
}

