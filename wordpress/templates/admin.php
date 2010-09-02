<?php if( !empty( $flash ) ) : ?>
    <div id="message" class="updated fade">
        <?php foreach( $flash as $f ): ?>
            <p><strong><?php echo $f; ?></strong></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<div id="icon-tools" class="icon32"><br /></div>
<div class="wrap">
    <h2><?php _e('BuddyPress Courseware','bpsp')?></h2>
    <div id="poststuff" class="metabox-holder">
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Global Settings', 'bpsp' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Enabling this, will enable Courseware component for
                    every existing group.', 'bpsp' )?>
                </p>
                <p>
                    <?php _e( 'A per group option is available in group Admin screen.', 'bpsp' )?>
                </p>
                <form action="" method="post" >
                    <p>
                        <label>
                            <input type="checkbox" name="bpsp_global_status" <?php checked( !empty( $bpsp_global_status ) ); ?> />
                            <strong><?php _e( 'Enable Courseware globally', 'bpsp' ); ?></strong>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?php _e( 'Save Changes','bpsp' ); ?>" />
                        <input type="hidden" name="bpsp_global_status_check" value="true" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Default behaviour for Courseware', 'bpsp' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'In short, this feature was added due to differences
                    between how (for example) European and US academic institutions
                    are managing curriculum and student participation along
                    educational process.', 'bpsp' ); ?>
                </p>
                <p>
                    <?php _e( 'In Europe, is more common the concept of classes,
                    where students are grouped as <strong><em>students enrolled in
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
                <form action="" method="post" >
                    <label>
                        <p>
                            <input type="radio" name="bpsp_curriculum" value="eu" <?php checked( !empty( $eu ) ); ?> />
                            <strong><?php _e( 'European style', 'bpsp' ); ?></strong> &mdash;
                            <?php _e( 'Use this setting if a single roster of students
                            is shared between multiple courses.', 'bpsp' ); ?>
                        </p>
                    </label>
                    <label>
                        <p>
                            <input type="radio" name="bpsp_curriculum" value="us" <?php checked( !empty( $us ) ); ?> />
                            <strong><?php _e( 'US style', 'bpsp' ); ?></strong> &mdash;
                            <?php _e( 'Use this setting if each course will have its own roster.', 'bpsp' ); ?>
                        </p>
                    </label>
                    <p>
                        <input type="submit" class="button" value="<?=__( 'Save Changes', 'bpsp' )?>" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Collaboration settings', 'bpsp' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Enabling this, will allow any teacher to contribute to
                    group Courseware by editing/adding new courses, schedules and assignments.', 'bpsp' )?>
                </p>
                <p>
                    <?php _e( 'By default only group admins can manage Courseware.', 'bpsp' )?>
                </p>
                <form action="" method="post" >
                    <p>
                        <label>
                            <input type="checkbox" name="bpsp_allow_only_admins" <?php checked( !empty( $bpsp_allow_only_admins ) ); ?> />
                            <strong><?php _e( 'Allow any teacher to contribute to class Courseware content', 'bpsp' ); ?></strong>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?php _e( 'Save Changes','bpsp' ); ?>" />
                        <input type="hidden" name="bpsp_allow_only_admins_check" value="true" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Gradebook default grade format', 'bpsp' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Courseware gradebook has support
                    for most common grading formats, setting a default one
                    will save some time when grading students.', 'bpsp' )?>
                </p>
                <form action="" method="post" >
                    <p>
                        <label for="bpsp_gradebook_format">
                            <strong><?php _e( 'Current gradebook format', 'bpsp' )?></strong>
                        </label>
                        <select name="bpsp_gradebook_format">
                            <option value="numeric" <?php selected( $bpsp_gradebook_format, 'numeric' ); ?> ><?php _e( 'Numeric', 'bpsp' )?></option>
                            <option value="letter" <?php selected( $bpsp_gradebook_format, 'letter' ); ?> ><?php _e( 'Letter', 'bpsp' )?></option>
                            <option value="percentage" <?php selected( $bpsp_gradebook_format, 'percentage' ); ?> ><?php _e( 'Percentage', 'bpsp' )?></option>
                        </select>
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?php _e( 'Save Changes','bpsp' ); ?>" />
                        <input type="hidden" name="bpsp_gradebook_format_check" value="true" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Webservices API Integration', 'bpsp' )?></h3>
            <div class="inside">
                 <p>
                    <?php _e( 'This will allow you to use webservices to query
                    books and articles easily.', 'bpsp' ); ?>
                </p>
                <form action="" method="post" >
                    <p>
                        <label for="wordcat_key">
                            <strong><?php _e( 'WorldCat Webservices Key','bpsp' ); ?></strong> &mdash;
                            <a href="http://www.worldcat.org/wcpa/content/affiliate/default.jsp">
                                <?php _e( 'Free registration for Basic API','bpsp' ); ?>
                            </a>
                        </label>
                    </p>
                    <p>
                        <input type="text" name="worldcat_key" style="width: 50%;" value="<?php echo $worldcat_key ? $worldcat_key : ''; ?>" />
                    </p>
                    <p>
                        <label for="isbndb_key">
                            <strong><?php _e( 'ISBNdb Webservices Key', 'bpsp' ); ?></strong> &mdash;
                            <a href="https://isbndb.com/account/create.html?">
                                <?php _e( 'Free Registration' , 'bpsp' ); ?>
                            </a>
                        </label>
                    </p>
                    <p>
                        <input type="text" name="isbndb_key" style="width: 50%;" value="<?php echo $isbndb_key ? $isbndb_key : ''; ?>" />
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?php _e( 'Save Changes','bpsp' ); ?>" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
        <div class="postbox">
            <h3 class="hndle" ><?php _e( 'Customization', 'bpsp' )?></h3>
            <div class="inside">
                <p>
                    <?php _e( 'Enabling this, will check if there\'s
                    a <code>courseware.css</code> file in your theme directory,
                    and will load it automatically.', 'bpsp' )?>
                </p>
                <p>
                    <?php _e( 'This will make Courseware use your CSS rules. File should exist physically!', 'bpsp' )?>
                </p>
                <form action="" method="post" >
                    <p>
                        <label>
                            <input type="checkbox" name="bpsp_load_css" <?php checked( !empty( $bpsp_load_css ) ); ?> />
                            <strong><?php _e( 'Load <code>courseware.css</code> from my theme directory.', 'bpsp' ); ?></strong>
                        </label>
                    </p>
                    <p>
                        <input type="submit" class="button" value="<?php _e( 'Save Changes','bpsp' ); ?>" />
                        <input type="hidden" name="bpsp_load_css_check" value="true" />
                        <?php echo $nonce; ?>
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>