<?php

/*
  Plugin Name: GiphyPress
  Plugin URI: http://labs.giphy.com/
  Description: The official GIPHY plugin allows you to embed awesome GIFs into your Wordpress posts with ease.
  Version: 1.6.2
  Author: Team GIPHY
  Author URI: http://http://labs.giphy.com/
  License: GPL2

  Add some flava to your blog with the GIPHY Wordpress plugin!! Animated GIFs are a proven way to improve
  traffic, increase time on site, and promote sharing on social media platforms. Don't take our word for it,
  blogs have already utilized GIPHY gifs to boost the popularity of their posts.

  Adding a GIF with the GIPHY plugin couldn't be easier. Just click the GIPHY logo button in the text editor,
  search or browse via tags to find gifs, and click on a GIF to get a better view. Once you've found the GIF you
  want, simply click the "Embed into Post" button, and, voila!, the GIF is automatically inserted into your
  post.
 */

include_once dirname(__FILE__) . '/utils.php';
include_once dirname(__FILE__) . '/widget.php';
include_once dirname(__FILE__) . '/iframe.php';

$giphyWidget = new GiphyPressPlugin();
$giphyPressMce = new Add_new_tinymce_btn_Giphy('|', 'giphypresswizard', plugins_url() . '/giphypress/js/giphypress_mce.js');


if (GiphyPressPlugin::wp_above_version('2.9')) {
  doLog("wp above version 2.9");
  add_action('admin_enqueue_scripts', 'giphypresswizard_admin_enqueue_scripts');
  add_action('wp_enqueue_scripts', 'giphypresswizard_enqueue_scripts' );
  //add_action( 'after_wp_tiny_mce', 'custom_after_wp_tiny_mce' );

}
else {
  // do we want to support WP < 2.9 ??
  // add some stuff here if we do!
}

function custom_after_wp_tiny_mce() {
    printf('<!-- ******* AFTER TINY MCE --->');
}

function giphypress_plugin_menu() {
  doLog("giphypress plugin menu");
  add_menu_page('GiphyPress Settings', 'GiphyPress', 'manage_options', 'giphypress-options', 'GiphyPressPlugin::giphypress_show_options', NULL, '10.00392854349');
}

function giphypresswizard_admin_enqueue_scripts() {
  doLog("start gipypresswizard_admin_enqueue_scripts");
  wp_enqueue_style('giphypresswizard', plugins_url() . '/giphypress/css/giphypress_mce.css');

  doLog("end gipypresswizard_admin_enqueue_scripts");
}

function giphypresswizard_output_scriptvars() {
  doLog("giphypresswizard_output_scriptvars callback");
  // TODO do some configurization here if necessary
}

function giphypresswizard_enqueue_scripts() {
  doLog("giphypress enqueue scripts");
  //wp_register_script('custom-script', plugins_url() . '/giphypress/js/GiphySearch.js', array( 'jquery', 'jquery-masonry' ), NULL, true);
  //wp_enqueue_script('custom-script');
  doLog("end of giphypress enqueue scripts");
}

//doLog("eof");
