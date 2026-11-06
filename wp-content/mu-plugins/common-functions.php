<?php
/*
Plugin Name: CommonFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A plugin to include common functions which affects to all themes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

require_once __DIR__ . '/common/lib.php';

load_muplugin_textdomain('common-functions', '/common/languages');

function xtec_enqueue_style() {
    // The file is very small (< 1 KB), so it is added inline to save a render-blocking request.
    $css = file_get_contents(__DIR__ . '/common/styles/common-functions.css');

    if ($css !== false) {
        wp_register_style('common-functions', false);
        wp_enqueue_style('common-functions');
        wp_add_inline_style('common-functions', $css);
    }
}

add_action('wp_enqueue_scripts', 'xtec_enqueue_style');
