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

const NUM_BUTTONS_IN_HEADER = 6;
const NUM_CARDS_IN_FRONT_PAGE = 4;

include_once WPMU_PLUGIN_DIR . '/astra-nodes/customizer/customize.php';

// Load translations.
load_muplugin_textdomain('astra-nodes', '/astra-nodes/languages');

// Load styles to customize Astra theme.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions-css', plugins_url('/astra-nodes/styles/style.css', __FILE__));
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
        'title' => '<img id="logo-dept-educacio" alt="Logo Educació" src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/logo_gene.png' . '">',
        'href' => 'https://educacio.gencat.cat/ca/inici/',
        'meta' => [
            'tabindex' => -1,
        ],
    ]);

    $wp_admin_bar->add_node([
        'id' => 'recursosXTEC',
        'title' => '<img id="logo-xtec" alt="Logotip XTEC" src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/logo_xtec.png' . '">',
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

// Customizer: Remove Astra sections in customizer.
add_filter('astra_customizer_sections', function ($configurations) {

    // xtecadmin has access to all sections.
    if (is_xtec_super_admin()) {
        return $configurations;
    }

    // Remove all the sections added by Astra theme in customizer.
    return array_filter($configurations, static function ($configuration) {
        return $configuration['name'] !== 'panel-global'
            && $configuration['name'] !== 'section-breadcrumb'
            && $configuration['name'] !== 'section-blog-group'
            && $configuration['name'] !== 'section-blog'
            && $configuration['name'] !== 'section-blog-single'
            && $configuration['name'] !== 'section-page-dynamic-group'
            && $configuration['name'] !== 'section-sidebars';
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

// Header: Content of the central area (html-3), which includes the name of the client.
add_filter('astra_get_option_header-html-3', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    // Check if the option exists and is not null.
    $pre_blog_name = $astra_nodes_options['pre_blog_name'] ?? '';

    return '
        <div id="client-type">' . $pre_blog_name . '</div>
        <h1 id="blog-name">' . get_bloginfo('name') . '</h1>
        <h2><span id="blog-description">' . get_bloginfo('description') . '</span></h2>
        ';

}, 20, 0);

// Header: Content of the area that shows the contact information (html-1).
add_filter('astra_get_option_header-html-1', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    // Check if the option exists and is not null.
    $postal_address = $astra_nodes_options['postal_address'] ?? '';
    $postal_code_city = $astra_nodes_options['postal_code_city'] ?? '';
    $email_address = $astra_nodes_options['email_address'] ?? '';
    $phone_number = $astra_nodes_options['phone_number'] ?? '';
    $link_to_map = $astra_nodes_options['link_to_map'] ?? '';
    $contact_page = $astra_nodes_options['contact_page'] ?? '';

    // Contact is a mailto link if contact_page is empty.
    $contact_page_url = $contact_page !== '' ? $contact_page : 'mailto:' . $email_address;

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
                        <a style="color: #1ea19b;" href="' . $link_to_map . '" target="_blank">' . __('Map', 'astra-nodes') . '</a> |
                        <a style="color: #1ea19b;" href="' . $contact_page_url . '" target="_blank">' . __('Contact', 'astra-nodes') . '</a>
                    </strong>
                </span>
            </p>
        ';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}, 20, 0);

// Header: Content of the buttons area (html-2).
add_filter('astra_get_option_header-html-2', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    $content = '
            <div class="detail-container">
                <div class="grid-container">
    ';

    // Array of background colors for the buttons.
    $background_colors = ['#38a09b', '#25627e', '#2b245e', '#2b245e', '#38a09b', '#25627e'];
    $border_radii = ['', '', 'border-radius: 0 30px 0 0;', '', '', ''];

    // Loop through the 6 buttons.
    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {
        $classes_icon = $astra_nodes_options['header_icon_' . $i . '_classes'] ?? 'fa-solid fa-graduation-cap';
        $text_icon = $astra_nodes_options['header_icon_' . $i . '_text'] ?? __('Item', 'astra-nodes') . ' ' . $i;
        $link_icon = $astra_nodes_options['header_icon_' . $i . '_link'] ?? '';
        $open_in_new_tab = $astra_nodes_options['header_icon_' . $i . '_open_in_new_tab'] ?? false;

        // Add the button to the content.
        $content .= '
            <div class="grid-item" style="background-color: ' . $background_colors[$i - 1] . ';' . $border_radii[$i - 1] . '">
                <i id="header-button-' . $i . '" class="' . $classes_icon . '"></i> <br>
                <a href="' . $link_icon . '" ' . ($open_in_new_tab ? ' target="_blank"' : '') . '>' . $text_icon . '</a>
            </div>
            ';
    }

    $content .= '</div></div>';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}, 20, 0);

// Header: Add the carousel in the header if it is the front page.
add_action('astra_masthead_bottom', function () {

    if (is_front_page()) {
        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/front_page_slider.php';
        $block = parse_blocks(get_front_page_slider());
        foreach ($block as $item) {
            if (empty($item['blockName'])) {
                continue;
            }
            echo '
                <div class="astra-nodes-header-block">
                ' . apply_filters('the_content', render_block($item)) . '
                </div>
                ';
        }
    }
});

$astra_nodes_options = get_theme_mod('astra_nodes_options');
$front_page_config = $astra_nodes_options['front_page_config'] ?? 1;

// Default is configuration 3.
$cards_action = 'astra_content_before';
$cards_priority = 20;
$notice_action = 'astra_content_before';
$notice_priority = 10;

if ($front_page_config === '1') {
    $cards_action = 'astra_entry_after';
    $notice_action = 'astra_entry_after';
    $notice_priority = 30;
}

if ($front_page_config === '2') {
    $cards_action = 'astra_entry_before';
    $notice_action = 'astra_entry_before';
}

// Front page: Show the cards if they are enabled.
add_action($cards_action, function () {

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

    for ($i = 1; $i <= NUM_CARDS_IN_FRONT_PAGE; $i++) {
        $card_title = $astra_nodes_options['card_' . $i . '_title'] ?? '';
        $card_image = $astra_nodes_options['card_' . $i . '_image'] !== '' ? $astra_nodes_options['card_' . $i . '_image'] : '/wordpress/wp-includes/images/blank.gif';
        $card_url = $astra_nodes_options['card_' . $i . '_url'] !== '' ? $astra_nodes_options['card_' . $i . '_url'] : home_url();

        echo '
            <div class="wp-block-column has-ast-global-color-0-background-color has-background is-layout-flow front-page-card" onclick="window.open(\'' . $card_url . '\')">
                    <div>
                        <h3>' . $card_title . '</h3>
                    </div>
                    <div>
                        <img decoding="async" src="' . $card_image . '" alt="">
                    </div>
            </div>
        ';
    }

    echo '</div>';

}, $cards_priority, 0);

// Front page: Show the notice if it is enabled.
add_action($notice_action, function () {

    // Check if it is front page.
    if (!is_front_page()) {
        return;
    }

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    // If front page notice is not enabled, don't show it.
    if (!$astra_nodes_options['front_page_notice_enable']) {
        return;
    }

    $front_page_notice_image = $astra_nodes_options['front_page_notice_image'];
    $front_page_notice_pre_title = $astra_nodes_options['front_page_notice_pre_title'];
    $front_page_notice_title = $astra_nodes_options['front_page_notice_title'];
    $front_page_notice_content = $astra_nodes_options['front_page_notice_content'];

    echo '
    <div id="front-page-notice-container" class="wp-block-columns">
        <div class="wp-block-column" style="flex: 1">
            <img id="front-page-notice-image" src="' . $front_page_notice_image . '" class="wp-image" alt="">
        </div>
        <div id="front-page-notice-body" class="wp-block-column" style="flex: 2">
            <div id="front-page-notice-pre-title" class="has-ast-global-color-0-color">' . $front_page_notice_pre_title . '</div>
            <h2 id="front-page-notice-title" class="has-ast-global-color-1-color">' . $front_page_notice_title . '</h2>
            <div id="front-page-notice-content">' . $front_page_notice_content . '</div>
        </div>
    </div>
    ';

}, $notice_priority, 0);

// Breadcrumb: Add the breadcrumb on top of the content on all pages except the front page.
add_action('astra_content_before', function () {
    if (!is_front_page()) {
        echo astra_get_breadcrumb();
    }
});

// Side menu: Add the accordion to the sidebar in case the post_type is "page", excluding the front page.
add_action('astra_sidebars_after', function () {

    if (!is_front_page() && is_page()) {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });

        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/accordion.php';
    }

});
