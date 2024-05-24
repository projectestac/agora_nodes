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
        'href' => 'https://xtec.gencat.cat/ca/inici/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'edu365',
        'href' => 'https://www.edu365.cat/',
        'title' => 'Edu365',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'digital',
        'href' => 'https://projectes.xtec.cat/digital/',
        'title' => 'Digital',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'nus',
        'href' => 'https://comunitat.edigital.cat/',
        'title' => 'Nus (Xarxa docent)',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'alexandria',
        'title' => 'Alexandria',
        'href' => 'https://alexandria.xtec.cat/',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'arc',
        'title' => 'ARC',
        'href' => 'https://apliense.xtec.cat/arc/',
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
        'href' => 'https://projectes.xtec.cat/clic/',
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

    $args = [
        'id' => 'sinapsi',
        'href' => 'https://sinapsi.xtec.cat/',
        'title' => 'Sinapsi',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

    $args = [
        'id' => 'dossier',
        'href' => 'https://dossier.xtec.cat/',
        'title' => 'Dossier',
        'parent' => 'recursosXTEC',
    ];

    $wp_admin_bar->add_node($args);

}
