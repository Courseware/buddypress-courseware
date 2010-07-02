<div id="courseware-bibs">
    <div id="courseware-bibs-form">
        <form action="" method="post" >
            <div class="book">
                <h4><?php _e( 'Add an existing bibliography', 'bpsp'); ?></h4>
                <select name="bib[existing]">
                    <?php
                    if( is_array( $bibdb ) )
                        foreach( $bibdb as $b ):
                            $b_hash = md5( $b );
                    ?>
                    <option value="<?php echo $b_hash; ?>"><?php echo $b; ?></option>
                    <?php
                        endforeach;
                    ?>
                </select>
                <h4><?php _e( 'Add a book', 'bpsp'); ?></h4>
                <label for="bib[book][title]">
                    <?php _e( 'Entry title', 'bpsp'); ?>
                    <input type="text" name="bib[book][title]" />
                </label>
                <label for="bib[book][isbn]">
                    <?php _e( 'Book ISBN', 'bpsp'); ?>
                    <input type="text" name="bib[book][isbn]" />
                </label>
                <label for="bib[book][page]">
                    <?php _e( 'Recommended book page to check', 'bpsp'); ?>
                    <input type="text" name="bib[book][page]" />
                </label>
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add book', 'bpsp' ); ?>" />
            </div>
            <div class="www">
                <h4><?php _e( 'Add a webpage', 'bpsp'); ?></h4>
                <label for="bib[www][title]">
                    <?php _e( 'Entry title', 'bpsp'); ?>
                    <input type="text" name="bib[www][title]" />
                </label>
                <label for="bib[www][uri]">
                    <?php _e( 'Webpage address', 'bpsp'); ?>
                    <input type="text" name="bib[www][uri]" />
                </label>
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add entry', 'bpsp' ); ?>" />
            </div>
            <div class="wiki">
                <h4><?php _e( 'Add a Wikipedia link', 'bpsp'); ?></h4>
                <label for="bib[wiki][title]">
                    <?php _e( 'Entry title', 'bpsp'); ?>
                    <input type="text" name="bib[wiki][title]" />
                </label>
                <label for="bib[wiki][uri]">
                    <?php _e( 'Wikipedia address', 'bpsp'); ?>
                    <input type="text" name="bib[wiki][uri]" />
                </label>
                <input type="submit" name="bib[submit]" value="<?php _e( 'Add entry', 'bpsp' ); ?>" />
            </div>
            <?php echo $bibs_nonce; ?>
        </form>
    </div>
    <div id="courseware-bibs-list">
        <?php var_dump( $bibs ); ?>
    </div>
</div>