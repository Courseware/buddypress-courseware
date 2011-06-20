<?php if ( !empty( $lectures ) ): ?>
    <div id="lectures-tree">
        <input type="text" id="lectures-tree-search-text" class="hide-if-no-js" />
        <input type="submit" id="lectures-tree-search-submit" class="hide-if-no-js" value="<?php _e( "Search Lectures", "bpsp" ); ?>" />
        <a href="#lectures-tree-toggle" id="lectures-tree-toggle" class="button" rel="collapse">
            <span class="collapse-all"><?php _e( "Collapse All", "bpsp" ); ?></span>
            <span class="expand-all" style="display: none;"><?php _e( "Expand All", "bpsp" ); ?></span>
        </a>
        
        <div id="lectures-tree-container"></div>
        <ul id="lectures-tree-data" class="no-js">
            <?php echo $lectures; ?>
        </ul>
    </div>
<?php endif; ?>