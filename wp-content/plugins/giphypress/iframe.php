<?php

/** iframe shortcode processing **/
if (!function_exists('iframe_unqprfx_embed_shortcode')) :

  function iframe_unqprfx_enqueue_script() {
    wp_enqueue_script('jquery');
  }

  add_action('wp_enqueue_scripts', 'iframe_unqprfx_enqueue_script');

  function iframe_unqprfx_embed_shortcode($atts, $content = null) {
    $defaults = array(
        'src' => 'https://giphy.com/embed/igTITYMk5sE5W',
        'width' => '500',
        'height' => '281',
        'scrolling' => 'no',
        'class' => 'iframe-class',
        'frameborder' => '0'
    );

    foreach ($defaults as $default => $value) { // add defaults
      if (!@array_key_exists($default, $atts)) { // hide warning with "@" when no params at all
        $atts[$default] = $value;
      }
    }

    $src_cut = substr($atts["src"], 0, 35); // special case for google maps
    if (strpos($src_cut, 'maps.google')) {
      $atts["src"] .= '&output=embed';
    }

    // get_params_from_url
    if (isset($atts["get_params_from_url"]) && ( $atts["get_params_from_url"] == '1' || $atts["get_params_from_url"] == 1 || $atts["get_params_from_url"] == 'true' )) {
      if ($_GET != NULL) {
        if (strpos($atts["src"], '?')) { // if we already have '?' and GET params
          $encode_string = '&';
        } else {
          $encode_string = '?';
        }
        foreach ($_GET as $key => $value) {
          $encode_string .= $key . '=' . $value . '&';
        }
      }
      $atts["src"] .= $encode_string;
    }

    $html = '';
    if (isset($atts["same_height_as"])) {
      $same_height_as = $atts["same_height_as"];
    } else {
      $same_height_as = '';
    }

    if ($same_height_as != '') {
      $atts["same_height_as"] = '';
      if ($same_height_as != 'content') { // we are setting the height of the iframe like as target element
        if ($same_height_as == 'document' || $same_height_as == 'window') { // remove quotes for window or document selectors
          $target_selector = $same_height_as;
        } else {
          $target_selector = '"' . $same_height_as . '"';
        }
        $html .= '<script>
                  jQuery(function($){
                          var target_height = $(' . $target_selector . ').height();
                          $("iframe.' . $atts["class"] . '").height(target_height);
                          //alert(target_height);
                  });
                  </script>';
      } else { // set the actual height of the iframe (show all content of the iframe without scroll)
        $html .= '
                  <script>
                  jQuery(function($){
                          $("iframe.' . $atts["class"] . '").bind("load", function() {
                                  var embed_height = $(this).contents().find("body").height();
                                  $(this).height(embed_height);
                          });
                  });
                  </script>';
      }
    }
    $html .= '<iframe';
    foreach ($atts as $attr => $value) {
      if ($attr != 'same_height_as') { // remove some attributes
        if ($value != '') { // adding all attributes
          $html .= ' ' . $attr . '="' . $value . '"';
        } else { // adding empty attributes
          $html .= ' ' . $attr;
        }
      }
    }
    $html .= '></iframe>';
    return $html;
  }

  add_shortcode('iframe', 'iframe_unqprfx_embed_shortcode');
  add_filter('tiny_mce_before_init','iframe_unqprfx_extend_tinymce_elements');

  function iframe_unqprfx_extend_tinymce_elements( $options ) {

        ///$options["plugins"] = str_replace("media,","",$options["plugins"]);

        if ( ! isset( $options['extended_valid_elements'] ) ) {
            $options['extended_valid_elements'] = '';
        } else {
            $options['extended_valid_elements'] .= ',';
        }

        $options['extended_valid_elements'] .= "iframe[src|width|height|name|align|frameborder]";
        return $options;
    }


endif; // end of if(function_exists('iframe_unqprfx_embed_shortcode'))
