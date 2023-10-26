<?php
function add_recursos($wp_admin_bar) {

    $args = [
        'id' => 'recursosXTEC',
        'title' => '<img alt ="Logotip XTEC" src="' . get_template_directory_uri() . '/custom-tac/imatges/logo_xtec.png' . "\">",
        'parent' => false,
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'xtec',
        'title' => 'XTEC',
        'href' => 'http://www.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'edu365',
        'href' => 'http://www.edu365.cat/',
        'title' => 'Edu365',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'edu3',
        'href' => 'http://www.edu3.cat/',
        'title' => 'Edu3',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'sinapsi',
        'href' => 'https://sinapsi.xtec.cat',
        'title' => 'Sinapsi',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'alexandria',
        'title' => 'Alexandria',
        'href' => 'http://alexandria.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'xarxadocent',
        'title' => 'Xarxa Docent',
        'href' => 'http://educat.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'arc',
        'title' => 'ARC',
        'href' => 'http://apliense.xtec.cat/arc/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'merli',
        'title' => 'Merlí',
        'href' => 'http://aplitic.xtec.cat/merli/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'jclic',
        'title' => 'jClic',
        'href' => 'https://clic.xtec.cat/legacy/ca/index.html',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'linkat',
        'title' => 'Linkat',
        'href' => 'http://linkat.xtec.cat/portal/index.php',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'odissea',
        'title' => 'Odissea',
        'href' => 'https://odissea.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'agora',
        'title' => 'Àgora',
        'href' => 'https://agora.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

}
