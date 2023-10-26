<?php

function add_logo($wp_admin_bar) {

    $isConsorci = stripos(reactor_option('cpCentre'), "barcelona") ? true : false;

    if ($isConsorci) {
        $args = [
            'id' => 'consorci',
            'title' => '<img alt="Logotip Consorci" src="' . get_template_directory_uri() . '/custom-tac/imatges/logo_consorci.png">',
            'href' => 'https://www.edubcn.cat/ca/',
            'parent' => false,
        ];
    } else {
        $args = [
            'id' => 'gencat',
            'title' => '<img alt="Logotip Generalitat" src="' . get_template_directory_uri() . '/custom-tac/imatges/logo_gene.png">',
            'href' => 'https://www.gencat.cat/ensenyament/',
            'parent' => false,
        ];
    }

    $wp_admin_bar->add_menu($args);

}
