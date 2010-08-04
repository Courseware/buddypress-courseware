<style>
    label{display: block}
</style>
<div id="courseware-new-bibliography" >
    <h4>
        <a href="javascript:jQuery('form.add-bib').slideToggle();"><?php _e( 'Add a new bibliography', 'bpsp' ); ?></a> |
        <a href="<?php echo $import_uri ?>"><?php _e( 'Import Bibliography', 'bpsp' ); ?></a>
    </h4>
    <form action="" method="post" class="standard-form add-bib" style="display: none;" >
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
        <h4><?php _e( 'Author(s)', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[author_lname]"><?php _e( 'Author Last Name', 'bpsp' ); ?></label>
                <input type="text" name="bib[author_lname]" />
            <label for="bib[author_fname]"><?php _e( 'Author First Name', 'bpsp' ); ?></label>
                <input type="text" name="bib[author_fname]" />
            <label for="bib[author_lname2]"><?php _e( 'Author Two Last Name', 'bpsp' ); ?></label>
                <input type="text" name="bib[author_lname2]" />
            <label for="bib[author_fname2]"><?php _e( 'Author Two First Name', 'bpsp' ); ?></label>
                <input type="text" name="bib[author_fname2]" />
        </div>
        <h4><?php _e( 'Publish Information', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" name="bib[title]" />
            <label for="bib[stitle]"><?php _e( 'Short Title', 'bpsp' ); ?></label>
                <input type="text" name="bib[stitle]" />
            <label for="bib[jtitle]"><?php _e( 'Journal Title', 'bpsp' ); ?></label>
                <input type="text" name="bib[jtitle]" />
            <label for="bib[vtitle]"><?php _e( 'Volume Title', 'bpsp' ); ?></label>
                <input type="text" name="bib[vtitle]" />
            <label for="bib[veditors]"><?php _e( 'Volume Editor(s)', 'bpsp' ); ?></label>
                <input type="text" name="bib[veditors]" />
            <label for="bib[pubplace]"><?php _e( 'Publication Place', 'bpsp' ); ?></label>
                <input type="text" name="bib[pubplace]" />
            <label for="bib[pub]"><?php _e( 'Publisher', 'bpsp' ); ?></label>
                <input type="text" name="bib[pub]" />
            <label for="bib[wwwtitle]"><?php _e( 'Website Title', 'bpsp' ); ?></label>
                <input type="text" name="bib[wwwtitle]" />
        </div>
        <h4><?php _e( 'Additional Information', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[pubdate]"><?php _e( 'Date Published', 'bpsp' ); ?></label>
                <input type="text" name="bib[pubdate]" />
            <label for="bib[accdate]"><?php _e( 'Date Accessed', 'bpsp' ); ?></label>
                <input type="text" name="bib[accdate]" />
            <label for="bib[url]"><?php _e( 'URL', 'bpsp' ); ?></label>
                <input type="text" name="bib[url]" />
            <label for="bib[vol]"><?php _e( 'Volume', 'bpsp' ); ?></label>
                <input type="text" name="bib[vol]" />
            <label for="bib[issue]"><?php _e( 'Issue', 'bpsp' ); ?></label>
                <input type="text" name="bib[issue]" />
            <label for="bib[pages]"><?php _e( 'Pages', 'bpsp' ); ?></label>
                <input type="text" name="bib[pages]" />
            <label for="bib[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea name="bib[desc]" cols="60" rows="6"></textarea>
        </div>
        <input type="submit" value="<?php _e( 'Add', 'bpsp' ); ?>" />
        <?php echo $bibs_nonce; ?>
    </form>
</div>
<hr/>
<?php
if( isset( $has_bibs ) )
    // Load bibs
    bpsp_partial( $templates_path, '_bibs', get_defined_vars() );
?>