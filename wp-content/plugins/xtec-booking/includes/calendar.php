<?php

const LOOP_LIMIT = 400; // Used in xtec_booking_get_event()

function xtec_booking_show_calendar_page( $shortcode = null ){

	$data = xtec_booking_get_events();

	$calendar = xtec_display_calendar( $data, $shortcode );

	if ( $shortcode == null ){

		print_r( $calendar );

	} else {

		return $calendar;
	}

}

function xtec_booking_calendar_libraries( $shortcode = false ){

	// BOOTSTRAP JS
	wp_register_script( 'bootstrap-js', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js' );
	wp_enqueue_script( 'bootstrap-js' );

	if ($shortcode === ''){
		// BOOTSTRAP CSS
		wp_enqueue_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
	} else {
		// CUSTOM JS AND CSS TO CALENDAR
		wp_register_script( 'booking-calendar-js', plugins_url() .
							'/xtec-booking/js/xtec-booking-calendar.js',
							array('jquery'),'1.1', true );
		wp_enqueue_script( 'booking-calendar-js' );
		wp_enqueue_style( 'calendar-booking-css', plugins_url() . '/xtec-booking/css/xtec-booking-calendar.css' );
	}

	// UNDERSCORE
	wp_register_script( 'underscore-js', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js' );
	wp_enqueue_script( 'underscore-js' );

	// CALENDAR
	wp_register_script( 'calendar-js', plugins_url() .
						'/xtec-booking/includes/vendor/calendar/js/calendar.min.js',
						array('jquery'),'1.1', true );
	wp_enqueue_script( 'calendar-js' );

	$language_locale = get_locale();

	if( strpos($language_locale,'_') === false ) {
		$language_locale = $language_locale.'-ES.js';
	} else {
		$language_locale = str_replace('_', '-', $language_locale).'.js';
	}

	// Add Catalan translation
	wp_register_script( 'calendar-language-js', plugins_url() .
						'/xtec-booking/includes/vendor/calendar/js/language/'.
						$language_locale, array('jquery'),'1.1', true );
	wp_enqueue_script( 'calendar-language-js' );
	wp_enqueue_style( 'calendar-css', plugins_url() . '/xtec-booking/includes/vendor/calendar/css/calendar.min.css' );

}

function xtec_booking_get_event( $eventID ){
	//GET POST BOOKING
	$post = get_post( $eventID );

	// GET AUTHOR BOOKING
	$user = get_userdata($post->post_author);

	// GET POST META
	$post_meta = get_post_meta( $eventID, '_xtec-booking-data' );

	// GET RESOURCE
	$resource = get_post( $post_meta[0][XTEC_BOOKING_RESOURCE_KEY] );

	$request = array(
		"title" 	=>	$post->post_title,
		"content"	=>	$post->post_content,
		"resource"	=>	$resource->post_title,
		"startDate"	=>	$post_meta[0][XTEC_BOOKING_START_DATE_KEY],
		"endDate"	=>	$post_meta[0][XTEC_BOOKING_FINISH_DATE_KEY],
		"startHour"	=>	$post_meta[0][XTEC_BOOKING_START_TIME_KEY],
		"endHour"	=>	$post_meta[0][XTEC_BOOKING_FINISH_TIME_KEY],
		"by"		=>	$user->user_nicename
	);

	return json_encode( $request );
}

// --- Global Helpers -----------------------------------------------
// Map a color to a CSS class
function xtec_map_color($color) {
    $map = [
        'red'        => 'event-important',
        'yellow'     => 'event-warning',
        'blue'       => 'event-info',
        'black'      => 'event-inverse',
        'green'      => 'event-success',
        'purple'     => 'event-special',
        'orange'     => 'event-orange',
        'pink'       => 'event-pink',
        'brown'      => 'event-brown',
        'light_blue' => 'event-lightBlue',
    ];
    return $map[$color] ?? 'grey';
}

// Shorten and sanitize a description
function xtec_format_description($content, $limit = 50) {
    $desc = strip_tags($content);
    if (strlen($desc) > $limit) {
        $desc = substr($desc, 0, $limit) . '...';
    }
    return esc_html($desc);
}

// Build a single event object
function xtec_build_event($post, $resource, $user, $inactive, $color, $data, $start, $end, $desc) {
    return [
        'id'    => $post->ID,
        'title' => trim($inactive . ' ' . $data[XTEC_BOOKING_START_TIME_KEY] . '-' .
                   $data[XTEC_BOOKING_FINISH_TIME_KEY] . ' ' .
                   ucfirst($resource->post_title) . '/' . $post->post_title .
                   ': ' . $desc . ' (' . $user . ')'),
        'class' => $color,
        'start' => $start * 1000,
        'end'   => $end * 1000
    ];
}

// Build a single-day event
function xtec_single_day_event($post, $resource, $user, $inactive, $color, $data) {
    $timeStart = explode(':', $data[XTEC_BOOKING_START_TIME_KEY]);
    $timeEnd   = explode(':', $data[XTEC_BOOKING_FINISH_TIME_KEY]);
    $dateStart = explode('-', $data[XTEC_BOOKING_START_DATE_KEY]);

    if (!isset($timeStart[1], $dateStart[2])) { return []; }

    $start = mktime((int)$timeStart[0], (int)$timeStart[1], 0, (int)$dateStart[1], (int)$dateStart[0], (int)$dateStart[2]);
    $end   = mktime((int)$timeEnd[0], (int)$timeEnd[1], 0, (int)$dateStart[1], (int)$dateStart[0], (int)$dateStart[2]);

    $desc = xtec_format_description($post->post_content);
    return [xtec_build_event($post, $resource, $user, $inactive, $color, $data, $start, $end, $desc)];
}

// Build recurring multi-day events
function xtec_multi_day_events($post, $resource, $user, $inactive, $color, $data) {
    $events = [];
    $timeStart = explode(':', $data[XTEC_BOOKING_START_TIME_KEY]);
    $timeEnd   = explode(':', $data[XTEC_BOOKING_FINISH_TIME_KEY]);
    $dateStart = explode('-', $data[XTEC_BOOKING_START_DATE_KEY]);
    $dateEnd   = explode('-', $data[XTEC_BOOKING_FINISH_DATE_KEY]);

    $day       = (int)$dateStart[0];
    $startDate = mktime((int)$timeStart[0], (int)$timeStart[1], 0, (int)$dateStart[1], $day, (int)$dateStart[2]);
    $endDate   = mktime((int)$timeEnd[0], (int)$timeEnd[1], 0, (int)$dateEnd[1], (int)$dateEnd[0], (int)$dateEnd[2]);

    $dayMap = [
        'Mon' => '_xtec-booking-day-monday',
        'Tue' => '_xtec-booking-day-tuesday',
        'Wed' => '_xtec-booking-day-wednesday',
        'Thu' => '_xtec-booking-day-thursday',
        'Fri' => '_xtec-booking-day-friday',
        'Sat' => '_xtec-booking-day-saturday',
        'Sun' => '_xtec-booking-day-sunday',
    ];

    $iteration = 0;
    while ($startDate <= $endDate && $iteration++ < LOOP_LIMIT) {
        $endCurrent = mktime((int)$timeEnd[0], (int)$timeEnd[1], 0, (int)$dateStart[1], $day, (int)$dateStart[2]);
        $weekday    = date('D', $startDate);

        if (!empty($dayMap[$weekday]) && !empty($data[$dayMap[$weekday]])) {
            $desc = xtec_format_description($post->post_content);
            $events[] = xtec_build_event($post, $resource, $user, $inactive, $color, $data, $startDate, $endCurrent, $desc);
        }

        $day++;
        $startDate = mktime((int)$timeStart[0], (int)$timeStart[1], 0, (int)$dateStart[1], $day, (int)$dateStart[2]);
    }

    return $events;
}

// Check if a post should be skipped
function xtec_should_skip_post($resource, $resourceStatus, $currentUser, $resourceID) {
    if ($resourceStatus === 'admin_users' && !in_array('administrator', (array)$currentUser->roles)) {
        return true;
    }
    if ($resourceID === 'no-events') { return true; }
    if ($resourceID !== false && !in_array($resource->ID, (array)$resourceID)) {
        return true;
    }
    return false;
}

// Generate events for a single post
function xtec_process_post_events($post, $resource, $user, $resourceStatus, $color) {
    $inactive = '';
    if ($resourceStatus === 'inactive') {
        $color    = 'grey';
        $inactive = '(' . __('Not available resource', 'xtec-booking') . ')';
    } else {
        $color = xtec_map_color($color);
    }

    $data = unserialize(get_post_meta($post->ID, '_xtec-booking-data', true)['_xtec-booking-data'][0] ?? '');
    if (!$data) { return []; }

    return ($data[XTEC_BOOKING_START_DATE_KEY] ?? '') === ($data[XTEC_BOOKING_FINISH_DATE_KEY] ?? '')
        ? xtec_single_day_event($post, $resource, $user, $inactive, $color, $data)
        : xtec_multi_day_events($post, $resource, $user, $inactive, $color, $data);
}

// --- Main Function ------------------------------------------------
function xtec_booking_get_events($resourceID = false) {
    $events       = [];
    $currentUser  = wp_get_current_user();
    $posts        = get_posts([
        'post_type'           => 'calendar_booking',
        'post_status'         => 'publish',
        'posts_per_page'      => -1,
        'ignore_sticky_posts' => 1
    ]);

    foreach ($posts as $post) {
        $author        = get_userdata($post->post_author);
        $userName      = $author->data->display_name ?: $author->data->user_login;
        $resourceIdMeta= get_post_meta($post->ID, XTEC_BOOKING_RESOURCE_KEY, true);
        $color         = get_post_meta($resourceIdMeta, '_xtec_resources_color', true);
        $resourceStatus= get_post_meta($resourceIdMeta, '_xtec_resources_status', true);
        $resource      = get_post($resourceIdMeta);

        if (!$resource || xtec_should_skip_post($resource, $resourceStatus, $currentUser, $resourceID)) {
            continue;
        }

        $canSeePrivate = array_intersect(['administrator','editor'], (array)$currentUser->roles);
        if ($resource->post_status === "publish" || ($resource->post_status === "private" && $canSeePrivate)) {
            $events = array_merge($events, xtec_process_post_events($post, $resource, $userName, $resourceStatus, $color));
        }
    }

    return json_encode($events);
}

// Map resource color and status into calendar CSS classes
function xtec_map_color_display($color, $status) {
    if ($status === 'inactive') { return 'grey'; }
    $map = [
        'red'        => 'event-important',
        'yellow'     => 'event-warning',
        'blue'       => 'event-info',
        'black'      => 'event-inverse',
        'green'      => 'event-success',
        'purple'     => 'event-special',
        'orange'     => 'event-orange',
        'pink'       => 'event-pink',
        'brown'      => 'event-brown',
        'light_blue' => 'event-lightBlue',
    ];
    return $map[$color] ?? 'all-resources';
}

// Check if user is admin or editor
function xtec_is_admin_or_editor($roles) {
    return in_array('administrator', (array)$roles->roles) || in_array('editor', (array)$roles->roles);
}

// --- Render calendar modal HTML ---------------------------------
function xtec_render_calendar_modal() {
    return '
    <div class="modal fade" id="events-modal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 id="modalTitle"><p><img id="xtec-booking-wait" src="../wp-admin/images/loading.gif"></p></h3>
                </div>
                <div class="" style="max-height: 500px;padding:20px;">
                    <div class="xtec_booking_titles">
                        <strong class="xtec_booking_color">'.__('Resource','xtec-booking').':</strong>
                        <span id="modalResource"></span>
                    </div>
                    <div class="xtec_booking_titles">
                        <div class="xtec_booking_titles_hour xtec_booking_float">
                            <strong class="xtec_booking_color">'.__('Start time','xtec-booking').':</strong>
                            <span id="modalStartHour"></span>
                        </div>
                        <div class="xtec_booking_titles_hour xtec_booking_float">
                            <strong class="xtec_booking_color">'.__('End Hour','xtec-booking').':</strong>
                            <span id="modalEndHour"></span>
                        </div>
                    </div>
                    <div class="xtec_booking_titles xtec_booking_clear">
                        <strong class="xtec_booking_color">'.__('Booking make by','xtec-booking').': </strong>
                        <span id="modalBy"></span>
                    </div>
                    <br>
                    <div class="xtec_booking_modal_content xtec_booking_clear">
                        <strong class="xtec_booking_color">'.__('Description','xtec-booking').':</strong>
                        <p id="modalContent"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="#" data-dismiss="modal" class="btn">'.__('Close','xtec-booking').'</a>
                </div>
            </div>
        </div>
    </div>';
}

// --- Render resources list --------------------------------------
function xtec_render_resources_list($posts, $roles, $shortcode) {
    $html = '';
    foreach ($posts as $post) {
        $color  = get_post_meta($post->ID, '_xtec_resources_color', true);
        $status = get_post_meta($post->ID, '_xtec_resources_status', true);

        // Restrict admin-only resources
        if ($status === 'admin_users' && !xtec_is_admin_or_editor($roles)) { continue; }

        $colorClass = xtec_map_color_display($color, $status);
        $checked = !empty($shortcode) ? '' : 'checked';
        $selectionClass = (isset($_GET['resource']) && $_GET['resource'] == $post->ID)
                            ? "xtec-booking-selected" : "xtec-booking-not-selected";

        $html .= '<div class="col-md-4">
                    <div class="'.$selectionClass.'">
                        <input id="resource-'.$post->ID.'" type="checkbox" name="resource-'.$post->ID.'"
                               '.$checked.' class="pull-left xtec-booking-check-resources">
                        <a href="javascript:void(0)" data-event-class="'.$colorClass.'" class="pull-left event '
                        .$colorClass.' xtec-bullet-resource-list" title=""></a>
                        &nbsp;&nbsp;'.$post->post_title.'
                    </div>
                  </div>';
    }
    return $html;
}

// --- Main calendar display function -----------------------------
function xtec_display_calendar($data, $shortcode) {
    $roles = wp_get_current_user();

    // Load all resources
    $args = [
        'post_type'      => 'calendar_resources',
        'post_per_pages' => -1,
        'nopaging'       => true,
        'orderby'        => 'post_title',
        'order'          => 'ASC'
    ];
    $posts = get_posts($args);

    // Add private resources if user is admin or editor
    if (xtec_is_admin_or_editor($roles)) {
        $args['post_status'] = 'private';
        $postsAdmins = get_posts($args);
        if (count($postsAdmins) > 0) { $posts = array_merge($posts, $postsAdmins); }
    }

    // Initialize calendar scripts
    $calendar = '<script>';
    if (!empty($shortcode)) {
        $calendar .= 'var events = [];';
    } else {
        $calendar .= 'var events = '.$data.';
                      var unselectResources = "'.__('Unselect resources','xtec-booking').'";
                      var selectResources = "'.__('Select resources','xtec-booking').'";';
    }
    $calendar .= 'var tmplsCalendar = "'.plugins_url().'/xtec-booking/includes/vendor/calendar/tmpls/";';
    $calendar .= '</script>';

    // Modal for non-shortcode view
    if (empty($shortcode)) { $calendar .= xtec_render_calendar_modal(); }

    // Calendar container
    $calendar .= '<div class="wrap">';
    $calendar .= !empty($shortcode)
        ? '<div class="page-header" style="min-width:250px;margin:0px;border:0px"><h3 style="font-size:22px;margin:20px 0 0 0;"></h3></div>'
        : '<div class="page-header" style="margin-left:50px;max-width:92%;">...buttons & navigation...</div>'; // shortened for clarity

    // Resources section
    if (count($posts) === 0) {
        $calendar .= __('Resources','xtec-booking').' - <span class="xtec-booking-error">'.__('Not resources available','xtec-booking').'</span>';
    } else {
        $calendar .= __('Resources','xtec-booking').'<span class="dashicons dashicons-arrow-down xtec-booking-collapse xtec-booking-unstyle-link click_cursor"></span>';
        $calendar .= '<div id="collapse1" class="panel-collapse collapse xtec-row-panel">'.xtec_render_resources_list($posts, $roles, $shortcode).'</div>';
    }

    // Calendar container + spinner
    $calendar .= !empty($shortcode)
        ? '<div id="xtec_calendar_pos"><div id="xtec_calendar_wait" class="div_wait_ajax_booking display_div_wait"><center><img id="xtec-booking-wait" src="../wp-admin/images/wpspin_light-2x.gif"></center></div><div id="xtec_calendar" style="min-width:252px;min-height:290px;"></div></div>'
        : '<div id="xtec_calendar_pos"><div id="xtec_calendar_wait" class="div_wait_ajax display_div_wait"><center><img id="xtec-booking-wait" src="../wp-admin/images/wpspin_light-2x.gif"></center></div><div id="xtec_calendar" style="margin-left:50px;max-width:92%;"></div></div>';

    $calendar .= '</div>'; // close wrap
    return $calendar;
}
