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
    wp_enqueue_style('common-functions', get_site_url() . '/wp-content/mu-plugins/common/styles/common-functions.css');
}

add_action('wp_enqueue_scripts', 'xtec_enqueue_style');

function hide_theme_button_change() {
    if (!is_xtec_super_admin()) {
        echo '<style>
            .change-theme {
                display: none !important;
            }
        </style>';
    }
}
add_action('customize_controls_print_styles', 'hide_theme_button_change');