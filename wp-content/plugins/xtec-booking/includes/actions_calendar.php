<?php

const XTEC_BOOKING_DAY_MONDAY_KEY    = '_xtec-booking-day-monday';
const XTEC_BOOKING_DAY_TUESDAY_KEY   = '_xtec-booking-day-tuesday';
const XTEC_BOOKING_DAY_WEDNESDAY_KEY = '_xtec-booking-day-wednesday';
const XTEC_BOOKING_DAY_THURSDAY_KEY  = '_xtec-booking-day-thursday';
const XTEC_BOOKING_DAY_FRIDAY_KEY    = '_xtec-booking-day-friday';
const XTEC_BOOKING_DAY_SATURDAY_KEY  = '_xtec-booking-day-saturday';
const XTEC_BOOKING_DAY_SUNDAY_KEY    = '_xtec-booking-day-sunday';

// Check available dates
function check_available_dates($data, $post_id): bool
{
    if (xtec_should_skip_conflict_check()) {
        return true;
    }

    global $wpdb;

    $available = true;
    $results = xtec_get_existing_bookings($wpdb, $post_id, $data['_xtec-booking-resource']);

    $dataStart = xtec_parse_date_to_timestamp($data[XTEC_BOOKING_START_DATE_KEY]);
    $dataFinish = xtec_parse_date_to_timestamp($data[XTEC_BOOKING_FINISH_DATE_KEY]);

    foreach ($results as $row) {
        $post = get_post($row->post_id);
        if ($post->post_status !== 'publish') {
            continue;
        }

        $meta = unserialize($row->meta_value);
        $metaStart = xtec_parse_date_to_timestamp($meta[XTEC_BOOKING_START_DATE_KEY]);
        $metaFinish = xtec_parse_date_to_timestamp($meta[XTEC_BOOKING_FINISH_DATE_KEY]);

        if (!xtec_dates_overlap($dataStart, $dataFinish, $metaStart, $metaFinish)) {
            continue;
        }

        if (xtec_days_conflict($data, $meta, $dataStart, $dataFinish, $metaStart, $metaFinish)
            && xtec_times_conflict($data, $meta)) {
            $available = false;
        }
    }

    return $available;
}

/**
 * Skip check if booking is private, draft or pending.
 */
function xtec_should_skip_conflict_check(): bool {
    return ($_POST['visibility'] !== 'public'
        || $_POST['post_status'] === 'draft'
        || $_POST['post_status'] === 'pending');
}

/**
 * Query DB for bookings that share the same resource.
 */
function xtec_get_existing_bookings($wpdb, int $post_id, $resourceId) {
    return $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_postmeta 
             WHERE post_id != %d
               AND post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_value = %d) 
               AND meta_key = '_xtec-booking-data'",
            $post_id,
            $resourceId
        )
    );
}

/**
 * Convert dd/mm/yyyy string to timestamp (00:00:00).
 */
function xtec_parse_date_to_timestamp(string $date): int {
    return mktime(
        0, 0, 0,
        (int)substr($date, 3, 2),
        (int)substr($date, 0, 2),
        (int)substr($date, 6, 4)
    );
}

/**
 * Check if two date ranges overlap.
 */
function xtec_dates_overlap(int $start1, int $end1, int $start2, int $end2): bool {
    return ($start1 <= $end2 && $end1 >= $start2);
}

/**
 * Check if the days selection causes conflict.
 */
function xtec_days_conflict(array $data, array $meta, int $dataStart, int $dataFinish, int $metaStart, int $metaFinish): bool {
    // Exact same single day
    if ($metaStart === $dataStart && $metaFinish === $dataFinish && $dataStart === $dataFinish) {
        return true;
    }

    // Old booking is a single day
    if ($metaStart === $metaFinish) {
        return xtec_day_matches($data, $metaStart);
    }

    // New booking is a single day
    if ($dataStart === $dataFinish) {
        return xtec_day_matches($meta, $dataStart);
    }

    // Multi-day ranges: check common day selections
    return xtec_week_days_overlap($data, $meta);
}

/**
 * Check if a timestamp corresponds to a selected day in booking data.
 */
function xtec_day_matches(array $bookingData, int $timestamp): bool {
    $dayMap = [
        'MO' => XTEC_BOOKING_DAY_MONDAY_KEY,
        'TU' => XTEC_BOOKING_DAY_TUESDAY_KEY,
        'WE' => XTEC_BOOKING_DAY_WEDNESDAY_KEY,
        'TH' => XTEC_BOOKING_DAY_THURSDAY_KEY,
        'FR' => XTEC_BOOKING_DAY_FRIDAY_KEY,
        'SA' => XTEC_BOOKING_DAY_SATURDAY_KEY,
        'SU' => XTEC_BOOKING_DAY_SUNDAY_KEY,
    ];

    $day = strtoupper(substr(date('D', $timestamp), 0, 2));
    return !empty($bookingData[$dayMap[$day]]) && $bookingData[$dayMap[$day]] === 'true';
}

/**
 * Compare week day selections between two bookings.
 */
function xtec_week_days_overlap(array $data, array $meta): bool {
    $keys = [
        XTEC_BOOKING_DAY_MONDAY_KEY,
        XTEC_BOOKING_DAY_TUESDAY_KEY,
        XTEC_BOOKING_DAY_WEDNESDAY_KEY,
        XTEC_BOOKING_DAY_THURSDAY_KEY,
        XTEC_BOOKING_DAY_FRIDAY_KEY,
        XTEC_BOOKING_DAY_SATURDAY_KEY,
        XTEC_BOOKING_DAY_SUNDAY_KEY,
    ];

    foreach ($keys as $key) {
        if (!empty($data[$key]) && $data[$key] === $meta[$key] && $meta[$key] === 'true') {
            return true;
        }
    }
    return false;
}

/**
 * Check if two time ranges conflict.
 */
function xtec_times_conflict(array $data, array $meta): bool {
    $dataStart = strtotime($data[XTEC_BOOKING_START_TIME_KEY]);
    $dataEnd   = strtotime($data[XTEC_BOOKING_FINISH_TIME_KEY]);
    $metaStart = strtotime($meta[XTEC_BOOKING_START_TIME_KEY]);
    $metaEnd   = strtotime($meta[XTEC_BOOKING_FINISH_TIME_KEY]);

    if ($dataStart === $metaStart) {
        return true;
    }

    return (
        ($dataStart >= $metaStart && $dataStart <= $metaEnd) ||
        ($dataEnd   >= $metaStart && $dataEnd   <= $metaEnd) ||
        ($dataStart <= $metaStart && $dataEnd   >= $metaEnd)
    ) && ($dataEnd !== $metaStart && $dataStart !== $metaEnd);
}


// CAPABILITIES TO ROLES
function xtec_booking_active_plugin(){

	// Add capabilities to role Administrator
	$roleAdmin = get_role('administrator');
    $roleAdmin->add_cap('edit_posts_bookings');
    $roleAdmin->add_cap('delete_posts_bookings');
    $roleAdmin->add_cap('delete_pages_bookings');
    $roleAdmin->add_cap('publish_posts_bookings');
    $roleAdmin->add_cap('edit_published_posts_bookings');
    $roleAdmin->add_cap('delete_published_posts_bookings');

    // Add capabilities to role Editor
	$roleEditor = get_role('editor');
	$roleEditor->add_cap('edit_posts_bookings');
    $roleEditor->add_cap('delete_posts_bookings');
    $roleEditor->add_cap('delete_pages_bookings');
    $roleEditor->add_cap('publish_posts_bookings');
    $roleEditor->add_cap('edit_published_posts_bookings');
    $roleEditor->add_cap('delete_published_posts_bookings');

	// Add capabilities to role Author
	$roleAuthor = get_role('author');
	$roleAuthor->add_cap('delete_pages_bookings');
	$roleAuthor->add_cap('edit_posts_bookings');
    $roleAuthor->add_cap('delete_posts_bookings');
    $roleAuthor->add_cap('publish_posts_bookings');
    $roleAuthor->add_cap('delete_pages');
    $roleAuthor->add_cap('edit_published_posts_bookings');
    $roleAuthor->add_cap('delete_published_posts_bookings');

    // Add capabilities to role Teacher
    $roleTeacher = get_role('xtec_teacher');
    if ( ! is_null( $roleTeacher ) ) {
        $roleTeacher->add_cap('delete_pages_bookings');
        $roleTeacher->add_cap('edit_posts_bookings');
        $roleTeacher->add_cap('delete_posts_bookings');
        $roleTeacher->add_cap('publish_posts_bookings');
        $roleTeacher->remove_cap('edit_published_posts');
        $roleTeacher->remove_cap('delete_published_posts');
        $roleTeacher->add_cap('edit_published_posts_bookings');
        $roleTeacher->add_cap('delete_published_posts_bookings');
    }

	// Add capabilities to role Contributor
	$roleContributor = get_role('contributor');
	$roleContributor->add_cap('delete_pages_bookings');
	$roleContributor->add_cap('delete_pages');
	$roleContributor->remove_cap('edit_posts_bookings');
	$roleContributor->remove_cap('delete_posts_bookings');

}

function xtec_booking_deactive_plugin(){

	// Remove capabilities to role Administrator
	$roleAdmin = get_role('administrator');
    $roleAdmin->remove_cap('edit_posts_bookings');
    $roleAdmin->remove_cap('delete_posts_bookings');
    $roleAdmin->remove_cap('delete_pages_bookings');
    $roleAdmin->remove_cap('publish_posts_bookings');
    $roleAdmin->remove_cap('edit_published_posts_bookings');
    $roleAdmin->remove_cap('delete_published_posts_bookings');

    // Remove capabilities to role Editor
	$roleEditor = get_role('editor');
	$roleEditor->remove_cap('edit_posts_bookings');
    $roleEditor->remove_cap('delete_posts_bookings');
    $roleEditor->remove_cap('delete_pages_bookings');
    $roleEditor->remove_cap('publish_posts_bookings');
    $roleEditor->remove_cap('edit_published_posts_bookings');
    $roleEditor->remove_cap('delete_published_posts_bookings');

	// Remove capabilities to role Author
	$roleAuthor = get_role('author');
	$roleAuthor->remove_cap('delete_pages_bookings');
	$roleAuthor->remove_cap('edit_posts_bookings');
    $roleAuthor->remove_cap('delete_posts_bookings');
    $roleAuthor->remove_cap('publish_posts_bookings');
    $roleAuthor->remove_cap('delete_pages');
    $roleAuthor->remove_cap('edit_published_posts_bookings');
    $roleAuthor->remove_cap('delete_published_posts_bookings');

    // Add capabilities to role Teacher
	$roleTeacher = get_role('xtec_teacher');
	if ( ! is_null( $roleTeacher ) ){
        $roleTeacher->remove_cap('delete_published_posts');
        $roleTeacher->remove_cap('edit_published_posts');
        $roleTeacher->remove_cap('delete_pages_bookings');
        $roleTeacher->remove_cap('edit_posts_bookings');
        $roleTeacher->remove_cap('delete_posts_bookings');
        $roleTeacher->remove_cap('publish_posts_bookings');
        $roleTeacher->remove_cap('edit_published_posts_bookings');
        $roleTeacher->remove_cap('delete_published_posts_bookings');
	}

	// Add capabilities to role Contributor
	$roleContributor = get_role('contributor');
	$roleContributor->remove_cap('delete_pages_bookings');
	$roleContributor->remove_cap('delete_pages');

}
