<!-- groups/templates/new_assignment.php -->
<form action="" method="post" class="standard-form" id="new-assignment-form">
    <div id="new-assignment-meta" class="courseware-sidebar">
        <h4 class="meta assignments"><span class="icon"></span><?php _e( 'Lecture &amp; Due Date', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="new-assignment-lecture">
                <label for="new-assignment-lecture"><?php _e( 'Linked Lecture', 'bpsp' ); ?></label>
                <select id="new-assignment-lecture" name="assignment[lecture_id]">
                    <option value=""><?php _e( 'Select Lecture', 'bpsp' ) ?></option>
                    <?php
                        echo walk_page_dropdown_tree( $lectures, 0,
                            array(
                                'echo' => 1,
                                'depth' => 0,
                                'child_of' => 0,
                                'selected' => 0,
                                'post_type' => 'lecture',
                                'sort_column'=> 'menu_order, post_title'
                            )
                        );
                    ?>
                </select>
            </li>
            
            <li id="new-assignment-due-date">
                <label for="new-assignment-duedate"><?php _e( 'Due Date', 'bpsp' ); ?></label>
                <input type="text" id="new-assignment-duedate" name="assignment[due_date]" title="yyyy-mm-dd hh:mm:ss"
                    value="<?php echo $posted_data['due_date'] ? $posted_data['due_date'] : ''; ?>"/>
            </li>
            <li id="new-assignment-content-options">
                <input type="hidden" id="new-assignment-post-object" name="assignment[object]" value="group"/>
                <input type="hidden" id="new-assignment-post-in" name="assignment[group_id]" value="<?php echo $group_id; ?>">
                <input type="hidden" name="assignment[course_id]" value="<?php echo $course_id; ?>">
                <input type="hidden" id="new-assignment-post-form" name="assignment[form]" value=""/>
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="new-assignment-content-submit">
                    <input type="submit" name="assignment[submit]" id="new-assignment-submit" value="<?php _e( 'Publish assignment', 'bpsp' ); ?>">
                </div>
            </li>
        </ul>
    </div>
    <div id="new-assignment-content" class="courseware-content-wrapper" >
        <div id="new-assignment-content-title">
            <input type="text" id="assignment-editor-title" name="assignment[title]"
                   value="<?php echo $posted_data['title'] ? $posted_data['title'] : ''; ?>"
                   class="long" title="<?php _e( 'Assignment Title', 'bpsp' ); ?>"/>
        </div>
        <div id="new-assignment-content-textarea">
            <?php $content = $posted_data['content'] ? $posted_data['content'] : ''; ?>
            <?php courseware_editor( $content, 'assignment-editor', array('textarea_name' => 'assignment[content]') ); ?>
        </div>
        
        <p class="clearall fat"></p>
        
        <p class="alignright">
            <label class="inline"><?php _e( 'Add a Quiz/Test', 'bpsp' ); ?></label>
            <span id="new-assignment-formbuilder-control-box" class="formbuilder-control-box" class="hide-if-no-js"></span>
        </p>
        
        <p class="alignright clearall">
            <em><?php _e( "For Text and Paragraph fields, end the title with a question mark, the correct answer is what will follow after.", 'bpsp' ); ?></em>
        </p>
        
        <p class="clearall"></p>
        
        <div id="courseware-assignment-builder" class="hide-if-no-js"></div>
    </div>
</form>