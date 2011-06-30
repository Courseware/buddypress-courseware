<div id="courseware-new-bibliography" > 
    <form action="" method="post" class="standard-form add-bib" >
        <div class="courseware-sidebar">
            <h4 class="meta"><?php _e( 'Bibliography Tools', 'bpsp' ); ?></h4>
            <ul class="courseware-meta">
                <li class="bib-types-list">
                    <div class="courseware-form-section">
                        <select name="bib[type]">
                            <option value=""><?php _e( 'Select a type of source...', 'bpsp' ); ?></option>
                            <option value="book"><?php _e( 'Book', 'bpsp' ); ?></option>
                            <option value="article"><?php _e( 'Article', 'bpsp' ); ?></option>
                            <option value="chapter"><?php _e( 'Volume Chapter', 'bpsp' ); ?></option>
                            <option value="unpublished"><?php _e( 'Unpublished', 'bpsp' ); ?></option>
                            <option value="www"><?php _e( 'Website', 'bpsp' ); ?></option>
                            <option value="wwwpage"><?php _e( 'Webpage', 'bpsp' ); ?></option>
                            <option value="video"><?php _e( 'Video', 'bpsp' ); ?></option>
                            <option value="audio"><?php _e( 'Audio', 'bpsp' ); ?></option>
                        </select>
                    </div>
                </li>
                <li class="add-new-bib">
                    <a href="#bibliography-form-content" class="action"><?php _e( 'Add a new bibliography', 'bpsp' ); ?></a>
                </li>
                <li>
                    <a href="<?php echo $import_uri ?>" class="action"><?php _e( 'Import Bibliography', 'bpsp' ); ?></a>
                </li>
                <li class="show-bibs">
                    <a href="#courseware-bibs-list" class="action"><?php _e( 'Bibliography', 'bpsp' ); ?></a>
                </li>
                <li class="add book">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Book', 'bpsp' ); ?></a>
                </li>
                <li class="add www">
                    <a href="#courseware-bibs-form" class="action"><?php _e( 'Quick Add Webpage', 'bpsp' ); ?></a>
                </li>
            </ul>
        </div>
        <div class="courseware-content-wrapper" >
            <div id="bibliography-form-content">
                <h4 class="meta"><?php _e( 'Author(s)', 'bpsp' ); ?></h4>
                <div class="courseware-form-section">
                    <label for="bib[author_lname]"><?php _e( 'Author Last Name', 'bpsp' ); ?></label>
                        <input type="text" name="bib[author_lname]" title="<?php _e( 'Author Last Name', 'bpsp' ); ?>" />
                    <label for="bib[author_fname]"><?php _e( 'Author First Name', 'bpsp' ); ?></label>
                        <input type="text" name="bib[author_fname]" title="<?php _e( 'Author First Name', 'bpsp' ); ?>" />
                    <label for="bib[author_lname2]"><?php _e( 'Author Two Last Name', 'bpsp' ); ?></label>
                        <input type="text" name="bib[author_lname2]" title="<?php _e( 'Author Two Last Name', 'bpsp' ); ?>" />
                    <label for="bib[author_fname2]"><?php _e( 'Author Two First Name', 'bpsp' ); ?></label>
                        <input type="text" name="bib[author_fname2]" title="<?php _e( 'Author Two First Name', 'bpsp' ); ?>" />
                </div>
                <h4 class="meta"><?php _e( 'Publish Information', 'bpsp' ); ?></h4>
                <div class="courseware-form-section">
                    <label for="bib[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                        <input type="text" name="bib[title]" title="<?php _e( 'Title', 'bpsp' ); ?>" />
                    <label for="bib[stitle]"><?php _e( 'Short Title', 'bpsp' ); ?></label>
                        <input type="text" name="bib[stitle]" title="<?php _e( 'Short Title', 'bpsp' ); ?>" />
                    <label for="bib[jtitle]"><?php _e( 'Journal Title', 'bpsp' ); ?></label>
                        <input type="text" name="bib[jtitle]" title="<?php _e( 'Journal Title', 'bpsp' ); ?>" />
                    <label for="bib[vtitle]"><?php _e( 'Volume Title', 'bpsp' ); ?></label>
                        <input type="text" name="bib[vtitle]" title="<?php _e( 'Volume Title', 'bpsp' ); ?>" />
                    <label for="bib[veditors]"><?php _e( 'Volume Editor(s)', 'bpsp' ); ?></label>
                        <input type="text" name="bib[veditors]" title="<?php _e( 'Volume Editor(s)', 'bpsp' ); ?>" />
                    <label for="bib[pubplace]"><?php _e( 'Publication Place', 'bpsp' ); ?></label>
                        <input type="text" name="bib[pubplace]" title="<?php _e( 'Publication Place', 'bpsp' ); ?>" />
                    <label for="bib[pub]"><?php _e( 'Publisher', 'bpsp' ); ?></label>
                        <input type="text" name="bib[pub]" title="<?php _e( 'Publisher', 'bpsp' ); ?>" />
                    <label for="bib[wwwtitle]"><?php _e( 'Website Title', 'bpsp' ); ?></label>
                        <input type="text" name="bib[wwwtitle]" title="<?php _e( 'Website Title', 'bpsp' ); ?>" />
                </div>
                <h4 class="meta"><?php _e( 'Additional Information', 'bpsp' ); ?></h4>
                <div class="courseware-form-section">
                    <label for="bib[pubdate]"><?php _e( 'Date Published', 'bpsp' ); ?></label>
                        <input type="text" name="bib[pubdate]" title="<?php _e( 'Date Published', 'bpsp' ); ?>" />
                    <label for="bib[accdate]"><?php _e( 'Date Accessed', 'bpsp' ); ?></label>
                        <input type="text" name="bib[accdate]" title="<?php _e( 'Date Accessed', 'bpsp' ); ?>" />
                    <label for="bib[url]"><?php _e( 'URL', 'bpsp' ); ?></label>
                        <input type="text" name="bib[url]" title="<?php _e( 'URL', 'bpsp' ); ?>" />
                    <label for="bib[vol]"><?php _e( 'Volume', 'bpsp' ); ?></label>
                        <input type="text" name="bib[vol]" title="<?php _e( 'Volume', 'bpsp' ); ?>" />
                    <label for="bib[issue]"><?php _e( 'Issue', 'bpsp' ); ?></label>
                        <input type="text" name="bib[issue]" title="<?php _e( 'Issue', 'bpsp' ); ?>" />
                    <label for="bib[pages]"><?php _e( 'Pages', 'bpsp' ); ?></label>
                        <input type="text" name="bib[pages]" title="<?php _e( 'Pages', 'bpsp' ); ?>" />
                    <label for="bib[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                        <textarea name="bib[desc]" cols="60" rows="6" title="<?php _e( 'Description', 'bpsp' ); ?>"></textarea>
                </div>
                <input type="submit" value="<?php _e( 'Publish', 'bpsp' ); ?>" />
                <?php echo $bibs_nonce; ?>
            </div>
        </div>
     </form>
</div>
<?php
if( isset( $has_bibs ) )
    // Load bibs
    bpsp_partial( $templates_path, '_bibs', get_defined_vars() );
?>