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
const NUM_SLIDES_IN_FRONT_PAGE = 5;

include_once WPMU_PLUGIN_DIR . '/astra-nodes/customizer/customize.php';

// Get the option array from the wp_options table. Will be used in several places.
$astra_nodes_options = get_theme_mod('astra_nodes_options');

// Load translations.
load_muplugin_textdomain('astra-nodes', '/astra-nodes/languages');

// Load styles to customize Astra theme.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions-css', plugins_url('/astra-nodes/styles/style.css', __FILE__));
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css');
});

// Welcome panel: Remove the box in the dashboard.
add_action('admin_init', function () {
    remove_action('welcome_panel', 'wp_welcome_panel');
});

// Dashboard: Remove some boxes in the dashboard.
add_action('wp_dashboard_setup', function () {

    if (is_xtec_super_admin()) {
        return;
    }

    remove_meta_box('dashboard_primary', 'dashboard', 'side');
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');

});

// Admin bar: Force to be always shown, including for non-logged users.
add_action('show_admin_bar', '__return_true');

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
add_action('astra_customizer_sections', function ($configurations) {

    // xtecadmin has access to all sections.
    if (is_xtec_super_admin()) {
        return $configurations;
    }

    // Remove all the sections added by Astra theme and by plugins (custom post-types) in customizer.
    return array_filter($configurations, static function ($configuration) {
        return $configuration['name'] !== 'panel-global'
            && $configuration['name'] !== 'section-breadcrumb'
            && $configuration['name'] !== 'section-blog-group'
            && $configuration['name'] !== 'section-blog'
            && $configuration['name'] !== 'section-blog-single'
            && $configuration['name'] !== 'section-page-dynamic-group'
            && $configuration['name'] !== 'section-sidebars'
            && $configuration['name'] !== 'section-posts-structure'
            && !str_starts_with($configuration['section'] ?? '', 'section-posttype-');
    });

});

// Customizer: Remove all header and footer sections.
add_action('astra_header_builder_sections', 'nodes_remove_customizer_header_footer_sections');
//add_action('astra_footer_builder_sections', 'nodes_remove_customizer_header_footer_sections');

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
add_action('astra_get_option_header-html-3', function () use ($astra_nodes_options) {

    // Check if the option exists and is not null.
    $pre_blog_name = $astra_nodes_options['pre_blog_name'] ?? '';

    return '
        <div id="client-type">' . $pre_blog_name . '</div>
        <h1 id="blog-name">' . get_bloginfo('name') . '</h1>
        <h2><span id="blog-description">' . get_bloginfo('description') . '</span></h2>
        ';

});

// Header: Content of the area that shows the contact information (html-1).
add_action('astra_get_option_header-html-1', function () use ($astra_nodes_options) {

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

});

// Header: Content of the buttons area (html-2).
add_action('astra_get_option_header-html-2', function () use ($astra_nodes_options) {

    $content = '
            <div class="detail-container">
                <div class="grid-container">
    ';

    // Array of background colors for the buttons.
    $background_colors = ['#38a09b', '#25627e', '#2b245e', '#2b245e', '#38a09b', '#25627e'];
    $border_radius = ['', '', 'border-radius: 0 30px 0 0;', '', '', ''];

    // Loop through the 6 buttons.
    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {
        $classes_icon = $astra_nodes_options['header_icon_' . $i . '_classes'] ?? 'fa-solid fa-graduation-cap';
        $text_icon = $astra_nodes_options['header_icon_' . $i . '_text'] ?? __('Item', 'astra-nodes') . ' ' . $i;
        $link_icon = $astra_nodes_options['header_icon_' . $i . '_link'] ?? '';
        $open_in_new_tab = $astra_nodes_options['header_icon_' . $i . '_open_in_new_tab'] ?? false;

        // Add the button to the content.
        $content .= '
            <div class="grid-item" style="background-color: ' . $background_colors[$i - 1] . ';' . $border_radius[$i - 1] . '">
                <i id="header-button-' . $i . '" class="' . $classes_icon . '"></i> <br>
                <a href="' . $link_icon . '" ' . ($open_in_new_tab ? ' target="_blank"' : '') . '>' . $text_icon . '</a>
            </div>
            ';
    }

    $content .= '</div></div>';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

});

// Header: Add the slider in the header if it is the front page.
add_action('astra_masthead_bottom', function () use ($astra_nodes_options) {

    $slider_enabled = isset($astra_nodes_options['front_page_slider_enable']) && $astra_nodes_options['front_page_slider_enable'];

    if ($slider_enabled && is_front_page()) {
        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/front_page_slider.php';
        echo '<div id="astra-nodes-header-block">' . get_front_page_slider($astra_nodes_options) . '</div>';
    }

});

$front_page_config = $astra_nodes_options['front_page_config'] ?? 1;

// Default is configuration 3.
$cards_action = 'astra_content_before';
$cards_priority = 10;
$notice_action = 'astra_content_before';
$notice_priority = 20;

if ($front_page_config === '1') {
    $cards_action = 'astra_entry_after';
    $notice_action = 'astra_entry_after';
    $notice_priority = 5;
}

if ($front_page_config === '2') {
    $cards_action = 'astra_entry_before';
    $notice_action = 'astra_entry_before';
    $notice_priority = 30;
}

// Front page: Show the cards if they are enabled.
add_action($cards_action, function () use ($astra_nodes_options) {

    // Check if it is front page.
    if (!is_front_page()) {
        return;
    }

    // If cards are not enabled, don't show them.
    if (!$astra_nodes_options['cards_enable']) {
        return;
    }

    echo '
        <div id="front-page-cards-container" class="wp-block-columns has-small-font-size is-layout-flex wp-container-7">
    ';

    for ($i = 1; $i <= NUM_CARDS_IN_FRONT_PAGE; $i++) {
        $card_title = $astra_nodes_options['card_' . $i . '_title'] ?? '';
        $card_image = $astra_nodes_options['card_' . $i . '_image'] !== '' ? $astra_nodes_options['card_' . $i . '_image'] : '/wordpress/wp-includes/images/blank.gif';
        $card_url = $astra_nodes_options['card_' . $i . '_url'] !== '' ? $astra_nodes_options['card_' . $i . '_url'] : home_url();

        echo '
            <div class="wp-block-column has-ast-global-color-0-background-color has-background is-layout-flow front-page-card" onclick="window.open(\'' . $card_url . '\')">
                    <div class="astra-nodes-card-title">
                        <h3>' . $card_title . '</h3>
                    </div>
                    <div class="astra-nodes-card-body">
                        <img class="astra-nodes-card-image" decoding="async" src="' . $card_image . '" alt="">
                    </div>
            </div>
        ';
    }

    echo '</div>';

}, $cards_priority, 0);

// Front page: Show the notice if it is enabled.
add_action($notice_action, function () use ($astra_nodes_options) {

    // Check if it is front page.
    if (!is_front_page()) {
        return;
    }

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

// Side menu: To remove the sidebar is necessary to use an early hook, but to change the contents in the sidebar, we need to use a
// later hook. So depending on the configuration, different actions are used.
$pages_sidebar = $astra_nodes_options['pages_sidebar'] ?? 'menu';

if ($pages_sidebar === 'menu') {
    add_action('astra_sidebars_before', function () {
        // is_front_page() and is_page() are not defined when this file is loaded, so they must be called using hooks.
        if (is_front_page() || !is_page()) {
            return ;
        }
        unregister_sidebar('sidebar-1');
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });
        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/accordion.php';
    });
}

if ($pages_sidebar === 'widgets') {
    add_action('astra_sidebars_before', function () {
        if (is_front_page() || !is_page()) {
            return ;
        }
        astra_get_sidebar('primary_menu');
    });
}

if ($pages_sidebar === 'none') {
    add_action('astra_head_top', function () {
        if (is_front_page() || !is_page()) {
            return ;
        }
        add_filter('astra_page_layout', function () {
            return 'no-sidebar';
        });
    });
}

// Footer: Add the legal notice in the footer.
add_action('astra_footer_after', function () {
    echo '
        <div class="site-below-footer-wrap ast-builder-grid-row-container site-footer-focus-item ast-builder-grid-row-full ast-builder-grid-row-tablet-full ast-builder-grid-row-mobile-full ast-footer-row-stack ast-footer-row-tablet-stack ast-footer-row-mobile-stack"
         data-section="section-below-footer-builder">
            <div class="ast-builder-grid-row-container-inner">
                <div class="ast-builder-footer-grid-columns site-below-footer-inner-wrap">
                    <div class="site-footer-below-section-1 site-footer-section">
                        <div class="footer-widget-area widget-area site-footer-focus-item">
                            <div class="ast-header-html inner-link-style-">
                                <div class="ast-builder-html-element">
                                    <p id="astra-nodes-copyright">
                                        <a target="_blank" href="https://web.gencat.cat/ca/ajuda/avis_legal/">Avís legal</a> |
                                        <a target="_blank" href="https://agora.xtec.cat/nodes/">Sobre el web</a> |
                                        <span class="copyright">© ' . date("Y") . ' Generalitat de Catalunya | </span>
                                        <span class="site-source">Fet amb el <a target="_blank" href="https://wordpress.org">WordPress</a></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';
});

// Widgets: Remove areas not used in the theme.
add_action('widgets_init', function () {

    if (is_xtec_super_admin()) {
        return;
    }

    unregister_sidebar('ast-widgets');
    unregister_sidebar('header-widget');
    unregister_sidebar('footer-widget-5');
    unregister_sidebar('footer-widget-6');
    unregister_sidebar('footer-widget-7');
    unregister_sidebar('footer-widget-8');
    unregister_sidebar('footer-widget-9');
    unregister_sidebar('footer-widget-10');
    unregister_sidebar('header-widget-1');
    unregister_sidebar('header-widget-2');
    unregister_sidebar('header-widget-3');
    unregister_sidebar('header-widget-4');
    unregister_sidebar('header-widget-5');
    unregister_sidebar('header-widget-6');
    unregister_sidebar('header-widget-7');
    unregister_sidebar('header-widget-8');
    unregister_sidebar('header-widget-9');
    unregister_sidebar('header-widget-10');
    unregister_sidebar('advanced-footer-widget-1');
    unregister_sidebar('advanced-footer-widget-2');
    unregister_sidebar('advanced-footer-widget-3');
    unregister_sidebar('advanced-footer-widget-4');

}, 11);

// Menus: Remove all the menus not used in the theme.
add_action('init', function () {

    if (is_xtec_super_admin()) {
        return;
    }

    unregister_nav_menu('secondary_menu');
    unregister_nav_menu('mobile_menu');
    unregister_nav_menu('menu_3');
    unregister_nav_menu('menu_4');
    unregister_nav_menu('menu_5');
    unregister_nav_menu('menu_6');
    unregister_nav_menu('menu_7');
    unregister_nav_menu('menu_8');
    unregister_nav_menu('menu_9');
    unregister_nav_menu('menu_10');
    unregister_nav_menu('loggedin_account_menu');
    unregister_nav_menu('footer_menu');

}, 11);

// Admin: Remove Astra menu from the admin panel and block access.
add_action('admin_menu', function () {

    if (!is_xtec_super_admin()) {
        remove_menu_page('astra');

        global $pagenow;
        if ($pagenow === 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'astra') {
            wp_redirect(home_url());
            exit;
        }
    }

}, 11);