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

    /*
    // Get the value of the 'astra_nodes_options[custom_logo]' setting
    $logo = $wp_customize->get_setting('astra_nodes_options[card_1_image]')->value();

    // Get the attachment ID from the URL
    $logo_id = attachment_url_to_postid($logo);

    // Set the custom logo to the value of the 'astra_nodes_options[custom_logo]' setting.
    set_theme_mod('astra_nodes_options[card_1_image]', $logo_id);
    */

    // Set the blog name and description.
    update_option('blogname', $wp_customize->get_setting('blogname')->value());
    update_option('blogdescription', $wp_customize->get_setting('blogdescription')->value());

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

    $classes_icon_1 = $astra_nodes_options['header_icon_1_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_1 = $astra_nodes_options['header_icon_1_text'] ?? __('Item', 'astra-nodes') . ' 1';
    $link_icon_1 = $astra_nodes_options['header_icon_1_link'] ?? '';

    $classes_icon_2 = $astra_nodes_options['header_icon_2_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_2 = $astra_nodes_options['header_icon_2_text'] ?? __('Item', 'astra-nodes') . ' 2';
    $link_icon_2 = $astra_nodes_options['header_icon_2_link'] ?? '';

    $classes_icon_3 = $astra_nodes_options['header_icon_3_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_3 = $astra_nodes_options['header_icon_3_text'] ?? __('Item', 'astra-nodes') . ' 3';
    $link_icon_3 = $astra_nodes_options['header_icon_3_link'] ?? '';

    $classes_icon_4 = $astra_nodes_options['header_icon_4_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_4 = $astra_nodes_options['header_icon_4_text'] ?? __('Item', 'astra-nodes') . ' 4';
    $link_icon_4 = $astra_nodes_options['header_icon_4_link'] ?? '';

    $classes_icon_5 = $astra_nodes_options['header_icon_5_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_5 = $astra_nodes_options['header_icon_5_text'] ?? __('Item', 'astra-nodes') . ' 5';
    $link_icon_5 = $astra_nodes_options['header_icon_5_link'] ?? '';

    $classes_icon_6 = $astra_nodes_options['header_icon_6_classes'] ?? 'fa-solid fa-graduation-cap';
    $text_icon_6 = $astra_nodes_options['header_icon_6_text'] ?? __('Item', 'astra-nodes') . ' 6';
    $link_icon_6 = $astra_nodes_options['header_icon_6_link'] ?? '';

    $content = '
        <div class="detail-container">
        <div class="grid-container">
        <div class="grid-item" style="background-color: #38a09b;">
            <i id="header-button-1" class="' . $classes_icon_1 . '""></i> 
            <br>
            <a href="' . $link_icon_1 . '">' . $text_icon_1 . '</a>
        </div>
        <div class="grid-item" style="background-color: #25627e;">
            <i id="header-button-2" class="' . $classes_icon_2 . '"></i>
            <br>
            <a href="' . $link_icon_2 . '">' . $text_icon_2 . '</a>
        </div>
        <div class="grid-item" style="background-color: #2b245e; border-radius: 0 30px 0 0;">
            <i id="header-button-3" class="' . $classes_icon_3 . '"></i>
            <br>
            <a href="' . $link_icon_3 . '">' . $text_icon_3 . '</a>
        </div>
        <div class="grid-item" style="background-color: #2b245e;">
            <i id="header-button-4" class="' . $classes_icon_4 . '"></i>
            <br>
            <a href="' . $link_icon_4 . '">' . $text_icon_4 . '</a>
        </div>
        <div class="grid-item" style="background-color: #38a09b;">
            <i id="header-button-5" class="' . $classes_icon_5 . '"></i>
            <br>
            <a href="' . $link_icon_5 . '">' . $text_icon_5 . '</a>
        </div>
        <div class="grid-item" style="background-color: #25627e; color: white; border-radius: 0 0 30px 0;">
            <i id="header-button-6" class="' . $classes_icon_6 . '"></i>
            <br>
            <a href="' . $link_icon_6 . '">' . $text_icon_6 . '</a>
        </div>
        </div>
        </div>
        ';

    // Remove all the "\n" characters.
    return str_replace("\n", '', $content);

}, 20, 0);

add_filter('astra_header_after', function () {

    // Get the option array from the wp_options table.
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    echo '
        <div class="wp-block-columns has-small-font-size is-layout-flex wp-container-7" style="padding: var(--wp--preset--spacing--60);">
    ';

    for ($i = 1; $i <= 4; $i++) {
        $card_title = $astra_nodes_options['card_' . $i . '_title'] ?? '';
        $card_image = $astra_nodes_options['card_' . $i . '_image'] ?? '';

        echo '
            <div class="wp-block-column border-radius-30-r has-ast-global-color-0-background-color has-background is-layout-flow"
                 style="border-style:none; border-width:0; padding:0;">
                <div class="wp-block-uagb-info-box uagb-block-681b6c87 uagb-infobox__content-wrap  uagb-infobox_cta-type-all uagb-infobox-icon-below-title uagb-infobox-image-valign-top">
                    <a href="" class="uagb-infobox-link-wrap uagb-infbox__link-to-all" target="_self" aria-label="Infobox Link" rel="noopener noreferrer"
                       onclick="return false;"></a>
                    <div class="uagb-ifb-content">
                        <div class="uagb-ifb-title-wrap">
                            <h3 class="uagb-ifb-title">' . $card_title . '</h3>
                        </div>
                        <div class="uagb-ifb-image-content">
                            <img decoding="async"
                                 src="' . $card_image . '"
                                 alt="" width="1010" height="673" loading="lazy">
                        </div>
                        <div class="uagb-ifb-button-wrapper wp-block-button"></div>
                    </div>
                </div>
            </div>
        ';
    }

    echo '</div>';

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

