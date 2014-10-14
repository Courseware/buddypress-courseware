<!-- groups/templates/edit_lecture.php -->
<form action="<?php echo $lecture_edit_uri; ?>" method="post" class="standard-form" id="update-lecture-form">
    <div id="update-lecture-meta" class="courseware-sidebar">
        <h4 class="meta lectures"><span class="icon"></span><?php _e( 'Lecture parent &amp; Order', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="update-lecture-parent">
                <label for="update-lecture-parent"><?php _e( 'Parent Lecture', 'bpsp' ); ?></label>
                <select id="update-lecture-parent" name="lecture[parent]">
                    <option value=""><?php _e( '(no parent)' ); ?></option>
                    <?php
                        echo walk_page_dropdown_tree( $lectures, 0,
                            array(
                                'echo' => 1,
                                'depth' => 0,
                                'child_of' => 0,
                                'selected' => $lecture->post_parent,
                                'post_type' => 'lecture',
                                'sort_column'=> 'menu_order, post_title'
                            )
                        );
                    ?>
                </select>
            </li>
            <li id="update-lecture-order">
                <label for="update-lecture-order"><?php _e( 'Lecture Order', 'bpsp' ); ?></label>
                <input type="text" id="update-lecture-order" name="lecture[order]" class="number" title="<?php _e( 'Order', 'bpsp' ); ?>"
                    value="<?php echo $lecture->menu_order; ?>"/>
            </li>
            <li id="update-lecture-content-options">
                <input type="hidden" id="update-lecture-post-object" name="lecture[object]" value="group"/>
                <input type="hidden" id="update-lecture-post-in" name="lecture[group_id]" value="<?php echo $group_id; ?>">
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="update-lecture-content-submit">
                    <input type="submit" name="lecture[submit]" id="update-lecture-submit" value="<?php _e( 'Update lecture', 'bpsp' ); ?>">
                    <div class="alignright submits">
                        <a href="<?php echo $lecture_permalink ?>" ><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a> | 
                        <?php if( $delete_nonce ): ?>
                            <a href="<?php echo $delete_nonce; ?>" class="alert"><?php _e( 'Delete Lecture', 'bpsp' ); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div id="update-lecture-content" class="courseware-content-wrapper" >
        <div id="update-lecture-content-title">
            <input type="text" id="lecture-editor-title" name="lecture[title]"
                   value="<?php echo get_the_title( $lecture->ID ); ?>"
                   class="long" title="<?php _e( 'Lecture Title', 'bpsp' ); ?>"/>
        </div>
        <div id="update-lecture-content-textarea">
            <?php courseware_editor( $lecture->post_content, 'lecture-editor', array('textarea_name' => 'lecture[content]') ); ?>
        </div>
    </div>
</form>
