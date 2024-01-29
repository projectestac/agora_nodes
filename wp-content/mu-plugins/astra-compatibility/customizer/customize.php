<?php

// Customizer: JavaScript's handlers to make Theme Customizer preview reload changes asynchronously.
add_action('customize_preview_init', function () {
    wp_enqueue_script(
        'astra-nodes-customizer',
        plugins_url('/js/theme-customizer.js', __FILE__),
        ['jquery','customize-preview'],
        '',
        true
    );
});

// Customizer: Add custom CSS.
add_action('customize_controls_print_styles', function () {
    $custom_css = '
        .customize-control {
            margin-top: 30px !important;
            padding-top: 30px;
            border-top: 1px solid #cccccc;
        }
    ';
    wp_add_inline_style('customize-controls', $custom_css);
}, 1);

/**
 * Register Customizer
 */
add_action('customize_register', 'nodes_customize_register');

function nodes_customize_register($wp_customize) {

    // Header section.
    $wp_customize->add_section('astra_nodes_customizer_header', [
        'title' => __('Header', 'astra-nodes'),
        'priority' => 1,
    ]);

    // Custom logo.
    $wp_customize->add_setting('astra_nodes_options[custom_logo]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize, 'astra_nodes_customizer_header_logo', [
                'label' => __('Header logo', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_header',
                'description' => 'Recomanacions: 300 x 200 px i menys de 200 kB',
                'settings' => 'astra_nodes_options[custom_logo]',
                'priority' => 1,
            ]
        )
    );

    // Text preceding the blog name.
    $wp_customize->add_setting('astra_nodes_options[pre_blog_name]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_pre_blog_name', [
        'label' => __('Text preceding the blog name', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[pre_blog_name]',
        'priority' => 2,
    ]);

    // Blog name.
    $wp_customize->add_setting('astra_nodes_options[blog_name]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);
    
    $wp_customize->add_control('astra_nodes_customizer_header_blog_name', [
        'label' => __('Blog name', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[blog_name]',
        'priority' => 3,
    ]);






    include_once WPMU_PLUGIN_DIR . '/astra-compatibility/classes/WP_Customize_Dropdown_Categories_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-compatibility/classes/simpleHTML.php';

    // Graella d'icones
    $wp_customize->add_setting('icones_capcalera', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(new simpleHTML($wp_customize, 'icones_capcalera', [
        'label' => __('Graella d\'icones', 'reactor'),
        'section' => 'astra_nodes_customizer_header',
        'priority' => 7,
    ]));

    $wp_customize->add_setting('reactor_options[favicon_image]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'reactor_favicon_image', [
        'label' => __('Favicon', 'reactor'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'reactor_options[favicon_image]',
        'description' => 'Icona a la pestanya del navegador',
        'priority' => 8,
    ]));

    // Pestanya Identificació del centre
    $wp_customize->add_section('reactor_customizer_idcentre', [
        'title' => __('Identificació del centre', 'reactor'),
        'priority' => 2,
    ]);

    $wp_customize->add_setting('reactor_options[logo_image]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'reactor_logo_image', [
        'label' => __('Logotip', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'settings' => 'reactor_options[logo_image]',
        'priority' => 1,
    ]));

    $wp_customize->add_setting('reactor_options[logo_inline]', [
        'default' => 1,
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('reactor_options[logo_inline]', [
        'label' => __('Alineat amb l\'adreça', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'type' => 'checkbox',
        'priority' => 2,
    ]);

    // Tornem a demanar el nom del centre perquè pot ser diferent (noms llargs)

    $wp_customize->add_setting('reactor_options[nomCanonicCentre]', [
        'default' => 'Nom del centre', // S'agafa de la base de dades
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('reactor_options[nomCanonicCentre]', [
        'label' => __('Nom del centre', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'priority' => 3,
    ]);

    $wp_customize->add_setting('reactor_options[direccioCentre]', array(
        'default' => 'C/Carrer 1', // S'agafa de la base de dades
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('reactor_options[direccioCentre]', [
        'label' => __('Adreça (física)', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'priority' => 4,
    ]);

    $wp_customize->add_setting('reactor_options[cpCentre]', array(
        'default' => '00000 Localitat', // S'agafa de la base de dades
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ));

    $wp_customize->add_control('reactor_options[cpCentre]', [
        'label' => __('Codi postal i localitat', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'priority' => 5,
    ]);

    $wp_customize->add_setting('reactor_options[telCentre]', [
        'default' => '00 000 000', // S'agafa de la base de dades
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('reactor_options[telCentre]', [
        'label' => __('Telèfon', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'priority' => 6,
    ]);

    $wp_customize->add_setting('reactor_options[googleMaps]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('reactor_options[googleMaps]', [
        'label' => __('Mapa', 'reactor'),
        'description' => 'Adreça de Google Maps',
        'section' => 'reactor_customizer_idcentre',
        'priority' => 7,
    ]);

    // Field to contact page
    $wp_customize->add_setting('reactor_options[contacteCentre]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    // Field to contact page
    $wp_customize->add_control('reactor_options[contacteCentre]', [
        'label' => __('Contacte principal', 'reactor'),
        'section' => 'reactor_customizer_idcentre',
        'description' => " <a style='float:left;margin-top:-44px; margin-left:135px;height:0px;width:0px;font-style: normal;' target='_blank' href='http://agora.xtec.cat/moodle/moodle/mod/glossary/view.php?id=1741&mode=entry&hook=2681'> <br> (" . __('Ajuda', 'reactor') . ")</a>" . __('Pàgina de contacte', 'reactor'),
        'priority' => 8,
    ]);

    // Add field mail
    $wp_customize->add_setting('reactor_options[correuCentre]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('reactor_options[correuCentre]', [
        'section' => 'reactor_customizer_idcentre',
        'description' => __('Adreça electrònica', 'reactor'),
        'priority' => 9,
    ]);

    global $colors_nodes;

    foreach ($colors_nodes as $color_value => $color_properties) {
        $paletes[$color_value] = $color_properties['nom'];
    }

    $wp_customize->add_section('reactor_customizer_colors', [
        'title' => __('Colors', 'reactor'),
        'priority' => 7,
    ]);

    $wp_customize->add_setting('reactor_options[paleta_colors]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);
    $wp_customize->add_control('reactor_options[paleta_colors]', [
        'label' => __('Paleta', 'reactor'),
        'section' => 'reactor_customizer_colors',
        'type' => 'radio',
        'choices' => $paletes,
    ]);

    $templates = get_theme_support('reactor-page-templates');

    if (!is_array($templates[0])) {
        $templates[0] = [];
    }

    // Front Page
    if (in_array('front-page', $templates[0])) {
        $wp_customize->add_section('frontpage_settings', [
            'title' => __('Pàgina d\'inici', 'reactor'),
            'priority' => 6,
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_setting('reactor_options[frontpage_page]', [
            'default' => '',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'reactor_options[frontpage_page]', [
            'label' => __('Pàgina d\'inici', 'theme-name'),
            'section' => 'frontpage_settings',
            'type' => 'dropdown-pages',
            'settings' => 'reactor_options[frontpage_page]',
            'priority' => 1,
        ]));

        $wp_customize->add_setting('reactor_options[frontpage_post_category]', [
            'default' => '',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control(new WP_Customize_Dropdown_Categories_Control($wp_customize, 'reactor_frontpage_post_category', [
            'label' => __('Categoria d\'articles', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'dropdown-categories',
            'settings' => 'reactor_options[frontpage_post_category]',
            'priority' => 2,
        ]));

        $wp_customize->add_setting('reactor_options[frontpage_layout]', [
            'default' => '2c-r',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control('reactor_options[frontpage_layout]', [
            'label' => __('Composició', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'select',
            'choices' => [
                '1c' => __('Sense barres laterals', 'reactor'),
                '2c-l' => __('Barra esquerra', 'reactor'),
                '2c-r' => __('Barra dreta', 'reactor'),
                '3c-c' => __('Barra esquerra i dreta', 'reactor'),
            ],
            'priority' => 3,
        ]);

        $wp_customize->add_setting('reactor_options[frontpage_posts_per_fila_1]', [
            'default' => '2',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control('reactor_options[frontpage_posts_per_fila_1]', [
            'label' => __('Fila 1', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'select',
            'choices' => [
                '0' => __('0 articles', 'reactor'),
                '1' => __('1 article', 'reactor'),
                '2' => __('2 articles iguals', 'reactor'),
                '33' => __('2 articles (1/3+2/3)', 'reactor'),
                '66' => __('2 articles (2/3+1/3)', 'reactor'),
                '3' => __('3 articles', 'reactor'),
                '4' => __('4 articles', 'reactor'),
            ],
            'priority' => 4,
        ]);

        $wp_customize->add_setting('reactor_options[frontpage_posts_per_fila_2]', [
            'default' => '2',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control('reactor_options[frontpage_posts_per_fila_2]', [
            'label' => __('Fila 2', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'select',
            'choices' => [
                '0' => __('0 articles', 'reactor'),
                '1' => __('1 article', 'reactor'),
                '2' => __('2 articles iguals', 'reactor'),
                '33' => __('2 articles (1/3+2/3)', 'reactor'),
                '66' => __('2 articles (2/3+1/3)', 'reactor'),
                '3' => __('3 articles', 'reactor'),
                '4' => __('4 articles', 'reactor'),
            ],
            'priority' => 5,
        ]);

        $wp_customize->add_setting('reactor_options[frontpage_posts_per_fila_n]', [
            'default' => '3',
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control('reactor_options[frontpage_posts_per_fila_n]', [
            'label' => __('Resta de files', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'select',
            'choices' => [
                '0' => __('0 articles', 'reactor'),
                '1' => __('1 article', 'reactor'),
                '2' => __('2 articles', 'reactor'),
                '3' => __('3 articles', 'reactor'),
                '4' => __('4 articles', 'reactor'),
            ],
            'priority' => 6,
        ]);

        $wp_customize->add_setting('reactor_options[frontpage_number_posts]', [
            'default' => 3,
            'type' => 'option',
            'capability' => 'manage_options',
            'theme_supports' => 'reactor-page-templates',
        ]);

        $wp_customize->add_control('reactor_options[frontpage_number_posts]', [
            'label' => __('Nombre d\'articles per pàgina', 'reactor'),
            'section' => 'frontpage_settings',
            'type' => 'text',
            'priority' => 7,
        ]);
    }

}
