<?php
/**
 * BPSP Class for notifications
 */
class BPSP_Notifications {
    
    /**
     * BPSP_Activity()
     *
     * Constructor, adds hooks to existing actions
     */
    function BPSP_Notifications() {
        add_action( 'courseware_grade_updated', array( &$this, 'send_message' ) );
        add_action( 'courseware_response_added', array( &$this, 'send_message' ) );
    }
    
    /**
     * send_message( $data )
     *
     * Generates a message about graded assignment and sends it to the student
     * @param Mixed $data required to send the message etc...
     */
    function send_message( $data ) {
        if( isset( $data['grade'] ) ) {
            $content = $this->gradebook_update_message( $data, true );
            $subject = $this->gradebook_update_message( $data, false, true );
            $recipients = $data['grade']['uid'];
        } elseif( isset( $data['response'] ) ) {
            $content = $this->response_added_message( $data, true );
            $subject = $this->response_added_message( $data, false, true );
            $recipients = $data['assignment']->post_author;
        }
        // Hack: ob_*() - to get rid of nasy warnings on messages_new_message()
        ob_start();
        messages_new_message(
            array(
                'recipients' => $recipients,
                'subject' => $subject,
                'content' => $content
            )
        );
        ob_clean();
    }
    
    /**
     * gradebook_update_message( $data, $body = false, $subject = false )
     * 
     * Generated content for new request messages.
     * 
     * @param Mixed $data, contains the student id, teacher id, assignment id etc.
     * @param bool $body if true generates the message body content
     * @param bool $subject if true generates the message subject content
     * @return String $content the generated message content
     */
    function gradebook_update_message( $data, $body = false, $subject = false ) {
        $content = null;
        if( $subject )
            $content = __( 'Your assignment was graded.', 'bpsp' );
        if( $body ) {
            $content = $data['teacher']->user_nicename;
            $content.= __( " graded your assignment: ", 'bpsp' );
            $content.= "\n";
            $content.= '<a href="' . $data['assignment']->permalink . '">' . $data['assignment']->post_title . '</a>';
            $content.= "\n";
            $content.= __( "Follow the link above to see the grade.", 'bpsp' );
        }
        return $content;
    }
    
    /**
     * response_added_message( $data, $body = false, $subject = false )
     * 
     * Generated content for new request messages.
     * 
     * @param Mixed $data, contains the student id, teacher id, response id etc.
     * @param bool $body if true generates the message body content
     * @param bool $subject if true generates the message subject content
     * @return String $content the generated message content
     */
    function response_added_message( $data, $body = false, $subject = false ) {
        $content = null;
        if( $subject )
            $content = __( 'Student replied to your assignment.', 'bpsp' );
        if( $body ) {
            $content = bp_core_get_userlink( $data['response']->post_author );
            $content.= __( " added a response to: ", 'bpsp' );
            $content.= "\n";
            $content.= '<a href="' . $data['assignment']->permalink . '">' . $data['assignment']->post_title . '</a>';
            $content.= "\n";
            $content.= __( "Follow the link above to see it.", 'bpsp' );
        }
        return $content;
    }
}