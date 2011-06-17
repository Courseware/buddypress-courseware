<?php bpsp_load_editor_files(); ?>

<form action="<?php echo $lecture_edit_uri; ?>" method="post" class="standard-form" id="update-lecture-form">
    <div id="update-lecture-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Lecture Parent &amp; Order', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="update-lecture-parent">
                    <select name="lecture[parent]">
                        <?php
                            // Dummy object to improve UI/UX
                            array_unshift( $lectures, (object) array(
                                'ID' => 0,
                                'post_title' => __( 'No parent lecture', 'bpsp' )
                            ) );
                        ?>
                        <?php foreach( $lectures as $l ): ?>
                            <option value="<?php echo $l->ID; ?>" <?php selected( $l->ID, $lecture->post_parent ); ?> >
                                <?php echo $l->post_title; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
            </li>
            <li id="update-lecture-order">
                <input type="text" name="lecture[order]" title="<?php _e( 'Order', 'bpsp' ); ?>"
                    value="<?php echo $lecture->menu_order; ?>"/>
            </li>
        </ul>
    </div>
    <div id="update-lecture-content" class="courseware-content-wrapper" >
        <div id="update-lecture-content-title">
            <input type="text" id="lecture-title" name="lecture[title]"
                   value="<?php echo get_the_title( $lecture->ID ); ?>"
                   class="long" title="<?php _e( 'Lecture Title', 'bpsp' ); ?>"/>
        </div>
        <div id="update-lecture-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php the_editor( $lecture->post_content, 'lecture[content]', 'lecture[title]', false ); ?>
            </div>
        </div>
        <div id="update-lecture-content-options">
            <input type="hidden" id="update-lecture-post-object" name="lecture[object]" value="group"/>
            <input type="hidden" id="update-lecture-post-in" name="lecture[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="update-lecture-content-submit">
                <input type="submit" name="lecture[submit]" id="update-lecture-submit" value="<?php _e( 'Update Lecture', 'bpsp' ); ?>">
                <div class="alignright submits">
                    <?php if( $delete_nonce ): ?>
                        <a href="<?php echo $delete_nonce; ?>" class="action alert"><?php _e( 'Delete Lecture', 'bpsp' ); ?></a>
                    <?php endif; ?>
                    <a href="<?php echo $lecture_permalink ?>" class="action safe"><?php _e( 'Cancel/Go Back', 'bpsp' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>

<?php wp_tiny_mce(); ?>