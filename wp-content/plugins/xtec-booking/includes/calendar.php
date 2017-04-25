<?php

const LOOP_LIMIT = 200; // Used in xtec_booking_get_event()

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

	if ( is_null( $shortcode ) or $shortcode == '' ){
		// BOOTSTRAP CSS
		wp_enqueue_style( 'bootstrap-css', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css' );
	} else {
		// CUSTOM JS AND CSS TO CALENDAR
		wp_register_script( 'booking-calendar-js', plugins_url() . '/xtec-booking/js/xtec-booking-calendar.js', array('jquery'),'1.1', true );
		wp_enqueue_script( 'booking-calendar-js' );
		wp_enqueue_style( 'calendar-booking-css', plugins_url() . '/xtec-booking/css/xtec-booking-calendar.css' );
	}

	// UNDERSCORE
	wp_register_script( 'underscore-js', '//cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js' );
	wp_enqueue_script( 'underscore-js' );

	// CALENDAR
	wp_register_script( 'calendar-js', plugins_url() . '/xtec-booking/includes/vendor/calendar/js/calendar.min.js', array('jquery'),'1.1', true );
	wp_enqueue_script( 'calendar-js' );

	$language_locale = get_locale();

	if( strpos($language_locale,'_') === false ) {
		$language_locale = $language_locale.'-ES.js';
	} else {
		$language_locale = str_replace('_', '-', $language_locale).'.js';
	}

	// Add Catalan translation
	wp_register_script( 'calendar-language-js', plugins_url() . '/xtec-booking/includes/vendor/calendar/js/language/'.$language_locale, array('jquery'),'1.1', true );
	wp_enqueue_script( 'calendar-language-js' );
	wp_enqueue_style( 'calendar-css', plugins_url() . '/xtec-booking/includes/vendor/calendar/css/calendar.min.css' );

}

function xtec_booking_get_event( $eventID ){

	global $wpdb;

	//GET POST BOOKING
	$post = get_post( $eventID );

	// GET AUTHOR BOOKING
	$user = get_userdata($post->post_author);

	// GET POST META
	$post_meta = get_post_meta( $eventID, '_xtec-booking-data' );

	// GET RESOURCE
	$resource = get_post( $post_meta[0]['_xtec-booking-resource'] );

	$request = array(
		"title" 	=>	$post->post_title,
		"content"	=>	$post->post_content,
		"resource"	=>	$resource->post_title,
		"startDate"	=>	$post_meta[0]['_xtec-booking-start-date'],
		"endDate"	=>	$post_meta[0]['_xtec-booking-finish-date'],
		"startHour"	=>	$post_meta[0]['_xtec-booking-start-time'],
		"endHour"	=>	$post_meta[0]['_xtec-booking-finish-time'],
		"by"		=>	$user->user_nicename
	);

	return json_encode( $request );
}

function xtec_booking_get_events( $resourceID = false ){

	global $wpdb;

	$numCharacters = 50;
	$events = array();
	$args = array(
		'post_type'   			=> 'calendar_booking',
		'post_status' 			=> 'publish',
		'posts_per_page' 		=> -1,
  		'ignore_sticky_posts'	=> 1
	);

	$posts = get_posts( $args );

	foreach ( $posts as $post ) {

		$postmeta = get_post_meta( $post->ID );
		$user = get_userdata( $post->post_author );

		$roles = wp_get_current_user();

		if ( $user->data->display_name == '' ){ $user = $user->data->user_login; } else { $user = $user->data->display_name; }

		$color = get_post_meta( $postmeta['_xtec-booking-resource'][0], '_xtec_resources_color', true );
		$ResourceStatus = get_post_meta( $postmeta['_xtec-booking-resource'][0], '_xtec_resources_status', true );
		$resource = get_post( $postmeta['_xtec-booking-resource'][0] );

		if ( $ResourceStatus == 'admin_users' ){
			if ( ! in_array( 'administrator', (array) $roles->roles ) ){
				continue;
			}
		}

		if ( ( ( $resourceID == false ) || in_array( $resource->ID, (array) $resourceID ) ) && $resourceID != 'no-events' ){

			if ( $resource->post_status == "publish" || ( ( $resource->post_status == "private" ) && ( in_array( 'administrator', (array) $roles->roles ) or in_array( 'editor', (array) $roles->roles ) ) ) ){

				if( $ResourceStatus == 'inactive' ){

					$color = 'grey';
					$InactiveResource = '('.__('Not available resource','xtec-booking').')';

				} else {

					switch ( $color ) {
						case 'red': $color = 'event-important'; break;
						case 'yellow': $color = 'event-warning'; break;
						case 'blue': $color = 'event-info'; break;
						case 'black': $color = 'event-inverse'; break;
						case 'green': $color = 'event-success'; break;
						case 'purple': $color = 'event-special'; break;
						case 'orange': $color = 'event-orange'; break;
						case 'pink': $color = 'event-pink'; break;
						case 'brown': $color = 'event-brown'; break;
						case 'light_blue': $color = 'event-lightBlue'; break;
						default: $color = 'grey'; break;
					}
					$InactiveResource = '';

				}

				$data = unserialize( $postmeta['_xtec-booking-data'][0] );

				if ( $data['_xtec-booking-start-date'] == $data['_xtec-booking-finish-date'] ){

					$timeStart = explode( ':',$data['_xtec-booking-start-time'] );
					$timeEnd = explode( ':',$data['_xtec-booking-finish-time'] );

					$dateStart = explode( '-',$data['_xtec-booking-start-date'] );
					$dateEnd = explode( '-',$data['_xtec-booking-finish-date'] );

					$startDateTime = mktime( $timeStart[0],$timeStart[1],0,$dateStart[1],$dateStart[0],$dateStart[2] );
					$endDateTime = mktime( $timeEnd[0],$timeEnd[1],0,$dateEnd[1],$dateEnd[0],$dateEnd[2] );

					$description = strip_tags( $post->post_content );
					if ( strlen( $description ) > 0 ){
						if ( strlen( $description ) > $numCharacters ){
							$description = substr( $description,0,$numCharacters )."...";
						}
					}

					$description = esc_html($description);

					$event = array(
						'id'	=> $post->ID,
						'title'	=> $InactiveResource.' '.$data['_xtec-booking-start-time'].'-'.$data['_xtec-booking-finish-time'].' '.ucfirst($resource->post_title).'/'.$post->post_title.': '.$description.' ('.$user.')',
						'class' => $color,
						'start' => ($startDateTime*1000),
						'end'	=> ($endDateTime*1000)
					);

					array_push( $events,$event );

				} else {

					$timeStart = explode( ':',$data['_xtec-booking-start-time'] );
					$timeEnd = explode( ':',$data['_xtec-booking-finish-time'] );

					$dateStart = explode( '-',$data['_xtec-booking-start-date'] );
					$dateEnd = explode( '-',$data['_xtec-booking-finish-date'] );

					$day = $dateStart[0];

					$startDateTime = mktime( $timeStart[0],$timeStart[1],0,$dateStart[1],$dateStart[0],$dateStart[2] );
					$endDateTime = mktime( $timeEnd[0],$timeEnd[1],0,$dateEnd[1],$dateEnd[0],$dateEnd[2] );

					$iteration = 0; // Set a limit to the number of iterations to avoid too-big-loops when viewing the calendar
					while (( $startDateTime <= $endDateTime ) && ( $iteration++ < LOOP_LIMIT )) {

						$startEndDateTime = mktime( $timeEnd[0],$timeEnd[1],0,$dateStart[1],$day,$dateStart[2] );

						$nDay = date( 'D',$startDateTime );
						$addEvent = false;

						switch ($nDay) {
							case 'Mon': if ( isset($data['_xtec-booking-day-monday']) && $data['_xtec-booking-day-monday'] == true ){ $addEvent = true; } break;
							case 'Tue': if ( isset($data['_xtec-booking-day-tuesday']) && $data['_xtec-booking-day-tuesday'] == true ){ $addEvent = true; } break;
							case 'Wed': if ( isset($data['_xtec-booking-day-wednesday']) && $data['_xtec-booking-day-wednesday'] == true ){ $addEvent = true; } break;
							case 'Thu': if ( isset($data['_xtec-booking-day-thursday']) && $data['_xtec-booking-day-thursday'] == true ){ $addEvent = true; } break;
							case 'Fri': if ( isset($data['_xtec-booking-day-friday']) && $data['_xtec-booking-day-friday'] == true ){ $addEvent = true; } break;
							case 'Sat': if ( isset($data['_xtec-booking-day-saturday']) && $data['_xtec-booking-day-saturday'] == true ){ $addEvent = true; } break;
							case 'Sun': if ( isset($data['_xtec-booking-day-sunday']) && $data['_xtec-booking-day-sunday'] == true ){ $addEvent = true; } break;
							default: $addEvent = false; break;
						}

						if ( $addEvent == true ){

							$description = strip_tags( $post->post_content );
							if ( strlen( $description ) > 0 ){
								if ( strlen( $description ) > $numCharacters ){
									$description = substr( $description,0,$numCharacters )."...";
								}
							}

							$description = esc_html($description);

							$event = array(
								'id'	=> $post->ID,
								'title'	=> $InactiveResource.' '.$data['_xtec-booking-start-time'].'-'.$data['_xtec-booking-finish-time'].' '.ucfirst($resource->post_title).'/'.$post->post_title.': '.$description.' ('.$user.')',
								'class' => $color,
								'start' => ( $startDateTime*1000 ),
								'end'	=> ( $startEndDateTime*1000 )
							);

							array_push( $events,$event );

						}

						$day++;
						$startDateTime = mktime( $timeStart[0],$timeStart[1],0,$dateStart[1],$day,$dateStart[2] );

					}
				}
			}
		}
	}

	return json_encode( $events );

}

function xtec_display_calendar($data,$shortcode){

	$roles = wp_get_current_user();

	$args = array(
		'post_type'   		=> 'calendar_resources',
		'post_per_pages'	=> -1,
		'nopaging' 			=> true,
		'orderby' 			=> 'post_title',
		'order' 			=> 'ASC'
	);

	$posts = get_posts( $args );

	if ( in_array( 'administrator', (array) $roles->roles ) || in_array( 'editor', (array) $roles->roles ) ){

		$args = array(
			'post_type'  	=> 'calendar_resources',
			'post_per_pages'=> -1,
			'nopaging' 		=> true,
			'post_status' 	=> 'private',
			'orderby' 		=> 'post_title',
			'order' 		=> 'ASC'
		);

		$postsAdmins = get_posts( $args );

		if( count( $postsAdmins ) > 0 ){
			$posts = array_merge( $posts,$postsAdmins );
		}

	}

	if ( ( ! is_null( $shortcode ) ) and $shortcode != '' ){
			$calendar = "<script>
			var events = [];";
	} else {
		$calendar = '<script>
			var events = '. $data .';
			var unselectResources = "'. __('Unselect resources','xtec-booking') .'";
			var selectResources = "'. __('Select resources','xtec-booking') .'";';
	}

	$calendar .= '
			var tmplsCalendar = "' . plugins_url() . '/xtec-booking/includes/vendor/calendar/tmpls/";
		</script>';

	if ( is_null( $shortcode ) or $shortcode == '' ){

		$calendar .= '
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
			            		<span  id="modalBy"></span>
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

	$calendar .=	'<div class="wrap">';

	if ( ( ! is_null( $shortcode ) ) and $shortcode != '' ){

		$calendar .= '<div class="page-header" style="min-width:250px;margin:0px;border:0px">
			<h3 style="font-size:22px;margin:20px 0 0 0;></h3>
		</div>
		<div style="overflow:hidden;visibility:hidden;height:0px;">
			<div class="panel panel-default">
				<div class="panel-heading">
			      <h4 class="panel-title click_cursor" data-toggle="collapse" href="#collapse1" style="visibility:hidden;height:0px;">';

	} else {

	$calendar .= '<div class="page-header" style="margin-left:50px;max-width:92%;">
			<div class="pull-right form-inline">
				<div class="btn-group">
					<button class="btn btn-sm btn-primary" data-calendar-nav="prev"><< '. __('Prev','xtec-booking') .'</button>
					<button class="btn btn-sm btn-primary" data-calendar-nav="today"><u> '. __('Today','xtec-booking') .'</u></button>
					<button class="btn btn-sm btn-primary" data-calendar-nav="next">'. __('Next','xtec-booking') .' >></button>
				</div>
				<div class="btn-group">
					<button class="btn btn-sm btn-warning" data-calendar-view="year">'. __('Year','xtec-booking') .'</button>
					<button class="btn btn-sm btn-warning active" data-calendar-view="month">'. __('Month','xtec-booking') .'</button>
					<button class="btn btn-sm btn-warning" data-calendar-view="week">'. __('Week','xtec-booking') .'</button>
					<button class="btn btn-sm btn-warning" data-calendar-view="day">'. __('Day','xtec-booking') .'</button>
				</div>
			</div>
			<h3></h3>
		</div>
		<br>
		<div class="panel-group" style="margin-left:50px;max-width:92%;overflow:hidden">
			<div class="panel panel-default">
				<div class="panel-heading">
			      <h4 class="panel-title click_cursor" data-toggle="collapse" href="#collapse1">';

	}

	if( count( $posts ) == 0 ){

		$calendar .= __('Resources','xtec-booking').' - <span class="xtec-booking-error">'.__('Not resources available','xtec-booking').'</span>';

	} else {

		$resource = isset($_GET['resource'])?get_post( $_GET['resource'] ):new stdClass();

		$calendar .= __('Resources','xtec-booking').'<span class="dashicons dashicons-arrow-down xtec-booking-collapse xtec-booking-unstyle-link click_cursor"></span>';

		if ( ! isset( $_GET['resource'] ) ){
			$class = "xtec-booking-selected";
		} else {
			$class = "xtec-booking-not-selected";
		}

		if ( ( ! is_null( $shortcode ) ) and $shortcode != '' ){
			$calendar .=      '</h4>
						    </div>
						    <div id="collapse1" class="panel-collapse collapse xtec-row-panel" style="visibility:hidden;height:0px;">';
		} else {
			$calendar .=      '</h4>
						    </div>
						    <div id="collapse1" class="panel-collapse collapse xtec-row-panel">';
		}

		$calendar .= 	'<div class="row">
							<div class="col-md-12">
								<input id="xtec_selection" data-action="unselect" type="button" class="btn btn-primary xtec-button-unselect" value="'.__('Unselect resources','xtec-booking').'">
							</div>
						</div>';

		$i = 0;

		foreach ( $posts as $post ) {

			$color = get_post_meta( $post->ID, '_xtec_resources_color', true );
			$status = get_post_meta( $post->ID, '_xtec_resources_status', true );

			$roles = wp_get_current_user();
			if( $status == 'admin_users' ){
				if ( ( ! in_array( 'administrator', (array) $roles->roles ) || ( ! in_array( 'editor', (array) $roles->roles ) ) ) ){
					continue;
				}
			}

			if( $status == 'inactive' ){

				$color = 'grey';

			} else {

				switch ( $color ) {
					case 'red': $color = 'event-important'; break;
					case 'yellow': $color = 'event-warning'; break;
					case 'blue': $color = 'event-info'; break;
					case 'black': $color = 'event-inverse'; break;
					case 'green': $color = 'event-success'; break;
					case 'purple': $color = 'event-special'; break;
					case 'orange': $color = 'event-orange'; break;
					case 'pink': $color = 'event-pink'; break;
					case 'brown': $color = 'event-brown'; break;
					case 'light_blue': $color = 'event-lightBlue'; break;
					default: $color = 'all-resources'; break;
				}

			}

			if ( isset( $_GET['resource'] ) && $_GET['resource'] == $post->ID ){
				$class = "xtec-booking-selected";
			} else {
				$class = 'xtec-booking-not-selected';
			}

			$i++;

			if ( ( ! is_null( $shortcode ) ) and $shortcode != '' ){
				$calendar .= '<div class="col-md-4">
						<div class="'.$class.'">
							<input id="resource-'.$post->ID.'" type="checkbox" name="resource-'.$post->ID.'" class="pull-left xtec-booking-check-resources">
							<a href="javascript:void(0)" data-event-class="'.$color.'" class="pull-left event '.$color.' xtec-bullet-resource-list" title=""></a>&nbsp;&nbsp;'.$post->post_title.'
						</div>
					  </div>';
			} else {
				$calendar .= '<div class="col-md-4">
							<div class="'.$class.'">
								<input id="resource-'.$post->ID.'" type="checkbox" name="resource-'.$post->ID.'" checked class="pull-left xtec-booking-check-resources">
								<a href="javascript:void(0)" data-event-class="'.$color.'" class="pull-left event '.$color.' xtec-bullet-resource-list" title=""></a>&nbsp;&nbsp;'.$post->post_title.'
							</div>
						  </div>';
			}
		}
	}

	$calendar .= '</div></form></div></div>
			<br>
			<div id="xtec_calendar_pos">';

	if ( ( ! is_null( $shortcode ) ) and $shortcode != '' ){
		$calendar .= '<div id="xtec_calendar_wait" class="div_wait_ajax_booking display_div_wait">
						<center><img id="xtec-booking-wait" src="../wp-admin/images/wpspin_light-2x.gif"></center>
					</div>
					<div class="clearfix"></div>
					<div id="xtec_calendar" style="min-width:252px;min-height:290px;"></div>
					</div>
					<div>
					<div class="pull-right form-inline" style="margin:5px 0px 0px 0px;">
						<div class="btn-group">
							<button class="button button-primary button-small" data-calendar-nav="prev"><< '. __('Prev','xtec-booking') .'</button>
							<button class="button button-primary button-small" data-calendar-nav="today"><u> '. __('Today','xtec-booking') .'</u></button>
							<button class="button button-primary button-small" data-calendar-nav="next">'. __('Next','xtec-booking') .' >></button>
						</div>
					</div>
					<br>';
	} else {
		$calendar .= '<div id="xtec_calendar_wait" class="div_wait_ajax display_div_wait">
					<center><img id="xtec-booking-wait" src="../wp-admin/images/wpspin_light-2x.gif"></center>
				</div><div id="xtec_calendar" style="margin-left:50px;max-width:92%;"></div>';
	}

	$calendar .= '</div>
			</div>';

	return $calendar;
}
