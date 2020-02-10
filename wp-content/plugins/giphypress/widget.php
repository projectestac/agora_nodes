<?php

require_once dirname(__FILE__) . '/utils.php';

class GiphyPressPlugin {

  public static $API_KEY = 'G46lZIryTGCUU';
  public static $API_HOST = 'https://api.giphy.com';

  public function __construct() {

  }

  public static function wp_above_version($ver) {
    global $wp_version;
    if (version_compare($wp_version, $ver, '>=')) {
      return true;
    }
    return false;
  }

  public static function giphypress_show_options() {
    //doLog("giphy press show options called");
    if (!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    echo "<h1>GiphyPress Settings</h1>";
    // TODO - put any plugin specific options in here.

  }

}
