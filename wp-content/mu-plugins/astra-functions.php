<?php
/**
Plugin Name: AstraFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: Customizations for Astra theme
Version: 1.0
Author: Departament d'Educació - Generalitat de Catalunya
*/

// Options | General: Added parameter to force the use of the original Astra theme. Must be set
// before checking whether the theme is Astra. Otherwise, the option will not be available when
// the theme is not Astra.
add_filter('admin_init', function () {

    // Only xtecadmin can set this option.
    if (!is_xtec_super_admin()) {
        return;
    }

    register_setting('general', 'astra_nodes_use_vanilla_astra', ['type' => 'boolean']);

    // Add a new section to a settings page.
    add_settings_section(
        'use_vanilla_astra_section',
        __('Astra Nodes', 'astra-nodes'),
        static function () { },
        'general'
    );

    // Add a new field to the previous section.
    add_settings_field(
        'use_vanilla_astra',
        '<label for="use_vanilla_astra">' . __('Use vanilla Astra', 'astra-nodes') . '</label>',
        static function () {
            $value = (boolean)get_option('astra_nodes_use_vanilla_astra', 0);
            $checked = $value ? 'checked="checked"' : '';
            echo '<input type="checkbox" id="astra_nodes_use_vanilla_astra" name="astra_nodes_use_vanilla_astra" ' . $checked . ' />';
        },
        'general',
        'use_vanilla_astra_section'
    );

});

// Only load Astra Nodes if Astra theme is active.
if (wp_get_theme()->name !== 'Astra') {
    return;
}

// Only load Astra Nodes if the option to use vanilla Astra is not set.
if (get_option('astra_nodes_use_vanilla_astra')) {
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
load_muplugin_textdomain('astra-nodes', '/astra-nodes/languages/');

// Load styles to customize Astra theme.
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions', plugins_url('/astra-nodes/styles/style.css', __FILE__));
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css', [], '6.5.1');
    if (is_plugin_active('getwid/getwid.php')) {
        wp_enqueue_script('slick', getwid_get_plugin_url('vendors/slick/slick/slick.min.js'), ['jquery'], '1.9.0', true);
    }
});

// Load styles for the login page.
add_action('login_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions-login', plugins_url('/astra-nodes/styles/login-style.css', __FILE__));
});

// Load styles for the admin area.
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('astra-functions-admin', plugins_url('/astra-nodes/styles/admin-style.css', __FILE__));
});

// Welcome panel: Remove the default welcome panel and add a custom one.
add_action('admin_init', function () {

    remove_action('welcome_panel', 'wp_welcome_panel');
    add_action('welcome_panel', function () {
        ?>
        <div class="welcome-panel-content">
            <div class="welcome-panel-header">
                <h2><?php _e('Welcome to Nodes', 'astra-nodes'); ?></h2>
                <p><?php _e('Web platform for the schools in Catalonia', 'astra-nodes'); ?> </p>
            </div>
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true" focusable="false">
                        <rect width="48" height="48" rx="4" fill="#1E1E1E"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M32.0668 17.0854L28.8221 13.9454L18.2008 24.671L16.8983 29.0827L21.4257 27.8309L32.0668 17.0854ZM16 32.75H24V31.25H16V32.75Z"
                              fill="white"/>
                    </svg>
                    <div class="welcome-panel-column-content">
                        <h3><?php _e('Documentation and videotutorials', 'astra-nodes'); ?></h3>
                        <p><?php _e('All related information can be found in Digital website.', 'astra-nodes'); ?></p>
                        <a href="<?= esc_url('https://projectes.xtec.cat/digital/serveis-digitals/nodes/') ?>" target="_blank">
                            <?= __('Go to Digital', 'astra-nodes') ?>
                        </a>
                    </div>
                </div>
                <div class="welcome-panel-column">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true" focusable="false">
                        <rect width="48" height="48" rx="4" fill="#1E1E1E"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M18 16h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H18a2 2 0 0 1-2-2V18a2 2 0 0 1 2-2zm12 1.5H18a.5.5 0 0 0-.5.5v3h13v-3a.5.5 0 0 0-.5-.5zm.5 5H22v8h8a.5.5 0 0 0 .5-.5v-7.5zm-10 0h-3V30a.5.5 0 0 0 .5.5h2.5v-8z"
                              fill="#fff"/>
                    </svg>
                    <div class="welcome-panel-column-content">
                        <h3><?php _e('Custom support', 'astra-nodes'); ?></h3>
                        <p><?php _e('Any doubts? Trouble with any configuration? You can ask in the forum.', 'astra-nodes'); ?></p>
                        <a href="<?= esc_url('https://educaciodigital.cat/moodle/moodle/mod/forum/view.php?id=1721') ?>" target="_blank">
                            <?= __('Go to the forum', 'astra-nodes') ?>
                        </a>
                    </div>
                </div>
                <div class="welcome-panel-column">
                    <svg width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg"
                         aria-hidden="true" focusable="false">
                        <rect width="48" height="48" rx="4" fill="#1E1E1E"/>
                        <path fill-rule="evenodd" clip-rule="evenodd"
                              d="M31 24a7 7 0 0 1-7 7V17a7 7 0 0 1 7 7zm-7-8a8 8 0 1 1 0 16 8 8 0 0 1 0-16z" fill="#fff"/>
                    </svg>
                    <div class="welcome-panel-column-content">
                        <h3><?php _e('Teachers\' network', 'astra-nodes'); ?></h3>
                        <p><?php _e('Website administrators can get support in the teacher\'s network.', 'astra-nodes'); ?></p>
                        <a href="<?= esc_url('https://comunitat.edigital.cat/tag/nodes') ?>" target="_blank">
                            <?= __('Go to the teacher\'s network', 'astra-nodes') ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <?php
    });

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
    $wp_admin_bar->remove_node('bp-login'); // In case buddypress is active.

});

// Admin bar: Add Departament d'Educació logo in the first position and menu with XTEC resources.
add_action('admin_bar_menu', function ($wp_admin_bar) {

    global $astra_nodes_options;

    $logo = $astra_nodes_options['organism_logo'] ?? 'department';

    if ($logo === 'ceb') {
        $logo_image = WPMU_PLUGIN_URL . '/astra-nodes/images/logo_ceb.svg';
        $logo_url = 'https://www.edubcn.cat/ca/';
    } else {
        $logo_image = WPMU_PLUGIN_URL . '/astra-nodes/images/logo_department.svg';
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
            'title' => '<img id="logo-xtec" alt="Logo XTEC" src="' . WPMU_PLUGIN_URL . '/astra-nodes/images/logo_xtec.svg' . '">',
            'href' => '',
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

// Customizer: Remove the button to change the theme to all users except xtecadmin.
add_action('customize_controls_print_styles', function () {
    if (!is_xtec_super_admin()) {
        echo '
            <style>
                h3.accordion-section-title .change-theme {
                    display: none !important;
                }
            </style>
            ';
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
        if (isset($configuration['name'])) {
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
        }
        return false;
    });

});

// Customizer: Remove all header sections.
add_action('astra_header_builder_sections', function ($configurations): array {

    return is_xtec_super_admin() ? $configurations : [];

});

// Customizer: Update logo and other information when saving.
add_action('customize_save_after', function ($wp_customize) {

    // Get the value of the 'astra_nodes_options[custom_logo]' setting
    $logo = $wp_customize->get_setting('astra_nodes_options[custom_logo]')->value();

    // Get the attachment ID from the URL
    $logo_id = attachment_url_to_postid($logo);

    // Set the custom logo to the value of the 'astra_nodes_options[custom_logo]' setting.
    set_theme_mod('custom_logo', $logo_id);
    astra_update_option('custom_logo', $logo_id);
    update_option('site_logo', $logo_id);

    // Set the blog name and description.
    update_option('blogname', $wp_customize->get_setting('blogname')->value());
    update_option('blogdescription', $wp_customize->get_setting('blogdescription')->value());

    // Update the colour palette in astra-settings option.
    $palette = $wp_customize->get_setting('astra-color-palettes[currentPalette]')->value();
    $astra_color_palettes = get_option('astra-color-palettes');
    $astra_settings = get_option('astra-settings');
    $astra_settings['global-color-palette']['palette'] = $astra_color_palettes['palettes'][$palette];

    // Categories parameters.
    $astra_settings['blog-post-per-page'] = $wp_customize->get_setting('astra_nodes_pages_posts_per_page')->value();
    $astra_settings['blog-grid-resp']['desktop'] = $wp_customize->get_setting('astra_nodes_pages_columns')->value();
    $astra_settings['blog-grid-resp']['tablet'] = $wp_customize->get_setting('astra_nodes_pages_columns')->value();
    $astra_settings['blog-pagination'] = $wp_customize->get_setting('astra_nodes_pages_infinite_scroll')->value();
    $astra_settings['blog-pagination'] = ($astra_settings['blog-pagination'] === false) ? 'number' : 'infinite';

    astra_update_option('astra-settings', $astra_settings);
    update_option('astra-settings', $astra_settings);

});

// Customizer: Update the register in wp_options that makes possible to change the colour palette
// in the preview. Also, update the global $astra_nodes_options to update the preview.
add_action('customize_preview_init', function ($wp_customize) {

    // This filter updates the preview when changing some parameters in the customizer.
    add_filter('option_astra-settings', function ($value) {

        global $wp_customize;

        if (!isset($wp_customize)) {
            return $value;
        }

        $astra_settings = $value;

        // Categories parameters.
        $astra_settings['blog-post-per-page'] = $wp_customize->get_setting('astra_nodes_pages_posts_per_page')->value();
        $astra_settings['blog-grid-resp']['desktop'] = $wp_customize->get_setting('astra_nodes_pages_columns')->value();
        $astra_settings['blog-grid-resp']['tablet'] = $wp_customize->get_setting('astra_nodes_pages_columns')->value();
        $astra_settings['blog-pagination'] = $wp_customize->get_setting('astra_nodes_pages_infinite_scroll')->value();
        $astra_settings['blog-pagination'] = ($astra_settings['blog-pagination'] === false) ? 'number' : 'infinite';

        // Colour palette.
        $palette = $wp_customize->get_setting('astra-color-palettes[currentPalette]')->value();
        $astra_color_palettes = get_option('astra-color-palettes');
        $astra_settings['global-color-palette']['palette'] = $astra_color_palettes['palettes'][$palette];

        return $astra_settings;

    });

    global $astra_nodes_options;

    // Parameters that require a refresh to update the preview.
    $layout = $wp_customize->get_setting('astra_nodes_options[front_page_layout]')->value();
    $astra_nodes_options['front_page_layout'] = $layout;

    $notice_enable = $wp_customize->get_setting('astra_nodes_options[front_page_notice_enable]')->value();
    $astra_nodes_options['front_page_notice_enable'] = $notice_enable;

    $cards_enable = $wp_customize->get_setting('astra_nodes_options[front_page_cards_enable]')->value();
    $astra_nodes_options['front_page_cards_enable'] = $cards_enable;

    $news_enable = $wp_customize->get_setting('astra_nodes_options[front_page_news_enable]')->value();
    $astra_nodes_options['front_page_news_enable'] = $news_enable;

    $slider_autoplay = $wp_customize->get_setting('astra_nodes_options[front_page_slider_autoplay]')->value();
    $astra_nodes_options['front_page_slider_autoplay'] = $slider_autoplay;

    $slider_enable = $wp_customize->get_setting('astra_nodes_options[front_page_slider_enable]')->value();
    $astra_nodes_options['front_page_slider_enable'] = $slider_enable;

    $slider_arrows = $wp_customize->get_setting('astra_nodes_options[front_page_slider_arrows]')->value();
    $astra_nodes_options['front_page_slider_arrows'] = $slider_arrows;

    $slider_dots = $wp_customize->get_setting('astra_nodes_options[front_page_slider_dots]')->value();
    $astra_nodes_options['front_page_slider_dots'] = $slider_dots;

    $news_category = $wp_customize->get_setting('astra_nodes_options[front_page_news_category]')->value();
    $astra_nodes_options['front_page_news_category'] = $news_category;

    $news_num_posts = $wp_customize->get_setting('astra_nodes_options[front_page_news_num_posts]')->value();
    $astra_nodes_options['front_page_news_num_posts'] = $news_num_posts;

    $notice_layout = $wp_customize->get_setting('astra_nodes_options[front_page_notice_layout]')->value();
    $astra_nodes_options['front_page_notice_layout'] = $notice_layout;

    $pages_sidebar = $wp_customize->get_setting('astra_nodes_options[pages_sidebar]')->value();
    $astra_nodes_options['pages_sidebar'] = $pages_sidebar;

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

    // If the school code seems to be real, show it. Otherwise, don't show anything.
    $school_code = SCHOOL_CODE;
    $prefixes = ['08', '17', '25', '43'];
    $begins_with = static function ($school_code, $prefixes) {
        foreach ($prefixes as $prefix) {
            if (str_starts_with($school_code, $prefix)) {
                return true;
            }
        }
        return false;
    };
    $code = $begins_with($school_code, $prefixes) ? SCHOOL_CODE : '';

    $content = '
            <div id="contact-info-1-wrapper">
                <ol class="fa-ul">
                    <li id="postal-address">
                        <span class="fa-li"><i class="fa-solid fa-location-dot"></i></span>
                        <a id="contact-info-link-to-map" href="' . $link_to_map . '" target="_blank">' . $postal_address . '<br />
                        <span id="postal-code-city">' . $postal_code_city . '</span>
                    </a>
                    </li>
                    ';

    if (!empty($code)) {
        $content .= '
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-barcode"></i></span>' . $code . '
                    </li>
                    ';
    }

    if (!empty($email_address)) {
        $content .= '
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-envelope"></i></span>
                        <a id="email-address" href="mailto:' . $email_address . '">' . $email_address . '</a>
                    </li>
                    ';
    }

    if (!empty($phone_number)) {
        $content .= '
                    <li>
                        <span class="fa-li"><i class="fa-solid fa-mobile-screen-button"></i></span>' . $phone_number . '
                    </li>
                    ';
    }

    $content .= '
                </ol>
            </div>
            <div id="contact-info-2-wrapper">
                <strong>
                    <a id="contact-info-page-url" href="' . $contact_page_url . '">' . __('Contact', 'astra-nodes') . '</a>
                </strong>
            </div>
        ';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}

function astra_nodes_header_buttons(): string {

    global $astra_nodes_options;
    $content = '';

    // Loop through the 6 buttons.
    for ($i = 1; $i <= NUM_BUTTONS_IN_HEADER; $i++) {

        $classes_icon = $astra_nodes_options['header_icon_' . $i . '_classes'] ?? 'fa-solid fa-graduation-cap';
        $text_icon = $astra_nodes_options['header_icon_' . $i . '_text'] ?? __('Item', 'astra-nodes') . ' ' . $i;
        $link_icon = $astra_nodes_options['header_icon_' . $i . '_link'] ?? '';
        $open_in_new_tab = $astra_nodes_options['header_icon_' . $i . '_open_in_new_tab'] ?? false;
        $small_text = $astra_nodes_options['header_icon_' . $i . '_small_text'] ?? false;

        // Add inline CSS to reduce font size if the "small text" option is enabled
        $style_attr = $small_text ? ' style="font-size: smaller;"' : '';

        // Build the button HTML
        $content .= '
            <a class="grid-item grid-item-' . $i . '" href="' . $link_icon . '" ' . ($open_in_new_tab ? ' target="_blank"' : '') . '>
                <i id="header-button-' . $i . '" class="' . $classes_icon . ' astra-nodes-header-icon"></i>
                <span class="header-button-link-' . $i . ' astra-nodes-header-icon-link"' . $style_attr . '>' . $text_icon . '</span>
            </a>
            ';
    }

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}

// Header: Content of the buttons area (mobile).
add_action('astra_nodes_mobile_under_header', function () {

    // For some unknown reason, an echo is required here instead of returning the content.
    echo '
            <div class="detail-container mobile">
                <div class="grid-container">' . astra_nodes_header_buttons() . '</div>
            </div>
            ';

});

// Header: Content of the buttons area (desktop, html-2).
add_action('astra_get_option_header-html-2', function () {

    return '
            <div class="detail-container">
                <div class="grid-container">' . astra_nodes_header_buttons() . '</div>
            </div>
            ';

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

    add_filter('body_class', function ($classes) use ($front_page_layout) {
        $classes[] = 'astra-nodes-' . $front_page_layout;
        return $classes;
    });

    // Default layout is 'sidebar_news'.
    $action = 'astra_primary_content_bottom';
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
            <div id="card-color-' . $i . '" class="wp-block-column has-background is-layout-flow front-page-card"
                 style="' . $card_background . '">
                <div class="astra-nodes-card-title">
                    <h2 id="card-title-' . $i . '" >' . $card_title . '</h2>
                </div>
                <div class="astra-nodes-card-body">
                     <a id="card-link-' . $i . '" href="' . $card_url . '" ' . $card_target . '>
                     ';

            if (!empty($card_image)) {
                echo '
                        <img id="card-image-' . $i . '" class="astra-nodes-card-image" decoding="async" src="' . $card_image . '" alt="">
                     ';
            } else {
                echo '
                        <div class="astra-nodes-card-body-empty"></div>
                     ';
            }

            echo '
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

        // If the notice has no link, use a span instead of an anchor tag.
        if (empty($url)) {
            $anchor_open = '<span id="notice-img-url">';
            $anchor_close = '</span>';
        } else {
            $anchor_open = '<a id="notice-img-url" href="' . $url . '" ' . ($open_in_new_tab ? 'target="_blank"' : '') . '>';
            $anchor_close = '</a>';
        }


        switch ($layout) {
            case 'image':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns front-page-notice-image" style="' . $style . '">
                        <div id="front-page-notice-image-container" class="wp-block-column">' .
                            $anchor_open . '
                                <img id="front-page-notice-image" src="' . $image . '" alt="' . __('Image of the notice', 'astra-nodes') . '" />' .
                            $anchor_close . '
                        </div>
                    </div>
                ';
                break;
            case 'text':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns front-page-notice-text" style="' . $style . '">
                        <div id="front-page-notice-text" class="wp-block-column">
                            <div id="front-page-notice-pre-title" class="has-ast-global-color-0-color">' . $pre_title . '</div>
                                <h2 id="front-page-notice-title" class="has-ast-global-color-1-color">' .
                                    $anchor_open . $title . $anchor_close . '
                                </h2>
                            <div id="front-page-notice-content">' . $content . '</div>
                        </div>
                    </div>
                ';
                break;
            case 'image_text':
                echo '
                    <div id="front-page-notice-container" class="wp-block-columns front-page-notice-image-text">
                            <div id="front-page-notice-image-container" class="wp-block-column">' .
                                $anchor_open . '
                                    <img id="front-page-notice-image" src="' . $image . '" alt="' . __('Image of the notice', 'astra-nodes') . '" />' .
                                $anchor_close . '
                            </div>
                            <div id="front-page-notice-text" class="wp-block-column" style="' . $style . '">
                                <div id="front-page-notice-pre-title" class="has-ast-global-color-0-color">' . $pre_title . '</div>
                                <h2 id="front-page-notice-title" class="has-ast-global-color-1-color">' .
                                    $anchor_open . $title . $anchor_close . '
                                </h2>
                                <div id="front-page-notice-content">' . $content . '</div>
                            </div>
                        </div>
                ';
                break;
        }

    }, $notice_priority, 0);

    // Front page: News configuration.
    add_action('astra_primary_content_bottom', function () {

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

        $category_link = get_category_link($astra_nodes_options['front_page_news_category']);
        $blocks = parse_blocks(get_front_page_news($astra_nodes_options, $category_link));

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

    } elseif (($is_buddypress_active && is_buddypress()) || !is_page()) { // Buddypress or posts.

        unregister_sidebar('sidebar-frontpage');
        unregister_sidebar('categories');

    } else { // All other cases, including pages.

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
                                        <a target="_blank" href="https://projectes.xtec.cat/digital/politica-de-galetes/">Política de galetes</a> |
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

    // Disable the mobile menu in the admin pages. This is necessary to hide the dropdown menu in the
    // menu configuration page and, at the same time, keep it registered in the user interface.
    if (is_admin()) {
        unregister_nav_menu('mobile_menu');
    }

    unregister_nav_menu('secondary_menu');
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

// Admin: Remove Astra menu and Buddypress from the admin panel and block access.
add_action('admin_menu', function () {

    if (!is_xtec_super_admin()) {
        remove_menu_page('astra');
        remove_menu_page('bp-activity');
        remove_menu_page('bp-groups');

        global $pagenow;
        if ($pagenow === 'admin.php' && isset($_GET['page']) && $_GET['page'] === 'astra') {
            wp_redirect(home_url());
            exit;
        }
    }

}, 11);

// Unregister Buddypress post type to disable functionality.
add_action('init', function () {

    if (!is_xtec_super_admin()) {
        unregister_post_type('bp-email');
    }

}, 11);

// Login: Show logo.
add_filter('login_message', function () {

    global $astra_nodes_options;

    $logo_id = $astra_nodes_options['custom_logo'];
    $logo_url = wp_get_attachment_url($logo_id);
    $blog_name = get_option('blogname');

    if (empty($logo_url)) {
        $img = '';
    } else {
        $img = '<img src="' . $logo_url . '" alt="' . $blog_name . '">';
    }

    echo '
        <div id="login_logo">
            ' . $img . '
            <h1>' . $blog_name . '</h1>
        </div>
        ';

}, 10);

// Login: If WordPress Social Login is active, show the widget in the login form (if it configured). It is important to
//        check if the plugin is active to avoid the potential fatal error when the function is called.
include_once ABSPATH . 'wp-admin/includes/plugin.php';

if (is_plugin_active('wordpress-social-login/wp-social-login.php')) {

    add_filter('login_message', 'wsl_render_auth_widget_in_wp_login_form', 20);

    // Remove WP Social Login widget from the login and registration pages.
    add_action('plugins_loaded', function () {

        // Remove the WP Social Login widget from the login page.
        remove_action('login_form', 'wsl_render_auth_widget_in_wp_login_form');

        // Remove the WP Social Login widget from the registration page.
        remove_action('register_form', 'wsl_render_auth_widget_in_wp_login_form');

    });

}

// Menus: Mobile menu must copy main menu. Regular admin users have access only to the main menu.
add_action('after_setup_theme', function () {

    // Get all the menu locations.
    $menu_locations = get_theme_mod('nav_menu_locations');

    // Check that the primary location exists.
    if (isset($menu_locations['primary'])) {
        // Copy the primary menu to the mobile and the secondary menus.
        $menu_locations['mobile_menu'] = $menu_locations['primary'];
        // Update the value.
        set_theme_mod('nav_menu_locations', $menu_locations);
    }

});

// Post and page edit: Hide the Astra sections "Disable Elements" and "Advanced settings" for all users but xtecadmin.
add_action('admin_enqueue_scripts', function () {
    // In admin pages, if the user is not xtecadmin, add an inline style to hide Astra sections.
    if (is_admin() && is_user_logged_in() && !is_xtec_super_admin()) {
        wp_register_style('astra-admin-style', false);
        wp_enqueue_style('astra-admin-style');
        wp_add_inline_style('astra-admin-style', '
            div.components-panel > div#astra_settings_meta_box > div.components-panel__body:last-of-type,
            div.components-panel > div#astra_settings_meta_box > div.components-panel__body:nth-last-of-type(2) {
                display: none;
            }
        ');
    }
});

// Core widget "Navigation Menu": Improve the CSS styles and behaviour.
add_action('wp_footer', function () {
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select all menu items that have submenus.
            const menuItems = document.querySelectorAll('.sidebar-main .menu-item-has-children');

            menuItems.forEach(function (menuItem) {
                // Find the link and the existing SVG icon
                const link = menuItem.querySelector('a');
                const arrow = menuItem.querySelector('.ast-arrow-svg');

                if (link && arrow) {
                    // Add the click event.
                    link.addEventListener('click', function (event) {
                        event.preventDefault();
                        const subMenu = menuItem.querySelector('.sub-menu');
                        const isVisible = subMenu.style.display === 'block';

                        // Toggle the display of the submenu.
                        subMenu.style.display = isVisible ? 'none' : 'block';

                        // Rotate the icon.
                        arrow.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
                        arrow.style.transition = 'transform 0.3s ease';
                    });
                }
            });
        });
    </script>
    <?php
});

// Front page slider: Copy the links of the slides to the divs that show the text over the slides.
add_action('wp_footer', function () {
    if (is_front_page()) {
        ?>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const sliders = document.querySelectorAll(".wp-block-getwid-media-text-slider-slide-content");

                sliders.forEach(slider => {
                    const contentDiv = slider.querySelector(".wp-block-getwid-media-text-slider-slide-content__content");
                    const link = slider.querySelector("a[id^='slider-link']");

                    if (contentDiv && link) {
                        // Change the cursor to "pointer" (hand) to indicate it's clickable.
                        contentDiv.style.cursor = "pointer";

                        // Add a click event to the div.
                        contentDiv.addEventListener("click", function () {
                            const href = link.getAttribute("href");
                            const target = link.getAttribute("target");
                            if (href) {
                                if (target === "_blank") {
                                    window.open(href, "_blank");
                                } else {
                                    window.location.href = href;
                                }
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    }
});

// Front page slider: Truncate the text over the slides to 320 characters in mobile view.
add_action('wp_footer', function () { 
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            if (window.innerWidth < 922) {
                const elements = document.querySelectorAll('p[id^="slider-text-"]');
                elements.forEach(el => {
                    let text = el.textContent.trim();
                    if (text.length > 320) {
                        el.textContent = text.substring(0, 320) + '…';
                    }
                });
            }
        });
    </script>
    <?php
});

// Script to hide/show the options "Show headings" and "Show texts" in the front page slider whether we are in mobile or not.
add_action('customize_controls_print_footer_scripts', function () {
    ?>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // We need to use an interval to check if the elements are loaded in the DOM.
            // When DOMContentLoaded is fired, the elements are not yet available.
            const interval = setInterval(function () {

                const toggleCarousel = document.getElementById('astra_nodes_customizer_front_page_slider_mobile_enable');
                const controlHeadings = document.getElementById('customize-control-astra_nodes_customizer_front_page_slider_mobile_show_headings');
                const controlTexts = document.getElementById('customize-control-astra_nodes_customizer_front_page_slider_mobile_show_texts');

                // Stop interval once elements are found
                if (toggleCarousel && controlHeadings && controlTexts) {
                    clearInterval(interval);

                    // Function to update visibility
                    function updateVisibility() {
                        const isChecked = toggleCarousel.checked;
                        controlHeadings.style.display = isChecked ? 'block' : 'none';
                        controlTexts.style.display = isChecked ? 'block' : 'none';
                    }

                    // Run once on load
                    updateVisibility();

                    // Run on checkbox change
                    toggleCarousel.addEventListener('change', updateVisibility);
                }
            }, 200); // Check every 200ms
        });
    </script>
    <?php
});

// Add default category settings when the theme is activated.
add_action('after_switch_theme', function () {

    // Get Astra settings from wp_options
    $astra_settings_name = 'astra-settings';
    $astra_settings = maybe_unserialize(get_option($astra_settings_name, []));

    if (!is_array($astra_settings)) {
        $astra_settings = [];
    }

    // Columns for category archives.
    $astra_settings['blog-post-per-page'] = 2;

    // Handle responsive columns.
    $astra_settings['blog-grid-resp'] = [
            'desktop' => 2,
            'tablet' => 2,
            'mobile' => 1,
    ];

    // Infinite scroll toggle.
    $astra_settings['blog-pagination'] = 'infinite';

    // Posts per page (default WordPress setting).
    $posts_per_page = 10;
    update_option('posts_per_page', $posts_per_page);

    // Save updated Astra settings.
    update_option($astra_settings_name, $astra_settings);

});
