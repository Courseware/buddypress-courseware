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
    function FormBuilder(){
    }
    
    /**
     * load_serialized( $data )
     * Parses the serialized string
     *
     * @param String $data, a serialized data string to process
     */
    function load_serialized( $data ) {
        $this->serialized = $data;
        if( !empty( $data ) )
            parse_str( $data, $this->unserialized );
        $this->unserialized = reset( $this->unserialized );
    }
    
    /**
     * get_json( $data )
     * Compiles an unserialized string into a json object
     *
     * @param String $data, an unserialized data string to process
     * @return Mixed, the compiled json object
     */
    function get_json() {
        return json_encode( $this->unserialized );
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
        
        if( !$data )
            $data = $this->get_data();
        
        if( $data )
            foreach( $data as $field )
                $html[] = $this->load_field( $field );
        
        return $html;
    }
    
    /**
     * Loads a new field based on its type
     *
     * @param Mixed $field, field data
     * @return String, rendered field data
     */
    function load_field( $field ) {
        if( is_array( $field ) && isset( $field['class'] ) ) {
            switch( $field['class'] ) {
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
        $field_name = trim( preg_replace( "/\?.*/i", '?', $field['values'] ) );
        $field_id = sanitize_title( $field_name );
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            sanitize_title( $field['class'] ),
            $field['required'],
            $field_id
        );
        $html .= sprintf(
            '<label for="%s">%s</label>' . "\n",
            $field_id,
            $field_name
        );
        $html .= sprintf(
            '<input type="text" id="%s" name="%s" value="" />' . "\n",
            $field_id,
            $this->name_prefix . '[' . $field_id . ']'
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
        $field_name = trim( preg_replace( "/\?.*/i", '?', $field['values'] ) );
        $field_id = sanitize_title( $field_name );
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            sanitize_title( $field['class'] ),
            $field['required'],
            $field_id
        );
        $html .= sprintf(
            '<label for="%s">%s</label>' . "\n",
            $field_id,
            $field_name
        );
        $html .= sprintf(
            '<textarea id="%s" name="%s" rows="5" cols="50"></textarea>' . "\n",
            $field_id,
            $this->name_prefix . '[' . $field_id . ']'
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
            sanitize_title( $field['class'] ),
            $field_title
        );
        
        if( isset( $field['title'] ) && !empty( $field['title'] ) ) {
            $html .= sprintf(
                '<label>%s</label>' . "\n",
                $field['title']
            );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $html .= '<span class="multi-row clearall">' . "\n";
            
            foreach( $field['values'] as $item ){
                // set the default checked value
                $checked = $item['default'] == 'true' ? true : false;
                $field_id = $field_title . '-' . sanitize_title( $item['value'] );
                
                $checkbox = '<span class="row clearall">';
                $checkbox .= '<input type="checkbox" id="%s" name="%s" value="%s" />';
                $checkbox .= '<label for="%s">%s</label></span>' . "\n";
                $html .= sprintf(
                    $checkbox,
                    $field_id,
                    $this->name_prefix . '[' . $field_id . ']',
                    esc_attr( $item['value'] ),
                    $field_id,
                    $item['value']
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
        
        $html = sprintf(
            '<li class="%s%s" id="fld-%s">' . "\n",
            sanitize_title( $field['values'] ),
            $field['required'],
            sanitize_title( $field['title'] )
        );
        
        if( isset( $field['title'] ) && !empty( $field['title'] ) ) {
            $html .= sprintf( '<label>%s</label>' . "\n", $field['title'] );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $html .= '<span class="multi-row">' . "\n";
            
            foreach( $field['values'] as $item ) {
                $field_id = sanitize_title( $field['title'] );
                $radio = '<span class="row clearall">';
                $radio .= '<input type="radio" id="%s" name="%s" value="%s" />';
                $radio .= '<label for="%1$s">%s</label>';
                $radio .= '</span>' . "\n";
                $html .= sprintf(
                    $radio,
                    $field_id,
                    $this->name_prefix . '[' . $field_id . ']',
                    esc_attr( $item['value'] ),
                    $item['value']
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
        $field_id = sanitize_title( $field['class'] );
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
                $field['title']
            );
        }
        
        if( isset( $field['values'] ) && is_array( $field['values'] ) ) {
            $multiple = $field['multiple'] == "true" ? ' multiple="multiple"' : '';
            $html .= sprintf(
                '<select name="%s" id="%s"%s>' . "\n",
                $this->name_prefix . '[' . $field_title . ']',
                $field_title,
                $multiple
            );
            
            foreach( $field['values'] as $item ) {
                $option = '<option value="%s">%s</option>' . "\n";
                $html .= sprintf(
                    $option,
                    sanitize_title( $item['value'] ),
                    esc_attr( $item['value'] )
                );
            }
            
            $html .= '</select>' . "\n";
            $html .= '</li>' . "\n";
        }
        
        return $html;
    }
}
?>