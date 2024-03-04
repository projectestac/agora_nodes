<?php

// Customizer: JavaScript's handlers to make Theme Customizer preview reload changes asynchronously.
add_action('customize_preview_init', function () {
    wp_enqueue_script(
        'astra-nodes-customizer',
        plugins_url('/js/theme-customizer.js', __FILE__),
        ['jquery', 'customize-preview'],
        '',
        true
    );
});

// Customizer: Add custom CSS.
add_action('customize_controls_print_styles', function () {
    $custom_css = '
        .customize-control {
            margin-top: 10px !important;
            padding-top: 10px;
        }
        .uip-modal {
            z-index: 1000000;
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
    $wp_customize->add_setting('blogname', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_blog_name', [
        'label' => __('Blog name', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'blogname',
        'priority' => 3,
    ]);

    // Text following the blog name.
    $wp_customize->add_setting('blogdescription', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_blog_description', [
        'label' => __('Blog description', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'blogdescription',
        'priority' => 4,
    ]);

    // Postal address.
    $wp_customize->add_setting('astra_nodes_options[postal_address]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_postal_address', [
        'label' => __('Postal address', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[postal_address]',
        'priority' => 5,
    ]);

    // Postal code and city.
    $wp_customize->add_setting('astra_nodes_options[postal_code_city]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_postal_code_city', [
        'label' => __('Postal code and city', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[postal_code_city]',
        'priority' => 6,
    ]);

    // Phone number.
    $wp_customize->add_setting('astra_nodes_options[phone_number]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_phone_number', [
        'label' => __('Phone number', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[phone_number]',
        'priority' => 8,
    ]);

    // Link to the map.
    $wp_customize->add_setting('astra_nodes_options[link_to_map]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_link_to_map', [
        'label' => __('Map', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[link_to_map]',
        'priority' => 9,
    ]);

    // Link to the contact page.
    $wp_customize->add_setting('astra_nodes_options[contact_page]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_link_to_contact_page', [
        'label' => __('Contact', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[contact_page]',
        'priority' => 10,
    ]);

    // Email address.
    $wp_customize->add_setting('astra_nodes_options[email_address]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_email', [
        'label' => __('Email', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header',
        'settings' => 'astra_nodes_options[email_address]',
        'priority' => 11,
    ]);


    // Header: Buttons section.

    include_once WPMU_PLUGIN_DIR . '/astra-compatibility/classes/WP_Customize_Font_Icon_Picker_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-compatibility/classes/WP_Customize_Raw_HTML_Control.php';

    $wp_customize->add_section('astra_nodes_customizer_header_buttons', [
        'title' => __('Header Buttons', 'astra-nodes'),
        'priority' => 2,
    ]);

    define('NUM_BUTTONS', 6);

    for ($i = 1; $i <= NUM_BUTTONS; $i++) {

        // Add the Icon title text, the preview and the button to change the icon.
        $wp_customize->add_setting('icon_preview_' . $i, [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control(new WP_Customize_Raw_HTML_Control($wp_customize, 'icon_preview_' . $i, [
            'id' => 'icon_preview_' . $i,
            'label' => __('Header Button HTML', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'priority' => 1,
            'content' => '
                <strong>' . __('Icon ' . $i, 'astra-nodes') . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                <i style="font-size: 20px;"
                   class="' . get_theme_mod('astra_nodes_options')['header_icon_' . $i . '_classes'] . '"></i>
                <input type="button" id="_customize-input-astra_nodes_customizer_header_button_' . $i . '" class="universal-icon-picker" value="Change" />
                ',
        ]));

        // Field to receive the select icon value. It simulates a change event in this field to trigger
        // the "Publish" button of WordPress.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_classes]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_classes', [
            'label' => '',
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_classes]',
            'priority' => 2,
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Choose an icon', 'astra-nodes'),
                'style' => 'display: none',
            ],
        ]);

        // Field to edit the text for the link.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_text]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_text', [
            'label' => '',
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_text]',
            'priority' => 1,
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Icon text', 'astra-nodes'),
            ],
        ]);

        // Field to edit URL.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_link]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_link', [
            'label' => '',
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_link]',
            'priority' => 1,
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('https://', 'astra-nodes'),
            ],
        ]);

        // Add the javascript code to load the icon picker.
        $wp_customize->add_setting('header_buttons_script_' . $i, [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control(new WP_Customize_Font_Icon_Picker_Control($wp_customize, 'header_buttons_script_' . $i, [
            'label' => __('Header Button HTML', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'header_buttons_script_' . $i,
            'priority' => 1,
            'i' => $i,
        ]));

    }


    // Configure palette colors.

    $wp_customize->add_section('astra_nodes_customizer_themes', [
        'title' => __('Theme Colors', 'astra-nodes'),
        'priority' => 3,
    ]);

    $astra_color_palettes = get_option('astra-color-palettes');
    $current_palette = $astra_color_palettes['currentPalette'];
    $palettes = $astra_color_palettes['palettes'];

    $palettes_form = [];
    foreach ($palettes as $name => $palette) {
        $palettes_form[$name] = $name;
    }

    $wp_customize->add_setting('astra-color-palettes[currentPalette]', [
        'default' => $current_palette,
        'type' => 'option',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_select_color', [
        'label' => __('Theme Colors', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_themes',
        'settings' => 'astra-color-palettes[currentPalette]',
        'priority' => 1,
        'type' => 'radio',
        'choices' => $palettes_form,
    ]);

    // Cards in the front page.
    $wp_customize->add_section('astra_nodes_customizer_front_page_cards', [
        'title' => __('Front Page Cards', 'astra-nodes'),
        'priority' => 4,
    ]);

    // Activate cards in front page.
    $wp_customize->add_setting('astra_nodes_options[cards_enable]', [
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'default' => '',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_cards_enable', [
        'label' => __('Use cards in front page', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_cards',
        'settings' => 'astra_nodes_options[cards_enable]',
        'priority' => 1,
        'type' => 'checkbox',
    ]);

    // Number of cards.
    $wp_customize->add_setting('astra_nodes_options[number_of_cards]', [
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'default' => '',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_cards_number', [
        'label' => __('Number of cards', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_cards',
        'settings' => 'astra_nodes_options[number_of_cards]',
        'priority' => 2,
        'type' => 'number',
        'input_attrs' => [
            'min' => 3,
            'max' => 4,
            'step' => 1,
        ],
    ]);

    define('NUM_CARDS', 4);

    for ($i = 1; $i <= NUM_CARDS; $i++) {

        // Card $i.
        $wp_customize->add_setting('astra_nodes_options[card_' . $i . '_title]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_card_' . $i . '_title', [
            'label' => __('Card', 'astra-nodes') . ' ' . $i,
            'section' => 'astra_nodes_customizer_front_page_cards',
            'settings' => 'astra_nodes_options[card_' . $i . '_title]',
            'priority' => $i + 3,
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Card title', 'astra-nodes'),
            ],
        ]);

        $wp_customize->add_setting('astra_nodes_options[card_' . $i . '_image]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize, 'astra_nodes_customizer_front_page_card_' . $i . '_image', [
                    'label' => '',
                    'section' => 'astra_nodes_customizer_front_page_cards',
                    'description' => 'Recomanacions: 300 x 200 px i menys de 200 kB',
                    'settings' => 'astra_nodes_options[card_' . $i . '_image]',
                    'priority' => $i + 3
                ]
            )
        );

        $wp_customize->add_setting('astra_nodes_options[card_' . $i . '_url]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_card_' . $i . '_url', [
            'label' => __('Camp d\'URL', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_front_page_cards',
            'settings' => 'astra_nodes_options[card_' . $i . '_url]',
            'priority' => $i + 4,
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Introduïu l\'URL', 'astra-nodes'),
            ],
        ]);
    }

    // Notice in the front page.
    $wp_customize->add_section('astra_nodes_customizer_front_page_notice', [
        'title' => __('Front Page Notice', 'astra-nodes'),
        'priority' => 4,
    ]);

    $wp_customize->add_setting('astra_nodes_options[front_page_notice_enable]', [
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'default' => '',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_notice_enable', [
        'label' => __('Utilitzar avís a la pàgina principal', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_enable]',
        'priority' => 1,
        'type' => 'checkbox',
    ]);

    // Afegir paràmetres per a la imatge
    $wp_customize->add_setting('front_page_notice_image');
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'front_page_notice_image', array(
        'label' => __('Imatge de l\'avis a la pàgina principal', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'front_page_notice_image',
    )));

    // Afegir paràmetres per al títol en majúscules
    $wp_customize->add_setting('front_page_notice_title_uppercase');
    $wp_customize->add_control('front_page_notice_title_uppercase', [
        'label' => __('Títol en majúscules', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'type' => 'text',
    ]);



    // Afegir paràmetres per al títol en majúscules
    $wp_customize->add_setting('front_page_notice_title_uppercase');
    $wp_customize->add_control('front_page_notice_title_uppercase', [
        'label' => __('Títol en majúscules', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('front_page_notice_title_normal');
    $wp_customize->add_control('front_page_notice_title_normal', [
        'label' => __('Títol normal', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'type' => 'text',
    ]);

    $wp_customize->add_setting('front_page_notice_text');
    $wp_customize->add_control('front_page_notice_text', [
        'label' => __('Text', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'type' => 'textarea',
    ]);

    /*
    include_once WPMU_PLUGIN_DIR . '/astra-compatibility/classes/WP_Customize_Dropdown_Categories_Control.php';

    // Pestanya Identificació del centre
    $wp_customize->add_section('reactor_customizer_idcentre', [
        'title' => __('Identificació del centre', 'reactor'),
        'priority' => 2,
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
    */

}
