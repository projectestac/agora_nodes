<?php

/**
 * Upgrade functions
 *
 * @package   GCE
 * @author    Phil Derksen <pderksen@gmail.com>, Nick Young <mycorpweb@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2014 Phil Derksen
 */

// I put the priority to 20 here so it runs after the gce_feed CPT code and we don't get errors
add_action( 'init', 'gce_upgrade', 20 );

/**
 * Main GCE Upgrade function. Call this and branch of from here depending on what we need to do
 * 
 * @since 2.0.0
 */
function gce_upgrade() {
	
	$version = get_option( 'gce_version' );
	
	if( ! empty( $version ) ) {
		
		// Clear out cache when upgrading no matter the version
		gce_upgrade_clear_cache();
		
		// Check if under version 2 and run the v2 upgrade if we are
		if( version_compare( $version, '2.0.0-beta1', '<' ) && false === get_option( 'gce_upgrade_has_run' ) ) {
			gce_v2_upgrade();
		}
		
		// Version 2.0.4 upgrade
		if( version_compare( $version, '2.0.4', '<' ) ) {
			gce_v204_upgrade();
		}
		
		// Version 2.0.6 upgrade
		if( version_compare( $version, '2.0.6', '<' ) ) {
			gce_v206_upgrade();
		}
		
		if( version_compare( $version, '2.1.0', '<' ) ) {
			gce_v210_upgrade();
		}
	}
	
	$new_version = Google_Calendar_Events::get_instance()->get_plugin_version();
	update_option( 'gce_version', $new_version );
	
	add_option( 'gce_upgrade_has_run', 1 );
}

/*
 * Run the upgrade to version 2.1.0
 */
function gce_v210_upgrade() {
	
	$q = new WP_Query( 'post_type=gce_feed' );
	
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {
			$q->the_post();
			
			$url = get_post_meta( get_the_ID(), 'gce_feed_url', true );
			
			// https://www.google.com/calendar/feeds/umsb0ekhivs1a2ubtq6vlqvcjk%40group.calendar.google.com/public/basic
			
			$url = str_replace( 'https://www.google.com/calendar/feeds/', '', $url );
			$url = str_replace( '/public/basic', '', $url );
			$url = str_replace( '%40', '@', $url );
			
			update_post_meta( get_the_ID(), 'gce_feed_url', $url );
		}
	}
	
	wp_reset_postdata();
}

/*
 * Run the upgrade to version 2.0.6
 * 
 * @since 2.0.4
 */
function gce_v206_upgrade() {
	
	// Update feeds
	$q = new WP_Query( 'post_type=gce_feed' );
	
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {
			$q->the_post();
			
			update_post_meta( get_the_ID(), 'gce_feed_start', '1' );
			update_post_meta( get_the_ID(), 'gce_feed_start_interval', 'months' );
			update_post_meta( get_the_ID(), 'gce_feed_end', '2' );
			update_post_meta( get_the_ID(), 'gce_feed_end_interval', 'years' );
		}
	}
	
	wp_reset_postdata();
}

/*
 * Run the upgrade to version 2.0.4
 * 
 * @since 2.0.4
 */
function gce_v204_upgrade() {
	
	// Update feeds
	$q = new WP_Query( 'post_type=gce_feed' );
	
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {
			$q->the_post();
			
			update_post_meta( get_the_ID(), 'gce_paging', '1' );
			update_post_meta( get_the_ID(), 'gce_list_max_num', '7' );
			update_post_meta( get_the_ID(), 'gce_list_max_length', 'days' );
			update_post_meta( get_the_ID(), 'gce_list_start_offset_num', '0' );
			update_post_meta( get_the_ID(), 'gce_list_start_offset_direction', 'back' );
		}
	}
	
	wp_reset_postdata();
	

	// Update widgets
	$widget = get_option( 'widget_gce_widget' );
	
	if( is_array( $widget ) && ! empty( $widget ) ) {
		foreach( $widget as $a => $b ) {
			if( ! is_array( $b ) ) {
				continue;
			} 

			foreach( $b as $k => $v ) {
				$widget[$a]['paging']                      = '1';
				$widget[$a]['list_max_num']                = '7';
				$widget[$a]['list_max_length']             = 'days';
				$widget[$a]['list_start_offset_num']       = '0';
				$widget[$a]['list_start_offset_direction'] = 'back';
			}
		}
		
		update_option( 'widget_gce_widget', $widget );
	}
}

/*
 * Run the upgrade to version 2.0.0
 * 
 * @since 2.0.0
 */
function gce_v2_upgrade() {
	$old_options = get_option( 'gce_options' );
	
	if( false !== $old_options ) {
		
		if( ! empty( $old_options ) ) {
			foreach( $old_options as $key => $value ) {
				convert_to_cpt_posts( $value );
			}
		}

		update_widget_feed_ids();
	}
}

/**
 * Converts the old database options to the new CPT layout for 2.0.0+
 * 
 * @since 2.0.0
 */
function convert_to_cpt_posts( $args ) {
	// Setup our new post
	$post = array(
			'post_name'      => $args['title'],
			'post_title'     => $args['title'],
			'post_status'    => 'publish',
			'post_type'      => 'gce_feed'
		);
	
	if( $args['use_builder'] == true ) {
		$post['post_content'] = $args['builder'];
	}
	
	$post_id = wp_insert_post( $post );
	
	create_cpt_meta( $post_id, $args );
	
	clear_old_transients( $args['id'] );
}

/**
 * Add the CPT post meta based on options set for the old feeds prior to v2
 * 
 * @since 2.0.0
 */
function create_cpt_meta( $id, $args ) {
	
	// Convert the dropdown values to the new values for "Retrieve Events From"
	switch( $args['retrieve_from'] ) {
		case 'now':
		case 'today':
			$from = 'today';
			break;
		case 'week':
			$from = 'start_week';
			break;
		case 'month-start':
			$from = 'start_month';
			break;
		case 'month-end':
			$from = 'end_month';
			break;
		case 'date':
			$from = 'custom_date';
			break;
		default: 
			$from = 'start_time';
			break;
	}
	
	// Convert the dropdown values to the new values for "Retrieve Events Until"
	switch( $args['retrieve_until'] ) {
		case 'now':
		case 'today':
			$until = 'today';
			break;
		case 'week':
			$until = 'start_week';
			break;
		case 'month-start':
			$until = 'start_month';
			break;
		case 'month-end':
			$until = 'end_month';
			break;
		case 'date':
			$until = 'custom_date';
			break;
		default: 
			$until = 'end_time';
			break;
	}
	
	$gce_expand_recurring = ( isset( $args['expand_recurring'] ) ? ( $args['expand_recurring'] == 'true' ? '1' : '0' ) : '1' );
	
	// An array to hold all of our post meta ids and values so that we can loop through and add as post meta easily
	$post_meta_fields = array(
		'gce_feed_url'         => $args['url'],
		'gce_retrieve_from'    => $from,
		'gce_retrieve_until'   => $until,
		'gce_retrieve_max'     => $args['max_events'],
		'gce_date_format'      => $args['date_format'],
		'gce_time_format'      => $args['time_format'],
		'gce_cache'            => $args['cache_duration'],
		'gce_multi_day_events' => ( $args['multiple_day'] != 'false' ? '1' : '0' ),
		'gce_display_mode'     => 'grid',
		'gce_custom_from'      => gce_convert_timestamp( $args['retrieve_from_value'] ),
		'gce_custom_until'     => gce_convert_timestamp( $args['retrieve_until_value'] ),
		'old_gce_id'           => $args['id'],
		'gce_search_query'     => ( isset( $args['query'] ) ? $args['query'] : '' ),
		'gce_expand_recurring' => $gce_expand_recurring
	);
	
	if( $args['use_builder'] == 'false' || $args['use_builder'] == false ) {
		$display_meta = array( 
			'gce_display_simple'            => 1,
			'gce_display_start'             => $args['display_start'],
			'gce_display_start_text'        => $args['display_start_text'],
			'gce_display_end'               => $args['display_end'],
			'gce_display_end_text'          => $args['display_end_text'],
			'gce_display_separator'         => $args['display_separator'],
			'gce_display_location'          => ( $args['display_location'] == 'on' ? '1' : '0' ),
			'gce_display_location_text'     => $args['display_location_text'],
			'gce_display_description'       => ( $args['display_desc'] == 'on' ? '1' : '0' ),
			'gce_display_description_text'  => $args['display_desc_text'],
			'gce_display_description_max'   => $args['display_desc_limit'],
			'gce_display_link'              => ( $args['display_link'] == 'on' ? '1' : '0' ),
			'gce_display_link_tab'          => ( $args['display_link_target'] == 'on' ? '1' : '0' ),
			'gce_display_link_text'         => $args['display_link_text']
		);
		
		$post_meta_fields = array_merge( $post_meta_fields, $display_meta );
	}
	
	// Loop through each $post_meta_field and add as an entry
	foreach( $post_meta_fields as $k => $v ) {
		update_post_meta( $id, $k, $v );
	}
}

function gce_convert_timestamp( $t ) {
	// mm/dd/yyyy
	return date( 'm/d/Y', $t );
}

/**
 * Remove the old transient values from the database
 * 
 * @since 2.0.0
 */
function clear_old_transients( $id ) {
	
	delete_transient( 'gce_feed_' . $id );
	delete_transient( 'gce_feed_' . $id . '_url' );
}

/** 
 * Update widget IDs
 * 
 * @since 2.0.0
 */
function update_widget_feed_ids() {
	
	$widget = get_option( 'widget_gce_widget' );
	
	if( is_array( $widget ) && ! empty( $widget ) ) {
		foreach( $widget as $a => $b ) {
			if( ! is_array( $b ) ) {
				continue;
			} 

			foreach( $b as $k => $v ) {

				if( $k != 'id' ) {
					continue;
				}

				$id = $v;
				
				//$multi = str_replace( ' ', '', $v );
				
				$multi = explode( ',', str_replace( ' ', '', $id ) );
				
				if( is_array( $multi ) ) {
					
					$new_ids = '';
					
					foreach( $multi as $m ) {
						$q = new WP_Query( "post_type=gce_feed&meta_key=old_gce_id&meta_value=$m&order=ASC" );

						if( $q->have_posts() ) {

							$q->the_post();
							// Set our ID to the old ID if found
							$m = get_the_ID();
							
							$new_ids .= $m . ',';
						}
					}
					
					wp_reset_postdata();
					
					$widget[$a][$k] = substr( $new_ids, 0, -1 );
				} else {

					$q = new WP_Query( "post_type=gce_feed&meta_key=old_gce_id&meta_value=$id&order=ASC" );

					if( $q->have_posts() ) {

						$q->the_post();
						// Set our ID to the old ID if found
						$id = get_the_ID();
					}
					
					wp_reset_postdata();

					$widget[$a][$k] = $id;
				}
			}
		}
		
		update_option( 'widget_gce_widget', $widget );
	}
	
}

/** 
 * Update widget IDs
 * 
 * @since 2.0.6.3
 */
function gce_upgrade_clear_cache() {
	// Update feeds
	$q = new WP_Query( 'post_type=gce_feed' );
	
	if( $q->have_posts() ) {
		while( $q->have_posts() ) {
			
			$q->the_post();
			
			delete_transient( 'gce_feed_' . get_the_ID() );
		}
	}
	
	wp_reset_postdata();
}