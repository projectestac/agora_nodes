<?php
/*
Plugin Name: CommonFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A pluggin to include common functions which affects to all themes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

/**
 * Hide screen option's items. Best for usability
 * @author Sara Arjona
 */
function common_hidden_meta_boxes($hidden) {
	$hidden[] = 'authordiv';
	$hidden[] = 'commentsdiv';
	$hidden[] = 'commentstatusdiv';
	$hidden[] = 'layout_meta';
	$hidden[] = 'slugdiv';
	$hidden[] = 'revisionsdiv';
	return $hidden;
}

add_filter('hidden_meta_boxes', 'common_hidden_meta_boxes');


/**
 * Remove screen options from posts to simplify user experience
 * @author Xavi Meler
 */
function remove_post_meta_boxes() {
	remove_meta_box('trackbacksdiv', 'post', 'normal');
	remove_meta_box('trackbacksdiv', 'post', 'side');
	remove_meta_box('formatdiv', 'post', 'normal');
	remove_meta_box('formatdiv', 'post', 'side');
	remove_meta_box('postcustom', 'post', 'normal');
	remove_meta_box('postcustom', 'post', 'side');
	remove_meta_box('rawhtml_meta_box', 'post', 'side');
	remove_meta_box('rawhtml_meta_box', 'post', 'normal');
	remove_meta_box('layout_meta', 'post', 'side');
	remove_meta_box('layout_meta', 'post', 'normal');
}

add_action('do_meta_boxes', 'remove_post_meta_boxes');

/**
 * Remove screen options from pages to simplify user experience
 * @author Xavi Meler
 */
function remove_page_meta_boxes() {
	remove_meta_box('rawhtml_meta_box', 'page', 'side');
	remove_meta_box('postcustom', 'page', 'normal');
	remove_meta_box('postimagediv', 'page', 'side');
}

add_action('do_meta_boxes', 'remove_page_meta_boxes');

// XTEC ************ AFEGIT
// This function defines the order and the position for the boxes
// 2015.03.04 @author Nacho Abejaro
function set_order_meta_boxes($hidden, $screen) {

	$post_type = $screen->post_type;

	// So this can be used without hooking into user_register
	if ( ! $user_id)
		$user_id = get_current_user_id();

	$meta_key = array(
		'order' => "meta-box-order_$post_type",
	);

	if ( $post_type == 'post' ) {
		
		// Defines the position
		$meta_value = array(
			'side' => 'submitdiv,postimagediv,postexcerpt,metabox1,tagsdiv-post',
			'normal' => 'categorydiv',
			'advanced' => '',
		);

		// Sets Order
		update_user_meta( $user_id, $meta_key['order'], $meta_value );
	}elseif ( $post_type == 'page' ) {
	
		// Defines the position
		$meta_value = array(
			'side' => 'submitdiv,pageparentdiv',
			'normal' => 'commentstatusdiv',
			'advanced' => '',
		);

		//Sets Order
		update_user_meta( $user_id, $meta_key['order'], $meta_value );
	}else {
		// Default, do nothing
	}
}

add_action('add_meta_boxes', 'set_order_meta_boxes', 10, 2);
//************ FI