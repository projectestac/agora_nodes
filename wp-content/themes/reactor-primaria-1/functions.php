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

include "custom-tac/metabox-post-parametres.php";
include "custom-tac/capcalera/icones-capcalera-settings.php";
include "custom-tac/ginys/giny-logo-centre.php";
include "custom-tac/menu-principal.php";
include "custom-tac/capcalera/menu-logo.php";
include "custom-tac/capcalera/menu-recursos-tac.php";
include "custom-tac/colors_nodes.php";

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
	 	array('1c','2c-l')
	 );

	/* Support for custom post types */
	 remove_theme_support('reactor-post-types');

	/* Support for page templates */
	add_theme_support(
		'reactor-page-templates',
		array('front-page'/*, 'news-page', 'portfolio', 'contact'*/)
	);

	/* Remove support for background options in customizer */
	remove_theme_support('reactor-backgrounds');

        add_theme_support('reactor-tumblog-icons');

}

/**
 * Fil d'ariadna
 * @author Xavi Meler
 */
function add_breadcrumbs(){
    reactor_breadcrumbs();
}
add_action ('reactor_content_before','add_breadcrumbs',999);

/**************************************************************
* Contingut barra superior (admin bar)
***************************************************************/

//Sempre visible
show_admin_bar( true );

add_action( 'admin_bar_menu', 'add_logo',1 );
add_action( 'admin_bar_menu', 'add_recursos',2);

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

/* Camps extra per definir disposició de noticies a cada categoria*/
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

//Filtre categoria
function filter_by_taxonomy($query) {
    global $categoria;
    global $etiqueta;

    if ($categoria && $query->query['post_type'] == 'post') {
        $query->set('cat', $categoria);
    }

    if ($etiqueta && $query->query['post_type'] == 'post') {
        $query->set('tag', $etiqueta);
    }
}

add_action( 'pre_get_posts', 'filter_by_taxonomy');

// Permet algunes etiquetes html a l'extracte d'un post
function improved_trim_excerpt($text) {

        global $post;
	$allowed_tags='<a>,<ul>,<li>,<ol>';

        $excerpt_more = apply_filters('excerpt_more', ' ' . $excerpt_more);

        if ( '' == $text ) {
            $text = get_the_content('');
            $text = apply_filters('the_content', $text);
            $text = str_replace('\]\]\>', ']]&gt;', $text);
            $text = preg_replace('@<script[^>]*?>.*?</script>@si', '', $text);
            $text = strip_tags($text,$allowed_tags);
            $excerpt_length = 45;

            $words = explode(' ', $text, $excerpt_length + 1);
            if (count($words)> $excerpt_length) {
                    array_pop($words);
                    $text = implode(' ', $words);
            }
            else
                 return $text;

        }

        return $text . $excerpt_more;
}

remove_filter('get_the_excerpt', 'wp_trim_excerpt');

add_filter('get_the_excerpt', 'improved_trim_excerpt');

// Allow HTML in description category/tag
// remove the html filtering
/*
Plugin Name: Tinymce Category Description
Description: Adds a tinymce editor to the category description box
Author: Paulund
Author URI: http://www.paulund.co.uk
Version: 1.0
License: GPL2
*/
// remove the html filtering
remove_filter( 'pre_term_description', 'wp_filter_kses' );

remove_filter( 'term_description', 'wp_kses_data' );

function cat_description($tag)
{ ?>
    <table class="form-table">
        <tr class="form-field">
            <th scope="row" valign="top"><label for="description"><?php _ex('Description', 'Taxonomy Description'); ?></label></th>
            <td>
            <?php
                $settings = array('wpautop' => true, 'media_buttons' => true, 'quicktags' => true, 'textarea_rows' => '15', 'textarea_name' => 'description' );
                wp_editor(htmlspecialchars_decode(wp_kses_post($tag->description , ENT_QUOTES, 'UTF-8'),ENT_QUOTES), 'cat_description', $settings);
            ?>
            <br />
            <span class="description"><?php _e('The description is not prominent by default; however, some themes may show it.'); ?></span>
            </td>
        </tr>
    </table>
    <?php
}

add_filter('edit_category_form_fields', 'cat_description');

function remove_default_category_description()
{
    global $current_screen;
    if ( $current_screen->id == 'edit-category' )
    {
    ?>
        <script type="text/javascript">
        jQuery(function($) {
            $('textarea#description').closest('tr.form-field').remove();
        });
        </script>
    <?php
    }
}
add_action('admin_head', 'remove_default_category_description');


add_action("reactor_content_before","menu_principal");

// Zona de Ginys per categories
if ( function_exists('register_sidebar') ) {
    register_sidebars( 1,
    array(
    'name'          => __( 'Categories (Barra esquerra)', 'custom_tac' ),
    'id'            => 'categoria',
    'description'   => 'Barra lateral a les pàgines de categories (ESO, ESO1, ESO1A...)',
    'class'         => '',
    'before_widget' => '
    <div id="%1$s" class="widget %2$s">',
    'after_widget' => '</div>
    ',
    'before_title' => '
    <h4 class="widgettitle">',
    'after_title' => '</h4>
    '
    ));
}

/**
 * Hide widgets
 * @author Xavi Meler
 */
function unregister_default_widgets() {
     unregister_widget('WP_Widget_Calendar');
     unregister_widget('WP_Widget_Meta');
     unregister_widget('WP_Widget_Search');
     unregister_widget('BBP_Login_Widget');
     unregister_widget('BBP_Search_Widget');
     unregister_widget('BBP_Stats_Widget');
     unregister_widget('InviteAnyoneWidget');
     unregister_widget('BP_Core_Whos_Online_Widget');
     unregister_widget('BP_Core_Login_Widget');
     unregister_widget('BP_Messages_Sitewide_Notices_Widget');

 }
 add_action('widgets_init', 'unregister_default_widgets', 11);


/**
 * Remove not used dashboard metaboxes
 * @author Xavi Meler
 */
function remove_dashboard_widgets(){
    //remove_meta_box('dashboard_right_now', 'dashboard', 'normal');   // Right Now
    //remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal'); // Recent Comments
    remove_meta_box('dashboard_incoming_links', 'dashboard', 'normal');  // Incoming Links
    remove_meta_box('dashboard_plugins', 'dashboard', 'normal');   // Plugins
    remove_meta_box('dashboard_quick_press', 'dashboard', 'side');  // Quick Press
    remove_meta_box('dashboard_recent_drafts', 'dashboard', 'side');  // Recent Drafts
    remove_meta_box('dashboard_primary', 'dashboard', 'side');   // WordPress blog
    remove_meta_box('dashboard_secondary', 'dashboard', 'side');   // Other WordPress News
    remove_meta_box('bbp-dashboard-right-now', 'dashboard', 'side');   // Other WordPress News
}

add_action('wp_dashboard_setup', 'remove_dashboard_widgets');

include "custom-tac/rss-metabox.php";

add_action('wp_dashboard_setup', 'rss_register_widgets');

function rss_register_widgets() {
    global $wp_meta_boxes;
    wp_add_dashboard_widget('widget_rss_nodes', "Notícies", 'rss_box');
}

// Tauler personalitzat
include "custom-tac/welcome-panel.php";

add_action( 'welcome_panel', 'rc_my_welcome_panel' );

/**
 * Remove option in admin bar added by the extension 'WordPress Social Login'.
 * It is removed for all users
 *
 * @global Array $wp_admin_bar
 * @author Toni Ginard
 */
function tweak_admin_bar() {

    global $wp_admin_bar;

    $wp_admin_bar->remove_menu('wp-admin-wordpress-social-login');

}

add_action('wp_before_admin_bar_render', 'tweak_admin_bar');

/**
 * Build HTML page to centralize all BuddyPress-related stuff
 *
 * @author Toni Ginard
 *
 */
function bp_options_page() {
    ?>
    <div class="wrap">

        <div style="width:150px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e('BuddyPress', 'buddypress'); ?></h3>
            <p><a href="admin.php?page=bp-components"><?php _e('Components', 'buddypress'); ?></a></p>
            <p><a href="admin.php?page=bp-activity"><?php _e('Activity', 'buddypress'); ?></a></p>
            <p><a href="admin.php?page=bpfb-settings"><?php _e('Activity Plus', 'bpfb'); ?></a></p>
            <p><a href="admin.php?page=bp-groups"><?php _e('Groups', 'buddypress'); ?></a></p>
            <p><a href="admin.php?page=bp-like-settings"><?php _e('BuddyPress Like', 'buddypress-like'); ?></a></p>
            <p><a href="admin.php?page=ass_admin_options"><?php _e('Group Email Options', 'bp-ass'); ?></a></p>
        </div>

        <div style="width:150px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e('BuddyPress Docs', 'bp-docs'); ?></h3>
            <p><a href="edit.php?post_type=bp_doc"><?php _e('BuddyPress Docs', 'bp-docs'); ?></a></p>
            <p><a href="post-new.php?post_type=bp_doc"><?php _ex( 'Add New', 'add new', 'bp-docs' ) ?></a></p>
            <p><a href="edit-tags.php?taxonomy=bp_docs_associated_item&post_type=bp_doc"><?php _e('Associated Items', 'bp-docs'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=bp_docs_tag&post_type=bp_doc"><?php _e('Docs Tags', 'bp-docs' ); ?></a></p>
            <p><a href="edit.php?post_type=bp_doc&page=bp-docs-settings"><?php _e('Settings', 'bp-docs'); ?></a></p>
        </div>

        <div style="width:150px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e('BuddyPress Invitations', 'bp-invite-anyone'); ?></h3>
            <p><a href="admin.php?page=invite-anyone"><?php _e('Invite Anyone', 'bp-invite-anyone'); ?></a></p>
            <p><a href="edit.php?post_type=ia_invites"><?php _e('BuddyPress Invitations', 'bp-invite-anyone'); ?></a></p>
            <p><a href="post-new.php?post_type=ia_invites"><?php _e('Add New', 'bp-invite-anyone'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=ia_invitees&post_type=ia_invites"><?php _e('Invitee', 'bp-invite-anyone'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=ia_invited_groups&post_type=ia_invites"><?php _e('Invited Group', 'bp-invite-anyone'); ?></a></p>
        </div>

        <div style="width:150px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e('BP Moderation', 'bp-moderation'); ?></h3>
            <p><a href="admin.php?page=bp-moderation&view=contents"><?php _e('Contents', 'bp-moderation'); ?></a></p>
            <p><a href="admin.php?page=bp-moderation&view=users"><?php _e('Users', 'bp-moderation'); ?></a></p>
            <p><a href="admin.php?page=bp-moderation&view=settings"><?php _e('Settings', 'bp-moderation'); ?></a></p>
        </div>

    </div>
    <?php
}

/**
 * Move options from Settings to custom BuddyPress page, step 1. This movement
 * is broken in two steps because some actions need to be done early and some
 * need to be done later, depending on the implementation of every plugin.
 *
 * @author Toni Ginard
 *
 */
function rebuild_bp_menus_step_1() {

    remove_submenu_page('options-general.php', 'bpfb-settings'); // Activity Plus
    remove_submenu_page('options-general.php', 'invite-anyone'); // Invite anyone

    add_menu_page(__('BuddyPress', 'buddypress'), __('BuddyPress', 'buddypress'), 'manage_options', 'xtec-bp-options', 'bp_options_page', '', 59);

    add_submenu_page('xtec-bp-options', __('Components', 'buddypress'), __('Components', 'buddypress'), 'manage_options', 'bp-components', 'bp_core_admin_components_settings');
    add_submenu_page('xtec-bp-options', __('Activity', 'buddypress'), __('Activity', 'buddypress'), 'manage_options', 'bp-activity');
    add_submenu_page('xtec-bp-options', __('Activity Plus', 'bpfb'), __('Activity Plus', 'bpfb'), 'manage_options', 'bpfb-settings', 'settings_page');
    add_submenu_page('xtec-bp-options', __('Groups', 'buddypress'), __('Groups', 'buddypress'), 'manage_options', 'bp-groups');
    add_submenu_page('xtec-bp-options', __('BuddyPress Like', 'buddypress-like'), __('BuddyPress Like', 'buddypress-like'), 'manage_options' , 'bp-like-settings' , 'bp_like_admin_page');
    add_submenu_page('xtec-bp-options', __('Group Email Options', 'bp-ass'), __('Group Email Options', 'bp-ass'), 'manage_options', 'ass_admin_options', 'ass_admin_options');
    add_submenu_page('xtec-bp-options', __('Invite Anyone', 'invite-anyone'), __('Invite Anyone', 'invite-anyone'), 'manage_options', 'invite-anyone', 'invite_anyone_admin_panel');

}

/**
 * Move options from Settings to custom BuddyPress page, step 2. This movement
 * is broken in two steps because some actions need to be done early and some
 * need to be done later, depending on the implementation of every plugin.
 *
 * @author Toni Ginard
 *
 */
function rebuild_bp_menus_step_2() {

    remove_menu_page('bp-activity');
    remove_menu_page('bp-groups');

    remove_submenu_page('options-general.php', 'bp-components'); // Tab in BuddyPress
    remove_submenu_page('bp-general-settings', 'ass_admin_options'); // Group Email
    remove_submenu_page('options-general.php', 'bp-like-settings'); // BuddyPress Like

}

/**
 * Build HTML page to centralize all bbpress-related stuff
 *
 * @author Toni Ginard
 *
 */
function bbpress_options_page() {
    ?>
    <div class="wrap">
        <div style="width:150px; padding:20px; float:left;">
            <h3><?php _e('Forums', 'bbpress'); ?></h3>
            <p><a href="edit.php?post_type=forum"><?php _e('All Forums', 'bbpress'); ?></a></p>
            <p><a href="post-new.php?post_type=forum"><?php _e('New Forum', 'bbpress'); ?></a></p>
        </div>

        <div style="width:150px; padding:20px; float:left;">
            <h3><?php _e('Topics', 'bbpress'); ?></h3>
            <p><a href="edit.php?post_type=topic"><?php _e('All Topics', 'bbpress'); ?></a></p>
            <p><a href="post-new.php?post_type=topic"><?php _e('New Topic', 'bbpress'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=topic-tag&post_type=topic"><?php _e('Topic Tags', 'bbpress'); ?></a></p>
        </div>

        <div style="width:150px; padding:20px; float:left;">
            <h3><?php _e('Replies', 'bbpress'); ?></h3>
            <p><a href="edit.php?post_type=reply"><?php _e('All Replies', 'bbpress'); ?></a></p>
            <p><a href="post-new.php?post_type=reply"><?php _e('New Reply', 'bbpress'); ?></a></p>
        </div>
    </div>
    <?php
}

/**
 * Build bbpress custom menu
 *
 * @author Toni Ginard
 *
 */
function rebuild_bbpress_menus() {
    add_menu_page(__('Forums', 'bbpress'), __('Forums', 'bbpress'), 'manage_options', 'xtec-bbpress-options', 'bbpress_options_page', '', 58);
}

/**
 * Remove menus for administrators who are not xtecadmin
 *
 * @author Toni Ginard
 *
 */
function remove_admin_menus() {

    if (!is_xtecadmin()) {
        // Forum
        remove_submenu_page('options-general.php', 'bbpress');

        // BuddyPress
        remove_submenu_page('options-general.php', 'bp-page-settings'); // Tab in BuddyPress
        remove_submenu_page('options-general.php', 'bp-settings'); // Tab in BuddyPress

        // Private BP Pages
        remove_submenu_page('options-general.php', 'bphelp-pbp-settings'); // In this case, it doesn't block access

        // Settings | Writing
        remove_submenu_page('options-general.php', 'options-writing.php'); // In this case, it doesn't block access
    }
}

/**
 * Unregister WordPress Social Login admin tabs
 *
 * @author Toni Ginard
 */
function wsl_unregister_admin_tabs() {
    global $WORDPRESS_SOCIAL_LOGIN_ADMIN_TABS;
    unset($WORDPRESS_SOCIAL_LOGIN_ADMIN_TABS['login-widget']);
    unset($WORDPRESS_SOCIAL_LOGIN_ADMIN_TABS['components']);
    unset($WORDPRESS_SOCIAL_LOGIN_ADMIN_TABS['help']);
}

// Remove items for all users but xtecadmin (check for xtecadmin is in the function
//   because global $current_user is not set at this stage)
add_action('admin_menu', 'remove_admin_menus');
add_action('wsl_register_setting_end', 'wsl_unregister_admin_tabs');

// Rebuild menus for all users
add_action('admin_menu', 'rebuild_bp_menus_step_1', 1); // Priority 1 is important!
add_action('admin_menu', 'rebuild_bp_menus_step_2'); // Default priority (10) is important!
add_action('admin_menu', 'rebuild_bbpress_menus');

/**
 * Avoid delete this pages: Activitat(5), Membres(6), Nodes(sec 16,pri 141)
 *
 * @author Xavi Meler & Toni Ginard
 */

// XTEC ************ ELIMINAT - moved and extended function at agora-functions.php file
// 2015.07.31 @nacho
/*

/*add_action('wp_trash_post', 'restrict_post_deletion', 10, 1);

function restrict_post_deletion($post_ID){

    $restricted_pages = array(5,6,16,141);
    if(get_post_type( $post_ID )=="page" && in_array($post_ID, $restricted_pages)){
        echo "Aquesta p&agrave;gina forma part de l'estructura de NODES. No es pot esborrar.";
        exit;
    }
}*/
//************ FI

/**
 * Remove Page Templates
 *
 * @author Xavi Meler
 * Thanks Alex Angas
 */

function remove_page_templates( $templates ) {
    unset( $templates['page-templates/contact.php'] );
    unset( $templates['page-templates/portfolio.php'] );
    unset( $templates['page-templates/news-page.php'] );
    return $templates;
}

add_filter( 'theme_page_templates', 'remove_page_templates' );

/**
 * Menú shortcode
 *
 * @author Xavi Meler
 * http://www.smashingmagazine.com/2012/05/01/wordpress-shortcodes-complete-guide/
 *
 * Usage: [menu name="main-menu"]


function menu_function($atts, $content = null) {
   extract(
      shortcode_atts(
         array( 'nom' => null,
                'mostra'=>"horitzontal",
                'nivells'=>1 ),
         $atts
      )
   );
   return wp_nav_menu(
      array(
          'menu' => $nom,
          'mostra' => $mostra,
          'echo' => false,
          'depth'=> $nivells,
          'walker'=> new themeslug_walker_nav_menu
          )
   );

}
add_shortcode('menu', 'menu_function');

*/


/**
 * get_description_text
 *
 * Get the description text depending of type of page
 *
 * @author Xavi Meler
 *
 */

function get_description_text (){
    switch (true){
        case is_category():
            $description = single_cat_title( '', false );
            break;
        case is_tag():
             $description = single_tag_title( '', false );
            break;
        default:
             $description = esc_attr( get_bloginfo( 'description', 'display' ) );
    }
    return $description;
}


/**
 * get_description_font_size
 *
 * Determina la mida de la font de la caixa descripció en funció del nombre de
 * paraules i de la mida de cada paraula
 *
 * Si hi ha una paraula molt llarga, redueix la font a 1.5em
 * Si hi ha més de 3 paraules, redueix a 1.8em
 * Si hi ha més de 8 paraules, redueix a 1.5em
 * Paraula mitja: 5 caràcteres/paraula
 *
 * @author Xavi Meler
 *
 */

function get_description_font_size($description){

    $description_len = strlen($description);
    $aDescription = explode(" ",$description);

    foreach ($aDescription as $word) {
        if (strlen($word) > 10)
            return "2vw";
    }

    switch (true) {
        case $description_len <= 15: //3 paraules aprox. Paraula mitja: 5caracters
            $fontSize = "2.5vw";
            break;
        case 15 < $description_len && $description_len <= 40:
            $fontSize = "2vw";
            break;
        case $description_len > 40:
            $fontSize = "1.5vw";
            break;
    }
    return $fontSize;
}

function get_icon_font_size($icon_text){
    $icon_text_len = strlen($icon_text);
    $aIcon_text = explode(" ",$icon_text);

    foreach ($aIcon_text as $word) {
        if (strlen($word) > 9)
            return "0.8vw";
    }
    return "1vw";
}

/**
 * Informació del centre al peu de la versió per imprimir
 *
 * @author Xavi Meler
 */

add_action('reactor_footer_after', 'footer_mediaprint');

function footer_mediaprint(){
    echo "<div id='info-footer-mediaprint'>". reactor_option('nomCanonicCentre')." | ".  get_home_url()."</div>";
}

/*
 * Fixem la portada amb la configuració de "pàgina" i establim la pàgina segons
 * el valor definit al customizer (Personalitza). Evitem dependre de les opcions de
 * Paràmetres -> Lectura i del problema de l'esborrat de la pàgina d'inici definida allà.
 *
 * @author Xavi Meler
 */

add_filter("pre_option_show_on_front","show_on_front_page");
function show_on_front_page($value) {
    return "page";
}

add_filter("pre_option_page_on_front","set_page_on_front");
function set_page_on_front($value) {
    return reactor_option("frontpage_page");
}

/**
 * Canvia la galeta perquè no sigui secura (wordpress_ enlloc de wordpress_sec) per no haver de validar dues vegades en accedir al Tauler
 *
 * @author Sara Arjona
 *
*/
// @aginard: Code commented because it breaks access to wp-admin in WordPress 4.0.1. Looks like
//            it is no longer necessary. Possibly it can be removed.
/*
add_filter('secure_auth_cookie', 'wpadmin_secure_cookie_filter');

function wpadmin_secure_cookie_filter( ) {
	return false;
}
*/

/**
 * If external adress, open link on new window
 *
 * @author Xavi Meler
*/

function set_target($link){
    if (strpos(trim($link),"http")===0)
        return "target='_blank'";
    else
        return "";
}

/**
 * Replace "es.scribd.com" per "www.scribd.com" cause es.scribd.com doesn't work as a oEmbed provider
 * I try to add as a oEmbed provider via wp_oembed_add_provider but doesn't work
 *
 * @author Xavi Meler
*/

add_filter('wp_insert_post_data', 'fix_spanish_scribd_oembed', 10, 2);

function fix_spanish_scribd_oembed ($filtered_data, $raw_data){
    $filtered_data['post_content'] = str_replace('es.scribd.com', 'www.scribd.com', $filtered_data['post_content']);
    return $filtered_data;
}


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

/**
 * Custom login form
 * 
 * Logo is a simple workaround to show a simple image. 
 * No default wp_logo is showed because is necessary to define dimensions (to fit school's logo accurently)
 * I has valorated getimagesize, but is disabled for security reasons.
 * @author Xavier Meler
 */

//Logo image from customizer 
function show_logo () {
    echo "<div id='login_logo'><img src=" . reactor_option('logo_image') . ">"
       . "<h1>" . reactor_option('nomCanonicCentre') . "</h1></div>";
}


// Show logo and WP_social_login widget
add_filter( 'login_message', 'show_logo',10 );
add_filter( 'login_message', 'wsl_render_auth_widget_in_wp_login_form',20 );
// Remove original WP_social_login widget because are in a bad position for usability
remove_action( 'login_form', 'wsl_render_auth_widget_in_wp_login_form' );

// Load styles
function load_stylesheet() {
    wp_enqueue_style( 'custom-login', get_template_directory_uri() . '-primaria-1/style.css' );
}
add_action( 'login_enqueue_scripts', 'load_stylesheet' );