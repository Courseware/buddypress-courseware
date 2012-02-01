<form action="<?php echo $schedule_edit_uri; ?>" method="post" class="standard-form schedule-form" id="edit-schedule-form">
    <div id="schedule-meta" class="courseware-sidebar">
        <h4 class="meta schedules"><span class="icon"></span><?php _e( 'Schedule Meta', 'bpsp' ); ?></h4>
        <ul class="courseware-meta">
            <li id="edit-schedule-lecture">
                <label for="schedule-lecture"><?php _e( 'Linked Lecture', 'bpsp' ); ?></label>
                <select id="schedule-lecture" name="schedule[lecture_id]">
                    <option value=""><?php _e( 'Select Lecture', 'bpsp' ) ?></option>
                    <?php
                        echo walk_page_dropdown_tree( $lectures, 0,
                            array(
                                'echo' => 1,
                                'depth' => 0,
                                'child_of' => 0,
                                'selected' => $lecture_id,
                                'post_type' => 'lecture',
                                'sort_column'=> 'menu_order, post_title'
                            )
                        );
                    ?>
                </select>
            </li>
            <li id="schedule-start-date">
                <label for="schedule-startdate"><?php _e( 'Start date', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-startdate" name="schedule[start_date]" title="<?php _e( 'Start date', 'bpsp' ); ?>" value="<?php echo $schedule->start_date; ?>" />
            </li>
            <li id="schedule-end-date">
                <label for="schedule-enddate"><?php _e( 'End date', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-enddate" name="schedule[end_date]" title="<?php _e( 'End date', 'bpsp' ); ?>" value="<?php echo $schedule->end_date; ?>" />
            </li>
            <li id="schedule-location">
                <label for="schedule-location"><?php _e( 'Location', 'bpsp' ); ?></label>
                    <input type="text" id="schedule-location" name="schedule[location]" title="<?php _e( 'Location', 'bpsp' ); ?>" value="<?php echo $schedule->location ? $schedule->location : '' ; ?>" />
            </li>
            <div id="schedule-content-options">
                <input type="hidden" name="schedule[object]" value="group"/>
                <input type="hidden" name="schedule[group_id]" value="<?php echo $group_id; ?>" />
                <input type="hidden" name="schedule[course_id]" value="<?php echo $course_id; ?>" />
                <?php echo $nonce ? $nonce: ''; ?>
                <div id="chedule-content-submit">
                    <input type="submit" name="schedule[submit]" id="schedule-submit" value="<?php _e( 'Update schedule', 'bpsp' ); ?>">
                    <div class="submits alignright">
                        <a href="<?php echo $schedule_permalink; ?>" ><?php _e( 'Cancel/Go back', 'bpsp' ); ?></a> |
                        <a href="<?php echo $delete_nonce; ?>" class="alert"><?php _e( 'Delete schedule', 'bpsp' ); ?></a>
                    </div>
                </div>
            </div>
        </ul>
    </div>
    <div id="schedule-content" class="courseware-content-wrapper">
        <div id="schedule-title">
            <input type="text" name="schedule[title]" class="long" title="<?php _e( 'Title', 'bpsp' ); ?>" value="<?php echo $schedule->post_title ? $schedule->post_title : '' ; ?>" />
        </div>
        <div id="schedule-content-description">
            <label for="schedule[desc]"><?php _e( 'Description', 'bpsp' ); ?></label>
                <textarea id="schedule-description" class="long" name="schedule[desc]"><?php echo $schedule->post_content; ?></textarea>
        </div>
    </div>
</form>
