<?php
/*
Plugin Name: AstraFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: Customizations for Astra theme
Version: 1.0
Author: Departament d'Educació - Generalitat de Catalunya
*/

// Only load if Astra theme is active.
if (wp_get_theme()->name !== 'Astra') {
    return;
}

include_once WPMU_PLUGIN_DIR . '/astra-compatibility/get-options.php';
include_once WPMU_PLUGIN_DIR . '/astra-compatibility/customizer/customize.php';
include_once WPMU_PLUGIN_DIR . '/astra-widgets/logo-client-widget.php';

// Load styles to customize Astra theme.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions-css', plugins_url('/astra-styles/style.css', __FILE__));
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
});

// Admin bar: Force to be always shown, including for non-logged users.
add_filter('show_admin_bar', '__return_true');

// Admin bar: Remove WordPress logo.
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('wp-logo');
});

// Admin bar: Add Departament d'Educació logo in the first position and menu with XTEC resources.
add_action('admin_bar_menu', function ($wp_admin_bar) {

    $wp_admin_bar->add_node([
        'id' => 'dept-educacio-logo-wrapper',
        'parent' => '',
        'title' => '<img id="logo-dept-educacio" alt="Logo Educació" src="' . WPMU_PLUGIN_URL . '/astra-images/logo_gene.png' . '">',
        'href' => 'https://educacio.gencat.cat/ca/inici/',
        'meta' => [
            'tabindex' => -1,
        ],
    ]);

    $wp_admin_bar->add_node([
        'id' => 'recursosXTEC',
        'title' => '<img id="logo-xtec" alt="Logotip XTEC" src="' . WPMU_PLUGIN_URL . '/astra-images/logo_xtec.png' . '">',
        'parent' => false,
    ]);

    $wp_admin_bar->add_node([
        'id' => 'xtec',
        'title' => 'XTEC',
        'href' => 'https://xtec.gencat.cat/ca/inici',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'edu365',
        'href' => 'https://www.edu365.cat/',
        'title' => 'Edu365',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'edu3',
        'href' => 'http://www.edu3.cat/',
        'title' => 'Edu3',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'sinapsi',
        'href' => 'https://sinapsi.xtec.cat',
        'title' => 'Sinapsi',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'alexandria',
        'title' => 'Alexandria',
        'href' => 'https://alexandria.xtec.cat/',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'arc',
        'title' => 'ARC',
        'href' => 'https://apliense.xtec.cat/arc/',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'merli',
        'title' => 'Merlí',
        'href' => 'http://aplitic.xtec.cat/merli/',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'jclic',
        'title' => 'jClic',
        'href' => 'https://clic.xtec.cat/legacy/ca/index.html',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'linkat',
        'title' => 'Linkat',
        'href' => 'http://linkat.xtec.cat/portal/index.php',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'odissea',
        'title' => 'Odissea',
        'href' => 'https://odissea.xtec.cat/',
        'parent' => 'recursosXTEC',
    ]);

    $wp_admin_bar->add_node([
        'id' => 'agora',
        'title' => 'Àgora',
        'href' => 'https://educaciodigital.cat/',
        'parent' => 'recursosXTEC',
    ]);

    if (!is_user_logged_in()) {
        $wp_admin_bar->add_node([
            'parent' => 'top-secondary',
            'id' => 'login-link-admin-bar',
            'title' => __('Log in'),
            'href' => wp_login_url($_SERVER['REQUEST_URI']),
        ]);
    }

});

// Customizer: Remove all Astra sections.
add_filter('astra_customizer_sections', function ($configurations) {
    if (is_xtec_super_admin()) {
        return $configurations;
    }
    return array_filter($configurations, static function ($configuration) {
        return $configuration['type'] !== 'section';
    });
});

// Customizer: Remove all header and footer sections.
add_filter('astra_header_builder_sections', 'nodes_remove_customizer_header_footer_sections');
add_filter('astra_footer_builder_sections', 'nodes_remove_customizer_header_footer_sections');

function nodes_remove_customizer_header_footer_sections($configurations): array {
    if (is_xtec_super_admin()) {
        return $configurations;
    }
    return [];
}

// Customizer: Update logo and other information when saving.
add_action('customize_save_after', function ($wp_customize) {

    // Get the value of the 'astra_nodes_options[custom_logo]' setting
    $logo = $wp_customize->get_setting('astra_nodes_options[custom_logo]')->value();

    // Get the attachment ID from the URL
    $logo_id = attachment_url_to_postid($logo);

    // Set the custom logo to the value of the 'astra_nodes_options[custom_logo]' setting.
    set_theme_mod('custom_logo', $logo_id);
    astra_update_option('custom_logo', $logo_id);

    // Set the blog name and description.
    update_option('blogname', $wp_customize->get_setting('blogname')->value());
    update_option('blogdescription', $wp_customize->get_setting('blogdescription')->value());

});

// Customizer: Update the register in wp_options that makes possible to change the color palette in the preview.
add_action('customize_preview_init', function ($wp_customize) {

    $palette = $wp_customize->get_setting('astra-color-palettes[currentPalette]')->value();
    $astra_color_palettes = get_option('astra-color-palettes');
    $astra_settings = get_option('astra-settings');
    $astra_settings['global-color-palette']['palette'] = $astra_color_palettes['palettes'][$palette];
    update_option('astra-settings', $astra_settings);

});

// Header: Content of the central area, which includes the name of the client.
add_filter('astra_get_option_header-html-3', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_option('astra_nodes_options');

    // Check if the option exists and is not null.
    $pre_blog_name = $astra_nodes_options['pre_blog_name'] ?? '';

    return '
        <div id="client-type">' . $pre_blog_name . '</div>
        <h1 id="blog-name">' . get_bloginfo('name') . '</h1>
        <h2><span id="blog-description">' . get_bloginfo('description') . '</span></h2>
        ';

}, 20, 0);

// Header: Content of the area that shows the contact information.
add_filter('astra_get_option_header-html-1', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_option('astra_nodes_options');

    // Check if the option exists and is not null.
    $postal_address = $astra_nodes_options['postal_address'] ?? '';
    $postal_code_city = $astra_nodes_options['postal_code_city'] ?? '';
    $email_address = $astra_nodes_options['email_address'] ?? '';
    $phone_number = $astra_nodes_options['phone_number'] ?? '';
    $link_to_map = $astra_nodes_options['link_to_map'] ?? '';
    $contact_page = $astra_nodes_options['contact_page'] ?? '';

    // Contact is a mailto link if contact_page is empty.
    $contact_page_url = $contact_page != '' ? $contact_page : 'mailto:' . $email_address;

    $content = '
            <p style="text-align: right; line-height: 1.1;">
                <span id="postal-address" style="">' . $postal_address . '</span>
                <br>
                <span id="postal-code-city" style="">' . $postal_code_city . '</span>
                <br>
                <span style="">
                    <a id="email-address" href="mailto:' . $email_address . '">' . $email_address . '</a>
                </span>
                <br>
                <span id="phone-number" style="">' . $phone_number . '</span>
            </p>
            <p style="text-align: right;">
                <span style="">
                    <strong>
                        <a style="color: #1ea19b;" href="' . $link_to_map . '" target="_blank">' . __('Map', 'astra-nodes'). '</a> |
                        <a style="color: #1ea19b;" href="' . $contact_page_url . '" target="_blank">' . __('Contact', 'astra-nodes'). '</a>
                    </strong>
                </span>
            </p>
        ';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

    }, 20, 0);

// Header: Content of the buttons area.
add_filter('astra_get_option_header-html-2', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    $content = '<div class="detail-container">
    <div class="grid-container">';

    // Array of background colors for the buttons.
    $background_colors = ['#38a09b', '#25627e', '#2b245e', '#2b245e', '#38a09b', '#25627e'];
    $border_radii = ['', '', 'border-radius: 0 30px 0 0;', '', '', ''];

    // Loop through the 6 buttons.
    for ($i = 1; $i <= 6; $i++) {
        $classes_icon = $astra_nodes_options['header_icon_' . $i . '_classes'] ?? 'fa-solid fa-graduation-cap';
        $text_icon = $astra_nodes_options['header_icon_' . $i . '_text'] ?? __('Item', 'astra-nodes') . ' ' . $i;
        $link_icon = $astra_nodes_options['header_icon_' . $i . '_link'] ?? '';
        $open_in_new_tab = $astra_nodes_options['header_icon_' . $i . '_open_in_new_tab'] ?? false;

        // Add the button to the content.
        $content .= '<div class="grid-item" style="background-color: ' . $background_colors[$i-1] . ';' . $border_radii[$i-1] . '">
                <i id="header-button-' . $i . '" class="' . $classes_icon . '"></i> <br>
                <a href="' . $link_icon . '" ' . ($open_in_new_tab ? ' target="_blank"' : '') . '>' . $text_icon . '</a>
            </div>';
    }

    $content .= '</div></div>';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}, 20, 0);

add_filter('astra_header_after', function () {

    // Check if it is front page.
    if (!is_front_page()) {
        return;
    }

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    // If cards are not enabled, don't show them.
    if (!$astra_nodes_options['cards_enable']) {
        return;
    }

    echo '
        <div class="wp-block-columns has-small-font-size is-layout-flex wp-container-7" style="padding: var(--wp--preset--spacing--60);">
    ';

    for ($i = 1; $i <= 4; $i++) {
        $card_title = $astra_nodes_options['card_' . $i . '_title'] ?? '';
        $card_image = $astra_nodes_options['card_' . $i . '_image'] != '' ? $astra_nodes_options['card_' . $i . '_image'] : '/wordpress/wp-includes/images/blank.gif';
        $card_url = $astra_nodes_options['card_' . $i . '_url'] != '' ? $astra_nodes_options['card_' . $i . '_url'] : home_url();

        echo '
            <div class="wp-block-column has-ast-global-color-0-background-color has-background is-layout-flow front-page-card" onclick="window.open(\'' . $card_url . '\')">
                    <div>
                        <h3>' . $card_title . '</h3>
                    </div>
                    <div>
                        <img decoding="async" src="' . $card_image . '">
                    </div>
            </div>
        ';
    }

    echo '</div>';

    
    // Display custom notice if enabled
    $front_page_notice_enable = $astra_nodes_options['front_page_notice_enable'] ?? false;

    $front_page_notice_image = get_theme_mod('front_page_notice_image');
    $front_page_notice_title_uppercase = get_theme_mod('front_page_notice_title_uppercase');
    $front_page_notice_title_normal = get_theme_mod('front_page_notice_title_normal');
    $front_page_notice_text = get_theme_mod('front_page_notice_text');

    if ($front_page_notice_enable) {
        echo '
        <div class="wp-block-columns custom-notice-container">
            <div class="wp-block-column" style="flex: 1">
                <img src="' . $front_page_notice_image . '" class="wp-image custom-notice-image">
            </div>
            
            <div class="wp-block-column custom-notice-content" style="flex: 2">
                <div class="front-page-notice-title-uppercase has-ast-global-color-0-color">' . $front_page_notice_title_uppercase . '</div>
                <h2 class="front-page-notice-title-normal has-ast-global-color-1-color">' . $front_page_notice_title_normal . '</h2>
                <div class="front-page-notice-text">' . $front_page_notice_text . '</div>
            </div>
        </div>
        ';
    }

    // Move homepage carrousel to top page header
    include realpath(ABSPATH . 'wp-content') . '/mu-plugins/astra-includes/carousel.php';
});

// Add the breadcrumb to the top of the content.
function show_breadcrumb_astra_content_before() {
    if (function_exists('astra_get_breadcrumb')) {
        echo astra_get_breadcrumb();
    }
}

add_action('astra_content_before', 'show_breadcrumb_astra_content_before');


// Add the accordion to the sidebar.
add_filter('astra_sidebars_after' , function () {
    if (!is_front_page()) {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });
        include_once WPMU_PLUGIN_DIR . '/astra-includes/accordion.php';
    }
});

function custom_header_html_3() {
//    $content = astra_get_option('header-html-1');
}

// Customizer: Add custom sections.
//add_action('customize_register', 'nodes_add_customizer_sections');
function nodes_add_customizer_sections($wp_customize): void {
    // Secció Color de Fons
    $wp_customize->add_section('nodes_header', [
        'title' => __('Header', 'astra-nodes'),
        'priority' => 30,
    ]);

    // Configuració de Color de Fons
    $wp_customize->add_setting('color_fons_setting', [
        'default' => '#ffffff',
        'transport' => 'refresh',
    ]);

    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_fons_control', [
        'label' => __('Selecciona el color de fons', 'astra-nodes'),
        'section' => 'nodes_header',
        'settings' => 'color_fons_setting',
    ]));
}

