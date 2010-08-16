<div id="courseware-edit-bibliography" >
    <form action="<?php echo $bibs_form_uri; ?>" method="post" class="standard-form" >
    <div class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Update Bibliography', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li>
                <div class="courseware-form-section">
                    <select name="bib[type]">
                        <option value="">
                            <?php _e( 'Select a type of source...', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'book', $bib['type'] ); ?> value="book">
                            <?php _e( 'Book', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'article', $bib['type'] ); ?> value="article">
                            <?php _e( 'Article', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'chapter', $bib['type'] ); ?> value="chapter">
                            <?php _e( 'Volume Chapter', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'unpublished', $bib['type'] ); ?> value="unpublished">
                            <?php _e( 'Unpublished', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'www', $bib['type'] ); ?> value="www">
                            <?php _e( 'Website', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'wwwpage', $bib['type'] ); ?> value="wwwpage">
                            <?php _e( 'Webpage', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'video', $bib['type'] ); ?> value="video">
                            <?php _e( 'Video', 'bpsp' ); ?>
                        </option>
                        <option <?php selected( 'audio', $bib['type'] ); ?> value="audio">
                            <?php _e( 'Audio', 'bpsp' ); ?>
                        </option>
                    </select>
                </div>
            </li>
        </ul>
    </div>
    <div class="courseware-content-wrapper" >
        <div id="bibliography-form-content">
            <h4 class="meta"><?php _e( 'Author(s)', 'bpsp' ); ?></h4>
            <div class="courseware-form-section">
                <label for="bib[author_lname]"><?php _e( 'Author Last Name', 'bpsp' ); ?></label>
                    <input type="text" name="bib[author_lname]" title="<?php _e( 'Author Last Name', 'bpsp' ); ?>" value="<?php echo $bib['author'] ? $bib['author'] : $bib['author_lname'] ; ?>" />
                <label for="bib[author_fname]"><?php _e( 'Author First Name', 'bpsp' ); ?></label>
                    <input type="text" name="bib[author_fname]" title="<?php _e( 'Author First Name', 'bpsp' ); ?>" value="<?php echo $bib['author_fname']; ?>" />
                <label for="bib[author_lname2]"><?php _e( 'Author Two Last Name', 'bpsp' ); ?></label>
                    <input type="text" name="bib[author_lname2]" title="<?php _e( 'Author Two Last Name', 'bpsp' ); ?>" value="<?php echo $bib['author_lname2']; ?>" />
                <label for="bib[author_fname2]"><?php _e( 'Author Two First Name', 'bpsp' ); ?></label>
                    <input type="text" name="bib[author_fname2]" title="<?php _e( 'Author Two First Name', 'bpsp' ); ?>" value="<?php echo $bib['author_fname2']; ?>" />
            </div>
            <h4 class="meta"><?php _e( 'Publish Information', 'bpsp' ); ?></h4>
            <div class="courseware-form-section">
                <label for="bib[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                    <input type="text" name="bib[title]" title="<?php _e( 'Title', 'bpsp' ); ?>" value="<?php echo $bib['title']; ?>" />
                <label for="bib[stitle]"><?php _e( 'Short Title', 'bpsp' ); ?></label>
                    <input type="text" name="bib[stitle]" title="<?php _e( 'Short Title', 'bpsp' ); ?>" value="<?php echo $bib['stitle']; ?>" />
                <label for="bib[jtitle]"><?php _e( 'Journal Title', 'bpsp' ); ?></label>
                    <input type="text" name="bib[jtitle]" title="<?php _e( 'Journal Title', 'bpsp' ); ?>" value="<?php echo $bib['jtitle']; ?>" />
                <label for="bib[vtitle]"><?php _e( 'Volume Title', 'bpsp' ); ?></label>
                    <input type="text" name="bib[vtitle]" title="<?php _e( 'Volume Title', 'bpsp' ); ?>" value="<?php echo $bib['vtitle']; ?>" />
                <label for="bib[veditors]"><?php _e( 'Volume Editor(s)', 'bpsp' ); ?></label>
                    <input type="text" name="bib[veditors]" title="<?php _e( 'Volume Editor(s)', 'bpsp' ); ?>" value="<?php echo $bib['veditors']; ?>" />
                <label for="bib[pubplace]"><?php _e( 'Publication Place', 'bpsp' ); ?></label>
                    <input type="text" name="bib[pubplace]" title="<?php _e( 'Publication Place', 'bpsp' ); ?>" value="<?php echo $bib['pubplace']; ?>" />
                <label for="bib[pub]"><?php _e( 'Publisher', 'bpsp' ); ?></label>
                    <input type="text" name="bib[pub]" title="<?php _e( 'Publisher', 'bpsp' ); ?>" value="<?php echo $bib['pub']; ?>" />
                <label for="bib[wwwtitle]"><?php _e( 'Website Title', 'bpsp' ); ?></label>
                    <input type="text" name="bib[wwwtitle]" title="<?php _e( 'Website Title', 'bpsp' ); ?>" value="<?php echo $bib['wwwtitle']; ?>" />
            </div>
            <h4 class="meta"><?php _e( 'Additional Information', 'bpsp' ); ?></h4>
            <div class="courseware-form-section">
                <label for="bib[pubdate]"><?php _e( 'Date Published', 'bpsp' ); ?></label>
                    <input type="text" name="bib[pubdate]" title="<?php _e( 'Date Published', 'bpsp' ); ?>" value="<?php echo $bib['pubdate']; ?>" />
                <label for="bib[accdate]"><?php _e( 'Date Accessed', 'bpsp' ); ?></label>
                    <input type="text" name="bib[accdate]" title="<?php _e( 'Date Accessed', 'bpsp' ); ?>" value="<?php echo $bib['accdate']; ?>" />
                <label for="bib[url]"><?php _e( 'URL', 'bpsp' ); ?></label>
                    <input type="text" name="bib[url]" title="<?php _e( 'URL', 'bpsp' ); ?>" value="<?php echo $bib['url']; ?>" />
                <label for="bib[vol]"><?php _e( 'Volume', 'bpsp' ); ?></label>
                    <input type="text" name="bib[vol]" title="<?php _e( 'Volume', 'bpsp' ); ?>" value="<?php echo $bib['vol']; ?>" />
                <label for="bib[issue]"><?php _e( 'Issue', 'bpsp' ); ?></label>
                    <input type="text" name="bib[issue]" title="<?php _e( 'Issue', 'bpsp' ); ?>" value="<?php echo $bib['issue']; ?>" />
                <label for="bib[pages]"><?php _e( 'Pages', 'bpsp' ); ?></label>
                    <input type="text" name="bib[pages]" title="<?php _e( 'Pages', 'bpsp' ); ?>" value="<?php echo $bib['pages']; ?>" />
                <label for="bib[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                    <textarea name="bib[desc]" cols="60" rows="6"  title="<?php _e( 'Description', 'bpsp' ); ?>"><?php echo $bib['desc'] ? $bib['desc'] : $bib['citation']; ?></textarea>
            </div>
            <?php echo $bibs_nonce; ?>
            <input type="submit" value="<?php _e( 'Update', 'bpsp' ); ?>" />
            <?php if( isset( $back_uri ) ): ?>
                <div class="alignright submits">
                    <a href="<?php echo $back_uri; ?>" class="safe action"><?php _e( 'Go back', 'bpsp' ); ?></a>
                </div>
            <?php endif; ?>
        </div>
    </form>
</div>