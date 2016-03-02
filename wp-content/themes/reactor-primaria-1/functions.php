<?php
/**
 * Reactor Child Theme Functions
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @author Xavi Meler (jmeler@xtec.cat)
 * @author Toni Ginard (aginard@xtec.cat)
 * @version 1.1.0
 * @since 1.0.0
 * @copyright Copyright (c) 2013, TODO
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

/* -------------------------------------------------------
 You can add your custom functions below
-------------------------------------------------------- */

/**react
 * Child Theme Features
 * The following function will allow you to remove features included with Reactor
 *
 * Remove the comment slashes (//) next to the functions
 * For add_theme_support, remove values from arrays to disable parts of the feature
 * remove_theme_support will disable the feature entirely
 * Reference the functions.php file in Reactor for add_theme_support functions
 */

$theme_root = get_theme_root();

include $theme_root . '/reactor/custom-tac/metabox-post-parametres.php';
include $theme_root . '/reactor/custom-tac/capcalera/icones-capcalera-settings.php';
include $theme_root . '/reactor/custom-tac/ginys/giny-logo-centre.php';
include $theme_root . '/reactor/custom-tac/menu-principal.php';
include $theme_root . '/reactor/custom-tac/capcalera/menu-logo.php';
include $theme_root . '/reactor/custom-tac/capcalera/menu-recursos-tac.php';
include $theme_root . '/reactor/custom-tac/colors_nodes.php';

add_action('after_setup_theme', 'reactor_child_theme_setup', 11);

function reactor_child_theme_setup() {

    /* Support for menus */
    // remove_theme_support('reactor-menus');
    add_theme_support(
        'reactor-menus',
        array('main-menu','side-menu')
    );

    /* Support for sidebars
    Note: this doesn't change layout options */
    // remove_theme_support('reactor-sidebars');
    add_theme_support(
    'reactor-sidebars',
    array('primary', 'secondary', 'front-primary', 'front-secondary','categoria', 'footer')
    );

    add_theme_support(
        'reactor-layouts',
        array('1c','2c-l', '2c-r', '3c-c')
    );

    /* Support for page templates */
    add_theme_support(
        'reactor-page-templates',
        array('front-page'/*, 'news-page', 'portfolio', 'contact'*/)
    );

    add_theme_support('reactor-tumblog-icons');
}

// Eliminem icones de la barra superior
function custom_toolbar($wp_toolbar) {
    $wp_toolbar->remove_node('wp-logo');
    $wp_toolbar->remove_node('updates');
    $wp_toolbar->remove_node('comments');
    $wp_toolbar->remove_node('new-content');
    $wp_toolbar->remove_node('search');
    $wp_toolbar->remove_node('themes');
    $wp_toolbar->add_node( array(
    'parent' => 'site-name',
    'id' => 'entrades',
    'title' => __('Articles'),
    'href' => admin_url( 'edit.php')
    ));
    $wp_toolbar->add_node( array(
'parent' => 'site-name',
'id' => 'pagines',
'title' => __('Pàgines'),
'href' => admin_url( 'edit.php?post_type=page')
));
}
add_action('admin_bar_menu', 'custom_toolbar',98);


function get_colors(){

    global $colors_nodes;

    $paleta = reactor_option('paleta_colors','blaus');

    $color_primary   = $colors_nodes[$paleta][1];
    $color_secondary = $colors_nodes[$paleta][2];
    $color_footer    = isset($colors_nodes[$paleta][3])?$colors_nodes[$paleta][3]:$color_secondary;
    $color_link      = isset($colors_nodes[$paleta][4])?$colors_nodes[$paleta][4]:$color_secondary;
    $color_icon22    = isset($colors_nodes[$paleta][5])?$colors_nodes[$paleta][5]:$color_secondary;
    $color_calendari = isset($colors_nodes[$paleta][6])?$colors_nodes[$paleta][6]:$color_secondary;

    $css="
            .box-title{
                background-color:$color_primary;
            }

            .box-description{
                background-color:$color_secondary;
            }

            #icon-11, #icon-23{
                background-color:$color_secondary;
            }

            #icon-21, #icon-13{
                background-color:$color_primary;
            }
            #icon-22 a {
                color:$color_icon22 !important;
            }

            h1, h2, h3, h4, h5, h6, a {
                color: $color_link  !important;
            }

            #menu-panel {
                    border-bottom: 2px solid $color_secondary;
            }

            .entry-comments,
            .entry-categories>a,
            .entry-tags >a {
                color: $color_secondary  !important;
            }

            .entry-comments:before,
            .entry-categories:before,
            .entry-tags:before{
                    color: $color_secondary; }
            .menu-link, .sub-menu-link {
                    color: $color_secondary  !important;
            }


            .gce-today span.gce-day-number{
                border: 3px solid $color_calendari !important;
            }

            .gce-widget-grid .gce-calendar th abbr {
                color: $color_calendari;
            }

            #footer {
                background-color: $color_footer;
            }
       ";

    return $css;

}

