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
include 'custom-tac/capcalera/icones-capcalera-settings.php';
include 'custom-tac/ginys/giny-logo-centre.php';
include 'custom-tac/menu-principal.php';
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
//    $wp_toolbar->remove_node('search');
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

// Moved and extended function at agora-functions.php file
// 2015.12.04 @nacho
/* Camps extra per definir disposició de noticies a cada categoria*/
/*
function extra_category_fields( $tag ) {    //check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id");
?>

<tr class="form-field">
<th scope="row" valign="top"><label for="articles_fila"><?php _e('Articles per fila'); ?></label></th>
<td>
<input type="text" name="Cat_meta[articles_fila]" id="Cat_meta[articles_fila]" size="25" style="width:60%;" value="<?php echo $cat_meta['articles_fila'] ? $cat_meta['articles_fila'] : ''; ?>"><br />
        <span class="description"><?php _e('Articles per fila que es mostraran a la pàgina de la categoria (entre 1 i 4)'); ?></span>
    </td>
</tr>

<?php }

// Moved and extended function at agora-functions.php file
// 2015.12.04 @nacho
add_action ( 'edit_category_form_fields', 'extra_category_fields');

// save extra category extra fields callback function
function save_extra_category_fields( $term_id ) {
    if ( isset( $_POST['Cat_meta'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['Cat_meta']);
        foreach ($cat_keys as $key){
            if (isset($_POST['Cat_meta'][$key])){
                $cat_meta[$key] = $_POST['Cat_meta'][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}
// save extra category extra fields hook
add_action ( 'edited_category', 'save_extra_category_fields');
*/


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

            #icon-1, #icon-23{
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

