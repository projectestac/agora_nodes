<?php

function check_old_bookings() {

	$args = array(
		'post_type'   			=> 'calendar_booking',
		'post_status' 			=> 'publish',
		'posts_per_page' 		=> -1,
  		'ignore_sticky_posts'	=> 1
	);

	$posts = get_posts( $args );

	foreach ( $posts as $post ) {

		$postmeta = get_post_meta( $post->ID );
		$data = unserialize( $postmeta['_xtec-booking-data'][0] );

		$timeStart = explode( ':',$data['_xtec-booking-start-time'] );
		$timeEnd = explode( ':',$data['_xtec-booking-finish-time'] );
		$dateStart = explode( '-',$data['_xtec-booking-start-date'] );
		$dateEnd = explode( '-',$data['_xtec-booking-finish-date'] );

		$StartDateTimePastYear = mktime( $timeStart[0],$timeStart[1],0,($dateStart[1]+1),$dateStart[0],($dateStart[2]+1) );
		$EndDateTimePastYear = mktime( $timeEnd[0],$timeEnd[1],0,$dateEnd[1],$dateEnd[0],($dateEnd[2]+1) );

		if( time() > $EndDateTimePastYear ){
			delete_post_meta( $post->ID, '_xtec-booking-data' );
			wp_delete_post( $post->ID );
		} else if( time() > $StartDateTimePastYear ){
			$StartDateTimePastYear = mktime( $timeStart[0],$timeStart[1],0,$dateStart[1],$dateStart[0],($dateStart[2]+1) );
			$data['_xtec-booking-start-date'] = date('d-m-Y',$StartDateTimePastYear);
			update_post_meta( $post->ID, '_xtec-booking-data', $data );
		}
	}
}
