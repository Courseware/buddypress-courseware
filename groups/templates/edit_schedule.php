<form action="<?php echo $schedule_edit_uri; ?>" method="post" class="standard-form" id="new-assignment-form">
    <h4 class="meta"><?php _e( 'Update schedule', 'bpsp' ); ?></h4>
    <div id="schedule-meta" class="courseware-sidebar">
        <ul class="courseware-meta">
            <li id="schedule-course">
                <label for="schedule[course_id]"><?php _e( 'Link to a Course', 'bpsp' ); ?></label>
                    <select name="schedule[course_id]">
                        <option value=""><?php _e( 'Select Course', 'bpsp' ) ?></option>
                        <?php foreach( $courses as $c ): ?>
                            <option value="<?php echo $c->ID; ?>" <?php selected($c->ID, $schedule->course->ID); ?> >
                                <?php echo $c->post_title; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
            </li>
            <li id="schedule-start-date">
                <label for="schedule[start_date]"><?php _e( 'Start date', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[start_date]" title="<?php _e( 'Start date', 'bpsp' ); ?>" value="<?php echo $schedule->start_date; ?>" />
            </li>
            <li id="schedule-end-date">
                <label for="schedule[end_date]"><?php _e( 'End date', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[end_date]" title="<?php _e( 'End date', 'bpsp' ); ?>" value="<?php echo $schedule->end_date; ?>" />
            </li>
            <li id="schedule-location">
                <label for="schedule[location]"><?php _e( 'Location', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[location]" title="<?php _e( 'Location', 'bpsp' ); ?>" value="<?php echo $schedule->location ? $schedule->location : '' ; ?>" />
            </li>
        </ul>
    </div>
    <div id="schedule-content" class="courseware-content-wrapper">
        <div id="schedule-title">
            <label for="schedule[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" name="schedule[title]" class="long" title="<?php _e( 'Title', 'bpsp' ); ?>" value="<?php echo $schedule->post_title ? $schedule->post_title : '' ; ?>" />
        </div>
        <div id="schedule-content-description">
            <label for="schedule[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea id="schedule-description" class="long" name="schedule[desc]"><?php echo $schedule->post_content; ?></textarea>
        </div>
        <div id="schedule-content-options">
            <input type="hidden" id="new-schedule-post-object" name="schedule[object]" value="group"/>
            <input type="hidden" id="new-schedule-post-in" name="schedule[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="chedule-content-submit">
                <input type="submit" name="schedule[submit]" id="schedule-submit" value="<?php _e( 'Update schedule', 'bpsp' ); ?>">
                <div class="submits alignright">
                    <a href="<?php echo $delete_nonce; ?>" class="action alert"><?php _e( 'Delete schedule', 'bpsp' ); ?></a>
                    <a href="<?php echo $schedule_permalink; ?>" class="action safe"><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
                </div>
            </div>
        </div>
    </div>
</form>