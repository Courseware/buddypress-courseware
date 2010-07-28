<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-assignment-form">
    <h5><?php _e( 'Add a new schedule', 'bpsp' ); ?></h5>
    <div id="new-schedule-content">
        <div id="new-schedule-content-description">
            <label for="schedule[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea id="schedule-description" name="schedule[desc]"></textarea>
        </div>
        <div id="new-schedule-course">
            <label for="schedule[course_id]"><?php _e( 'Include a course', 'bpsp' ); ?></label>
                <select name="schedule[course_id]">
                    <option value=""><?php _e( 'Select a course', 'bpsp' ) ?></option>
                    <?php foreach( $courses as $c ): ?>
                        <option value="<?php echo $c->ID; ?>"><?php echo $c->post_title; ?></option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div id="new-schedule-start-date">
            <label for="schedule[start_date]"><?php _e( 'Start date', 'bpsp' ); ?></label>
                <input type="text" name="schedule[start_date]" />
        </div>
        <div id="new-schedule-end-date">
            <label for="schedule[end_date]"><?php _e( 'End date', 'bpsp' ); ?></label>
                <input type="text" name="schedule[end_date]" />
        </div>
        <div id="new-schedule-location">
            <label for="schedule[location]"><?php _e( 'Location', 'bpsp' ); ?></label>
                <input type="text" name="schedule[location]" />
        </div>
        <div id="new-schedule-repetition">
            <label for="schedule[repetition]"><?php _e( 'Repeats', 'bpsp' ); ?></label>
                <select name="schedule[repetition]">
                    <option value=""><?php _e( 'Does not repeat', 'bpsp' ); ?></option>
                    <option value="day"><?php _e( 'Daily', 'bpsp' ); ?></option>
                    <option value="week"><?php _e( 'Weekly', 'bpsp' ); ?></option>
                    <option value="month"><?php _e( 'Monthly', 'bpsp' ); ?></option>
                </select>
            <label for="schedule[repetition_times]"><?php _e( 'for', 'bpsp' ); ?></label>
                <input type="text" name="schedule[repetition_times]" />
                <?php _e( 'times', 'bpsp' ); ?>.
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