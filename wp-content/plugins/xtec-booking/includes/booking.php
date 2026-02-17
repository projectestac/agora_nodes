<?php

function xtec_add_metabox_booking(): void
{
    // DATETIME PICKER
    wp_register_script('datetime-js', plugins_url() . '/xtec-booking/includes/vendor/datetimepicker/jquery.js', ['jquery'], '1.1', true);
    wp_enqueue_script('datetime-js');
    wp_register_script('datetimepicker-js', plugins_url() . '/xtec-booking/includes/vendor/datetimepicker/build/jquery.datetimepicker.full.min.js', ['jquery'], '1.1', true);
    wp_enqueue_script('datetimepicker-js');
    wp_enqueue_style('datetime-css', plugins_url() . '/xtec-booking/includes/vendor/datetimepicker/jquery.datetimepicker.css');

    $metabox = [
            'id' => 'xtec_booking_parameters',
            'title' => __('Parametes to booking', 'xtec-booking'),
            'callback' => 'xtec_add_metabox_parameters_booking',
            'page' => 'calendar_booking',
            'context' => 'normal',
            'priority' => 'high',
    ];

    $metabox_calendar = [
            'id' => 'xtec_booking_calendar',
            'title' => __('Booking calendar', 'xtec-booking'),
            'callback' => 'xtec_add_metabox_calendar_booking',
            'page' => 'calendar_booking',
            'context' => 'side',
            'priority' => 'low',
    ];

    $metabox_resource = [
            'id' => 'xtec_booking_info_resource',
            'title' => __('Resource information', 'xtec-booking'),
            'callback' => 'xtec_add_metabox_resource_information',
            'page' => 'calendar_booking',
            'context' => 'side',
            'priority' => 'low',
    ];

    add_meta_box($metabox['id'], $metabox['title'], $metabox['callback'], $metabox['page'], $metabox['context'], $metabox['priority']);
    add_meta_box($metabox_resource['id'], $metabox_resource['title'], $metabox_resource['callback'], $metabox_resource['page'], $metabox_resource['context'], $metabox_resource['priority']);
    add_meta_box($metabox_calendar['id'], $metabox_calendar['title'], $metabox_calendar['callback'], $metabox_calendar['page'], $metabox_calendar['context'], $metabox_calendar['priority']);
}

function xtec_add_metabox_parameters_booking($post): void
{
    $data = xtec_get_resources();
    $data['parameters'] = get_post_meta($post->ID);
    $user = wp_get_current_user();

    xtec_booking_maybe_show_notice($data['results'], $user);

    if (!empty($data['parameters'])) {
        $xtec_booking_data = unserialize($data['parameters']['_xtec-booking-data'][0], ['allowed_classes' => false]);
        $xtec_booking_data_start_date = $xtec_booking_data['_xtec-booking-start-date'];
        $xtec_booking_data_start_time = $xtec_booking_data['_xtec-booking-start-time'];
        $xtec_booking_data_finish_date = $xtec_booking_data['_xtec-booking-finish-date'];
        $xtec_booking_data_finish_time = $xtec_booking_data['_xtec-booking-finish-time'];
    } else {
        $xtec_booking_data = [];
        $xtec_booking_data_start_date = '';
        $xtec_booking_data_start_time = '';
        $xtec_booking_data_finish_date = '';
        $xtec_booking_data_finish_time = '';
    }

    xtec_add_metabox_parameters_booking_show_common_fields($data, $xtec_booking_data, $xtec_booking_data_start_date,
            $xtec_booking_data_start_time, $xtec_booking_data_finish_date, $xtec_booking_data_finish_time);

    xtec_add_metabox_parameters_booking_show_days_fields($data, $xtec_booking_data, $xtec_booking_data_start_date,
            $xtec_booking_data_finish_date);
}

function xtec_add_metabox_parameters_booking_show_common_fields(
        array $data, mixed $xtec_booking_data, mixed $xtec_booking_data_start_date,
        mixed $xtec_booking_data_start_time, mixed $xtec_booking_data_finish_date,
        mixed $xtec_booking_data_finish_time): void
{
 ?>
    <table class="form-table">
        <!-- The table head is added to meet code quality standards -->
        <thead style="display: none">
            <tr><th colspan="5"></th></tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="5">
                <span class="description <?php if (count($data['results']) === 0) { echo 'xtec-warning'; } ?>">
                    <?php
                        if (count($data['results']) > 0) {
                            _e('Select resource or classroom', 'xtec-booking');
                        } else {
                            _e('Not allow any resources to select.', 'xtec-booking');
                        }
                    ?>
                </span>
                <br>
                <select name="_xtec-booking-resource" class="xtec-booking-input" required>
                    <option value=""></option>
                    <?php
                    if (count($data['results']) > 0) {
                        foreach ($data['results'] as $resources) {
                            ?>
                            <option value="<?php echo $resources->ID; ?>"><?php echo $resources->post_title; ?></option>
                            <?php
                        }
                    }
                    ?>
                </select>
                <script>
                    jQuery('select[name="_xtec-booking-resource"] option[value="<?php
                            if (isset($data['parameters']['_xtec-booking-resource'][0])) {
                                echo $data['parameters']['_xtec-booking-resource'][0];
                            }
                            ?>"').prop('selected', true);
                </script>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="max-width:290px">
                <div class="xtec-booking-datetime">
                    <span id="xtec_booking_startDate_message" class="description"><?php
                        _e('Start date', 'xtec-booking'); ?></span>
                    <br>
                    <input id="xtec-booking-start-date" name="_xtec-booking-start-date" type="text"
                           class="xtec-booking-input-datetime"
                           style="width:246px; margin-bottom:5px;"
                           value="<?php echo $xtec_booking_data_start_date; ?>"
                           required>
                    <span id="xtec-booking-before-date" class="style_message display_message_date">
                        <?php _e('Date before at actual date', 'xtec-booking'); ?>
                    </span>
                </div>
            </td>
            <td colspan="2" rowspan="2">
                <div class="xtec-booking-datetime">
                    <span class="description"><?php _e('Start time', 'xtec-booking'); ?></span>
                    <br>
                    <input id="xtec-booking-start-time"
                           name="_xtec-booking-start-time"
                           class="xtec-booking-input-time xtec-booking-input-datetime"
                           value="<?php echo $xtec_booking_data_start_time; ?>"
                           type="text" required>
                </div>
                <div class="xtec-booking-datetime">
                    <span class="description"><?php _e('Finish time', 'xtec-booking'); ?></span>
                    <br>
                    <input id="xtec-booking-finish-time" name="_xtec-booking-finish-time"
                           class="xtec-booking-input-time xtec-booking-input-datetime"
                           value="<?php echo $xtec_booking_data_finish_time; ?>"
                           type="text"
                           <?php if (empty($xtec_booking_data_finish_time)) { ?> disabled <?php } ?> required>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="max-width:290px">
                <div class="xtec-booking-datetime">
                    <span class="description"><?php
                        _e('Finish date', 'xtec-booking'); ?></span>
                    <br>
                    <input id="xtec-booking-finish-date"
                           name="_xtec-booking-finish-date"
                           type="text"
                           class="xtec-booking-input-datetime"
                           style="width:246px; margin-bottom:4px;"
                           value="<?php echo $xtec_booking_data_finish_date ?>"
                           <?php if (!empty($xtec_booking_data) && $xtec_booking_data_finish_date === ''){ ?> disabled <?php } ?>
                           required>
                        <span id="xtec-booking-before-date-finish" class="style_message display_message_date">
                            <?php _e('Date before at actual date', 'xtec-booking'); ?>
                        </span>
                </div>
            </td>
        </tr>
        </tbody>
    </table>
<?php
}

function xtec_add_metabox_parameters_booking_show_days_fields(
        array $data, mixed $xtec_booking_data, mixed $xtec_booking_data_start_date,
        mixed $xtec_booking_data_finish_date): void
{
    ?>
    <table class="form-table">
        <!-- The table head is added to meet code quality standards -->
        <thead style="display: none">
        <tr><th colspan="8"></th></tr>
        </thead>
        <tbody>
        <tr id="text_days"
                <?php
                if (empty($data['parameters']) || ($xtec_booking_data_start_date === $xtec_booking_data_finish_date)) {
                    ?>
                    class="xtec-booking-display-none"
                    <?php
                } ?>>
            <td colspan="8" style="line-height: 1">
                <span class="description"><?php _e('Select day or days to repeat booking', 'xtec-booking'); ?></span>
            </td>
        </tr>
        <tr id="xtec-booking-row-days"
                <?php if (empty($data['parameters']) || ($xtec_booking_data_start_date === $xtec_booking_data_finish_date)) { ?>
                    class="xtec-booking-display-none"
                <?php } ?> >
            <td>
                <input type="checkbox" id="_xtec-booking-day-monday" name="_xtec-booking-day-monday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-monday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-monday">
                    <span class="description xtec-non-italic">
                        <?php _e('Monday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-tuesday" name="_xtec-booking-day-tuesday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-tuesday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-tuesday">
                    <span class="description xtec-non-italic">
                        <?php _e('Tuesday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-wednesday" name="_xtec-booking-day-wednesday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-wednesday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-wednesday">
                    <span class="description xtec-non-italic">
                        <?php _e('Wednesday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-thursday" name="_xtec-booking-day-thursday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-thursday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-thursday">
                    <span class="description xtec-non-italic">
                        <?php _e('Thursday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-friday" name="_xtec-booking-day-friday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-friday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-friday">
                    <span class="description xtec-non-italic">
                        <?php _e('Friday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>|</td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-saturday" name="_xtec-booking-day-saturday"
                       <?php if ($xtec_booking_data['_xtec-booking-day-saturday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-saturday">
                    <span class="description xtec-non-italic">
                        <?php _e('Saturday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
            <td>
                <input type="checkbox" id="_xtec-booking-day-sunday" name="_xtec-booking-day-sunday"
                        <?php if ($xtec_booking_data['_xtec-booking-day-sunday'] ?? false) { ?> checked <?php } ?>>
                <label for="_xtec-booking-day-sunday">
                    <span class="description xtec-non-italic">
                        <?php _e('Sunday', 'xtec-booking'); ?>
                    </span>
                </label>
            </td>
        </tr>
        <tr id="error_message_days" class="xtec-booking-display-none">
            <td colspan="5">
                <span class="description xtec-warning"><?php
                    _e('Is necessary that check at least one day', 'xtec-booking'); ?></span>
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}

function xtec_booking_maybe_show_notice(mixed $results, ?WP_User $user)
{
    if (count($results) === 0) {
        ?>
        <div id="message" class="notice notice-error is-dismissible xtec-red">
            <p class="xtec-white"><?php
                _e('Not allow any resources to select.', 'xtec-booking'); ?>
                <?php
                if (in_array('administrator', $user->roles)) {
                    ?>
                    <a href="<?php echo get_home_url() . '/wp-admin/post-new.php?post_type=calendar_resources'; ?>">
                        <?php _e('Add Resource', 'xtec-booking') ?>
                    </a>
                    <?php
                }
                ?>
            </p>
        </div>
        <?php
    }
}

function xtec_get_resources(): array
{
    $role = wp_get_current_user();

    if (in_array('administrator', $role->roles)) {
        $role = 'administrator';
    } elseif (in_array('editor', $role->roles)) {
        $role = 'editor';
    } else {
        $role = 'user';
    }

    if ($role === 'editor') {
        global $wpdb;
        $data['results'] = $wpdb->get_results('SELECT * FROM wp_posts WHERE post_type = "calendar_resources" AND ( post_status = "publish" OR post_status = "private" ) ORDER BY post_title ASC ');
    } else {
        $data['results'] = query_posts("post_type=calendar_resources&orderby=title&order=ASC&posts_per_page=-1");
    }

    $resources = $data['results'];

    $i = 0;

    foreach ($resources as $resource) {
        $status = get_post_meta($resource->ID, '_xtec_resources_status');

        $is_inactive = ($status[0] === 'inactive');
        $is_restricted_admin = ($status[0] === 'admin_users' && !in_array($role, ['administrator', 'editor']));

        if ($is_inactive || $is_restricted_admin) {
            unset($data['results'][$i]);
        }

        $i++;
    }

    return $data;
}

function xtec_add_metabox_calendar_booking(): void
{
    $calendar = xtec_booking_show_calendar_page(1);
    print_r($calendar);
}

function xtec_add_metabox_resource_information(): void
{
    $infoResource = '
        <div id="xtec_resource_thumbnail" class="resource_thumbnail" xmlns="http://www.w3.org/1999/html">
            <div id="img_resource"></div>
            <div id="resource_no_data" class="resource_no_data">
                <span id="no_image_resource" class="select_resource">' . __('Select resource to show image', 'xtec-booking') . '</span>
                <span id="resource_wait" style="display:none">
                    <p><img id="xtec-booking-wait" alt="" src="../wp-admin/images/loading.gif"></p>
                </span>
            </div>
        </div>
        ';

    print_r($infoResource);
}
