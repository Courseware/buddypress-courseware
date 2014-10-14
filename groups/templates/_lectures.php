<!-- groups/templates/_lectures.php -->
<?php if ( !empty( $lectures ) ): ?>
    <div id="lectures-tree">
        <h4 class="lectures">
            <span class="icon"></span>
            <?php _e( 'Course lectures', 'bpsp'); ?>
        </h4>
        <div id="lectures-search">
            <input type="text" id="lectures-tree-search-text" class="hide-if-no-js" />
            <input type="submit" id="lectures-tree-search-submit" class="hide-if-no-js" value="<?php _e( "Search Lectures", "bpsp" ); ?>" />
            <a href="#lectures-tree-toggle" id="lectures-tree-toggle" class="action" rel="collapse" name="Expand All"><?php _e( "Collapse All", "bpsp" ); ?></a>
        </div>
        
        <div id="lectures-tree-container"></div>
        <ul id="lectures-tree-data" class="no-js">
            <?php echo $lectures; ?>
        </ul>
    </div>
<?php endif; ?>