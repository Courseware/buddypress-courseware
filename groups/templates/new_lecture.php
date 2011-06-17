<?php bpsp_load_editor_files(); ?>

<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-lecture-form">
    <div id="new-lecture-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Lecture Parent &amp; Order', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="new-lecture-parent">
                    <?php
                        wp_dropdown_pages(
                            array(
                                'post_type' => 'lecture',
                                'name' => 'lecture[parent]',
                                'show_option_none' => __('(no parent)'),
                                'sort_column'=> 'menu_order, post_title',
                                'echo' => true
                            )
                        );
                    ?>
            </li>
            <li id="new-lecture-order">
                <input type="text" name="lecture[order]" title="<?php _e( 'Order', 'bpsp' ); ?>"
                    value="<?php echo $posted_data['order'] ? $posted_data['order'] : ''; ?>"/>
            </li>
        </ul>
    </div>
    <div id="new-lecture-content" class="courseware-content-wrapper" >
        <div id="new-lecture-content-title">
            <input type="text" id="lecture-title" name="lecture[title]"
                   value="<?php echo $posted_data['title'] ? $posted_data['title'] : ''; ?>"
                   class="long" title="<?php _e( 'Lecture Title', 'bpsp' ); ?>"/>
        </div>
        <div id="new-lecture-content-textarea">
            <div id="editor-toolbar">
                <div id="media-toolbar">
                    <?php echo bpsp_media_buttons(); ?>
                </div>
                <?php $content = $posted_data['content'] ? $posted_data['content'] : ''; ?>
                <?php the_editor( $content, 'lecture[content]', 'lecture[title]', false ); ?>
            </div>
        </div>
        <div id="new-lecture-content-options">
            <input type="hidden" id="new-lecture-post-object" name="lecture[object]" value="group"/>
            <input type="hidden" id="new-lecture-post-in" name="lecture[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-lecture-content-submit">
                <input type="submit" name="lecture[submit]" id="new-lecture-submit" value="<?php _e( 'Add a new lecture', 'bpsp' ); ?>">
            </div>
        </div>
    </div>
</form>
<script type="text/javascript" >
    var tb_closeImage = "/wp-includes/js/thickbox/tb-close.png";
</script>

<?php wp_tiny_mce(); ?>