<?php

const LOOP_LIMIT = 400; // Used in xtec_booking_get_event()
const NUM_CHARACTERS = 50; // Used in xtec_booking_get_event()
const XTEC_BOOKING_START_DATE = '_xtec-booking-start-date';
const XTEC_BOOKING_FINISH_DATE = '_xtec-booking-finish-date';
const XTEC_BOOKING_START_TIME = '_xtec-booking-start-time';
const XTEC_BOOKING_FINISH_TIME = '_xtec-booking-finish-time';
const XTEC_BOOKING_RESOURCE = '_xtec-booking-resource';
const XTEC_RESOURCES_STATUS = '_xtec_resources_status';

function xtec_booking_show_calendar_page($shortcode = null)
{
    $data = xtec_booking_get_events();
    $calendar = xtec_display_calendar($data, $shortcode);

    if ($shortcode === '') {
        print_r($calendar);
        return '';
    }

    return $calendar;
}

function xtec_booking_calendar_libraries($shortcode = false): void
{
    // BOOTSTRAP JS
    wp_register_script('bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
    wp_enqueue_script('bootstrap-js');

    if (is_null($shortcode) || $shortcode === false) {
        // BOOTSTRAP CSS
        wp_enqueue_style('bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
    } else {
        // CUSTOM JS AND CSS TO CALENDAR
        wp_register_script('booking-calendar-js', plugins_url() . '/xtec-booking/js/xtec-booking-calendar.js', ['jquery'], '1.1', true);
        wp_enqueue_script('booking-calendar-js');
        wp_enqueue_style('calendar-booking-css', plugins_url() . '/xtec-booking/css/xtec-booking-calendar.css');
    }

    // UNDERSCORE
    wp_enqueue_script('underscore');

    // CALENDAR
    wp_register_script(
        'calendar-js',
        plugins_url() . '/xtec-booking/includes/vendor/calendar/js/calendar.min.js',
        ['jquery', 'underscore'],
        '1.1',
        true
    );
    wp_enqueue_script('calendar-js');

    $language_locale = get_locale();

    if (!str_contains($language_locale, '_')) {
        $language_locale .= '-ES.js';
    } else {
        $language_locale = str_replace('_', '-', $language_locale) . '.js';
    }

    // Add Catalan translation
    wp_register_script('calendar-language-js', plugins_url() . '/xtec-booking/includes/vendor/calendar/js/language/' . $language_locale, ['jquery'], '1.1', true);
    wp_enqueue_script('calendar-language-js');
    wp_enqueue_style('calendar-css', plugins_url() . '/xtec-booking/includes/vendor/calendar/css/calendar.min.css');
}

function xtec_booking_get_event($eventID)
{
    //GET POST BOOKING
    $post = get_post($eventID);

    // GET AUTHOR BOOKING
    $user = get_userdata($post->post_author);

    // GET POST META
    $post_meta = get_post_meta($eventID, '_xtec-booking-data');

    // GET RESOURCE
    $resource = get_post($post_meta[0][XTEC_BOOKING_RESOURCE]);

    $request = [
        'title' => $post->post_title,
        'content' => $post->post_content,
        'resource' => $resource->post_title,
        'startDate' => $post_meta[0][XTEC_BOOKING_START_DATE],
        'endDate' => $post_meta[0][XTEC_BOOKING_FINISH_DATE],
        'startHour' => $post_meta[0][XTEC_BOOKING_START_TIME],
        'endHour' => $post_meta[0][XTEC_BOOKING_FINISH_TIME],
        'by' => $user->user_nicename,
    ];

    return json_encode($request);
}

function xtec_booking_get_events($resourceID = false)
{
    $events = [];
    $posts = get_posts([
        'post_type' => 'calendar_booking',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
    ]);

    $current_user = wp_get_current_user();
    $roles = $current_user->roles ?? [];
    $is_admin = in_array('administrator', $roles, true);
    $is_editor = in_array('editor', $roles, true);

    $target_resources = ($resourceID !== false && $resourceID !== 'no-events') ? (array)$resourceID : [];

    foreach ($posts as $post) {

        if (xtec_booking_get_events_check_post_fail($post, $is_admin, $is_editor, $target_resources)) {
            continue;
        }

        $postmeta = get_post_meta($post->ID);
        $resource_id = $postmeta[XTEC_BOOKING_RESOURCE][0] ?? null;
        $resource_status = get_post_meta($resource_id, XTEC_RESOURCES_STATUS, true);

        // At this point, resource and booking are valid. Let's get the color and if the resource is inactive.
        $color = get_post_meta($resource_id, '_xtec_resources_color', true);
        [$color, $inactive_resource] = xtec_booking_get_color_and_resource_state($color, $resource_status);

        $data = unserialize($postmeta['_xtec-booking-data'][0] ?? '', ['allowed_classes' => false]);
        if (!$data) {
            continue;
        }

        $format = '!d-m-Y H:i';
        $startStr = $data[XTEC_BOOKING_START_DATE] . ' ' . $data[XTEC_BOOKING_START_TIME];
        $endStr = $data[XTEC_BOOKING_FINISH_DATE] . ' ' . $data[XTEC_BOOKING_FINISH_TIME];

        $startDateTimeObj = DateTime::createFromFormat($format, $startStr);
        $endDateTimeObj = DateTime::createFromFormat($format, $endStr);

        if (!$startDateTimeObj || !$endDateTimeObj) {
            continue;
        }

        $time = [
            'startDateTimeObj' => $startDateTimeObj,
            'endDateTimeObj' => $endDateTimeObj,
        ];

        $resource = get_post($resource_id);

        $events = xtec_booking_get_events_process_post($post, $resource, $color, $inactive_resource, $data, $time, $events);
    }

    return json_encode($events);
}

function xtec_booking_get_events_check_post_fail(int|WP_Post $post, bool $is_admin, bool $is_editor, array $target_resources)
{
    $postmeta = get_post_meta($post->ID);
    $resource_id = $postmeta[XTEC_BOOKING_RESOURCE][0] ?? null;

    if (!$resource_id) {
        return true;
    }

    $resource_status = get_post_meta($resource_id, XTEC_RESOURCES_STATUS, true);

    if ($resource_status === 'admin_users' && !$is_admin) {
        return true;
    }

    if (!empty($target_resources) && !in_array((string)$resource_id, $target_resources, true)) {
        return true;
    }

    $resource = get_post($resource_id);
    if (!$resource || !isset($resource->post_status)) {
        return true;
    }

    $can_see_private = ($is_admin || $is_editor);
    if ($resource->post_status !== 'publish' && !($resource->post_status === 'private' && $can_see_private)) {
        return true;
    }

    return false;
}

function xtec_booking_get_events_process_post(int|WP_Post $post, array|WP_Post $resource, string $color,
                                              string $inactive_resource, mixed $data, array $time, mixed $events): array
{
    $startDateTimeObj = $time['startDateTimeObj'];
    $endDateTimeObj = $time['endDateTimeObj'];
    $startDateTime = $startDateTimeObj->getTimestamp();

    if ($data[XTEC_BOOKING_START_DATE] === $data[XTEC_BOOKING_FINISH_DATE]) {
        // Single day event.
        $endDateTime = $endDateTimeObj->getTimestamp();
        $events[] = xtec_booking_add_event($post, $resource, $color, $inactive_resource, $data, $startDateTime, $endDateTime);

    } else {
        // Multiple day event.
        $iteration = 0;
        $currentDayObj = clone $startDateTimeObj;
        $endDateString = $endDateTimeObj->format('Y-m-d');
        $timeEndParts = explode(':', $data[XTEC_BOOKING_FINISH_TIME]);

        while ($currentDayObj->format('Y-m-d') <= $endDateString && $iteration++ < LOOP_LIMIT) {
            $nDay = $currentDayObj->format('D');

            if (xtec_booking_check_event_day($nDay, $data)) {
                $currentStartTs = $currentDayObj->getTimestamp();

                $currentEndObj = clone $currentDayObj;
                $currentEndObj->setTime((int)$timeEndParts[0], (int)$timeEndParts[1]);
                $currentEndTs = $currentEndObj->getTimestamp();

                $events[] = xtec_booking_add_event($post, $resource, $color, $inactive_resource, $data, $currentStartTs, $currentEndTs);
            }

            $currentDayObj->modify('+1 day');
        }
    }

    return $events;
}

function xtec_booking_add_event(int|WP_Post $post, array|WP_Post|null $resource, string $color, string $inactive_resource,
                                mixed $data, int $startDateTime, int $startEndDateTime): array
{
    $user = get_userdata($post->post_author);
    $user = ($user->data->display_name === '') ? $user->data->display_name : $user->data->user_login;

    $description = strip_tags($post->post_content);
    if (($description !== '') && strlen($description) > NUM_CHARACTERS) {
        $description = substr($description, 0, NUM_CHARACTERS) . '...';
    }

    $description = esc_html($description);

    return [
        'id' => $post->ID,
        'title' => $inactive_resource . ' ' . $data[XTEC_BOOKING_START_TIME] . '-' . $data[XTEC_BOOKING_FINISH_TIME] . ' ' . ucfirst($resource->post_title) . '/' . $post->post_title . ': ' . $description . ' (' . $user . ')',
        'class' => $color,
        'start' => ($startDateTime * 1000),
        'end' => ($startEndDateTime * 1000),
    ];
}

function xtec_booking_check_event_day(string $nDay, mixed $data): bool
{
    $daysMap = [
        'Mon' => '_xtec-booking-day-monday',
        'Tue' => '_xtec-booking-day-tuesday',
        'Wed' => '_xtec-booking-day-wednesday',
        'Thu' => '_xtec-booking-day-thursday',
        'Fri' => '_xtec-booking-day-friday',
        'Sat' => '_xtec-booking-day-saturday',
        'Sun' => '_xtec-booking-day-sunday',
    ];

    $key = $daysMap[$nDay] ?? null;

    return $key && isset($data[$key]) && $data[$key] === true;
}

function xtec_booking_get_color_and_resource_state(mixed $color, mixed $resource_status): array
{
    if ($resource_status === 'inactive') {
        $color = 'grey';
        $inactive_resource = '(' . __('Not available resource', 'xtec-booking') . ')';
    } else {
        $color = match ($color) {
            'red' => 'event-important',
            'yellow' => 'event-warning',
            'blue' => 'event-info',
            'black' => 'event-inverse',
            'green' => 'event-success',
            'purple' => 'event-special',
            'orange' => 'event-orange',
            'pink' => 'event-pink',
            'brown' => 'event-brown',
            'light_blue' => 'event-lightBlue',
            default => 'grey',
        };
        $inactive_resource = '';
    }

    return [$color, $inactive_resource];
}

function xtec_display_calendar($data, $shortcode): string
{
    $posts = xtec_booking_get_posts();

    if (!empty($shortcode)) {
        $calendar = '<script>
                var events = [];';
    } else {
        $calendar = '<script>
            var events = ' . $data . ';
            var unselectResources = "' . __('Unselect resources', 'xtec-booking') . '";
            var selectResources = "' . __('Select resources', 'xtec-booking') . '";';
    }

    $calendar .= '
                var tmplsCalendar = "' . plugins_url() . '/xtec-booking/includes/vendor/calendar/tmpls/";
            </script>';

    $calendar .= xtec_booking_add_modal_html($shortcode);
    $calendar .= '<div class="wrap">';

    if (!empty($shortcode)) {

        $calendar .= '
            <div class="page-header" style="min-width:250px; margin:0; border:0;">
                <h3 style="font-size:22px; margin:20px 0 0 0;></h3>
            </div>
            <div style="">
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title click_cursor" data-toggle="collapse" href="#collapse1" style="visibility:hidden; height:0;">';

    } else {

        $calendar .= '
             <div class="page-header" style="margin-left:50px; max-width:92%;">
                <div class="pull-right form-inline">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" data-calendar-nav="prev"><< ' . __('Prev', 'xtec-booking') . '</button>
                        <button class="btn btn-sm btn-primary" data-calendar-nav="today"><u> ' . __('Today', 'xtec-booking') . '</u></button>
                        <button class="btn btn-sm btn-primary" data-calendar-nav="next">' . __('Next', 'xtec-booking') . ' >></button>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-warning" data-calendar-view="year">' . __('Year', 'xtec-booking') . '</button>
                        <button class="btn btn-sm btn-warning active" data-calendar-view="month">' . __('Month', 'xtec-booking') . '</button>
                        <button class="btn btn-sm btn-warning" data-calendar-view="week">' . __('Week', 'xtec-booking') . '</button>
                        <button class="btn btn-sm btn-warning" data-calendar-view="day">' . __('Day', 'xtec-booking') . '</button>
                    </div>
                </div>
                <h3></h3>
            </div>
            <br>
            <div class="panel-group" style="margin-left:50px; max-width:92%; overflow:hidden">
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4 class="panel-title click_cursor" data-toggle="collapse" href="#collapse1">';
    }

    if (count($posts) === 0) {
        $calendar .= __('Resources', 'xtec-booking') .
            ' - <span class="xtec-booking-error">' . __('Not resources available', 'xtec-booking') . '</span>';
    } else {

        $calendar .= __('Resources', 'xtec-booking') .
            '<span class="dashicons dashicons-arrow-down xtec-booking-collapse xtec-booking-unstyle-link click_cursor"></span>';

        if (!empty($shortcode)) {
            $calendar .= '</h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse xtec-row-panel" style="visibility:hidden; height:0;">';
        } else {
            $calendar .= '</h4>
                            </div>
                            <div id="collapse1" class="panel-collapse collapse xtec-row-panel">';
        }

        $calendar .= '<div class="row">
                        <div class="col-md-12">
                            <input id="xtec_selection" data-action="unselect" type="button"
                                   class="btn btn-primary xtec-button-unselect"
                                   value="' . __('Unselect resources', 'xtec-booking') . '">
                        </div>
                    </div>';

        foreach ($posts as $post) {

            xtec_display_calendar_process_post($post, $shortcode, $calendar);

        }
    }

    $calendar .= '</div></form></div></div>
                    <br>
                    <div id="xtec_calendar_pos">';

    if (!empty($shortcode)) {
        $calendar .= '<div id="xtec_calendar_wait" class="div_wait_ajax_booking display_div_wait">
                            <center><img id="xtec-booking-wait" alt="" src="../wp-admin/images/wpspin_light-2x.gif"></center>
                        </div>
                        <div class="clearfix"></div>
                        <div id="xtec_calendar" style="min-width:252px; min-height:290px;"></div>
                        </div>
                        <div>
                        <div class="pull-right form-inline" style="margin:5px 0 0 0;">
                            <div class="btn-group">
                                <button class="button button-primary button-small" data-calendar-nav="prev"><< ' . __('Prev', 'xtec-booking') . '</button>
                                <button class="button button-primary button-small" data-calendar-nav="today"><u> ' . __('Today', 'xtec-booking') . '</u></button>
                                <button class="button button-primary button-small" data-calendar-nav="next">' . __('Next', 'xtec-booking') . ' >></button>
                            </div>
                        </div>
                        <br>';
    } else {
        $calendar .= '<div id="xtec_calendar_wait" class="div_wait_ajax display_div_wait">
                        <center><img id="xtec-booking-wait" alt="" src="../wp-admin/images/wpspin_light-2x.gif"></center>
                      </div>
                      <div id="xtec_calendar" style="margin-left:50px; max-width:92%;"></div>';
    }

    $calendar .= '</div></div>';

    return $calendar;
}

function xtec_display_calendar_process_post(mixed $post, $shortcode, string $calendar): string
{
    $color = get_post_meta($post->ID, '_xtec_resources_color', true);
    $status = get_post_meta($post->ID, XTEC_RESOURCES_STATUS, true);

    $roles = wp_get_current_user();
    if (($status === 'admin_users') &&
        (!in_array('administrator', $roles->roles) || (!in_array('editor', $roles->roles)))) {
        return $calendar;
    }

    if ($status === 'inactive') {
        $color = 'grey';
    } else {
        $color = match ($color) {
            'red' => 'event-important',
            'yellow' => 'event-warning',
            'blue' => 'event-info',
            'black' => 'event-inverse',
            'green' => 'event-success',
            'purple' => 'event-special',
            'orange' => 'event-orange',
            'pink' => 'event-pink',
            'brown' => 'event-brown',
            'light_blue' => 'event-lightBlue',
            default => 'all-resources',
        };
    }

    if (isset($_GET['resource']) && ((int)$_GET['resource'] === (int)$post->ID)) {
        $class = 'xtec-booking-selected';
    } else {
        $class = 'xtec-booking-not-selected';
    }

    if (!empty($shortcode)) {
        $calendar .= '
                    <div class="col-md-4">
                        <div class="' . $class . '">
                            <input id="resource-' . $post->ID . '"
                                   name="resource-' . $post->ID . '"
                                   type="checkbox"
                                   class="pull-left xtec-booking-check-resources">
                            <a href="javascript:void(0)"
                               data-event-class="' . $color . '"
                               class="pull-left event ' . $color . ' xtec-bullet-resource-list" title=""></a>
                               &nbsp;' . $post->post_title . '
                        </div>
                    </div>
                    ';
    } else {
        $calendar .= '
                    <div class="col-md-4">
                        <div class="' . $class . '">
                            <input id="resource-' . $post->ID . '" type="checkbox" name="resource-' . $post->ID . '" checked class="pull-left xtec-booking-check-resources">
                            <a href="javascript:void(0)"
                               data-event-class="' . $color . '"
                               class="pull-left event ' . $color . ' xtec-bullet-resource-list" title=""></a>
                            &nbsp;' . $post->post_title . '
                        </div>
                    </div>
                    ';
    }

    return $calendar;
}

function xtec_booking_get_posts(): array
{
    $roles = wp_get_current_user();

    $args = [
        'post_type' => 'calendar_resources',
        'post_per_pages' => -1,
        'nopaging' => true,
        'orderby' => 'post_title',
        'order' => 'ASC',
    ];

    $posts = get_posts($args);

    if (in_array('administrator', $roles->roles) || in_array('editor', $roles->roles)) {

        $args = [
            'post_type' => 'calendar_resources',
            'post_per_pages' => -1,
            'nopaging' => true,
            'post_status' => 'private',
            'orderby' => 'post_title',
            'order' => 'ASC',
        ];

        $postsAdmins = get_posts($args);

        if (count($postsAdmins) > 0) {
            $posts = array_merge($posts, $postsAdmins);
        }

    }

    return $posts;
}

function xtec_booking_add_modal_html(mixed $shortcode): string
{
    if (is_null($shortcode) || $shortcode === '') {
        return '
            <div class="modal fade" id="events-modal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h3 id="modalTitle"><p><img id="xtec-booking-wait" alt="" src="../wp-admin/images/loading.gif"></p></h3>
                        </div>
                        <div class="" style="max-height: 500px;padding:20px;">
                            <div class="xtec_booking_titles">
                                <strong class="xtec_booking_color">' . __('Resource', 'xtec-booking') . ':</strong>
                                <span id="modalResource"></span>
                            </div>
                            <div class="xtec_booking_titles">
                                <div class="xtec_booking_titles_hour xtec_booking_float">
                                    <strong class="xtec_booking_color">' . __('Start time', 'xtec-booking') . ':</strong>
                                    <span id="modalStartHour"></span>
                                </div>
                                <div class="xtec_booking_titles_hour xtec_booking_float">
                                    <strong class="xtec_booking_color">' . __('End Hour', 'xtec-booking') . ':</strong>
                                    <span id="modalEndHour"></span>
                                </div>
                            </div>
                            <div class="xtec_booking_titles xtec_booking_clear">
                                <strong class="xtec_booking_color">' . __('Booking make by', 'xtec-booking') . ': </strong>
                                <span id="modalBy"></span>
                            </div>
                            <br>
                            <div class="xtec_booking_modal_content xtec_booking_clear">
                                <strong class="xtec_booking_color">' . __('Description', 'xtec-booking') . ':</strong>
                                <p id="modalContent"></p>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="#" data-dismiss="modal" class="btn">' . __('Close', 'xtec-booking') . '</a>
                        </div>
                    </div>
                </div>
            </div>';
    }

    return '';
}
