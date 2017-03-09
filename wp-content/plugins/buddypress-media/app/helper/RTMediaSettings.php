<?php

/**
 * Description of RTMediaSettings
 *
 * @author Gagandeep Singh <gagandeep.singh@rtcamp.com>
 * @author Joshua Abenazer <joshua.abenazer@rtcamp.com>
 */
if (!class_exists('RTMediaSettings')) {

    class RTMediaSettings {

        public function __construct() {
            if (!(defined('DOING_AJAX') && DOING_AJAX)) {
		add_action('admin_init', array($this, 'settings'));
		if (isset($_POST['rtmedia-options-save'])) {
		    add_action('init', array($this, 'settings'));
		}
	    }
//            if (is_multisite()) {
//                add_action('network_admin_notices', array($this, 'privacy_notice'));
//            } else {
//                add_action('admin_notices', array($this, 'privacy_notice'));
//            }
        }

	function get_default_options() {
	    global $rtmedia;

            $defaults = array(
                'general_enableAlbums' => 0,
                'general_enableComments' => 0,
                'general_downloadButton' => 0,
                'general_enableLightbox' => 0,
                'general_perPageMedia' => 10,
                'general_enableMediaEndPoint' => 0,
                'general_showAdminMenu' => 0,
                'general_videothumbs' => 2,
		'general_uniqueviewcount' => 0,
		'general_viewcount' => 0,
		'general_AllowUserData' => 1,
		'rtmedia_add_linkback' => 0,
		'rtmedia_affiliate_id' => '',
		'rtmedia_enable_api' => 0,
            );

            $defaults = apply_filters('rtmedia_general_content_default_values', $defaults);
            foreach ($rtmedia->allowed_types as $type) {
                // invalid keys handled in sanitize method
                $defaults['allowedTypes_' . $type['name'] . '_enabled'] = 0;
                $defaults['allowedTypes_' . $type['name'] . '_featured'] = 0;
            }

            /* Previous Sizes values from buddypress is migrated */
            foreach ($rtmedia->default_sizes as $type => $typeValue) {
                foreach ($typeValue as $size => $sizeValue) {
                    foreach ($sizeValue as $dimension => $value) {
                        $defaults['defaultSizes_' . $type . '_' . $size . '_' . $dimension] = 0;
                    }
                }
            }

            /* Privacy */
            $defaults['privacy_enabled'] = 0;
            $defaults['privacy_default'] = 0;
            $defaults['privacy_userOverride'] = 0;

            $defaults['buddypress_enableOnGroup'] = 0;
            $defaults['buddypress_enableOnActivity'] = 0;
            $defaults['buddypress_enableOnProfile'] = 0;
            $defaults['buddypress_limitOnActivity'] = 0;
            $defaults['styles_custom'] = '';
            $defaults['styles_enabled'] = 1;

            if(isset($options["general_videothumbs"]) && is_numeric($options["general_videothumbs"]) && intval($options["general_videothumbs"]) > 10){
                $options["general_videothumbs"] = 10;
                add_action ( 'admin_notices', array( &$this, 'add_max_video_thumb_notice' ) );
            }
	    return $defaults;
	}

        /**
         * Register Settings
         *
         * @global string 'rtmedia'
         */
        function sanitize_options($options) {
	    $defaults = $this->get_default_options();
            $options = wp_parse_args($options, $defaults);
            return $options;
        }

	function sanitize_before_save_options($options) {
	    $defaults = $this->get_default_options();
	    foreach($defaults as $key => $value) {
		if( !isset( $options[$key] ) ) {
		    $options[$key] = "0";
		}
	    }
	    return $options;
	}

        function add_max_video_thumb_notice(){
             echo '<div class="error"><p>' . __( 'Max Video thumbnail size is ', 'rtmedia' ) .' <strong>10</strong></p></div>';
        }
        /**
         *
         * @global BPMediaAddon $rtmedia_addon
         */
        public function settings() {
            global $rtmedia, $rtmedia_addon, $rtmedia_save_setting_single;
            $options = rtmedia_get_site_option('rtmedia-options');
            $options = $this->sanitize_options($options);
            $rtmedia->options = $options;
            // Save Settings first then proceed.
            if (isset($_POST['rtmedia-options-save'])) {
                $options = $_POST['rtmedia-options'];
                $options = $this->sanitize_before_save_options($options);
                $options = apply_filters("rtmedia_pro_options_save_settings", $options);
		$is_rewrite_rule_flush = apply_filters('rtmedia_flush_rewrite_rule',false);
                rtmedia_update_site_option('rtmedia-options', $options);
				do_action ( 'rtmedia_save_admin_settings', $options );
		if( $is_rewrite_rule_flush ) {
		    flush_rewrite_rules(false);
		}
                wp_redirect($_SERVER['HTTP_REFERER']);
                global $rtmedia;
                $rtmedia->options = $options;
            }

	    if(function_exists('add_settings_section') ) {
		$rtmedia_addon = new RTMediaAddon();
		add_settings_section('rtm-addons', __('BuddyPress Media Addons for Photos', 'rtmedia'), array($rtmedia_addon, 'get_addons'), 'rtmedia-addons');
		$rtmedia_support = new RTMediaSupport(false);
		add_settings_section('rtm-support', __('Support', 'rtmedia'), array($rtmedia_support, 'get_support_content'), 'rtmedia-support');
		$rtmedia_themes = new RTMediaThemes();
		add_settings_section('rtm-themes', __('rtMedia Themes', 'rtmedia'), array($rtmedia_themes, 'get_themes'), 'rtmedia-themes');
	    }



//            if (!BPMediaPrivacy::is_installed()) {
//                $rtmedia_privacy = new BPMediaPrivacySettings();
//                add_filter('rtmedia_add_sub_tabs', array($rtmedia_privacy, 'ui'), 99, 2);
//                add_settings_section('rtm-privacy', __('Update Database', 'rtmedia'), array($rtmedia_privacy, 'init'), 'rtmedia-privacy');
//            }
            //$rtmedia_album_importer = new BPMediaAlbumimporter();
            //add_settings_section('rtm-rt-album-importer', __('BP-Album Importer', 'rtmedia'), array($rtmedia_album_importer, 'ui'), 'rtmedia-importer');
            //register_setting('rtmedia', 'rtmedia_options', array($this, 'sanitize'));
	    if( !isset($rtmedia_save_setting_single) ) {
		$rtmedia_save_setting_single = true;
	    }
        }

	public function network_notices() {
            $flag = 1;
            if (rtmedia_get_site_option('rtm-media-enable', false)) {
                echo '<div id="setting-error-bpm-media-enable" class="error"><p><strong>' . rtmedia_get_site_option('rtm-media-enable') . '</strong></p></div>';
                delete_site_option('rtm-media-enable');
                $flag = 0;
            }
            if (rtmedia_get_site_option('rtm-media-type', false)) {
                echo '<div id="setting-error-bpm-media-type" class="error"><p><strong>' . rtmedia_get_site_option('rtm-media-type') . '</strong></p></div>';
                delete_site_option('rtm-media-type');
                $flag = 0;
            }
            if (rtmedia_get_site_option('rtm-media-default-count', false)) {
                echo '<div id="setting-error-bpm-media-default-count" class="error"><p><strong>' . rtmedia_get_site_option('rtm-media-default-count') . '</strong></p></div>';
                delete_site_option('rtm-media-default-count');
                $flag = 0;
            }

            if (rtmedia_get_site_option('rtm-recount-success', false)) {
                echo '<div id="setting-error-bpm-recount-success" class="updated"><p><strong>' . rtmedia_get_site_option('rtm-recount-success') . '</strong></p></div>';
                delete_site_option('rtm-recount-success');
                $flag = 0;
            }
            elseif (rtmedia_get_site_option('rtm-recount-fail', false)) {
                echo '<div id="setting-error-bpm-recount-fail" class="error"><p><strong>' . rtmedia_get_site_option('rtm-recount-fail') . '</strong></p></div>';
                delete_site_option('rtm-recount-fail');
                $flag = 0;
            }

            if (get_site_option('rtm-settings-saved') && $flag) {
                echo '<div id="setting-error-bpm-settings-saved" class="updated"><p><strong>' . get_site_option('rtm-settings-saved') . '</strong></p></div>';
            }
            delete_site_option('rtm-settings-saved');
        }

        public function allowed_types() {
            $allowed_types = rtmedia_get_site_option('upload_filetypes', 'jpg jpeg png gif');
            $allowed_types = explode(' ', $allowed_types);
            $allowed_types = implode(', ', $allowed_types);
            echo '<span class="description">' . sprintf(__('Currently your network allows uploading of the following file types. You can change the settings <a href="%s">here</a>.<br /><code>%s</code></span>', 'rtmedia'), network_admin_url('settings.php#upload_filetypes'), $allowed_types);
        }

        /**
         * Sanitizes the settings
         */

        /**
         *
         * @global type $rtmedia_admin
         * @param type $input
         * @return type
         */
        public function sanitize($input) {
            global $rtmedia_admin;
            if (isset($_POST['refresh-count'])) {
                if ($rtmedia_admin->update_count()) {
                    if (is_multisite())
                        rtmedia_update_site_option('rtm-recount-success', __('Recounting of media files done successfully', 'rtmedia'));
                    else
                        add_settings_error(__('Recount Success', 'rtmedia'), 'rtm-recount-success', __('Recounting of media files done successfully', 'rtmedia'), 'updated');
                } else {
                    if (is_multisite())
                        rtmedia_update_site_option('rtm-recount-fail', __('Recounting Failed', 'rtmedia'));
                    else
                        add_settings_error(__('Recount Fail', 'rtmedia'), 'rtm-recount-fail', __('Recounting Failed', 'rtmedia'));
                }
            }
//            if (!isset($_POST['rtmedia_options']['enable_on_profile']) && !isset($_POST['rtmedia_options']['enable_on_group'])) {
//                if (is_multisite())
//                    update_site_option('rtm-media-enable', __('Enable BuddyPress Media on either User Profiles or Groups or both. Atleast one should be selected.', 'rtmedia'));
//                else
//                    add_settings_error(__('Enable BuddyPress Media', 'rtmedia'), 'rtm-media-enable', __('Enable BuddyPress Media on either User Profiles or Groups or both. Atleast one should be selected.', 'rtmedia'));
//                $input['enable_on_profile'] = 1;
//            }
            if (!isset($_POST['rtmedia_options']['videos_enabled']) && !isset($_POST['rtmedia_options']['audio_enabled']) && !isset($_POST['rtmedia_options']['images_enabled'])) {
                if (is_multisite())
                    rtmedia_update_site_option('rtm-media-type', __('Atleast one Media Type Must be selected', 'rtmedia'));
                else
                    add_settings_error(__('Media Type', 'rtmedia'), 'rtm-media-type', __('Atleast one Media Type Must be selected', 'rtmedia'));
                $input['images_enabled'] = 1;
            }

            $input['default_count'] = intval($_POST['rtmedia_options']['default_count']);
            if (!is_int($input['default_count']) || ($input['default_count'] < 0 ) || empty($input['default_count'])) {
                if (is_multisite())
                    rtmedia_update_site_option('rtm-media-default-count', __('"Number of media" count value should be numeric and greater than 0.', 'rtmedia'));
                else
                    add_settings_error(__('Default Count', 'rtmedia'), 'rtm-media-default-count', __('"Number of media" count value should be numeric and greater than 0.', 'rtmedia'));
                $input['default_count'] = 10;
            }
            if (is_multisite())
                rtmedia_update_site_option('rtm-settings-saved', __('Settings saved.', 'rtmedia'));
            do_action('rtmedia_sanitize_settings', $_POST, $input);
            return $input;
        }

        public function image_settings_intro() {
            if (is_plugin_active('regenerate-thumbnails/regenerate-thumbnails.php')) {
                $regenerate_link = admin_url('/tools.php?page=regenerate-thumbnails');
            }
            elseif (array_key_exists('regenerate-thumbnails/regenerate-thumbnails.php', get_plugins())) {
                $regenerate_link = admin_url('/plugins.php#regenerate-thumbnails');
            }
            else {
                $regenerate_link = wp_nonce_url(admin_url('update.php?action=install-plugin&plugin=regenerate-thumbnails'), 'install-plugin_regenerate-thumbnails');
            }
            echo '<span class="description">' . sprintf(__('If you make changes to width, height or crop settings, you must use "<a href="%s">Regenerate Thumbnail Plugin</a>" to regenerate old images."', 'rtmedia'), $regenerate_link) . '</span>';
            echo '<div class="clearfix">&nbsp;</div>';
        }

        /**
         * Output a checkbox
         *
         * @global array $rtmedia
         * @param array $args
         */
        public function privacy_notice() {
            if (current_user_can('create_users')) {
//                if (BPMediaPrivacy::is_installed())
//                    return;
                $url = add_query_arg(
                        array('page' => 'rtmedia-privacy'), (is_multisite() ? network_admin_url('admin.php') : admin_url('admin.php'))
                );

                $notice = '
                <div class="error">
                <p>' . __('BuddyPress Media 2.6 requires a database upgrade. ', 'rtmedia')
                        . '<a href="' . $url . '">' . __('Update Database', 'rtmedia') . '.</a></p>
                </div>
                ';
                echo $notice;
            }
        }

        public function rtmedia_support_intro() {
            echo '<p>' . __('If your site has some issues due to BuddyPress Media and you want one on one support then you can create a support topic on the <a target="_blank" href="http://rtcamp.com/groups/buddypress-media/forum/?utm_source=dashboard&utm_medium=plugin&utm_campaign=buddypress-media">rtCamp Support Forum</a>.', 'rtmedia') . '</p>';
            echo '<p>' . __('If you have any suggestions, enhancements or bug reports, then you can open a new issue on <a target="_blank" href="https://github.com/rtCamp/buddypress-media/issues/new">GitHub</a>.', 'rtmedia') . '</p>';
        }

    }

}
