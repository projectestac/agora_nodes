<?php

const META_KEY = '_xtec-booking-data';

function check_old_bookings(): void
{
    $args = [
        'post_type' => 'calendar_booking',
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'ignore_sticky_posts' => 1,
    ];

    $posts = get_posts($args);

    foreach ($posts as $post) {

        $postmeta = get_post_meta($post->ID);
        $data = unserialize($postmeta[META_KEY][0], ['allowed_classes' => false]);

        $timeStart = explode(':', $data['_xtec-booking-start-time']);
        $timeEnd = explode(':', $data['_xtec-booking-finish-time']);
        $dateStart = explode('-', $data['_xtec-booking-start-date']);
        $dateEnd = explode('-', $data['_xtec-booking-finish-date']);

        $start_datetime_past_year = mktime((int)$timeStart[0], (int)$timeStart[1], 0, ((int)$dateStart[1] + 1), (int)$dateStart[0], ((int)$dateStart[2] + 1));
        $end_datetime_past_year = mktime((int)$timeEnd[0], (int)$timeEnd[1], 0, (int)$dateEnd[1], (int)$dateEnd[0], ((int)$dateEnd[2] + 1));

        if (time() > $end_datetime_past_year) {

            delete_post_meta($post->ID, META_KEY);
            wp_delete_post($post->ID);

        } elseif (time() > $start_datetime_past_year) {

            $start_datetime_past_year = mktime((int)$timeStart[0], (int)$timeStart[1], 0, (int)$dateStart[1], (int)$dateStart[0], ((int)$dateStart[2] + 1));
            $data['_xtec-booking-start-date'] = date('d-m-Y', $start_datetime_past_year);
            update_post_meta($post->ID, META_KEY, $data);

        }
    }
}
