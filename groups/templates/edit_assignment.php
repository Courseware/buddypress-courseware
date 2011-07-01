<?php bpsp_load_editor_files(); ?>

<form action="<?php echo $assignment_edit_uri; ?>" method="post" class="standard-form" id="edit-assignment-form">
    <div id="edit-assignment-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Course &amp; Due Date', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="edit-assignment-lecture">
                <label for="edit-assignment-lecture"><?php _e( 'Linked Lecture', 'bpsp' ); ?></label>
                <select id="edit-assignment-lecture" name="assignment[lecture_id]">
                    <option value=""><?php _e( 'Select Lecture', 'bpsp' ) ?></option>
                    <?php
                        echo walk_page_dropdown_tree( $lectures, 0,
                            array(
                                'echo' => 1,
                                'depth' => 0,
                                'child_of' => 0,
                                'selected' => $lecture_id,
                                'post_type' => 'lecture',
                                'sort_column'=> 'menu_order, post_title'
                            )
                        );
                    ?>
                </select>
            </li>
            <li id="edit-assignment-due-date">
                <label for="edit-assignment-duedate"><?php _e( 'Due Date', 'bpsp' ); ?></label>
                <input type="text" id="edit-assignment-duedate" name="assignment[due_date]" value="<?php echo $assignment->due_date; ?>"/>
            </li>
            <li id="edit-assignment-content-options">
                <input type="hidden" id="edit-assignment-post-object" name="assignment[object]" value="group"/>
                <input type="hidden" id="edit-assignment-post-in" name="assignment[group_id]" value="<?php echo $group_id; ?>">
                <input type="hidden" id="edit-assignment-post-form" name="assignment[form]" value=""/>
                <input type="hidden" name="assignment[course_id]" value="<?php echo $assignment->course->ID; ?>">
                <?php echo $nonce ? $nonce: ''; ?>
                <input type="submit" name="assignment[submit]" id="edit-assignment-submit" value="<?php _e( 'Update assignment', 'bpsp' ); ?>">
                <div class="alignright submits">
                    <a href="<?php echo $assignment_permalink ?>" ><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a> |
                    <?php if( $delete_nonce ): ?>
                        <a href="<?php echo $delete_nonce; ?>" class="alert"><?php _e( 'Delete Assignment', 'bpsp' ); ?></a>
                    <?php endif; ?>
                </div>
            </li>
        </ul>
    </div>
    <div id="edit-assignment-content" class="courseware-content-wrapper">
        <div id="edit-assignment-content-title">
            <input type="text" id="assignment-title" name="assignment[title]" class="long" value="<?php echo $assignment->post_title; ?>" />
        </div>
        <div id="edit-assignment-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php the_editor( $assignment->post_content, 'assignment[content]', 'assignment[title]', false ); ?>
            </div>
        </div>
        <br />
        
        <p class="clearall fat"></p>
        
        <p class="alignright">
            <label class="inline"><?php _e( 'Add a Quiz/Test', 'bpsp' ); ?></label>
            <span id="edit-assignment-formbuilder-control-box" class="formbuilder-control-box" class="hide-if-no-js"></span>
        </p>
        
        <p class="alignright clearall">
            <em><?php _e( "For Text and Paragraph fields, end the title with a question mark, the correct answer is what will follow after.", 'bpsp' ); ?></em>
        </p>
        
        <p class="clearall"></p>
        
        <div id="courseware-assignment-builder" class="hide-if-no-js"></div>
        
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>

<?php wp_tiny_mce(); ?>