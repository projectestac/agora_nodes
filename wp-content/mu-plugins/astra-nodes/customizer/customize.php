<?php

// Add JavaScript's handlers to make Theme Customizer preview reload changes asynchronously.
add_action('customize_preview_init', function () {
    $data = [
        'logo_image_ceb' => WPMU_PLUGIN_URL . '/astra-nodes/images/logo_ceb.svg',
        'logo_url_ceb' => 'https://www.edubcn.cat/ca/',
        'logo_image_department' => WPMU_PLUGIN_URL . '/astra-nodes/images/logo_department.svg',
        'logo_url_department' => 'https://educacio.gencat.cat/ca/inici/',
    ];
    wp_register_script(
        'astra-nodes-customizer',
        plugins_url('/js/theme-customizer.js', __FILE__),
        ['jquery', 'customize-preview'],
        '',
        true
    );
    wp_localize_script('astra-nodes-customizer', 'astra_nodes', $data);
    wp_enqueue_script(
        'astra-nodes-customizer',
        plugins_url('/js/theme-customizer.js', __FILE__),
        ['jquery', 'customize-preview'],
        '',
        true
    );
});

// Add JavaScript for the universal icon picker.
add_action('customize_controls_enqueue_scripts', function () {
    wp_enqueue_script(
        'universal-icon-picker',
        content_url('mu-plugins/astra-nodes/lib/universal-icon-picker/assets/js/universal-icon-picker.js'),
        [],
        null,
        true
    );
});

// Add custom CSS in the Customizer.
add_action('customize_controls_print_styles', function () {
    wp_enqueue_style(
        'astra-nodes-css',
        content_url('mu-plugins/astra-nodes/styles/customizer.css')
    );
}, 50); // Priority is important.

// Register Customizer settings.
add_action('customize_register', 'nodes_customize_register');

function nodes_customize_register($wp_customize) {

    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Custom_Palette_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Font_Icon_Picker_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Layout_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Logo_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Palette_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Raw_HTML_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Separator_Control.php';
    include_once WPMU_PLUGIN_DIR . '/astra-nodes/classes/WP_Customize_Toggle_Control.php';

    // Panel to group all header settings.

    $wp_customize->add_panel('astra_nodes_customizer_header', [
        'title' => __('Header', 'astra-nodes'),
        'description' => __('Customization of the site header', 'astra-nodes'),
        'priority' => 1,
    ]);

    // Header panel: Logos section.
    $wp_customize->add_section('astra_nodes_customizer_header_logo', [
        'title' => __('Logos', 'astra-nodes'),
        'panel' => 'astra_nodes_customizer_header',
        'priority' => 1,
    ]);

    // Header panel: Logos section: Site Logo.
    $wp_customize->add_setting('astra_nodes_options[custom_logo]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize, 'astra_nodes_customizer_header_logo_image', [
                'label' => __('Header logo', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_header_logo',
                'description' => __('Recommendations: 300 x 200 px and less than 200 kB', 'astra-nodes'),
                'settings' => 'astra_nodes_options[custom_logo]',
                'priority' => 1,
            ]
        )
    );

    // Header panel: Logos section: Organism logo.
    if (is_xtec_super_admin()) {
        $wp_customize->add_setting('astra_nodes_options[organism_logo]', [
            'default' => 'department',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);
    }

    $wp_customize->add_control(
        new WP_Customize_Logo_Control(
            $wp_customize, 'astra_nodes_customizer_header_organism_logo', [
                'label' => __('Organism logo', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_header_logo',
                'settings' => 'astra_nodes_options[organism_logo]',
                'priority' => 2,
                'type' => 'radio',
                'choices' => [
                    'department' => __('Department of Education', 'astra-nodes'),
                    'ceb' => __('CEB', 'astra-nodes'),
                ],
            ]
        )
    );

    // Header panel: Blog name section.
    $wp_customize->add_section('astra_nodes_customizer_header_blog_name', [
        'title' => __('Site name', 'astra-nodes'),
        'panel' => 'astra_nodes_customizer_header',
        'priority' => 2,
    ]);

    // Header panel: Blog name section: Text preceding the blog name.
    $wp_customize->add_setting('astra_nodes_options[pre_blog_name]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_pre_blog_name', [
        'label' => __('Text preceding the blog name', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_blog_name',
        'settings' => 'astra_nodes_options[pre_blog_name]',
        'priority' => 1,
    ]);

    // Header panel: Blog name section: Blog name.
    $wp_customize->add_setting('blogname', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_blog_name', [
        'label' => __('Blog name', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_blog_name',
        'settings' => 'blogname',
        'priority' => 2,
    ]);

    // Header panel: Blog name section: Text following the blog name.
    $wp_customize->add_setting('blogdescription', [
        'default' => '',
        'type' => 'option',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_blog_description', [
        'label' => __('Blog description', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_blog_name',
        'settings' => 'blogdescription',
        'priority' => 3,
    ]);

    // Header panel: Contact information section.
    $wp_customize->add_section('astra_nodes_customizer_header_contact', [
        'title' => __('Contact information', 'astra-nodes'),
        'panel' => 'astra_nodes_customizer_header',
        'priority' => 3,
    ]);

    // Header panel: Contact information section: Postal address.
    $wp_customize->add_setting('astra_nodes_options[postal_address]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_postal_address', [
        'label' => __('Postal address', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[postal_address]',
        'priority' => 1,
    ]);

    // Header panel: Contact information section: Postal code and city.
    $wp_customize->add_setting('astra_nodes_options[postal_code_city]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_postal_code_city', [
        'label' => __('Postal code and city', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[postal_code_city]',
        'priority' => 2,
    ]);

    // Header panel: Contact information section: Phone number.
    $wp_customize->add_setting('astra_nodes_options[phone_number]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_phone_number', [
        'label' => __('Phone number', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[phone_number]',
        'priority' => 3,
    ]);

    // Header panel: Contact information section: Link to the map.
    $wp_customize->add_setting('astra_nodes_options[link_to_map]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_link_to_map', [
        'label' => __('Map', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[link_to_map]',
        'priority' => 4,
    ]);

    // Header panel: Contact information section: Link to the contact page.
    $wp_customize->add_setting('astra_nodes_options[contact_page]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_link_to_contact_page', [
        'label' => __('Contact', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[contact_page]',
        'priority' => 5,
    ]);

    // Header panel: Contact information section: Email address.
    $wp_customize->add_setting('astra_nodes_options[email_address]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_header_email', [
        'label' => __('Email', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_contact',
        'settings' => 'astra_nodes_options[email_address]',
        'priority' => 6,
    ]);

    // Header panel: Buttons section.

    $wp_customize->add_section('astra_nodes_customizer_header_buttons', [
        'title' => __('Buttons', 'astra-nodes'),
        'panel' => 'astra_nodes_customizer_header',
        'priority' => 4,
    ]);

    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {

        // Header panel: Buttons section: Add the Icon title text, the preview and the button to change the icon.
        $wp_customize->add_setting('icon_preview_' . $i, [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Raw_HTML_Control(
                $wp_customize, 'icon_preview_' . $i, [
                    'id' => 'icon_preview_' . $i,
                    'section' => 'astra_nodes_customizer_header_buttons',
                    'priority' => 1,
                    'content' => '
                        <strong>' . __('Icon' . ' ' . $i, 'astra-nodes') . '</strong>&nbsp;&nbsp;&nbsp;&nbsp;
                        <i class="' . get_theme_mod('astra_nodes_options')['header_icon_' . $i . '_classes'] . ' astra-nodes-customizer-theme-icon"></i>
                        <input type="button" id="_customize-input-astra_nodes_customizer_header_button_' . $i . '"
                               class="button change-theme universal-icon-picker-button" value="' . __('Change') . '" />
                        ',
                ]
            )
        );

        // Header panel: Buttons section: Field to receive the selected icon value.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_classes]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
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

        // Header panel: Buttons section: Text for the link.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_text]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
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

        // Header panel: Buttons section: URL for the link.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_link]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
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

        // Header panel: Buttons section: Open the link in a new tab.
        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_open_in_new_tab]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_open_in_new_tab', [
            'label' => __('Open in new tab', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_open_in_new_tab]',
            'priority' => 1,
            'type' => 'checkbox',
        ]);

        // Header panel: Buttons section: Add the javascript code to load the icon picker.
        $wp_customize->add_setting('header_buttons_script_' . $i, [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Font_Icon_Picker_Control(
                $wp_customize, 'header_buttons_script_' . $i, [
                    'section' => 'astra_nodes_customizer_header_buttons',
                    'settings' => 'header_buttons_script_' . $i,
                    'priority' => 1,
                    'i' => $i,
                ]
            )
        );

    }

    // Header panel: Buttons section: Add the javascript code to translate the text of the icon picker.
    $wp_customize->add_setting('translation_script', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Raw_HTML_Control(
            $wp_customize, 'translation_script', [
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
            ]
        )
    );


    // Theme colors.

    $wp_customize->add_section('astra_nodes_customizer_themes', [
        'title' => __('Theme Colors', 'astra-nodes'),
        'priority' => 2,
    ]);

    // Theme colors: Select palette.
    $wp_customize->add_setting('astra-color-palettes[currentPalette]', [
        'default' => '',
        'type' => 'option',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Palette_Control(
            $wp_customize, 'astra-color-palettes[currentPalette]', [
                'label' => __('Theme Colors', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_themes',
                'settings' => 'astra-color-palettes[currentPalette]',
                'priority' => 1,
                'type' => 'radio',
            ]
        )
    );

    if (is_xtec_super_admin()) {

        $custom_palette = get_option('astra-color-palettes')['palettes']['Personalitzada'];

        // Palettes: Separator.
        $wp_customize->add_setting('astra-color-palettes[separator]', [
            'default' => '',
            'type' => 'theme_mod',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Separator_Control(
                $wp_customize, 'astra-color-palettes-separator', [
                    'label' => '',
                    'section' => 'astra_nodes_customizer_themes',
                    'settings' => 'astra-color-palettes[separator]',
                    'priority' => 2,
                ]
            )
        );

        // Custom palette.
        $wp_customize->add_setting('astra-color-palettes-personalitzada', [
            'default' => '',
            'type' => 'option',
            'capability' => 'edit_theme_options',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Custom_Palette_Control(
                $wp_customize, 'astra_nodes_palettes_custom', [
                    'label' => __('Custom', 'astra-nodes'),
                    'section' => 'astra_nodes_customizer_themes',
                    'settings' => 'astra-color-palettes-personalitzada',
                    'priority' => 3,
                ]
            )
        );

        // Color picker #1.
        $wp_customize->add_setting('astra-color-palettes[palettes][Personalitzada][0]', [
            'default' => $custom_palette[0],
            'type' => 'option',
            'capability' => 'edit_theme_options',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'astra_nodes_palettes_custom_0', [
                    'label' => __('Primary color', 'astra-nodes'),
                    'section' => 'astra_nodes_customizer_themes',
                    'settings' => 'astra-color-palettes[palettes][Personalitzada][0]',
                    'priority' => 4,
                ]
            )
        );

        // Color picker #2.
        $wp_customize->add_setting('astra-color-palettes[palettes][Personalitzada][1]', [
            'default' => $custom_palette[1],
            'type' => 'option',
            'capability' => 'edit_theme_options',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Color_Control(
                $wp_customize, 'astra_nodes_palettes_custom_1', [
                    'label' => __('Secondary color', 'astra-nodes'),
                    'section' => 'astra_nodes_customizer_themes',
                    'settings' => 'astra-color-palettes[palettes][Personalitzada][1]',
                    'priority' => 5,
                ]
            )
        );

    }


    // Panel to group all front page settings.

    $wp_customize->add_panel('astra_nodes_front_page', [
        'title' => __('Front Page', 'astra-nodes'),
        'description' => __('Customization of the front page', 'astra-nodes'),
        'priority' => 3,
    ]);


    // Front page slider.

    $wp_customize->add_section('astra_nodes_customizer_front_page_slider', [
        'title' => __('Slider', 'astra-nodes'),
        'panel' => 'astra_nodes_front_page',
        'priority' => 1,
    ]);

    // Front page slider: Enable slider.
    $wp_customize->add_setting('astra_nodes_options[front_page_slider_enable]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    if (is_xtec_super_admin()) {
        $wp_customize->add_control(
            new WP_Customize_Toggle_Control(
                $wp_customize, 'astra_nodes_customizer_front_page_slider_enable', [
                    'label' => __('Show the slider', 'astra-nodes'),
                    'section' => 'astra_nodes_customizer_front_page_slider',
                    'settings' => 'astra_nodes_options[front_page_slider_enable]',
                    'priority' => 1,
                ]
            )
        );
    }

    // Front page slider: Minimum height of the images.
    $wp_customize->add_setting('astra_nodes_options[front_page_slider_min_height]', [
        'default' => 500,
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_slider_min_height', [
        'label' => __('Height of the slider', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_slider',
        'settings' => 'astra_nodes_options[front_page_slider_min_height]',
        'priority' => 2,
        'type' => 'number',
        'input_attrs' => [
            'min' => 200,
            'max' => 1000,
            'step' => 10,
        ],
    ]);

    // Front page slider: Play automatically.
    $wp_customize->add_setting('astra_nodes_options[front_page_slider_autoplay]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_slider_autoplay', [
        'label' => __('Play automatically', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_slider',
        'settings' => 'astra_nodes_options[front_page_slider_autoplay]',
        'priority' => 3,
        'type' => 'checkbox',
    ]);

    // Front page slider: Show arrows.
    $wp_customize->add_setting('astra_nodes_options[front_page_slider_arrows]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_slider_arrows', [
        'label' => __('Show arrows', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_slider',
        'settings' => 'astra_nodes_options[front_page_slider_arrows]',
        'priority' => 4,
        'type' => 'select',
        'choices' => [
            'yes' => __('Yes'),
            'no' => __('No'),
        ],
    ]);

    // Front page slider: Show dots.
    $wp_customize->add_setting('astra_nodes_options[front_page_slider_dots]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_slider_dots', [
        'label' => __('Show dots', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_slider',
        'settings' => 'astra_nodes_options[front_page_slider_dots]',
        'priority' => 5,
        'type' => 'select',
        'choices' => [
            'yes' => __('Yes'),
            'no' => __('No'),
        ],
    ]);

    for ($i = 1, $num_controls = 6; $i <= NUM_SLIDES_IN_FRONT_PAGE; $i++) {

        // Front page slider: Separator.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_separator]', [
            'default' => '',
            'type' => 'theme_mod',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Separator_Control(
                $wp_customize, 'front_page_slider_separator_' . $i, [
                    'label' => '',
                    'section' => 'astra_nodes_customizer_front_page_slider',
                    'settings' => 'astra_nodes_options[front_page_slider_separator]',
                    'priority' => 6 + ($num_controls * $i),
                ]
            )
        );

        // Front page slider: Image $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_image_' . $i . ']', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize, 'astra_nodes_customizer_front_page_slider_image_' . $i, [
                    'label' => __('Image', 'astra-nodes') . ' ' . $i,
                    'section' => 'astra_nodes_customizer_front_page_slider',
                    'description' => __('astra-nodes', 'Recommendations: 1920 x 1080 px and less than 200 kB'),
                    'settings' => 'astra_nodes_options[front_page_slider_image_' . $i . ']',
                    'priority' => 7 + ($num_controls * $i),
                ]
            )
        );

        // Front page slider: Link for image $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_link_' . $i . ']', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_slider_link_' . $i, [
            'label' => __('Link', 'astra-nodes') . ' ' . $i,
            'section' => 'astra_nodes_customizer_front_page_slider',
            'settings' => 'astra_nodes_options[front_page_slider_link_' . $i . ']',
            'priority' => 8 + ($num_controls * $i),
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('https://', 'astra-nodes'),
            ],
        ]);

        // Front page slider: Open link in new tab for image $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_open_in_new_tab_' . $i . ']', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_slider_open_in_new_tab_' . $i, [
            'label' => __('Open in new tab', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_front_page_slider',
            'settings' => 'astra_nodes_options[front_page_slider_open_in_new_tab_' . $i . ']',
            'priority' => 9 + ($num_controls * $i),
            'type' => 'checkbox',
        ]);

        // Front page slider: Heading for image $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_heading_' . $i . ']', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_slider_heading_' . $i, [
            'label' => __('Heading', 'astra-nodes') . ' ' . $i,
            'section' => 'astra_nodes_customizer_front_page_slider',
            'settings' => 'astra_nodes_options[front_page_slider_heading_' . $i . ']',
            'priority' => 10 + ($num_controls * $i),
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Heading', 'astra-nodes') . ' ' . $i,
            ],
        ]);

        // Front page slider: Text for image $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_slider_text_' . $i . ']', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_slider_text_' . $i, [
            'label' => __('Text', 'astra-nodes') . ' ' . $i,
            'section' => 'astra_nodes_customizer_front_page_slider',
            'settings' => 'astra_nodes_options[front_page_slider_text_' . $i . ']',
            'priority' => 11 + ($num_controls * $i),
            'type' => 'textarea',
            'input_attrs' => [
                'placeholder' => __('Text', 'astra-nodes') . ' ' . $i,
            ],
        ]);

    }


    // Front page Layout.

    $wp_customize->add_section('astra_nodes_customizer_front_page_layout', [
        'title' => __('Layout', 'astra-nodes'),
        'panel' => 'astra_nodes_front_page',
        'priority' => 2,
    ]);

    // Front page Layout: Radio buttons to select the format of the front page.

    $wp_customize->add_setting('astra_nodes_options[front_page_layout]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'edit_theme_options',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Layout_Control(
            $wp_customize, 'astra_nodes_options[front_page_layout]', [
                'label' => __('Front Page Layout', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_front_page_layout',
                'settings' => 'astra_nodes_options[front_page_layout]',
                'priority' => 1,
                'type' => 'radio',
                'choices' => [
                    'boxes' => __('Cards', 'astra-nodes'),
                    'sidebar_boxes' => __('Sidebar and cards', 'astra-nodes'),
                    'sidebar_news' => __('Sidebar and news', 'astra-nodes'),
                    'wp_default' => __('WordPress default', 'astra-nodes'),
                    'wp_default_no_sidebar' => __('WordPress default - No sidebar', 'astra-nodes'),
                ],
            ]
        )
    );


    // Front page cards.

    $wp_customize->add_section('astra_nodes_customizer_front_page_cards', [
        'title' => __('Cards', 'astra-nodes'),
        'panel' => 'astra_nodes_front_page',
        'priority' => 3,
    ]);

    // Front page cards: Activate cards in front page.
    $wp_customize->add_setting('astra_nodes_options[front_page_cards_enable]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Toggle_Control(
            $wp_customize, 'astra_nodes_customizer_front_page_cards_enable', [
                'label' => __('Show the cards', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_front_page_cards',
                'settings' => 'astra_nodes_options[front_page_cards_enable]',
                'priority' => 1,
            ]
        )
    );

    for ($i = 1, $num_controls = 5; $i <= NUM_CARDS_IN_FRONT_PAGE; $i++) {

        // Cards: Separator.
        $wp_customize->add_setting('astra_nodes_options[front_page_cards_separator]', [
            'default' => '',
            'type' => 'theme_mod',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Separator_Control(
                $wp_customize, 'front_page_cards_separator_' . $i, [
                    'label' => '',
                    'section' => 'astra_nodes_customizer_front_page_cards',
                    'settings' => 'astra_nodes_options[front_page_cards_separator]',
                    'priority' => 1 + ($num_controls * $i),
                ]
            )
        );

        // Title of the card $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_card_' . $i . '_title]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_card_' . $i . '_title', [
            'label' => __('Card', 'astra-nodes') . ' ' . $i,
            'section' => 'astra_nodes_customizer_front_page_cards',
            'settings' => 'astra_nodes_options[front_page_card_' . $i . '_title]',
            'priority' => 2 + ($num_controls * $i),
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('Card title', 'astra-nodes'),
            ],
        ]);

        // Image of the card $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_card_' . $i . '_image]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control(
            new WP_Customize_Image_Control(
                $wp_customize, 'astra_nodes_customizer_front_page_card_' . $i . '_image', [
                    'label' => '',
                    'section' => 'astra_nodes_customizer_front_page_cards',
                    'description' => 'Recommendations: 300 x 200 px and less than 200 kB',
                    'settings' => 'astra_nodes_options[front_page_card_' . $i . '_image]',
                    'priority' => 3 + ($num_controls * $i),
                ]
            )
        );

        // Color picker for card $i.
        if (is_xtec_super_admin()) {
            $wp_customize->add_setting('astra_nodes_options[front_page_card_' . $i . '_color]', [
                'default' => '',
                'type' => 'theme_mod',
                'capability' => 'manage_options',
                'transport' => 'postMessage',
            ]);

            $wp_customize->add_control(
                new WP_Customize_Color_Control(
                    $wp_customize, 'astra_nodes_customizer_front_page_card_' . $i . '_color', [
                        'label' => __('Color', 'astra-nodes'),
                        'section' => 'astra_nodes_customizer_front_page_cards',
                        'settings' => 'astra_nodes_options[front_page_card_' . $i . '_color]',
                        'priority' => 4 + ($num_controls * $i),
                    ]
                )
            );
        }

        // URL of the card $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_card_' . $i . '_url]', [
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'default' => '',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_card_' . $i . '_url', [
            'label' => __('URL', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_front_page_cards',
            'settings' => 'astra_nodes_options[front_page_card_' . $i . '_url]',
            'priority' => 5 + ($num_controls * $i),
            'type' => 'text',
            'input_attrs' => [
                'placeholder' => __('https://', 'astra-nodes'),
            ],
        ]);

        // Open the link in a new tab $i.
        $wp_customize->add_setting('astra_nodes_options[front_page_card_' . $i . '_open_in_new_tab]', [
            'default' => '',
            'type' => 'theme_mod',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_front_page_card_' . $i . '_open_in_new_tab', [
            'label' => __('Open in new tab', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_front_page_cards',
            'settings' => 'astra_nodes_options[front_page_card_' . $i . '_open_in_new_tab]',
            'priority' => 6 + ($num_controls * $i),
            'type' => 'checkbox',
        ]);

    }


    // Front page notice.

    $wp_customize->add_section('astra_nodes_customizer_front_page_notice', [
        'title' => __('Notice', 'astra-nodes'),
        'panel' => 'astra_nodes_front_page',
        'priority' => 4,
    ]);

    // Front page notice: Enable notice.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_enable]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Toggle_Control(
            $wp_customize, 'astra_nodes_customizer_front_page_notice_enable', [
                'label' => __('Show the notice', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_front_page_notice',
                'settings' => 'astra_nodes_options[front_page_notice_enable]',
                'priority' => 1,
            ]
        )
    );

    // Front page notice: Layout.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_layout]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_notice_layout', [
        'label' => __('Show'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_layout]',
        'type' => 'radio',
        'choices' => [
            'image' => __('Image', 'astra-nodes'),
            'text' => __('Text', 'astra-nodes'),
            'image_text' => __('Image and text', 'astra-nodes'),
        ],
        'priority' => 2,
    ]);

    // Front page notice: Image.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_image]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Image_Control(
            $wp_customize, 'astra_nodes_customizer_front_page_notice_image', [
                'label' => '',
                'section' => 'astra_nodes_customizer_front_page_notice',
                'settings' => 'astra_nodes_options[front_page_notice_image]',
                'priority' => 3,
            ]
        )
    );

    // Front page notice: Background color.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_background_color]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Color_Control(
            $wp_customize, 'front_page_notice_background_color', [
                'label' => __('Background color', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_front_page_notice',
                'settings' => 'astra_nodes_options[front_page_notice_background_color]',
                'priority' => 4,
            ]
        )
    );

    // Front page notice: Link.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_url]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('front_page_notice_url', [
        'label' => __('Image URL', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_url]',
        'priority' => 5,
        'type' => 'text',
        'input_attrs' => [
            'placeholder' => __('https://', 'astra-nodes'),
        ],
    ]);

    // Front page notice: Open the link in a new tab.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_open_in_new_tab]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('front_page_notice_open_in_new_tab', [
        'label' => __('Open in new tab', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_open_in_new_tab]',
        'priority' => 6,
        'type' => 'checkbox',
    ]);

    // Front page notice: Text preceding the title.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_pre_title]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('front_page_notice_pre_title', [
        'label' => __('Text preceding the title', 'astra-nodes') . ' (' . __('optional', 'astra-nodes') . ')',
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_pre_title]',
        'type' => 'text',
        'priority' => 7,
    ]);

    // Front page notice: Title.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_title]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('front_page_notice_title', [
        'label' => __('Title', 'astra-nodes') . ' (' . __('optional', 'astra-nodes') . ')',
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_title]',
        'type' => 'text',
        'priority' => 8,
    ]);

    // Front page notice: Content.
    $wp_customize->add_setting('astra_nodes_options[front_page_notice_content]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
        'transport' => 'postMessage',
    ]);

    $wp_customize->add_control('front_page_notice_content', [
        'label' => __('Content', 'astra-nodes') . ' (' . __('optional', 'astra-nodes') . ')',
        'section' => 'astra_nodes_customizer_front_page_notice',
        'settings' => 'astra_nodes_options[front_page_notice_content]',
        'type' => 'textarea',
        'priority' => 9,
    ]);


    // Front page news.

    $wp_customize->add_section('astra_nodes_customizer_front_page_news', [
        'title' => __('News', 'astra-nodes'),
        'panel' => 'astra_nodes_front_page',
        'priority' => 5,
    ]);

    // Front page news: Enable slider.
    $wp_customize->add_setting('astra_nodes_options[front_page_news_enable]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control(
        new WP_Customize_Toggle_Control(
            $wp_customize, 'astra_nodes_customizer_front_page_news_enable', [
                'label' => __('Show the news', 'astra-nodes'),
                'section' => 'astra_nodes_customizer_front_page_news',
                'settings' => 'astra_nodes_options[front_page_news_enable]',
                'priority' => 1,
            ]
        )
    );

    // Get the list of categories (array of objects).
    $categories = get_categories([
        'orderby' => 'name',
        'order' => 'ASC',
    ]);

    // Get an array with the form [term_id => category_name].
    $categories_filtered = array_reduce($categories, static function ($carry, $object) {
        $carry[$object->term_id] = $object->name;
        return $carry;
    }, []);

    // Front page news: Post category.
    $wp_customize->add_setting('astra_nodes_options[front_page_news_category]', [
        'default' => array_search('Portada', $categories_filtered, true),
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_news_category', [
        'label' => __('Category', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_news',
        'settings' => 'astra_nodes_options[front_page_news_category]',
        'priority' => 2,
        'type' => 'select',
        'choices' => $categories_filtered,
    ]);

    // Front page news: Number of news.
    $wp_customize->add_setting('astra_nodes_options[front_page_news_num_posts]', [
        'default' => 20,
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_front_page_news_number', [
        'label' => __('Number of posts', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_front_page_news',
        'settings' => 'astra_nodes_options[front_page_news_num_posts]',
        'priority' => 3,
        'type' => 'number',
        'input_attrs' => [
            'min' => 1,
            'max' => 100,
            'step' => 1,
        ],
    ]);


    // Pages configuration.

    $wp_customize->add_section('astra_nodes_customizer_pages', [
        'title' => __('Pages', 'astra-nodes'),
        'priority' => 4,
    ]);

    $wp_customize->add_setting('astra_nodes_options[pages_sidebar]', [
        'default' => '',
        'type' => 'theme_mod',
        'capability' => 'manage_options',
    ]);

    $wp_customize->add_control('astra_nodes_customizer_pages_sidebar', [
        'label' => __('Sidebar type', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_pages',
        'settings' => 'astra_nodes_options[pages_sidebar]',
        'type' => 'radio',
        'choices' => [
            'menu' => __('Menu of pages', 'astra-nodes'),
            'widgets' => __('Widgets', 'astra-nodes'),
            'none' => __('No sidebar', 'astra-nodes'),
        ],
    ]);

}

add_action('customize_register', function ($wp_customize) {
    if (!is_xtec_super_admin()) {
        $wp_customize->remove_section('static_front_page');
    }
}, 11);
