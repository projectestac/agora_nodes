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
function astra_load_css(): void {
    wp_register_style('astra-functions-css', plugins_url('/astra-styles/style.css', __FILE__));
    wp_enqueue_style('astra-functions-css');
}
add_action('wp_enqueue_scripts', 'astra_load_css');

// Admin bar: Force to be always shown, including for non-logged users.
add_filter('show_admin_bar', '__return_true');

// Admin bar: Remove WordPress logo.
add_action('wp_before_admin_bar_render', 'nodes_admin_bar_remove_logo');
function nodes_admin_bar_remove_logo(): void {
    global $wp_admin_bar;
    $wp_admin_bar->remove_node('wp-logo');
}

// Admin bar: Add Departament d'Educació logo in the first position and menu with XTEC resources.
add_action('admin_bar_menu', 'nodes_modify_admin_bar');
function nodes_modify_admin_bar($wp_admin_bar): void {

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
            'href' => wp_login_url(),
        ]);
    }

}

// Customizer: Remove all Astra sections.
add_filter('astra_header_builder_sections', 'nodes_remove_customizer_header_footer_sections');
add_filter('astra_footer_builder_sections', 'nodes_remove_customizer_header_footer_sections');
add_filter('astra_customizer_sections', 'nodes_remove_customizer_general_sections');

function nodes_remove_customizer_general_sections ($configurations): array {
    if (is_xtec_super_admin()) {
        return $configurations;
    }

    return array_filter($configurations, static function ($configuration) {
        return $configuration['type'] !== 'section';
    });
}

function nodes_remove_customizer_header_footer_sections($configurations): array {
    if (is_xtec_super_admin()) {
        return $configurations;
    }

    return [];
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


// After the control is added, you can add an action to the 'customize_save_after' hook
// This action will be triggered after the customizer settings are saved
add_action('customize_save_after', function($wp_customize) {
    // Get the value of the 'astra_nodes_options[custom_logo]' setting
    $logo_id = $wp_customize->get_setting('astra_nodes_options[custom_logo]')->value();

    // Set the custom logo to the value of the 'astra_nodes_options[custom_logo]' setting
    set_theme_mod('custom_logo', $logo_id);
});







//add_filter('astra_get_option_header-html-3', 'custom_header_html_3', 120, 0);
function custom_header_html_3() {
//    $content = astra_get_option('header-html-1');
    return '<div class="tipus-centre">Centre formatiu</div>
<h1>Institut Agustí Serra i Fontanet</h1>
<h2><span style="color: #00b856; font-size: 18pt;">ESO Batxillerat Cicles Formatius</span></h2>';
}

//add_action('wp_footer', 'meu_tema_elimina_personalitzador', 200);
function meu_tema_elimina_personalitzador() {
    global $wp_customize;
    // Treure la secció Color de Fons
    //$wp_customize->remove_section('panel-global');
    $wp_customize->remove_panel('panel-global');

    // O bé, pots treure controls específics
    //$wp_customize->remove_control('astra-global');
}

//add_action('customize_register', 'llistar_seccions_personalitzador', 120);
function llistar_seccions_personalitzador($wp_customize) {
    foreach ($wp_customize->panels() as $key => $object ) {
        echo $key . ' - ' . $object->title . "\n";
    }
}
