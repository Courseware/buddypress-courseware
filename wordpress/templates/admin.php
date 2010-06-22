<?php if($flash) { ?><div id="message" class="updated fade"><p><strong><?=$flash?></strong></p></div><?php } ?>
<div id="icon-tools" class="icon32"><br /></div>
<div class="wrap">
    <h2><?php _e('BuddyPress Courseware','bpsp')?></h2>
    <div id="poststuff" class="metabox-holder">
        <div class="postbox">
            <h3 class="hndle" ><?php _e('About','bpsp')?></h3>
            <div class="inside">
                <p>
                    <?php _e('In short, this feature was added due to differences
                    between how (for example) European and US academic institutions
                    are managing curriculum and student participation along
                    educational process.','bpsp'); ?>
                </p>
                <p>
                    <?php _e( 'In Europe, is more common the concept of classes,
                    where students are gourped as <strong><em>students enrolled in
                    a class</em></strong>. This means that BuddyPress groups will be
                    treaten as classes. The main workflow difference is
                    that teachers will be able to manage a set of courses
                    within such a class.', 'bpsp' ); ?>
                </p>
                <p>
                    <?php _e( 'In United States, is more common the concept of courses,
                    where students are gourped as <strong><em>students enrolled on
                    a course</em></strong>. This means that BuddyPress groups will be
                    treaten as courses. The main workflow difference is
                    that teachers will be able to manage one course
                    per BuddyPress group and users will subscrie to each group.', 'bpsp' ); ?>
                </p>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e('Select the default behaviour of Courseware:','bpsp')?></h3>
            <div class="inside">
                <form action="" method="post" >
                    <p>
                        <input type="radio" name="bpsp_curriculum" value="eu" <?php echo $eu? 'checked' : ''; ?> />
                        <strong><?php _e( 'European style', 'bpsp' ); ?></strong> &mdash;
                        <?php _e( 'Use this setting if a single roster of students
                        is shared between multiple courses.', 'bpsp' ); ?>
                    </p>
                    <p>
                        <input type="radio" name="bpsp_curriculum" value="us" <?php echo $us? 'checked' : ''; ?> />
                        <strong><?php _e( 'US style', 'bpsp' ); ?></strong> &mdash;
                        <?php _e( 'Use this setting if each course will have its own roster.', 'bpsp' ); ?>
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?=__('Save Changes','bpsp')?>" />
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>