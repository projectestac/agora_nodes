<?php

function doLog($message) {
  $message = "GiphyPressPlugin :: " . $message;
  if (WP_DEBUG === true) {
    if (is_array($message) || is_object($message)) {
      error_log(print_r($message, true));
    } else {
      error_log($message);
    }
  }
}

if (!class_exists('Add_new_tinymce_btn_Giphy')) {

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //class start
  class Add_new_tinymce_btn_Giphy {

    public $btn_arr;
    public $js_file;

    /*
     * call the constructor and set class variables
     * From the constructor call the functions via wordpress action/filter
     */
    function __construct($seperator, $btn_name, $javascript_location) {
      //doLog("Add_new_tinymce_btn_Giphy " . $btn_name . " " . $javascript_location);
      
      $this->btn_arr = array("Seperator" => $seperator, "Name" => $btn_name);
      $this->js_file = $javascript_location;
      add_action('init', array($this, 'add_tinymce_button'));
      add_filter('tiny_mce_version', array($this, 'refresh_mce_version'));
    }

    /*
     * create the buttons only if the user has editing privs.
     * If so we create the button and add it to the tinymce button array
     */
    function add_tinymce_button() {
      if (!current_user_can('edit_posts') && !current_user_can('edit_pages'))
        return;
      if (get_user_option('rich_editing') == 'true') {
        //the function that adds the javascript
        add_filter('mce_external_plugins', array($this, 'add_new_tinymce_plugin'));
        //adds the button to the tinymce button array
        add_filter('mce_buttons', array($this, 'register_new_button'));
      }
    }

    /*
     * add the new button to the tinymce array
     */
    function register_new_button($buttons) {
      array_push($buttons, $this->btn_arr["Seperator"], $this->btn_arr["Name"]);
      return $buttons;
    }

    /*
     * Call the javascript file that loads the
     * instructions for the new button
     */
    function add_new_tinymce_plugin($plugin_array) {
      $plugin_array[$this->btn_arr['Name']] = $this->js_file;
      return $plugin_array;
    }

    /*
     * This function tricks tinymce in thinking
     * it needs to refresh the buttons
     */
    function refresh_mce_version($ver) {
      $ver += 3;
      return $ver;
    }
  }
} // end of class_exists (Add_new_tinymce_btn_EmbedPlus)