<?php
/*
Plugin Name: CommonFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A pluggin to include common functions which affects to all themes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/


/**
 * Remove screen options from posts to simplify user experience
 * @author Xavi Meler
 */
function remove_post_meta_boxes() {
	remove_meta_box('trackbacksdiv', 'post', 'normal');
	remove_meta_box('trackbacksdiv', 'post', 'side');
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
 * @author Sara Arjona
 */
function set_order_meta_boxes($hidden, $screen) {
	$post_type = $screen->post_type;
	// So this can be used without hooking into user_register
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	//$user_meta = get_user_meta($user_id);
	$meta_key = array(
		'order' => "meta-box-order_$post_type",
		'hidden' => "metaboxhidden_$post_type",
		'closed' => "closedpostboxes_$post_type",
	);

	// If user have preferences, do nothing
	if ( ! get_user_meta($user_id, $meta_key['order'], true) ) {
		if ( $post_type == 'post' ) {
			// Defines position of the meta-boxes
			$meta_value = array(
				'side' => 'submitdiv,postimagediv,postexcerpt,formatdiv,metabox1,tagsdiv-post',
				'normal' => 'categorydiv',
				'advanced' => '',
			);
			update_user_meta($user_id, $meta_key['order'], $meta_value);

			// Defines hidden meta-boxes
			$meta_value = array('authordiv', 'commentsdiv', 'commentstatusdiv', 'formatdiv', 'layout_meta', 'revisionsdiv', 'slugdiv', 'ping_status');
			update_user_meta($user_id, $meta_key['hidden'], $meta_value);
		} elseif ( $post_type == 'page' ) {
			// Defines position of the meta-boxes
			$meta_value = array(
				'side' => 'submitdiv,pageparentdiv',
				'normal' => 'commentstatusdiv',
				'advanced' => '',
			);
			update_user_meta($user_id, $meta_key['order'], $meta_value);

			// Defines hidden meta-boxes
			$meta_value = array('authordiv', 'commentsdiv', 'commentstatusdiv', 'revisionsdiv', 'slugdiv');
			update_user_meta($user_id, $meta_key['hidden'], $meta_value);

			// Defines collapsed meta-boxes
			$meta_value = array('layout_meta');
			update_user_meta($user_id, $meta_key['closed'], $meta_value);
		}
	}
}
add_action('add_meta_boxes', 'set_order_meta_boxes', 10, 2);

/**
 * Disable or enable comments and pings for pages or articles
 * @author Nacho Abejaro
 */
function default_comments_off( $data ) {
	if( $data['post_type'] == 'page' && $data['post_status'] == 'auto-draft' ) {
		$data['comment_status'] = 'close';
        $data['ping_status'] = 'close';
    }elseif ( $data['post_type'] == 'post' && $data['post_status'] == 'auto-draft' ) {
    	$data['comment_status'] = 'open';
    	$data['ping_status'] = 'open';
    }
	return $data;
}
add_filter( 'wp_insert_post_data', 'default_comments_off' );

/**
 * Add upload images capability to the contributor rol
 * @author Xavi Meler
 */
function add_contributor_caps() {
	$role = get_role('contributor');
	$role->add_cap('upload_files');
}
add_action('admin_init', 'add_contributor_caps');

/**
 * Restricting contributors to view only media library items they upload
 * TODO: fix counter (now counter show all files count)
 * @author Xavi Meler
*/
function users_own_attachments( $wp_query_obj ) {
	global $current_user, $pagenow;

	if ( ! is_a($current_user, 'WP_User') ) {
		return;
	}

	if ( ('edit.php' != $pagenow) && ('upload.php' != $pagenow ) &&
	(( 'admin-ajax.php' != $pagenow ) || ( $_REQUEST['action'] != 'query-attachments' ) ) ) {
		return;
	}

	// Apply to this roles: Subscriptor, Contributor and Author
	if ( ! current_user_can('delete_pages') ) {
		$wp_query_obj->set('author', $current_user->id);
	}

	return;
}
add_action('pre_get_posts','users_own_attachments');

/**
 * Remove the "Dashboard" from the admin menu for contributor user roles
 * @author Nacho Abejaro
 */
function remove_contributor_dashboard() {

    $user_id = get_current_user_id();

    $caps = get_user_meta($user_id, 'wp_capabilities', true);
    $roles = array_keys((array) $caps);
    $role = $roles[0];

    if ($role === 'contributor') {
        remove_menu_page('edit-comments.php');
        remove_menu_page('edit.php?post_type=gce_feed');
        remove_menu_page('tools.php');
    }
}

add_action('admin_menu', 'remove_contributor_dashboard');

/**
 * Disable gravatar.com calls.
 * @author Víctor Saavedra (vsaavedr@xtec.cat)
 */
function remove_gravatar ($avatar, $id_or_email, $size, $default, $alt) {
	$default = admin_url('images/mysteryman.png');
	return "<img alt='{$alt}' src='{$default}' class='avatar avatar-{$size} photo avatar-default' height='{$size}' width='{$size}' />";
}

add_filter('get_avatar', 'remove_gravatar', 1, 5);
