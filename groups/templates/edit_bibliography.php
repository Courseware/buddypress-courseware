<style>
    label{display: block}
</style>
<div id="courseware-new-bibliography" >
    <h4><?php _e( 'Update a bibliography entry', 'bpsp' ); ?></h4>
    <form action="" method="post" >
        <div class="courseware-form-section">
            <select name="bib[type]">
                <option value="">
                    <?php _e( 'Select a type of source...', 'bpsp' ); ?>
                </option>
                <option <?php if( 'book' == $bib['type'] ) echo 'selected=""'; ?> value="book">
                    <?php _e( 'Book', 'bpsp' ); ?>
                </option>
                <option <?php if( 'article' == $bib['type'] ) echo 'selected'; ?> value="article">
                    <?php _e( 'Article', 'bpsp' ); ?>
                </option>
                <option <?php if( 'chapter' == $bib['type'] ) echo 'selected'; ?> value="chapter">
                    <?php _e( 'Volume Chapter', 'bpsp' ); ?>
                </option>
                <option <?php if( 'unpublished' == $bib['type'] ) echo 'selected'; ?> value="unpublished">
                    <?php _e( 'Unpublished', 'bpsp' ); ?>
                </option>
                <option <?php if( 'www' == $bib['type'] ) echo 'selected'; ?> value="www">
                    <?php _e( 'Website', 'bpsp' ); ?>
                </option>
                <option <?php if( 'wwwpage' == $bib['type'] ) echo 'selected'; ?> value="wwwpage">
                    <?php _e( 'Webpage', 'bpsp' ); ?>
                </option>
                <option <?php if( 'video' == $bib['type'] ) echo 'selected'; ?> value="video">
                    <?php _e( 'Video', 'bpsp' ); ?>
                </option>
                <option <?php if( 'audio' == $bib['type'] ) echo 'selected'; ?> value="audio">
                    <?php _e( 'Audio', 'bpsp' ); ?>
                </option>
            </select>
        </div>
        <h4><?php _e( 'Author(s)', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[author_lname]"><?php _e( 'Author Last Name', 'bpsp' ); ?></label>
                <input name="bib[author_lname]" value="<?php echo $bib['author'] ? $bib['author'] : $bib['author_lname'] ; ?>" />
            <label for="bib[author_fname]"><?php _e( 'Author First Name', 'bpsp' ); ?></label>
                <input name="bib[author_fname]" value="<?php echo $bib['author_fname']; ?>" />
            <label for="bib[author_lname2]"><?php _e( 'Author Two Last Name', 'bpsp' ); ?></label>
                <input name="bib[author_lname2]" value="<?php echo $bib['author_lname2']; ?>" />
            <label for="bib[author_fname2]"><?php _e( 'Author Two First Name', 'bpsp' ); ?></label>
                <input name="bib[author_fname2]" value="<?php echo $bib['author_fname2']; ?>" />
        </div>
        <h4><?php _e( 'Publish Information', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input name="bib[title]" value="<?php echo $bib['title']; ?>" />
            <label for="bib[stitle]"><?php _e( 'Short Title', 'bpsp' ); ?></label>
                <input name="bib[stitle]" value="<?php echo $bib['stitle']; ?>" />
            <label for="bib[jtitle]"><?php _e( 'Journal Title', 'bpsp' ); ?></label>
                <input name="bib[jtitle]" value="<?php echo $bib['jtitle']; ?>" />
            <label for="bib[vtitle]"><?php _e( 'Volume Title', 'bpsp' ); ?></label>
                <input name="bib[vtitle]" value="<?php echo $bib['vtitle']; ?>" />
            <label for="bib[veditors]"><?php _e( 'Volume Editor(s)', 'bpsp' ); ?></label>
                <input name="bib[veditors]" value="<?php echo $bib['veditors']; ?>" />
            <label for="bib[pubplace]"><?php _e( 'Publication Place', 'bpsp' ); ?></label>
                <input name="bib[pubplace]" value="<?php echo $bib['pubplace']; ?>" />
            <label for="bib[pub]"><?php _e( 'Publisher', 'bpsp' ); ?></label>
                <input name="bib[pub]" value="<?php echo $bib['pub']; ?>" />
            <label for="bib[wwwtitle]"><?php _e( 'Website Title', 'bpsp' ); ?></label>
                <input name="bib[wwwtitle]" value="<?php echo $bib['wwwtitle']; ?>" />
        </div>
        <h4><?php _e( 'Additional Information', 'bpsp' ); ?></h4>
        <div class="courseware-form-section">
            <label for="bib[pubdate]"><?php _e( 'Date Published', 'bpsp' ); ?></label>
                <input name="bib[pubdate]" value="<?php echo $bib['pubdate']; ?>" />
            <label for="bib[accdate]"><?php _e( 'Date Accessed', 'bpsp' ); ?></label>
                <input name="bib[accdate]" value="<?php echo $bib['accdate']; ?>" />
            <label for="bib[url]"><?php _e( 'URL', 'bpsp' ); ?></label>
                <input name="bib[url]" value="<?php echo $bib['url']; ?>" />
            <label for="bib[vol]"><?php _e( 'Volume', 'bpsp' ); ?></label>
                <input name="bib[vol]" value="<?php echo $bib['vol']; ?>" />
            <label for="bib[issue]"><?php _e( 'Issue', 'bpsp' ); ?></label>
                <input name="bib[issue]" value="<?php echo $bib['issue']; ?>" />
            <label for="bib[pages]"><?php _e( 'Pages', 'bpsp' ); ?></label>
                <input name="bib[pages]" value="<?php echo $bib['pages']; ?>" />
            <label for="bib[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea name="bib[desc]" cols="60" rows="6"><?php echo $bib['citation'] ? $bib['citation'] : $bib['desc']; ?></textarea>
        </div>
        <input type="submit" value="<?php _e( 'Update', 'bpsp' ); ?>" />
        <?php echo $bibs_nonce; ?>
    </form>
</div>
<?php
if( isset( $has_bibs ) )
    require_once BPSP_PLUGIN_DIR . '/groups/templates/_bibs.php';
?>