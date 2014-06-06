<!-- groups/templates/add_response.php -->
<?php setup_postdata( $parent_assignment ); ?>
<div id="assignment-response-form">
    <form action="" method="post" class="standard-form" id="new-response-form">
        <?php if( isset( $parent_assignment->form ) ) : ?>
            <h4 id="assignment-quiz-title" class="courseware-title">
                <?php echo get_the_title( $assignment->ID ); ?>
            </h4>
            <div id="assignment-quiz-content">
                <?php the_content(); ?>
            </div>
            <div id="assignment-quiz">
                <ol>
                    <?php foreach( $parent_assignment->form as $form_lines ): ?>
                        <?php echo $form_lines; ?>
                    <?php endforeach; ?>
                </ol>
                <div id="assignment-quiz-submit">
                    <input type="hidden" name="response[parent_id]" value="<?php echo $parent_assignment->ID ?>" />
                    <?php echo $nonce ? $nonce: ''; ?>
                    <input type="submit" value="<?php _e( 'Publish my response', 'bpsp' ); ?>" <?php disabled( true, false ); ?> />
                    <div class="alignleft submits">
                        <a href="<?php echo $parent_assignment->permalink ?>"><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div id="new-response-content">
                <div id="new-response-content-title">
                    <input type="text" id="response-editor-title" name="response[title]" class="long" value="<?php _e( 'My response for: ', 'bpsp' ); echo get_the_title( $parent_assignment->ID ); ?>" />
                </div>
                <div id="new-response-content-textarea">
                    <?php courseware_editor( '', 'response-editor', array('textarea_name' => 'response[content]') ); ?>
                </div>
                <div id="new-response-content-options">
                    <input type="hidden" name="response[parent_id]" value="<?php echo $parent_assignment->ID ?>" />
                    <?php echo $nonce ? $nonce: ''; ?>
                    <div id="new-response-content-submit">
                        <input type="submit" name="response[submit]" id="new-response-submit" value="<?php _e( 'Publish my response', 'bpsp' ); ?>">
                        <div class="alignright submits">
                            <a href="<?php echo $parent_assignment->permalink ?>" ><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </form>
</div>