<div id="courseware-bibs">
    <?php if( $has_bib_caps ): ?>
    <div id="courseware-bibs-form">
        <form action="" method="post" class="standard-form" >
            <?php if( !$hide_existing ): ?>
            <div class="existing">
                <h4><?php _e( 'Add an existing bibliography', 'bpsp'); ?></h4>
                <select name="bib[existing]">
                    <option value=""><?php _e( 'Type something to search...', 'bpsp' ); ?></option>
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
            <div class="www" style="width: 45%; float: right;" >
                <h4><?php _e( 'Add a webpage', 'bpsp'); ?></h4>
                <label for="bib[www][title]"><?php _e( 'Entry title', 'bpsp'); ?></label>
                    <input type="text" name="bib[www][title]" />
                <label for="bib[www][uri]"><?php _e( 'Webpage address', 'bpsp'); ?></label>
                    <input type="text" name="bib[www][url]" />
                <p>
                    <input type="submit" name="bib[submit]" value="<?php _e( 'Add entry', 'bpsp' ); ?>" />
                </p>
            </div>
            <div class="book" style="width: 45%;" >
                <h4><?php _e( 'Add a book', 'bpsp'); ?></h4>
                <label for="bib[book][title]"><?php _e( 'Entry title', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][title]" />
                <p><?php _e( '&mdash; or &mdash;', 'bpsp'); ?></p>
                <label for="bib[book][isbn]"><?php _e( 'Book ISBN', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][isbn]" />
                <label for="bib[book][page]"><?php _e( 'Instructions/Description', 'bpsp'); ?></label>
                    <input type="text" name="bib[book][desc]" />
                <p>
                    <input type="submit" name="bib[submit]" value="<?php _e( 'Add book', 'bpsp' ); ?>" />
                </p>
            </div>
            <?php echo $bibs_nonce; ?>
        </form>
    </div>
    <?php endif; ?>
    <hr/>
    <div id="courseware-bibs-list" style="clear: both;">
        <h4><?php _e( 'Bibliography listing', 'bpsp'); ?></h4>
        <?php if( count( $bibs ) > 0 ): ?>
            <table class="datatables">
                <thead>
                    <tr>
                        <th><?php _e( '#', 'bpsp' ); ?> &nbsp; </th>
                        <th><?php _e( 'Title', 'bpsp' ); ?></th>
                        <?php if( $has_bib_caps ): ?>
                            <th><?php _e( 'Actions', 'bpsp' ); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                <?php $i = 0; foreach( $bibs as $b): $i++; ?>
                    <tr>
                        <td>
                            <a href="#B<?php echo $i; ?>" id="B<?php echo $i; ?>"><?php echo $i; ?></a>
                        </td>
                        <td>
                            <?php if( isset( $b['cover'] ) ): ?>
                                <img src="<?php echo $b['cover']; ?>" alt="<?php echo $b['plain']; ?>" class="alignleft" />
                            <?php
                                endif;
                                echo $b['html'];
                            ?>
                        </td>
                        <?php if( $has_bib_caps ): ?>
                            <td style="white-space: nowrap;">
                                <a href="<?php echo add_query_arg( 'bhash', $b['hash'] . ',' . $post_id, $bibs_edit_uri ); ?>">
                                    <?php _e( 'Edit Entry', 'bpsp' ); ?>
                                </a>
                                <br/>
                                <a href="<?php echo add_query_arg( 'bhash', $b['hash'] . ',' . $post_id, $bibs_delete_uri ); ?>">
                                    <?php _e( 'Delete Entry', 'bpsp' ); ?>
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>