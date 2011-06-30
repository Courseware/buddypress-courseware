<form action="<?php echo $current_option; ?>" method="post" class="standard-form" id="new-assignment-form">
    <div id="new-schedule-meta" class="courseware-sidebar">
        <h4 class="meta"><?php _e( 'Schedule Meta', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="new-schedule-course">
                <label for="schedule-lecture"><?php _e( 'Linked Lecture', 'bpsp' ); ?></label>
                <select id="schedule-lecture" name="schedule[lecture_id]">
                    <option value=""><?php _e( 'Select Lecture', 'bpsp' ) ?></option>
                    <?php
                        echo walk_page_dropdown_tree( $lectures, 0,
                            array(
                                'echo' => 1,
                                'depth' => 0,
                                'child_of' => 0,
                                'selected' => 0,
                                'post_type' => 'lecture',
                                'sort_column'=> 'menu_order, post_title'
                            )
                        );
                    ?>
                </select>
            </li>
            <li id="new-schedule-start-date">
                <label for="schedule-startdate"><?php _e( 'Start date', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-startdate" name="schedule[start_date]" title="<?php _e( 'Start date', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-end-date">
                <label for="schedule-enddate"><?php _e( 'End date', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-enddate" name="schedule[end_date]" title="<?php _e( 'End date', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-location">
                <label for="schedule-location"><?php _e( 'Location', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-location" name="schedule[location]" title="<?php _e( 'Location', 'bpsp' ); ?>" />
            </li>
            <li id="new-schedule-repetition">
                <label for="schedule-repetition"><?php _e( 'Repeats', 'bpsp' ); ?></label>
                    <select id="schedule-repetition" name="schedule[repetition]">
                        <option value=""><?php _e( 'No Repetition', 'bpsp' ); ?></option>
                        <option value="day"><?php _e( 'Daily', 'bpsp' ); ?></option>
                        <option value="week"><?php _e( 'Weekly', 'bpsp' ); ?></option>
                        <option value="month"><?php _e( 'Monthly', 'bpsp' ); ?></option>
                    </select>
                <label for="repetition_times"><?php _e( 'for', 'bpsp' ); ?></label>
                    <input type="text" id="repetition_times" name="schedule[repetition_times]" title="0" />
                    <?php _e( 'times', 'bpsp' ); ?>.
            </li>
            <li id="new-schedule-content-options">
                <input type="hidden" name="schedule[object]" value="group"/>
                <input type="hidden" name="schedule[group_id]" value="<?php echo $group_id; ?>" />
                <input type="hidden" name="schedule[course_id]" value="<?php echo $course_id; ?>" />
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="new-schedule-content-submit">
                    <input type="submit" name="schedule[submit]" id="new-schedule-submit" value="<?php _e( 'Publish schedule', 'bpsp' ); ?>">
                </div>
            </li>
        </ul>
    </div>
    <div id="new-schedule-content" class="courseware-content-wrapper">
        <div id="schedule-title">
            <input type="text" name="schedule[title]" class="long" title="<?php _e( 'Title', 'bpsp' ); ?>" value="<?php echo $schedule->title ? $schedule->title : '' ; ?>" />
        </div>
        <div id="new-schedule-content-description">
            <textarea class="long" id="schedule-description" name="schedule[desc]"><?php
                echo $schedule->desc;
            ?></textarea>
        </div>
    </div>
</form>