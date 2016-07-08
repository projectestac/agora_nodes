<?php

// Check available dates
function check_avalailable_dates( $data,$post_id ){

	if ( $_POST['visibility'] != 'public' || $_POST['post_status'] == 'draft' || $_POST['post_status'] == 'pending' ){

		$available = true;
		return $available;

	} else {

		global $wpdb;

		$available = true;
		$conflictBookings = array();

		$result = $wpdb->get_results("SELECT * FROM wp_postmeta WHERE post_id != ".$post_id." AND post_id IN (SELECT post_id FROM wp_postmeta WHERE meta_value = ".$data['_xtec-booking-resource'].") AND meta_key = '_xtec-booking-data'");

		// TIMESTAMP DATES NEW BOOKING
		$dataStart = mktime(0,0,0, substr($data['_xtec-booking-start-date'],3,2), substr($data['_xtec-booking-start-date'],0,2), substr($data['_xtec-booking-start-date'],6,4));
		$dataFinish = mktime(0,0,0, substr($data['_xtec-booking-finish-date'],3,2), substr($data['_xtec-booking-finish-date'],0,2), substr($data['_xtec-booking-finish-date'],6,4));

		foreach ($result as $key ) {

			$visibilityPost = get_post($key->post_id);

			if( $visibilityPost->post_status != 'publish' ){
				continue;
			}

			$meta_value = unserialize($key->meta_value);

			// CONVERT TO TIMESTAMP DATES OLD BOOKINGS
			$meta_valueStart = mktime(0,0,0, substr($meta_value['_xtec-booking-start-date'],3,2), substr($meta_value['_xtec-booking-start-date'],0,2), substr($meta_value['_xtec-booking-start-date'],6,4));
			$meta_valueFinish = mktime(0,0,0, substr($meta_value['_xtec-booking-finish-date'],3,2), substr($meta_value['_xtec-booking-finish-date'],0,2), substr($meta_value['_xtec-booking-finish-date'],6,4));

			// CHECK AVAILABLE DATES
			if ( ( $dataStart >= $meta_valueStart && $dataStart <= $meta_valueFinish ) || ( $dataFinish >= $meta_valueStart && $dataFinish <= $meta_valueFinish ) ||  ( $dataStart <= $meta_valueStart && $dataFinish >= $meta_valueFinish ) ){

			 	$day = false;

			 	// CHECK AVAILABLE DAYS
			 	if ( ( $meta_valueStart == $dataStart ) && ( $meta_valueFinish == $dataFinish ) && ( $dataStart == $dataFinish ) ){

			 		$day = true;

			 	} else if ( $meta_valueStart == $meta_valueFinish ){

			 		$checkDay = strtoupper( substr( date( 'D', $meta_valueStart ),0 ,2) );

			 		switch ($checkDay) {
			 			case 'MO': if ( $data['_xtec-booking-day-monday'] == 'true' ){ $day = true; } break;
			 			case 'TU': if ( $data['_xtec-booking-day-tuesday'] == 'true' ){ $day = true; } break;
			 			case 'WE': if ( $data['_xtec-booking-day-wednesday'] == 'true' ){ $day = true; } break;
			 			case 'TH': if ( $data['_xtec-booking-day-thursday'] == 'true' ){ $day = true; } break;
			 			case 'FR': if ( $data['_xtec-booking-day-friday'] == 'true' ){ $day = true; } break;
			 			case 'SA': if ( $data['_xtec-booking-day-saturday'] == 'true' ){ $day = true; } break;
			 			case 'SU': if ( $data['_xtec-booking-day-sunday'] == 'true' ){ $day = true; } break;
			 		}

			 	} else if ( $dataStart == $dataFinish ){

			 		$checkDay = strtoupper( substr( date( 'D', $dataStart ),0 ,2) );

			 		switch ($checkDay) {
			 			case 'MO': if ( $meta_value['_xtec-booking-day-monday'] == 'true' ){ $day = true; } break;
			 			case 'TU': if ( $meta_value['_xtec-booking-day-tuesday'] == 'true' ){ $day = true; } break;
			 			case 'WE': if ( $meta_value['_xtec-booking-day-wednesday'] == 'true' ){ $day = true; } break;
			 			case 'TH': if ( $meta_value['_xtec-booking-day-thursday'] == 'true' ){ $day = true; } break;
			 			case 'FR': if ( $meta_value['_xtec-booking-day-friday'] == 'true' ){ $day = true; } break;
			 			case 'SA': if ( $meta_value['_xtec-booking-day-saturday'] == 'true' ){ $day = true; } break;
			 			case 'SU': if ( $meta_value['_xtec-booking-day-sunday'] == 'true' ){ $day = true; } break;
			 		}

			 	} else {

			 		if ( $data['_xtec-booking-day-monday'] == $meta_value['_xtec-booking-day-monday'] && $meta_value['_xtec-booking-day-monday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-tuesday'] == $meta_value['_xtec-booking-day-tuesday'] && $meta_value['_xtec-booking-day-tuesday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-wednesday'] == $meta_value['_xtec-booking-day-wednesday'] && $meta_value['_xtec-booking-day-wednesday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-thursday'] == $meta_value['_xtec-booking-day-thursday'] && $meta_value['_xtec-booking-day-thursday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-friday'] == $meta_value['_xtec-booking-day-friday'] && $meta_value['_xtec-booking-day-friday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-saturday'] == $meta_value['_xtec-booking-day-saturday'] && $meta_value['_xtec-booking-day-saturday'] == 'true' ){ $day = true; }
					if ( $data['_xtec-booking-day-sunday'] == $meta_value['_xtec-booking-day-sunday'] && $meta_value['_xtec-booking-day-sunday'] == 'true' ){ $day = true; }

			 	}

			 	// CHECK AVAILABLES HOURS
			 	if ( $day == true ){

			 		$dataStartTime = strtotime( $data['_xtec-booking-start-time'] );
			 		$dataEndTime = strtotime( $data['_xtec-booking-finish-time'] );
			 		$meta_valueStartTime = strtotime( $meta_value['_xtec-booking-start-time'] );
			 		$meta_valueEndTime = strtotime( $meta_value['_xtec-booking-finish-time'] );

					if ( $dataStartTime == $meta_valueStartTime ){

						array_push( $conflictBookings,array( 'title' => $meta_value['post_title'] ) );

						$available = false;

					} else if ( ( $dataStartTime >= $meta_valueStartTime ) && ( $dataStartTime <= $meta_valueEndTime ) || ( $dataEndTime >= $meta_valueStartTime ) && ( $dataEndTime <= $meta_valueEndTime ) || ( $dataStartTime <= $meta_valueStartTime ) && ( $dataEndTime >= $meta_valueEndTime ) ){

						if ( ( $dataEndTime != $meta_valueStartTime ) && ( $dataStartTime != $meta_valueEndTime ) ){

							array_push( $conflictBookings, array( 'title' => $meta_value['post_title'] ) );

							$available = false;

						}
					}
				}
			}
		}

		return $available;

	}
}

// CAPABILITIES TO ROLES
function xtec_booking_active_plugin(){

	// Add capabilities to role Administrator
	$roleAdmin = get_role('administrator');
    $roleAdmin->add_cap('edit_posts_bookings');
    $roleAdmin->add_cap('delete_posts_bookings');
    $roleAdmin->add_cap('delete_pages_bookings');
    $roleAdmin->add_cap('publish_posts_bookings');

    // Add capabilities to role Editor
	$roleEditor = get_role('editor');
	$roleEditor->add_cap('edit_posts_bookings');
    $roleEditor->add_cap('delete_posts_bookings');
    $roleEditor->add_cap('delete_pages_bookings');
    $roleEditor->add_cap('publish_posts_bookings');

	// Add capabilities to role Author
	$roleAuthor = get_role('author');
	$roleAuthor->add_cap('delete_pages_bookings');
	$roleAuthor->add_cap('edit_posts_bookings');
    $roleAuthor->add_cap('delete_posts_bookings');
    $roleAuthor->add_cap('publish_posts_bookings');
    $roleAuthor->add_cap('delete_pages');

    // Add capabilities to role Teacher
	$roleTeacher = get_role('xtec_teacher');
	if ( ! is_null( $roleTeacher ) ){
		$roleTeacher->add_cap('delete_pages_bookings');
		$roleTeacher->add_cap('edit_posts_bookings');
	    $roleTeacher->add_cap('delete_posts_bookings');
	    $roleTeacher->add_cap('publish_posts_bookings');
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

    // Remove capabilities to role Editor
	$roleEditor = get_role('editor');
	$roleEditor->remove_cap('edit_posts_bookings');
    $roleEditor->remove_cap('delete_posts_bookings');
    $roleEditor->remove_cap('delete_pages_bookings');
    $roleEditor->remove_cap('publish_posts_bookings');

	// Remove capabilities to role Author
	$roleAuthor = get_role('author');
	$roleAuthor->remove_cap('delete_pages_bookings');
	$roleAuthor->remove_cap('edit_posts_bookings');
    $roleAuthor->remove_cap('delete_posts_bookings');
    $roleAuthor->remove_cap('publish_posts_bookings');
    $roleAuthor->remove_cap('delete_pages');

    // Add capabilities to role Teacher
	$roleTeacher = get_role('xtec_teacher');
	if ( ! is_null( $roleTeacher ) ){
		$roleTeacher->remove_cap('delete_pages_bookings');
		$roleTeacher->remove_cap('edit_posts_bookings');
	    $roleTeacher->remove_cap('delete_posts_bookings');
	    $roleTeacher->remove_cap('publish_posts_bookings');
	}

	// Add capabilities to role Contributor
	$roleContributor = get_role('contributor');
	$roleContributor->remove_cap('delete_pages_bookings');
	$roleContributor->remove_cap('delete_pages');

}