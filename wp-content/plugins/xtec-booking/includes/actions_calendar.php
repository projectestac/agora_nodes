<?php

// Check available dates
function check_available_dates($data, $post_id)
{
    // If the booking is not public or is a draft or pending, it will be available without checking the dates.
    if ($_POST['visibility'] !== 'public' || $_POST['post_status'] === 'draft' || $_POST['post_status'] === 'pending') {
        $available = true;
    } else {

        global $wpdb;
        $available = true;

        // Get all bookings of the same resource.
        $bookings = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE post_id != " . $post_id . " AND post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_value = " . $data['_xtec-booking-resource'] . ") AND meta_key = '_xtec-booking-data'");

        // Data example: $data['_xtec-booking-start-date'] => string(10) "23-03-2026"

        // Get the timestamp of the start and finish dates of the new booking.
        [$day_start_new, $day_end_new] = convert_days_to_timestamp($data['_xtec-booking-start-date'], $data['_xtec-booking-finish-date']);

        // If the conversion fails, return there is no availability.
        if (!is_int($day_start_new) || !is_int($day_end_new)) {
            return false;
        }

        foreach ($bookings as $booking) {

            $visibilityPost = get_post($booking->post_id);
            if ($visibilityPost->post_status !== 'publish') {
                continue;
            }

            // Example of $booking->meta_value:
            //  string(569) "a:14:{s:10:"post_title";s:22:"Reserva #3 (xtecadmin)";s:7:"content";s:3:"---";
            //               s:22:"_xte-booking-resource";s:3:"718";s:24:"_xtec-booking-start-date";s:10:"18-02-2026";
            //               s:25:"_xtec-booking-finish-date";s:10:"18-02-2026";s:24:"_xtec-booking-day-monday";b:0;
            //               s:25:"_xtec-booking-day-tuesday";b:0;s:27:"_xtec-booking-day-wednesday";b:0;
            //               s:26:"_xtec-booking-day-thursday";b:0;s:24:"_xtec-booking-day-friday";b:0;
            //               s:26:"_xtec-booking-day-saturday";b:0;s:24:"_xtec-booking-day-sunday";b:0;
            //               s:24:"_xtec-booking-start-time";s:5:"14:00";s:25:"_xtec-booking-finish-time";s:5:"15:00";}"

            $meta_value = unserialize($booking->meta_value, ['allowed_classes' => false]);

            // Check if there is the resource is available in the dates of the booking.
            $available = check_availability($data, $meta_value, $day_start_new, $day_end_new);

            if (!$available) {
                break;
            }
        }

    }

    return $available;
}

/**
 * Check if the resource is available in the dates of the booking.
 *
 * @param array $data with the data of the new booking
 * @param array $meta_value array with the data of the old booking
 * @param int $day_start_new
 * @param int $day_end_new
 * @return bool
 */
function check_availability(array $data, array $meta_value, int $day_start_new, int $day_end_new)
{
    $available = true;

    [$day_start_old, $day_end_old] = convert_days_to_timestamp($meta_value['_xtec-booking-start-date'], $meta_value['_xtec-booking-finish-date']);

    // Check if the new booking and the old one have at least one day in common. If they do, we check if they have
    // at least one day in common and if they have at least one time slot in common.
    if (($day_start_new >= $day_start_old && $day_start_new <= $day_end_old) ||
        ($day_end_new >= $day_start_old && $day_end_new <= $day_end_old) ||
        ($day_start_new <= $day_start_old && $day_end_new >= $day_end_old)) {

        // Explanation of the following conditions:
        // ($day_start_old === $day_start_new) && ($day_end_old === $day_end_new) && ($day_start_new === $day_end_new)
        // → The new booking starts and ends on the same day as the old one (the new one is a single day booking that coincides with the old one)
        // OR ($day_start_old === $day_end_old)
        // → The old booking is a single day booking, so we check if the new one has that day available
        // OR ($day_start_new === $day_end_new)
        // → The new booking is a single day booking, so we check if the old one has that day available
        if (($day_start_old === $day_start_new) && ($day_end_old === $day_end_new) && ($day_start_new === $day_end_new)) {
            $day = true;
        } elseif ($day_start_old === $day_end_old) {
            $day = check_day($data, $day_start_new);
        } elseif ($day_start_new === $day_end_new) {
            $day = check_day($meta_value, $day_start_old);
        } else {
            // If the new booking and the old one are not single day bookings, check if they have at least one day in common.
            $day = check_day_of_the_week($data, $meta_value);
        }

        // Check available times if the days are the same.
        if ($day) {
            $available = check_available_time($data, $meta_value, $available);
        }
    }

    return $available;
}

function convert_days_to_timestamp(string $start, string $finish): array
{
    $day_start_timestamp = mktime(0, 0, 0, (int)substr($start, 3, 2), (int)substr($start, 0, 2), (int)substr($start, 6, 4));
    $day_end_timestamp = mktime(0, 0, 0, (int)substr($finish, 3, 2), (int)substr($finish, 0, 2), (int)substr($finish, 6, 4));

    return [$day_start_timestamp, $day_end_timestamp];
}

function check_day(array $data, bool|int $start): bool
{
    $day = false;
    $checkDay = strtoupper(substr(date('D', $start), 0, 2));

    switch ($checkDay) {
        case 'MO':
            if ($data['_xtec-booking-day-monday'] === true) {
                $day = true;
            }
            break;
        case 'TU':
            if ($data['_xtec-booking-day-tuesday'] === true) {
                $day = true;
            }
            break;
        case 'WE':
            if ($data['_xtec-booking-day-wednesday'] === true) {
                $day = true;
            }
            break;
        case 'TH':
            if ($data['_xtec-booking-day-thursday'] === true) {
                $day = true;
            }
            break;
        case 'FR':
            if ($data['_xtec-booking-day-friday'] === true) {
                $day = true;
            }
            break;
        case 'SA':
            if ($data['_xtec-booking-day-saturday'] === true) {
                $day = true;
            }
            break;
        case 'SU':
            if ($data['_xtec-booking-day-sunday'] === true) {
                $day = true;
            }
            break;
        default:
            break;
    }

    return $day;
}

function check_day_of_the_week(array $data, array $meta_value): bool
{
    $day = false;
    $week_days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

    foreach ($week_days as $week_day) {
        $key = '_xtec-booking-day-' . $week_day;
        if (isset($data[$key], $meta_value[$key]) && $data[$key] === true && $meta_value[$key] === true) {
            $day = true;
            break;
        }
    }

    return $day;
}

function check_available_time(array $data, array $meta_value, bool $available): bool
{
    $dataStartTime = strtotime($data['_xtec-booking-start-time']);
    $dataEndTime = strtotime($data['_xtec-booking-finish-time']);
    $meta_valueStartTime = strtotime($meta_value['_xtec-booking-start-time']);
    $meta_valueEndTime = strtotime($meta_value['_xtec-booking-finish-time']);

    if ($dataStartTime === $meta_valueStartTime) {
        $available = false;
    }
    // Explanation of the following conditions:
    // ($dataStartTime >= $meta_valueStartTime) && ($dataStartTime <= $meta_valueEndTime)
    // → The new booking starts within the time slot of the old one (initial intersection)
    // OR ($dataEndTime >= $meta_valueStartTime) && ($dataEndTime <= $meta_valueEndTime)
    // → The new booking ends within the time slot of the old one (final intersection)
    // OR ($dataStartTime <= $meta_valueStartTime) && ($dataEndTime >= $meta_valueEndTime)
    // → The new booking starts before and ends after the old one (the new one encapsulates the old one)
    elseif ((($dataStartTime >= $meta_valueStartTime) && ($dataStartTime <= $meta_valueEndTime)) ||
        (($dataEndTime >= $meta_valueStartTime) && ($dataEndTime <= $meta_valueEndTime)) ||
        (($dataStartTime <= $meta_valueStartTime) && ($dataEndTime >= $meta_valueEndTime))) {

        if (($dataEndTime !== $meta_valueStartTime) && ($dataStartTime !== $meta_valueEndTime)) {
            $available = false;
        }
    }

    return $available;
}

// Add capabilities to roles when the plugin is activated and remove them when the plugin is deactivated.
function xtec_booking_active_plugin(): void
{
    // Add capabilities to role Administrator.
    $roleAdmin = get_role('administrator');
    $roleAdmin->add_cap('edit_posts_bookings');
    $roleAdmin->add_cap('delete_posts_bookings');
    $roleAdmin->add_cap('delete_pages_bookings');
    $roleAdmin->add_cap('publish_posts_bookings');
    $roleAdmin->add_cap('edit_published_posts_bookings');
    $roleAdmin->add_cap('delete_published_posts_bookings');

    // Add capabilities to role Editor.
    $roleEditor = get_role('editor');
    $roleEditor->add_cap('edit_posts_bookings');
    $roleEditor->add_cap('delete_posts_bookings');
    $roleEditor->add_cap('delete_pages_bookings');
    $roleEditor->add_cap('publish_posts_bookings');
    $roleEditor->add_cap('edit_published_posts_bookings');
    $roleEditor->add_cap('delete_published_posts_bookings');

    // Add capabilities to role Author.
    $roleAuthor = get_role('author');
    $roleAuthor->add_cap('delete_pages_bookings');
    $roleAuthor->add_cap('edit_posts_bookings');
    $roleAuthor->add_cap('delete_posts_bookings');
    $roleAuthor->add_cap('publish_posts_bookings');
    $roleAuthor->add_cap('delete_pages');
    $roleAuthor->add_cap('edit_published_posts_bookings');
    $roleAuthor->add_cap('delete_published_posts_bookings');

    // Add capabilities to role Teacher.
    $roleTeacher = get_role('xtec_teacher');
    if (!is_null($roleTeacher)) {
        $roleTeacher->add_cap('delete_pages_bookings');
        $roleTeacher->add_cap('edit_posts_bookings');
        $roleTeacher->add_cap('delete_posts_bookings');
        $roleTeacher->add_cap('publish_posts_bookings');
        $roleTeacher->remove_cap('edit_published_posts');
        $roleTeacher->remove_cap('delete_published_posts');
        $roleTeacher->add_cap('edit_published_posts_bookings');
        $roleTeacher->add_cap('delete_published_posts_bookings');
    }

    // Add capabilities to role Contributor.
    $roleContributor = get_role('contributor');
    $roleContributor->add_cap('delete_pages_bookings');
    $roleContributor->add_cap('delete_pages');
    $roleContributor->remove_cap('edit_posts_bookings');
    $roleContributor->remove_cap('delete_posts_bookings');
}

function xtec_booking_deactive_plugin(): void
{
    // Remove capabilities to role Administrator.
    $roleAdmin = get_role('administrator');
    $roleAdmin->remove_cap('edit_posts_bookings');
    $roleAdmin->remove_cap('delete_posts_bookings');
    $roleAdmin->remove_cap('delete_pages_bookings');
    $roleAdmin->remove_cap('publish_posts_bookings');
    $roleAdmin->remove_cap('edit_published_posts_bookings');
    $roleAdmin->remove_cap('delete_published_posts_bookings');

    // Remove capabilities to role Editor.
    $roleEditor = get_role('editor');
    $roleEditor->remove_cap('edit_posts_bookings');
    $roleEditor->remove_cap('delete_posts_bookings');
    $roleEditor->remove_cap('delete_pages_bookings');
    $roleEditor->remove_cap('publish_posts_bookings');
    $roleEditor->remove_cap('edit_published_posts_bookings');
    $roleEditor->remove_cap('delete_published_posts_bookings');

    // Remove capabilities to role Author.
    $roleAuthor = get_role('author');
    $roleAuthor->remove_cap('delete_pages_bookings');
    $roleAuthor->remove_cap('edit_posts_bookings');
    $roleAuthor->remove_cap('delete_posts_bookings');
    $roleAuthor->remove_cap('publish_posts_bookings');
    $roleAuthor->remove_cap('delete_pages');
    $roleAuthor->remove_cap('edit_published_posts_bookings');
    $roleAuthor->remove_cap('delete_published_posts_bookings');

    // Add capabilities to role Teacher.
    $roleTeacher = get_role('xtec_teacher');
    if (!is_null($roleTeacher)) {
        $roleTeacher->remove_cap('delete_published_posts');
        $roleTeacher->remove_cap('edit_published_posts');
        $roleTeacher->remove_cap('delete_pages_bookings');
        $roleTeacher->remove_cap('edit_posts_bookings');
        $roleTeacher->remove_cap('delete_posts_bookings');
        $roleTeacher->remove_cap('publish_posts_bookings');
        $roleTeacher->remove_cap('edit_published_posts_bookings');
        $roleTeacher->remove_cap('delete_published_posts_bookings');
    }

    // Add capabilities to role Contributor.
    $roleContributor = get_role('contributor');
    $roleContributor->remove_cap('delete_pages_bookings');
    $roleContributor->remove_cap('delete_pages');
}
