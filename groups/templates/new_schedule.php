<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-assignment-form">
    <h4 class="meta"><?php _e( 'New Schedule', 'bpsp' ); ?></h4>
    <div id="new-schedule-meta" class="courseware-sidebar">
        <ul class="courseware-meta">
            <li id="new-schedule-course">
                <label for="schedule[course_id]"><?php _e( 'Include a course', 'bpsp' ); ?></label>
                    <select name="schedule[course_id]">
                        <option value=""><?php _e( 'Select Course', 'bpsp' ) ?></option>
                        <?php foreach( $courses as $c ): ?>
                            <option value="<?php echo $c->ID; ?>"><?php echo $c->post_title; ?></option>
                        <?php endforeach; ?>
                    </select>
            </li>
            <li id="new-schedule-start-date">
                <label for="schedule[start_date]"><?php _e( 'Start date', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[start_date]" title="<?php _e( 'Start date', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-end-date">
                <label for="schedule[end_date]"><?php _e( 'End date', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[end_date]" title="<?php _e( 'End date', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-location">
                <label for="schedule[location]"><?php _e( 'Location', 'bpsp' ); ?></label>
                    <input type="text" name="schedule[location]" title="<?php _e( 'Location', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-repetition">
                <label for="schedule[repetition]"><?php _e( 'Repeats', 'bpsp' ); ?></label>
                    <select name="schedule[repetition]">
                        <option value=""><?php _e( 'No Repetition', 'bpsp' ); ?></option>
                        <option value="day"><?php _e( 'Daily', 'bpsp' ); ?></option>
                        <option value="week"><?php _e( 'Weekly', 'bpsp' ); ?></option>
                        <option value="month"><?php _e( 'Monthly', 'bpsp' ); ?></option>
                    </select>
                <label for="schedule[repetition_times]"><?php _e( 'for', 'bpsp' ); ?></label>
                    <input type="text" id="repetition_times" name="schedule[repetition_times]" title="<?php _e( 'for X', 'bpsp' ); ?>" />
                    <?php _e( 'times', 'bpsp' ); ?>.
            </li>
        </ul>
    </div>
    <div id="new-schedule-content" class="courseware-content-wrapper">
        <div id="schedule-title">
            <label for="schedule[title]"><?php _e( 'Title', 'bpsp' ); ?></label>
                <input type="text" name="schedule[title]" class="long" title="<?php _e( 'Title', 'bpsp' ); ?>" value="<?php echo $schedule->post_title ? $schedule->title : '' ; ?>" />
        </div>
        <div id="new-schedule-content-description">
            <label for="schedule[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea class="long" id="schedule-description" name="schedule[desc]"></textarea>
        </div>
        <div id="new-schedule-content-options">
            <input type="hidden" id="new-schedule-post-object" name="schedule[object]" value="group"/>
            <input type="hidden" id="new-schedule-post-in" name="schedule[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="new-schedule-content-submit">
                <input type="submit" name="schedule[submit]" id="new-schedule-submit" value="<?php _e( 'Add a new schedule', 'bpsp' ); ?>">
            </div>
        </div>
    </div>
</form>