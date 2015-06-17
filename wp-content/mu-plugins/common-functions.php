<?php
/*
Plugin Name: CommonFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A pluggin to include common functions which affects to all themes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

require_once dirname(__FILE__) . '/common/lib.php';

load_muplugin_textdomain('common-functions', '/common/languages');
wp_enqueue_style('common-functions', get_site_url() . '/wp-content/mu-plugins/common/styles/common-functions.css');
