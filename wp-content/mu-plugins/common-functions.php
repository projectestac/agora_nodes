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
//add_filter('hidden_meta_boxes', 'common_hidden_meta_boxes');

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
	remove_meta_box('rawhtml_meta_box', 'page', 'normal');
	remove_meta_box('rawhtml_meta_box', 'page', 'side');
	remove_meta_box('postcustom', 'page', 'normal');
	remove_meta_box('postimagediv', 'page', 'side');
}

add_action('do_meta_boxes', 'remove_page_meta_boxes');

/**
 * Check if user don't have preferences, and
 * Sets order and initial position from boxes
 * for pages or articles
 * @author Nacho Abejaro
 */
function set_order_meta_boxes($hidden, $screen) {

	$post_type = $screen->post_type;

	// So this can be used without hooking into user_register
	if ( ! $user_id)
		$user_id = get_current_user_id();

	$user_meta = get_user_meta($user_id);

	$meta_key = array(
		'order' => "meta-box-order_$post_type",
	);

	// If user have preferences, do nothing
	if ( ! get_user_meta( $user_id, $meta_key['order'], true) ) {

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
	}else {
		if ( $post_type == 'post' ) {
			// Sets comments enabled
			$meta_key['hidden'] = "metaboxhidden_$post_type";
			$meta_value = array('slugdiv', 'trackbacksdiv', 'postcustom', 'postexcerpt', 'commentstatusdiv', 'authordiv', 'revisionsdiv');
			update_user_meta( $user_id, $meta_key['hidden'], $meta_value );
		}
	}
}

add_action('add_meta_boxes', 'set_order_meta_boxes', 10, 2);

/**
 * Add upload images capability to the contributor rol
 * @author Xavi Meler
 */
function add_contributor_caps() {
    $role = get_role( 'contributor' );
    $role->add_cap('upload_files');
}
add_action( 'admin_init', 'add_contributor_caps');

/**
 * Restricting contributors to view only media library items they upload
 * TODO: fix counter (now counter show all files count)
 * @author Xavi Meler
*/
function users_own_attachments( $wp_query_obj ) {
    global $current_user, $pagenow;

    if( !is_a( $current_user, 'WP_User') )
        return;

    if(('edit.php' != $pagenow) && ('upload.php' != $pagenow ) &&
    (( 'admin-ajax.php' != $pagenow ) || ( $_REQUEST['action'] != 'query-attachments' )))
        return;

    // Apply to this roles: Subscriptor, Contributor and Author
    if(!current_user_can('delete_pages'))
        $wp_query_obj->set('author', $current_user->id );

    return;
}
add_action('pre_get_posts','users_own_attachments');