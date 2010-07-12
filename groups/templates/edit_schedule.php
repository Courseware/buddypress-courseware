<?php if( $message ): ?>
<div id="message" class="updated">
    <p><?php echo $message; ?></p>
</div>
<?php endif; ?>
<form action="<?php echo $schedule_edit_uri; ?>" method="post" id="new-assignment-form">
    <h5>
        <?php _e( 'Update schedule', 'bpsp' ); ?> |
        <a href="<?php echo $schedule_permalink; ?>"><?php _e( 'Preview schedule', 'bpsp' ); ?></a>
    </h5>
    <div id="schedule-content">
        <div id="schedule-content-description">
            <label for="schedule[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea id="schedule-description" name="schedule[desc]"><?php echo $schedule->post_title; ?></textarea>
        </div>
        <div id="schedule-course">
            <label for="schedule[course_id]"><?php _e( 'Include a course', 'bpsp' ); ?></label>
                <select name="schedule[course_id]">
                    <option value=""><?php _e( 'Select a course', 'bpsp' ) ?></option>
                    <?php foreach( $courses as $c ): ?>
                        <option value="<?php echo $c->ID; ?>" <?php echo ($c->ID == $schedule->course->ID) ? 'selected' : '';  ?> >
                            <?php echo $c->post_title; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
        </div>
        <div id="schedule-start-date">
            <label for="schedule[start_date]"><?php _e( 'Start date', 'bpsp' ); ?></label>
                <input type="text" name="schedule[start_date]" value="<?php echo $schedule->start_date; ?>" />
        </div>
        <div id="schedule-end-date">
            <label for="schedule[end_date]"><?php _e( 'End date', 'bpsp' ); ?></label>
                <input type="text" name="schedule[end_date]" value="<?php echo $schedule->end_date; ?>" />
        </div>
        <div id="schedule-location">
            <label for="schedule[location]"><?php _e( 'Location', 'bpsp' ); ?></label>
                <input type="text" name="schedule[location]" value="<?php echo $schedule->location ? $schedule->location : '' ; ?>" />
        </div>
        <div id="schedule-content-options">
            <input type="hidden" id="new-schedule-post-object" name="schedule[object]" value="group"/>
            <input type="hidden" id="new-schedule-post-in" name="schedule[group_id]" value="<?php echo $group_id; ?>">
            <?php echo $nonce ? $nonce: ''; ?>
            <div id="chedule-content-submit">
                <input type="submit" name="schedule[submit]" id="schedule-submit" value="<?php _e( 'Update schedule', 'bpsp' ); ?>">
                <a href="<?php echo $delete_nonce; ?>"><?php _e( 'Delete schedule', 'bpsp' ); ?></a>
            </div>
        </div>
    </div>
</form>