<?php
/*
Plugin Name: BuddyPress Activity Plus
Plugin URI: http://premium.wpmudev.org/project/media-embeds-for-buddypress-activity
Description: A Facebook-style media sharing improvement for the activity box.
Version: 1.6.2
Author: WPMU DEV
Author URI: http://premium.wpmudev.org
WDP ID: 232

Copyright 2009-2011 Incsub (http://incsub.com)
Author - Ve Bailovity (Incsub)
Designed by Brett Sirianni (The Edge)
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define ('BPFB_PLUGIN_SELF_DIRNAME', basename(dirname(__FILE__)));
define ('BPFB_PROTOCOL', (is_ssl() ? 'https://' : 'http://'));

//Setup proper paths/URLs and load text domains
if (is_multisite() && defined('WPMU_PLUGIN_URL') && defined('WPMU_PLUGIN_DIR') && file_exists(WPMU_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'mu-plugins');
	define ('BPFB_PLUGIN_BASE_DIR', WPMU_PLUGIN_DIR);
	define ('BPFB_PLUGIN_URL', str_replace('http://', BPFB_PROTOCOL, WPMU_PLUGIN_URL));
	$textdomain_handler = 'load_muplugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . BPFB_PLUGIN_SELF_DIRNAME . '/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'subfolder-plugins');
	define ('BPFB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR . '/' . BPFB_PLUGIN_SELF_DIRNAME);
	define ('BPFB_PLUGIN_URL', str_replace('http://', BPFB_PROTOCOL, WP_PLUGIN_URL) . '/' . BPFB_PLUGIN_SELF_DIRNAME);
	$textdomain_handler = 'load_plugin_textdomain';
} else if (defined('WP_PLUGIN_URL') && defined('WP_PLUGIN_DIR') && file_exists(WP_PLUGIN_DIR . '/' . basename(__FILE__))) {
	define ('BPFB_PLUGIN_LOCATION', 'plugins');
	define ('BPFB_PLUGIN_BASE_DIR', WP_PLUGIN_DIR);
	define ('BPFB_PLUGIN_URL', str_replace('http://', BPFB_PROTOCOL, WP_PLUGIN_URL));
	$textdomain_handler = 'load_plugin_textdomain';
} else {
	// No textdomain is loaded because we can't determine the plugin location.
	// No point in trying to add textdomain to string and/or localizing it.
	wp_die(__('There was an issue determining where BuddyPress Activity Plus plugin is installed. Please reinstall.'));
}
$textdomain_handler('bpfb', false, BPFB_PLUGIN_SELF_DIRNAME . '/languages/');

// Override oEmbed width in wp-config.php
//if (!defined('BPFB_OEMBED_WIDTH')) define('BPFB_OEMBED_WIDTH', 450, true); // Don't define by default
// Override image limit in wp-config.php
if (!defined('BPFB_IMAGE_LIMIT')) define('BPFB_IMAGE_LIMIT', 5);
// Override link target preference in wp-config.php
if (!defined('BPFB_LINKS_TARGET')) define('BPFB_LINKS_TARGET', false);


$wp_upload_dir = wp_upload_dir();
define('BPFB_TEMP_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/tmp/');
define('BPFB_TEMP_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/tmp/');
define('BPFB_BASE_IMAGE_DIR', $wp_upload_dir['basedir'] . '/bpfb/');
define('BPFB_BASE_IMAGE_URL', $wp_upload_dir['baseurl'] . '/bpfb/');



// Hook up the installation routine and check if we're really, really set to go
require_once BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_installer.php';
register_activation_hook(__FILE__, array('BpfbInstaller', 'install'));
BpfbInstaller::check();

// Require the data wrapper
require_once BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_data.php';

/**
 * Helper functions for going around the fact that
 * BuddyPress is NOT multisite compatible.
 */
function bpfb_get_image_url ($blog_id) {
	if (!defined('BP_ENABLE_MULTIBLOG') || !BP_ENABLE_MULTIBLOG) return str_replace('http://', BPFB_PROTOCOL, BPFB_BASE_IMAGE_URL);
	if (!$blog_id) return str_replace('http://', BPFB_PROTOCOL, BPFB_BASE_IMAGE_URL);
	switch_to_blog($blog_id);
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();
	return str_replace('http://', BPFB_PROTOCOL, $wp_upload_dir['baseurl']) . '/bpfb/';
}
function bpfb_get_image_dir ($blog_id) {
	if (!defined('BP_ENABLE_MULTIBLOG') || !BP_ENABLE_MULTIBLOG) return BPFB_BASE_IMAGE_DIR;
	if (!$blog_id) return BPFB_BASE_IMAGE_DIR;
	switch_to_blog($blog_id);
	$wp_upload_dir = wp_upload_dir();
	restore_current_blog();
	return $wp_upload_dir['basedir'] . '/bpfb/';
}


/**
 * Includes the core requirements and serves the improved activity box.
 */
function bpfb_plugin_init () {
	require_once(BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_binder.php');
	require_once(BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_codec.php');
	// Group Documents integration
	if (defined('BP_GROUP_DOCUMENTS_IS_INSTALLED') && BP_GROUP_DOCUMENTS_IS_INSTALLED) {
		require_once(BPFB_PLUGIN_BASE_DIR . '/lib/bpfb_group_documents.php');
	}
	if (is_admin()) {
		if (file_exists(BPFB_PLUGIN_BASE_DIR . '/lib/external/wpmudev-dash-notification.php')) {
			global $wpmudev_notices;
			if (!is_array($wpmudev_notices)) $wpmudev_notices = array();
			$wpmudev_notices[] = array(
				'id' => 232,
				'name' => 'BuddyPress Activity Plus',
				'screens' => array(
					'settings_page_bpfb-settings',
				),
			);
			require_once BPFB_PLUGIN_BASE_DIR . '/lib/external/wpmudev-dash-notification.php';
		}
		require_once BPFB_PLUGIN_BASE_DIR . '/lib/class_bpfb_admin_pages.php';
		Bpfb_Admin::serve();
	}

	do_action('bpfb_init');
	BpfbBinder::serve();
}

// Only fire off if BP is actually loaded.
// XTEC ************ MODIFICAT - Control disk percent usage when upload a file
// 2015.06.23 @nacho
/**
 * Shows a message if quota has exceed
 */
function bpfb_plugin_no_quota () {
	wp_enqueue_script('jquery');
	wp_enqueue_script('bpfb_quota_control', BPFB_PLUGIN_URL . '/js/quota_control.js', array('jquery'));
	wp_localize_script('bpfb_quota_control', 'l10nBpfb', array(
		'quota_exceeded' => __('You have exceeded your disk quota', 'agora-functions')
	));
}

if (isset($GLOBALS['diskPercentNodes'])&&($GLOBALS['diskPercentNodes'] <= 100)){
	add_action('bp_loaded', 'bpfb_plugin_init');
} else {
	add_action('bp_loaded', 'bpfb_plugin_no_quota');
}
//************ ORIGINAL
/*
add_action('bp_loaded', 'bpfb_plugin_init');
*/
//************ FI