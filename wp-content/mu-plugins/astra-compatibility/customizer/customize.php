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

    class WP_Customize_Raw_HTML extends WP_Customize_Control {
        public $type = 'html';
        private $content;

        public function __construct($manager, $id, $args = array()) {
            $this->content = $args['content'];
            parent::__construct($manager, $id, $args);
        }

        public function render_content() {
            echo $this->content;
        }
    }

    // To load dependencies
    class WP_Customize_Load_Dependencies extends WP_Customize_Control {
        public $type = 'html';

        public function render_content() {
            echo '
            <link rel="stylesheet" href="' . esc_url(content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/stylesheets/universal-icon-picker.min.css')) . '">
            <script src="' . esc_url(content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/js/universal-icon-picker.js')) . '"></script>';
        }
    }

    // Script to handle picker
    class WP_Customize_HTML_Control extends WP_Customize_Control {
        public $type = 'html';
        private $i;

        public function __construct($manager, $id, $args = array()) {
            $this->i = $args['i'];
            parent::__construct($manager, $id, $args);
        }

        public function render_content() {
            echo '
            <style>
                .uip-modal {
                    z-index: 1000000;
                }
            </style>
    
            <script>
                var uip = new UniversalIconPicker("#_customize-input-astra_nodes_customizer_header_button_' . esc_attr($this->i) . '", {
                    iconLibraries: [
                        "' . esc_url(content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/icons-libraries/happy-icons.min.json')) . '",
                        "' . esc_url(content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/icons-libraries/font-awesome.min.json')) . '"
                    ],
                    iconLibrariesCss: [
                        "' . esc_url(content_url('mu-plugins/astra-lib/universal-icon-picker-main/assets/stylesheets/happy-icons.min.css')) . '",
                        "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
                    ],
                    onSelect: function(jsonIconData) {
                        var iconField = document.querySelector("#_customize-input-astra_nodes_customizer_header_icon_' . esc_attr($this->i) . '_value");
    
                        // Changing field value
                        iconField.value = jsonIconData.iconClass;
    
                        // Simulating a user changing the field, to trigger WordPress "Publish" button.
                        iconField.dispatchEvent(new Event("change"));

                        // Updating UI
                        var iconImage = document.querySelector("#customize-control-icon_preview_' . esc_attr($this->i) . ' > i");
                        iconImage.setAttribute("class", jsonIconData.iconClass);
                    },
                    onReset: function() {
                        // Do something on reset if needed
                    }
                });
            </script>';
        }
    }

    $wp_customize->add_section('astra_nodes_customizer_header_buttons', [
        'title' => __('Header Buttons', 'astra-nodes'),
        'priority' => 2,
    ]);

    $wp_customize->add_setting('header_buttons_loading_scripts', array(
        'default' => '',
    ));

    // Loading dependencies
    $wp_customize->add_control(new WP_Customize_Load_Dependencies($wp_customize, 'header_buttons_loading_scripts', array(
        'label' => __('Header Button HTML', 'astra-nodes'),
        'section' => 'astra_nodes_customizer_header_buttons',
        'settings' => 'header_buttons_loading_scripts',
        'priority' => 1,
    )));

    $header_buttons = [
        ['default_url' => 'https://www.google.com'],
        ['default_url' => 'https://www.google.com'],
        ['default_url' => 'https://www.google.com'],
        ['default_url' => 'https://www.google.com'],
        ['default_url' => 'https://www.google.com'],
        ['default_url' => 'https://www.google.com'],
    ];

    foreach ($header_buttons as $c => $cValue) {

        $i = $c + 1;

        // Button to select icon

        $wp_customize->add_setting('astra_nodes_options[header_button_' . $i . '_icon]', [
            'default' => '',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control(
            'astra_nodes_customizer_header_button_' . $i, [
                'label' => __('Canvia la icona ' . $i, 'astra-nodes'),
                'section' => 'astra_nodes_customizer_header_buttons',
                'settings' => 'astra_nodes_options[header_button_' . $i . '_icon]',
                'type' => 'button',
                'input_attrs' => array(
                    'value' => __('Canvia la icona ' . $i, 'astra-nodes'),
                ),
                'priority' => 2,
            ]
        );

        // Field to receive the select icon value
        // We simulate a change event in this field to trigger the "Publicate" button of WordPress

        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_value]', [
            'default' => '',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_value', [
            'label' => __('Valor de la icona ' . $i, 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_value]',
            'priority' => 2,
            'type' => 'text',
            'input_attrs' => array(
                'placeholder' => __('Selecciona una icona', 'astra-nodes'),
                'style' => 'display: none',
            ),
        ]);

        $wp_customize->add_setting('icon_preview_' . $i, array(
            'default' => '',
        ));

        $wp_customize->add_control(new WP_Customize_Raw_HTML($wp_customize, 'icon_preview_' . $i, array(
            'id' => 'icon_preview_' . $i,
            'label' => __('Header Button HTML', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'priority' => 2,
            'content' => '<i style="font-size: 35px" class="' . get_theme_mod('astra_nodes_options')['header_icon_' . $i . '_value'] . '"></i>',
        )));

        // Field to edit URL

        $wp_customize->add_setting('astra_nodes_options[header_icon_' . $i . '_url]', [
            'default' => '',
            'capability' => 'manage_options',
            'transport' => 'postMessage',
        ]);

        $wp_customize->add_control('astra_nodes_customizer_header_icon_' . $i . '_url', [
            'label' => __('URL de la icona ' . $i, 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'astra_nodes_options[header_icon_' . $i . '_url]',
            'priority' => 2,
            'type' => 'text',
            'input_attrs' => array(
                'placeholder' => __('Introdueix l\'URL aquí', 'astra-nodes'),
            ),
        ]);

        $wp_customize->add_setting('header_buttons_script_' . $i, array(
            'default' => '',
        ));

        $wp_customize->add_control(new WP_Customize_HTML_Control($wp_customize, 'header_buttons_script_' . $i, array(
            'label' => __('Header Button HTML', 'astra-nodes'),
            'section' => 'astra_nodes_customizer_header_buttons',
            'settings' => 'header_buttons_script_' . $i,
            'priority' => 10,
            'i' => $i,
        )));
    }

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
