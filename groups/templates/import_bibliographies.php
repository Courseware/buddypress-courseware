<div id="courseware-import-bibliography" >
    <div id="courseware-import-bibliography-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Import Your Bibliography', 'bpsp' );  ?></h4>
        <ul class="courseware-meta">
            <li>
                <p><?php echo _e( 'Paste your BibTeX contents in the textarea and submit <strong>Import</strong>.', 'bpsp'); ?></p>
            </li>
        </ul>
    </div>
    <form action="" method="post" class="standard-form" >
        <textarea name="bib[source]" cols="100" rows="30" ></textarea>
        <input type="submit" value="<?php _e( 'Import', 'bpsp' ); ?>" />
    </form>
</div>