<?php if( !isset( $students ) || empty( $students ) ) : ?>
    <div id="message" class="info">
        <p><?php _e( 'There are no students in this class yet.', 'bpsp' ); ?></p>
    </div>
    <h5>
        <a href="<?php echo $assignment_permalink; ?>"> <?php _e( 'Cancel/Go back', 'bpsp' ); ?></a>
    </h5>
<?php else: ?>
<div id="courseware-gradebook">
    <a href="#import-gradebook-form" class="action alignright">
        <?php _e( 'Import from CSV', 'bpsp' ); ?>
    </a>
    <h4 class="meta"><?php
        _e( 'Gradebook for: ', 'bpsp' );
        echo get_the_title( $assignment->ID ); ?>
    </h4>
    <?php if( !empty( $students ) ): ?>
        <div class="import-gradebook-form">
            <div id="message" class="info">
                <p>
                    <strong><?php _e( 'About CSV source file format', 'bpsp' ); ?></strong>
                    <br/>
                    <?php _e( 'Here is an example of such a file with dummy data for user test.', 'bpsp' ); ?>
                    <br/>
                    <?php _e( 'It\'s important to preserve the first line!', 'bpsp' ); ?>
                </p>
            </div>
            <form action="<?php echo $gradebook_permalink . '/import'; ?>" method="post" class="standard-form" enctype="multipart/form-data">
                <textarea disabled="disabled"><?php _e( "uid,value,format,prv_comment,pub_comment
test,10,numeric,\"Private comment text\",\"Public comment text\"", 'bpsp' ); ?></textarea>
                <p>
                    <?php _e( 'Upload your file:', 'bpsp' ); ?>
                    <input type="file" name="csv_filename" />
                    <br />
                    <input type="submit" value="Import" />
                    <?php echo $import_gradebook_nonce; ?>
                </p>
            </form>
        </div>
        <div class="clearall fat"></div>
        <form method="post" class="standard-form" action="<?php echo $gradebook_permalink; ?>">
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
            <?php foreach ( $students as $student ):
                if( empty( $grades[$student->user_id]['format'] ) )
                    $grades[$student->user_id]['format'] = $bpsp_gradebook_format;
            ?>
                <tr>
                    <td class="student_info">
                        <span class="student_avatar">
                            <?php echo bp_core_fetch_avatar(
                                array( 'item_id' => $student->user_id,
                                        'type' => 'thumb',
                                        'email' => $student->user_email,
                                        'class' => 'alignleft' )
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
                            <option value="numeric" <?php selected( $grades[$student->user_id]['format'], 'numeric' ); ?> >
                                <?php _e( 'Numeric', 'bpsp' ); ?>
                            </option>
                            <option value="percentage" <?php selected( $grades[$student->user_id]['format'], 'percentage' ); ?> >
                                <?php _e( 'Percentage', 'bpsp' ); ?>
                            </option>
                            <option value="letter" <?php selected( $grades[$student->user_id]['format'], 'letter' ); ?> >
                                <?php _e( 'Letter', 'bpsp' ); ?>
                            </option>
                        </select>
                    </td>
                    <td class="private_comment">
                        <textarea name="grade[<?php echo $student->user_id ?>][prv_comment]"><?php
                            echo $grades[$student->user_id]['prv_comment'] ? $grades[$student->user_id]['prv_comment'] : '' ;
                        ?></textarea>
                    </td>
                    <td class="public_comment">
                        <textarea name="grade[<?php echo $student->user_id ?>][pub_comment]"><?php
                            echo $grades[$student->user_id]['pub_comment'] ? $grades[$student->user_id]['pub_comment'] : '' ;
                        ?></textarea>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="gradebook-actions" style="clear:both">
            <?php echo $nonce; ?>
            <input type="submit" name="grade[<?php echo $assignment->ID ?>][submit]" value="<?php _e( 'Save grades', 'bpsp' ); ?>" />
            <div class="submits alignright">
                <a href="<?php echo $assignment_permalink; ?>"> <?php _e( 'Cancel/Go back', 'bpsp' ); ?></a> |
                <a href="<?php echo $clear_gradebook_permalink; ?>" class="alert"><?php _e( 'Clear Gradebook', 'bpsp' ); ?></a>
            </div>
        </div>
        </form>
    <?php endif; ?>
</div>
<?php endif; ?>