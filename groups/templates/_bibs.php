<div id="courseware-bibs">
    <?php if( $has_bib_caps ): ?>
    <div id="courseware-bibs-form">
        <form action="" method="post" >
            <?php if( !$hide_existing ): ?>
            <div class="existing">
                <h4><?php _e( 'Add an existing bibliography', 'bpsp'); ?></h4>
                <select name="bib[existing]">
                    <?php
                    if( is_array( $bibdb ) )
                        foreach( $bibdb as $b_hash => $b ):
                    ?>
                    <option value="<?php echo $b_hash; ?>"><?php echo $b['plain']; ?></option>
                    <?php
                        endforeach;
                    ?>
                </select>
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add entry', 'bpsp' ); ?>" />
            </div>
            <?php endif; ?>
            <div class="book">
                <h4><?php _e( 'Add a book', 'bpsp'); ?></h4>
                <label for="bib[book][title]"><?php _e( 'Entry title', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][title]" />
                <label for="bib[book][isbn]"><?php _e( 'Book ISBN', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][isbn]" />
                <label for="bib[book][page]"><?php _e( 'Recommended book page to check', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][page]" />
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add book', 'bpsp' ); ?>" />
            </div>
            <div class="www">
                <h4><?php _e( 'Add a webpage', 'bpsp'); ?></h4>
                <label for="bib[www][title]"><?php _e( 'Entry title', 'bpsp'); ?></label>
                    <input type="text" name="bib[www][title]" />
                <label for="bib[www][uri]"><?php _e( 'Webpage address', 'bpsp'); ?></label>
                    <input type="text" name="bib[www][url]" />
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add entry', 'bpsp' ); ?>" />
            </div>
            <?php echo $bibs_nonce; ?>
        </form>
    </div>
    <?php endif; ?>
    <div id="courseware-bibs-list">
        <h4><?php _e( 'Bibliography listing', 'bpsp'); ?></h4>
        <?php if( count( $bibs ) > 0 ): ?>
            <ul>
                <?php foreach( $bibs as $b): ?>
                    <li>
                        <?php echo $b['html']; ?>
                        <span style="float: right">
                            <a href="<?php echo add_query_arg( 'bhash', $b['hash'] . ',' . $post_id, $bibs_edit_uri ); ?>">
                                <?php _e( 'Edit Entry', 'bpsp' ); ?>
                            </a>
                            &nbsp;|
                            <a href="<?php echo add_query_arg( 'bhash', $b['hash'] . ',' . $post_id, $bibs_delete_uri ); ?>">
                                <?php _e( 'Delete Entry', 'bpsp' ); ?>
                            </a>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>