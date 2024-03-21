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
        'type' => 'theme_mod',
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
        'type' => 'theme_mod',
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
        'type' => 'theme_mod',
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
        'type' => 'theme_mod',
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
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
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
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
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
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
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
        'type' => 'theme_mod',
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

    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Font_Icon_Picker_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Raw_HTML_Control.php';

    $wp_customize->add_section('astra_nodes_customizer_header_buttons', [
        'title' => __('Header Buttons', 'astra-nodes'),
        'priority' => 2,
    ]);

    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {

        // Add the Icon title text, the preview and the button to change the icon.
        $wp_customize->add_setting('icon_preview_' . $i, [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
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
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
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
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
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
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
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

        // Field to open the link in a new tab.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_open_in_new_tab]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_open_in_new_tab', [
            'label' => __('Open in new tab', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_open_in_new_tab]',
            'priority' => 1,
            'type' => 'checkbox',
        ]);

        // Add the javascript code to load the icon picker.
        $wp_customize->add_setting('header_buttons_script_' . $i, [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
        ]);

        $wp_customize->add_control(new WP_Customize_Font_Icon_Picker_Control($wp_customize, 'header_buttons_script_' . $i, [
            'label' => __('Header Button HTML', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'header_buttons_script_' . $i,
            'priority' => 1,
            'i' => $i,
        ]));

    }

    // Add the javascript code to translate the text of the icon picker.
    $wp_customize->add_setting('translation_script', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(new WP_Customize_Raw_HTML_Control($wp_customize, 'translation_script', [
        'label' => __('Header Button HTML', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_buttons',
        'priority' => 2,
        'content' =>
            '<script>
            // Translate the text of the icon picker.
            var elements =
                [
                    {"selector" : ".uip-insert-icon-button", "text" : "' . __('Insert', 'astra-nodes') . '", "type" : "textContent"},
                    {"selector" : ".uip-modal--icon-search > input", "text" : "' . __('Filter by name', 'astra-nodes') . '", "type" : "placeholder"}
                ];

            // Wait for the modal to be loaded and then translate the text.
            const intervalId = setInterval(() => {
                const modalContent = document.querySelector(".uip-modal--content");
                if (modalContent) {
                    elements.forEach((element) => {
                        const el = modalContent.querySelector(element.selector);
                        if (el) {
                            el[element.type] = element.text;
                        }
                    });
                    clearInterval(intervalId);
                }
            }, 100);
        </script>',
    ]));

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
                    'priority' => $i + 3,
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

}
