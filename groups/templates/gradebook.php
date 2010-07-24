<?php if( !isset( $students ) || empty( $students ) ) : ?>
    <div id="message" class="info">
        <p><?php _e( 'There are no students in this class yet.', 'bpsp' ); ?></p>
    </div>
<?php endif; ?>
<div id="courseware-gradebook">
    <h4><?php
        _e( 'Gradebook for: ', 'bpsp' );
        echo $assignment->post_title; ?>
    </h4>
    <?php
    if( !empty( $students ) ):
    ?>
      <ul class="gradebook-actions">
            <li class="import-gradebook-form">
                <h4><?php _e( 'Import Gradebook from CSV file', 'bpsp' ); ?></h4>
                <form action="<?php echo $gradebook_permalink . '/import'; ?>" method="post" enctype="multipart/form-data">
                    <input type="file" name="csv_filename" />
                    <input type="submit" value="Import" />
                    <?php echo $import_gradebook_nonce; ?>
                </form>
            </li>
            <li class="clear-gradebook-link">
                <a href="<?php echo $clear_gradebook_permalink; ?>">
                    <?php _e( 'Clear Gradebook', 'bpsp' ); ?>
                </a>
            </li>
        </ul>
        <form method="post" action="<?php echo $gradebook_permalink; ?>">
        <table>
            <thead>
                <tr>
                    <th><?php _e( 'Student', 'bpsp' ); ?></th>
                    <th><?php _e( 'Grade value', 'bpsp' ); ?></th>
                    <th><?php _e( 'Grade format', 'bpsp' ); ?></th>
                    <th><?php _e( 'Private comment', 'bpsp' ); ?></th>
                    <th><?php _e( 'Public comment', 'bpsp' ); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ( $students as $student ): ?>
                <tr>
                    <td class="student_info">
                        <span class="student_avatar">
                            <?php echo bp_core_fetch_avatar(
                                array( 'item_id' => $student->user_id,
                                        'type' => 'thumb',
                                        'email' => $student->user_email )
                            ); ?>
                        </span>
                        <span class="student_name">
                            <?php echo bp_core_get_userlink( $student->user_id ); ?>
                        </span>
                        <input type="hidden" name="grade[<?php echo $student->user_id ?>][uid]" value="<?php echo $student->user_id ?>" />
                    </td>
                    <td class="grade_value">
                        <input type="text"
                            name="grade[<?php echo $student->user_id ?>][value]"
                            value="<?php echo $grades[$student->user_id]['value'] ? $grades[$student->user_id]['value'] : '' ?>" />
                    </td>
                    <td class="grade_format">
                        <select name="grade[<?php echo $student->user_id ?>][format]">
                            <option value="numeric" <?php echo ( 'numeric' == $grades[$student->user_id]['format'] ) ? 'selected' : '' ?> >
                                <?php _e( 'Numeric', 'bpsp' ); ?>
                            </option>
                            <option value="percentage" <?php echo ( 'percentage' == $grades[$student->user_id]['format'] ) ? 'selected' : '' ?> >
                                <?php _e( 'Percentage', 'bpsp' ); ?>
                            </option>
                            <option value="letter" <?php echo ( 'letter' == $grades[$student->user_id]['format'] ) ? 'selected' : '' ?> >
                                <?php _e( 'Letter', 'bpsp' ); ?>
                            </option>
                        </select>
                    </td>
                    <td class="private_comment">
                        <textarea cols="20" rows="5" name="grade[<?php echo $student->user_id ?>][prv_comment]"><?php
                            echo $grades[$student->user_id]['prv_comment'] ? $grades[$student->user_id]['prv_comment'] : '' ;
                        ?></textarea>
                    </td>
                    <td class="public_comment">
                        <textarea cols="20" rows="5" name="grade[<?php echo $student->user_id ?>][pub_comment]"><?php
                            echo $grades[$student->user_id]['pub_comment'] ? $grades[$student->user_id]['pub_comment'] : '' ;
                        ?></textarea>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php echo $nonce; ?>
        <input type="submit" name="grade[<?php echo $assignment->ID ?>][submit]" value="<?php _e( 'Save grades', 'bpsp' ); ?>" />
        <a href="<?php echo $assignment_permalink; ?>"><?php _e( 'Go back', 'bpsp' ); ?></a>
        </form>
    <?php endif; ?>
</div>