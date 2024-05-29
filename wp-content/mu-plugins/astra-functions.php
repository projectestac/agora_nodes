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
global $astra_nodes_options;
$astra_nodes_options = get_theme_mod('astra_nodes_options');

// Load translations.
load_muplugin_textdomain('astra-nodes', '/astra-nodes/languages');

// Load styles to customize Astra theme.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions', plugins_url('/astra-nodes/styles/style.css', __FILE__));
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
    if (is_plugin_active('getwid/getwid.php')) {
        wp_enqueue_script('slick', getwid_get_plugin_url('vendors/slick/slick/slick.min.js'), ['jquery'], '1.9.0', true);
    }
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

// Admin bar: Remove WordPress logo and search box.
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('wp-logo');
    $wp_admin_bar->remove_node('search');
});

// Admin bar: Add Departament d'Educació logo in the first position and menu with XTEC resources.
add_action('admin_bar_menu', function ($wp_admin_bar) {

    global $astra_nodes_options;

    $logo = $astra_nodes_options['organism_logo'] ?? 'department';

    if ($logo === 'ceb') {
        $logo_image = WPMU_PLUGIN_URL . '/astra-nodes/images/logo_ceb.png';
        $logo_url = 'https://www.edubcn.cat/ca/';
    } else {
        $logo_image = WPMU_PLUGIN_URL . '/astra-nodes/images/logo_department.png';
        $logo_url = 'https://educacio.gencat.cat/ca/inici/';
    }

    $data = [
        [
            'parent' => false,
            'id' => 'logo-educacio-wrapper',
            'href' => $logo_url,
            'title' => '<img id="logo-educacio" alt="' . __('Logo organism', 'astra-nodes') . '" src="' . $logo_image . '">',
            'meta' => [
                'target' => '_blank',
            ],
        ],
        [
            'parent' => false,
            'id' => 'resources_xtec',
            'title' => '<img id="logo-xtec" alt="Logo XTEC" src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/logo_xtec.png' . '">',
            'href' => 'https://xtec.gencat.cat/ca/inici',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'xtec',
            'title' => 'XTEC',
            'href' => 'https://xtec.gencat.cat/ca/inici',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'edu365',
            'title' => 'Edu365',
            'href' => 'https://www.edu365.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'digital',
            'title' => 'Digital',
            'href' => 'https://projectes.xtec.cat/digital/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'nus',
            'title' => 'Nus (Xarxa docent)',
            'href' => 'https://comunitat.edigital.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'alexandria',
            'title' => 'Alexandria',
            'href' => 'https://alexandria.xtec.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'arc',
            'title' => 'ARC',
            'href' => 'https://apliense.xtec.cat/arc/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'merli',
            'title' => 'Merlí',
            'href' => 'https://merli.xtec.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'jclic',
            'title' => 'jClic',
            'href' => 'https://clic.xtec.cat/legacy/ca/index.html',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'linkat',
            'title' => 'Linkat',
            'href' => 'http://linkat.xtec.cat/portal/index.php',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'odissea',
            'title' => 'Odissea',
            'href' => 'https://odissea.xtec.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'agora',
            'title' => 'Àgora',
            'href' => 'https://educaciodigital.cat/',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'sinapsi',
            'title' => 'Sinapsi',
            'href' => 'https://sinapsi.xtec.cat',
        ],
        [
            'parent' => 'resources_xtec',
            'id' => 'dossier',
            'title' => 'Dossier',
            'href' => 'https://dossier.xtec.cat/',
        ],
    ];

    foreach ($data as $datum) {
        $wp_admin_bar->add_node($datum);
    }

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
            && $configuration['name'] !== 'section-single-page'
            && $configuration['name'] !== 'section-sidebars'
            && $configuration['name'] !== 'ast-section-search-page'
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

// Customizer: Update the register in wp_options that makes possible to change the color palette
// in the preview. Also, update the global $astra_nodes_options to update the preview.
add_action('customize_preview_init', function ($wp_customize) {

    global $astra_nodes_options;

    $palette = $wp_customize->get_setting('astra-color-palettes[currentPalette]')->value();
    $astra_color_palettes = get_option('astra-color-palettes');
    $astra_settings = get_option('astra-settings');
    $astra_settings['global-color-palette']['palette'] = $astra_color_palettes['palettes'][$palette];

    update_option('astra-settings', $astra_settings);

    // Parameters that require a refresh to update the preview.
    $layout = $wp_customize->get_setting('astra_nodes_options[front_page_layout]')->value();
    $astra_nodes_options['front_page_layout'] = $layout;

    $notice_enable = $wp_customize->get_setting('astra_nodes_options[front_page_notice_enable]')->value();
    $astra_nodes_options['front_page_notice_enable'] = $notice_enable;

    $cards_enable = $wp_customize->get_setting('astra_nodes_options[front_page_cards_enable]')->value();
    $astra_nodes_options['front_page_cards_enable'] = $cards_enable;

    $news_enable = $wp_customize->get_setting('astra_nodes_options[front_page_news_enable]')->value();
    $astra_nodes_options['front_page_news_enable'] = $news_enable;

    $news_category = $wp_customize->get_setting('astra_nodes_options[front_page_news_category]')->value();
    $astra_nodes_options['front_page_news_category'] = $news_category;

    $news_num_posts = $wp_customize->get_setting('astra_nodes_options[front_page_news_num_posts]')->value();
    $astra_nodes_options['front_page_news_num_posts'] = $news_num_posts;

});

// Header: Content of the central area (html-3), which includes the name of the client.
add_action('astra_get_option_header-html-3', function () {

    global $astra_nodes_options;

    // Check if the option exists and is not null.
    $pre_blog_name = $astra_nodes_options['pre_blog_name'] ?? '';

    return '
        <div id="client-type">' . $pre_blog_name . '</div>
        <h1 id="blog-name">' . get_bloginfo('name') . '</h1>
        <h2 id="blog-description">' . get_bloginfo('description') . '</h2>
        ';

});

// Header: Content of the area that shows the contact information (html-1).
add_action('astra_get_option_header-html-1', 'astra_nodes_contact_information');

// Footer: Content of the area that shows the contact information (html-1).
add_action('astra_get_option_footer-html-1', 'astra_nodes_contact_information');

function astra_nodes_contact_information(): string {

    global $astra_nodes_options;

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
            <div id="contact-info-1-wrapper">
                <div id="postal-address">' . $postal_address . '</div>
                <div id="postal-code-city">' . $postal_code_city . '</div>
                <div id="email-address-wrapper">
                    <a id="email-address" href="mailto:' . $email_address . '">' . $email_address . '</a>
                </div>
                <div id="phone-number">' . $phone_number . '</div>
            </div>
            <div id="contact-info-2-wrapper">
                <strong>
                    <a id="contact-info-link-to-map" href="' . $link_to_map . '" target="_blank">' . __('Map', 'astra-nodes') . '</a>
                    |
                    <a id="contact-info-page-url" href="' . $contact_page_url . '" target="_blank">' . __('Contact', 'astra-nodes') . '</a>
                </strong>
            </div>
        ';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}

// Header: Content of the buttons area (html-2).
add_action('astra_get_option_header-html-2', function () {

    global $astra_nodes_options;

    $content = '
            <div class="detail-container">
                <div class="grid-container">
    ';

    // Loop through the 6 buttons.
    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {
        $classes_icon = $astra_nodes_options['header_icon_' . $i . '_classes'] ?? 'fa-solid fa-graduation-cap';
        $text_icon = $astra_nodes_options['header_icon_' . $i . '_text'] ?? __('Item', 'astra-nodes') . ' ' . $i;
        $link_icon = $astra_nodes_options['header_icon_' . $i . '_link'] ?? '';
        $open_in_new_tab = $astra_nodes_options['header_icon_' . $i . '_open_in_new_tab'] ?? false;

        // Add the button to the content.
        $content .= '
            <div class="grid-item grid-item-' . $i . '">
                <i id="header-button-' . $i . '" class="' . $classes_icon . ' astra-nodes-header-icon"></i>
                <a class="header-button-link-' . $i . ' astra-nodes-header-icon-link"
                   href="' . $link_icon . '" ' . ($open_in_new_tab ? ' target="_blank"' : '') . '>' . $text_icon . '</a>
            </div>
            ';
    }

    $content .= '</div></div>';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

});

// Header: Add the slider in the header if it is the front page.
add_action('astra_masthead_bottom', function () {

    global $astra_nodes_options;

    // The slider is created using a block from the Getwid plugin.
    if (!is_plugin_active('getwid/getwid.php')) {
        return;
    }

    $slider_enabled = isset($astra_nodes_options['front_page_slider_enable']) && $astra_nodes_options['front_page_slider_enable'];

    if ($slider_enabled && is_front_page()) {
        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/front_page_slider.php';
        echo '<div id="astra-nodes-header-block">' . get_front_page_slider($astra_nodes_options) . '</div>';
    }

});

// Front page: layout, cards, notice and news.
add_action('astra_html_before', function () {

    global $astra_nodes_options;

    // Front page: The layout is hooked to the first Astra action available. When this file is loaded, the
    // layout cannot be set because the decision vary if it is a preview or not, so it is postponed to the
    // first action available. 
    $front_page_layout = $astra_nodes_options['front_page_layout'] ?? 'boxes';

    // Default layout is 'sidebar_news'.
    $action = 'astra_entry_after';
    $news_priority = 10;
    $notice_priority = 20;
    $cards_priority = 30;

    if ($front_page_layout === 'boxes') {
        $action = 'astra_content_before';
        $cards_priority = 10;
    }

    if ($front_page_layout === 'sidebar_boxes') {
        $cards_priority = 10;
        $news_priority = 30;
    }

    if ($front_page_layout === 'wp_default_no_sidebar') {
        add_filter('astra_page_layout', function () {
            return 'no-sidebar';
        });
    }

    // Front page: Show the cards if they are enabled.
    add_action($action, function () {

        global $astra_nodes_options;

        // Check if the layout is WordPress default or if it is not front page.
        if ($astra_nodes_options['front_page_layout'] === 'wp_default' ||
            $astra_nodes_options['front_page_layout'] === 'wp_default_no_sidebar' ||
            !is_front_page()) {
            return;
        }

        // If cards are not enabled, don't show them.
        if (!$astra_nodes_options['front_page_cards_enable']) {
            return;
        }

        echo '
        <div id="front-page-cards-container" class="wp-block-columns has-small-font-size is-layout-flex wp-container-7">
    ';

        for ($i = 1; $i <= NUM_CARDS_IN_FRONT_PAGE; $i++) {

            $card_title = $astra_nodes_options['front_page_card_' . $i . '_title'] ?? '';
            $card_image = $astra_nodes_options['front_page_card_' . $i . '_image'] ?? '';
            $card_url = $astra_nodes_options['front_page_card_' . $i . '_url'] ?? home_url();

            $card_open_in_new_tab = $astra_nodes_options['front_page_card_' . $i . '_open_in_new_tab'] ?? false;
            $card_target = $card_open_in_new_tab ? 'target="_blank"' : '';

            $card_color = $astra_nodes_options['front_page_card_' . $i . '_color'] ?? '';
            $card_background = !empty($card_color) ? 'background-color: ' . $card_color . ' !important' : ''; // Important to override Astra's default color.

            echo '
            <div id="card-color-' . $i . '" class="wp-block-column has-ast-global-color-0-background-color has-background is-layout-flow front-page-card"
                 style="' . $card_background . '">
                <div class="astra-nodes-card-title">
                    <h3 id="card-title-' . $i . '" style="color:' . $card_color . '">' . $card_title . '</h3>
                </div>
                <div class="astra-nodes-card-body">
                     <a id="card-link-' . $i . '" href="' . $card_url . '" ' . $card_target . '>
                        <img id="card-image-' . $i . '" class="astra-nodes-card-image" decoding="async" src="' . $card_image . '" alt="">
                     </a>
                </div>
            </div>
        ';

        }

        echo '</div>';

    }, $cards_priority, 0);

    // Front page: Show the notice if it is enabled.
    add_action($action, function () {

        global $astra_nodes_options;

        // Check if the layout is WordPress default or if it is not front page.
        if ($astra_nodes_options['front_page_layout'] === 'wp_default' ||
            $astra_nodes_options['front_page_layout'] === 'wp_default_no_sidebar' ||
            !is_front_page()) {
            return;
        }

        // If front page notice is not enabled, don't show it.
        if (!$astra_nodes_options['front_page_notice_enable']) {
            return;
        }

        $layout = $astra_nodes_options['front_page_notice_layout'] ?? 'image_text';
        $image = $astra_nodes_options['front_page_notice_image'] ?? '';
        $background_color = $astra_nodes_options['front_page_notice_background_color'];
        $url = $astra_nodes_options['front_page_notice_url'];
        $open_in_new_tab = $astra_nodes_options['front_page_notice_open_in_new_tab'];
        $pre_title = $astra_nodes_options['front_page_notice_pre_title'];
        $title = $astra_nodes_options['front_page_notice_title'];
        $content = $astra_nodes_options['front_page_notice_content'];

        $style = !empty($background_color) ? 'background-color: ' . $background_color : '';

        switch ($layout) {
            case 'image':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns" style="' . $style . '">
                        <div id="front-page-notice-image-container" class="wp-block-column">
                            <a id="notice-img-url" href="' . $url . '" ' . ($open_in_new_tab ? 'target="_blank"' : '') . '>
                                <img id="front-page-notice-image" src="' . $image . '" alt="' . __('Image of the notice', 'astra-nodes') . '" />
                            </a>
                        </div>
                    </div>
                ';
                break;
            case 'text':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns" style="' . $style . '">
                        <div id="front-page-notice-text" class="wp-block-column">
                            <div id="front-page-notice-pre-title" class="has-ast-global-color-0-color">' . $pre_title . '</div>
                            <h2 id="front-page-notice-title" class="has-ast-global-color-1-color">' . $title . '</h2>
                            <div id="front-page-notice-content">' . $content . '</div>
                        </div>
                    </div>
                ';
                break;
            case 'image_text':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns">
                            <div id="front-page-notice-image-container" class="wp-block-column">
                                <a id="notice-img-url" href="' . $url . '" ' . ($open_in_new_tab ? 'target="_blank"' : '') . '>
                                    <img id="front-page-notice-image" src="' . $image . '" alt="' . __('Image of the notice', 'astra-nodes') . '" />
                                </a>
                            </div>
                            <div id="front-page-notice-text" class="wp-block-column" style="' . $style . '">
                                <div id="front-page-notice-pre-title" class="has-ast-global-color-0-color">' . $pre_title . '</div>
                                <h2 id="front-page-notice-title" class="has-ast-global-color-1-color">' . $title . '</h2>
                                <div id="front-page-notice-content">' . $content . '</div>
                            </div>
                        </div>
                ';
                break;
        }

    }, $notice_priority, 0);

    // Front page: News configuration.
    add_action('astra_entry_after', function () {

        global $astra_nodes_options;

        if (!is_plugin_active('getwid/getwid.php')) {
            return;
        }

        // Check if the layout is WordPress default or if it is not front page.
        if ($astra_nodes_options['front_page_layout'] === 'wp_default' ||
            $astra_nodes_options['front_page_layout'] === 'wp_default_no_sidebar' ||
            !is_front_page()) {
            return;
        }

        // If news are not enabled, don't show them.
        if (!$astra_nodes_options['front_page_news_enable']) {
            return;
        }

        include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/front_page_news.php';

        $blocks = parse_blocks(get_front_page_news($astra_nodes_options));

        echo '<div id="front-page-news-carousel-container">';

        foreach ($blocks as $block) {
            echo render_block($block);
        }

        echo '</div>';

    }, $news_priority, 0);

});

// Breadcrumb: Add the breadcrumb on top of the content on all pages except the front page.
add_action('astra_content_before', function () {
    if (!is_front_page()) {
        echo astra_get_breadcrumb();
    }
});

// Sidebar: Specific case for the pages when there is no sidebar.
add_action('astra_head_top', function () {

    global $astra_nodes_options;

    $pages_sidebar = $astra_nodes_options['pages_sidebar'] ?? 'menu';
    $is_buddypress_active = is_plugin_active('buddypress/bp-loader.php');

    if ($pages_sidebar === 'none') {

        if (is_front_page() || !is_page() || ($is_buddypress_active && is_buddypress())) {
            return;
        }

        add_filter('astra_page_layout', function () {
            return 'no-sidebar';
        });

    }

});

// Sidebar: Choose the sidebar to show.
add_action('astra_sidebars_before', function () {

    $is_buddypress_active = is_plugin_active('buddypress/bp-loader.php');

    if (is_front_page()) {

        astra_get_sidebar('sidebar-frontpage');
        unregister_sidebar('sidebar-1');
        unregister_sidebar('categories');

    } elseif (is_category()) { // Blog pages.

        astra_get_sidebar('categories');
        unregister_sidebar('sidebar-1');
        unregister_sidebar('sidebar-frontpage');

    } elseif ($is_buddypress_active && !is_buddypress()) { // All other pages.

        global $astra_nodes_options;
        $pages_sidebar = $astra_nodes_options['pages_sidebar'] ?? 'menu';

        if ($pages_sidebar === 'menu') {

            unregister_sidebar('sidebar-1');
            add_action('wp_enqueue_scripts', function () {
                wp_enqueue_script('jquery');
            });
            include_once WPMU_PLUGIN_DIR . '/astra-nodes/includes/accordion.php';

        } elseif ($pages_sidebar === 'widgets') {

            unregister_sidebar('sidebar-frontpage');
            unregister_sidebar('categories');

        }

    }

});

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

// Sidebar: Create a sidebar for the categories pages.
add_action('widgets_init', function () {

    register_sidebar([
        'name' => __('Categories', 'astra-nodes'),
        'id' => 'categories',
        'description' => __('Add widgets to the categories pages', 'astra-nodes'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ]);

    register_sidebar([
        'name' => __('Front page', 'astra-nodes'),
        'id' => 'sidebar-frontpage',
        'description' => __('Add widgets to the front page', 'astra-nodes'),
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget' => '</aside>',
        'before_title' => '<h2 class="widget-title">',
        'after_title' => '</h2>',
    ]);

}, 1);

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
