<?php
/**
 * Reactor Theme Functions
 *
 * @package Reactor
 * @author Anthony Wilhelm (@awshout / anthonywilhelm.com)
 * @since 1.1.0
 * @copyright Copyright (c) 2013, Anthony Wilhelm
 * @license GNU General Public License v2 or later (http://www.gnu.org/licenses/gpl-2.0.html)
 */

require locate_template('library/reactor.php');
new Reactor();

add_action('after_setup_theme', 'reactor_theme_setup', 10);

function reactor_theme_setup() {

	/**
	 * Reactor features
	 */
	add_theme_support(
		'reactor-menus',
		array('top-bar-l', 'top-bar-r', 'main-menu', 'side-menu', 'footer-links')
	);

	add_theme_support(
		'reactor-sidebars',
		array('primary', 'secondary', 'front-primary', 'front-secondary', 'footer')
	);

	add_theme_support(
			'reactor-layouts',
			array('1c','2c-l', '2c-r', '3c-c')
	);

	add_theme_support(
		'reactor-page-templates',
		array('front-page', 'news-page', 'portfolio', 'contact')
	);

	add_theme_support('reactor-breadcrumbs');

	add_theme_support('reactor-page-links');

	add_theme_support('reactor-post-meta');

	add_theme_support('reactor-custom-login');

	add_theme_support('reactor-taxonomy-subnav');

	add_theme_support('reactor-tumblog-icons');

	add_theme_support('reactor-translation');

	/**
	 * WordPress features
	 */
	add_theme_support('menus');

	// different post formats for tumblog style posting
	add_theme_support(
		'post-formats',
		array('aside', 'gallery','link', 'image', 'quote', 'status', 'video', 'audio', 'chat')
	);

	add_theme_support('post-thumbnails');
	// thumbnail sizes - you can add more
	add_image_size('thumb-300', 300, 250, true);
	add_image_size('thumb-200', 200, 150, true);

	// these are not needed
	// add_theme_support('custom-background');
	// add_theme_support('custom-header');

	// RSS feed links to header.php for posts and comments.
	add_theme_support('automatic-feed-links');

	// editor stylesheet for TinyMCE
	add_editor_style('/library/css/editor.css');

	if ( !isset( $content_width ) ) $content_width = 1000;

}

/**************************************************************
 * Contingut barra superior (admin bar)
 ***************************************************************/

//Sempre visible
show_admin_bar( true );

add_action( 'admin_bar_menu', 'add_logo',1 );
add_action( 'admin_bar_menu', 'add_recursos',2);

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

	$excerpt_more = apply_filters('excerpt_more', ' ');

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

/**
 * Fil d'ariadna
 * @author Xavi Meler
 */
function add_breadcrumbs(){
	reactor_breadcrumbs();
}
add_action ('reactor_content_before','add_breadcrumbs',999);
add_action("reactor_content_before","menu_principal");

// Zona de Ginys per categories
if ( function_exists('register_sidebar') ) {
	register_sidebars( 1,
			array(
					'name'          => __( 'Categories (Barra esquerra)', 'reactor' ),
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

include $theme_root . '/reactor/custom-tac/rss-metabox.php';

add_action('wp_dashboard_setup', 'rss_register_widgets');

function rss_register_widgets() {
	global $wp_meta_boxes;
	wp_add_dashboard_widget('widget_rss_nodes', "Notícies", 'rss_box');
}

// Tauler personalitzat
include $theme_root . '/reactor/custom-tac/welcome-panel.php';

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
 * @author Xavier Nieto
 *
 */
function bp_options_page() {
	?>
	<div class="wrap">

		<div style="width:180px; height:250px; padding:20px; float:left;">
			<h3 style="height:40px;"><?php _e('BuddyPress', 'buddypress'); ?></h3>
			<p><a href="admin.php?page=bp-activity"><?php _e('Activity', 'buddypress'); ?></a></p>
			<p><a href="admin.php?page=bpfb-settings"><?php _e('Activity Plus', 'bpfb'); ?></a></p>
			<p><a href="admin.php?page=bp-components"><?php _e('Components', 'reactor'); ?></a></p>
			<p><a href="admin.php?page=bp-groups"><?php _e('Groups', 'buddypress'); ?></a></p>
			<p><a href="admin.php?page=bp-like-settings"><?php _e('BuddyPress Like', 'buddypress-like'); ?></a></p>
			<p><a href="tools.php?page=xtec_stats"><?php _e('Logs', 'xtec-stats'); ?></a></p>
			<p><a href="admin.php?page=ass_admin_options"><?php _e('Group Email Options', 'bp-ass'); ?></a></p>
			<p><a href="admin.php?page=bp-settings"><?php _e('Configuració', 'reactor'); ?></a></p>
		</div>

		<div style="width:160px; height:250px; padding:20px; float:left;">
			<h3 style="height:40px;"><?php _e('BuddyPress Docs', 'bp-docs'); ?></h3>
			<p><a href="edit.php?post_type=bp_doc"><?php _e('BuddyPress Docs', 'bp-docs'); ?></a></p>
			<p><a href="post-new.php?post_type=bp_doc"><?php _ex( 'Add New', 'add new', 'bp-docs' ) ?></a></p>
			<p><a href="edit-tags.php?taxonomy=bp_docs_associated_item&post_type=bp_doc"><?php _e('Associated Items', 'bp-docs'); ?></a></p>
			<p><a href="edit-tags.php?taxonomy=bp_docs_tag&post_type=bp_doc"><?php _e('Docs Tags', 'bp-docs' ); ?></a></p>
			<p><a href="edit.php?post_type=bp_doc&page=bp-docs-settings"><?php _e('Settings', 'bp-docs'); ?></a></p>
		</div>

        <div style="width:160px; height:250px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e('BuddyPress Invitations', 'invite-anyone'); ?></h3>
            <p><a href="admin.php?page=invite-anyone"><?php _e('Invite Anyone', 'invite-anyone'); ?></a></p>
            <p><a href="edit.php?post_type=ia_invites"><?php _e('Manage Invitations', 'invite-anyone'); ?></a></p>
            <p><a href="post-new.php?post_type=ia_invites"><?php _e('Add New Invitation', 'invite-anyone'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=ia_invitees&post_type=ia_invites"><?php _e('Invitees', 'invite-anyone'); ?></a></p>
            <p><a href="edit-tags.php?taxonomy=ia_invited_groups&post_type=ia_invites"><?php _e('Invited Groups', 'invite-anyone'); ?></a></p>
        </div>

        <div style="width:160px; height:250px; padding:20px; float:left;">
            <h3 style="height:40px;"><?php _e( 'Reports', 'agora-functions' ); ?></h3>
            <p><a href="edit.php?post_type=xtec_report"><?php _e( 'Activity report in social network', 'agora-functions' ); ?></a></p>
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
 * @author Xavier Nieto
 */
function rebuild_bp_menus_step_1() {

	add_menu_page(__('BuddyPress', 'buddypress'), __('BuddyPress', 'buddypress'), 'manage_options', 'xtec-bp-options', 'bp_options_page', '', 59);

	add_submenu_page('xtec-bp-options', __('Activity', 'buddypress'), __('Activity', 'buddypress'), 'manage_options', 'bp-activity');

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
	remove_submenu_page('options-general.php', 'bp-settings'); // Tab in BuddyPress
	remove_submenu_page('bp-general-settings', 'ass_admin_options'); // Group Email
	remove_submenu_page('options-general.php', 'bp-like-settings'); // BuddyPress Like
	remove_submenu_page('options-general.php', 'slideshare');

	add_submenu_page('xtec-bp-options', __('Components', 'buddypress'), __('Components', 'buddypress'), 'manage_options', 'bp-components', 'bp_core_admin_components_settings');

	add_submenu_page('xtec-bp-options', __('Groups', 'buddypress'), __('Groups', 'buddypress'), 'manage_options', 'bp-groups');
	add_submenu_page('xtec-bp-options', __('BuddyPress Like', 'buddypress-like'), __('BuddyPress Like', 'buddypress-like'), 'manage_options' , 'bp-like-settings' , 'bp_like_admin_page');
	add_submenu_page('xtec-bp-options', __('Group Email Options', 'bp-ass'), __('Group Email Options', 'bp-ass'), 'manage_options', 'ass_admin_options', 'ass_admin_options');

    // Don't load Invite Anyone menu
    remove_action('admin_menu', 'invite_anyone_admin_add', 80);

    // Reproduce the actions in the function previously cancelled
    $plugin_page = add_submenu_page('xtec-bp-options', __('Invite Anyone', 'invite-anyone'), __('Invite Anyone', 'invite-anyone'), 'manage_options', 'invite-anyone', 'invite_anyone_admin_panel');
    add_action("admin_print_scripts-$plugin_page", 'invite_anyone_admin_scripts');
    add_action("admin_print_styles-$plugin_page", 'invite_anyone_admin_styles');

    add_submenu_page('xtec-bp-options', __('Configuració', 'reactor'), __('Configuració', 'reactor'), 'manage_options', 'bp-settings', 'bp_core_admin_settings');

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
		<div style="width:150px; height:160px; padding:20px; float:left;">
			<h3 style="height:30px;"><?php _e('Forums', 'bbpress'); ?></h3>
			<p><a href="edit.php?post_type=forum"><?php _e('All Forums', 'bbpress'); ?></a></p>
			<p><a href="post-new.php?post_type=forum"><?php _e('New Forum', 'bbpress'); ?></a></p>
		</div>

		<div style="width:150px; height:160px; padding:20px; float:left;">
			<h3 style="height:30px;"><?php _e('Topics', 'bbpress'); ?></h3>
			<p><a href="edit.php?post_type=topic"><?php _e('All Topics', 'bbpress'); ?></a></p>
			<p><a href="post-new.php?post_type=topic"><?php _e('New Topic', 'bbpress'); ?></a></p>
			<p><a href="edit-tags.php?taxonomy=topic-tag&post_type=topic"><?php _e('Topic Tags', 'bbpress'); ?></a></p>
		</div>

		<div style="width:150px; height:160px; padding:20px; float:left;">
			<h3 style="height:30px;"><?php _e('Replies', 'bbpress'); ?></h3>
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
        remove_submenu_page('themes.php', 'bp-emails-customizer-redirect'); // Submenu in Appearance

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

// Unregister bp-mail post type to disable functionality
function unregister_bp_mail () {
	unregister_post_type( 'bp-email' );
}
// Fires after WordPress has finished loading but before any headers are sent
add_action('init', 'unregister_bp_mail');

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
 * If external address, open link on new window
 *
 * @author Xavi Meler
 * @param $link
 * @return string with the target property
 */
function set_target($link) {
	$link = str_replace('http://', '://', trim($link));
	$link = str_replace('https://', '://', $link);

	$siteURL = get_home_url();
	$siteURL = str_replace('http://', '://', trim($siteURL));
	$siteURL = str_replace('https://', '://', $siteURL);

	if (!strpos($link, $siteURL)) {
		return '_blank';
	} else {
		return '_self';
    }
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

// Load styles from the parent (first) and the child (second)
function theme_enqueue_styles() {

	$parent_style = 'parent-style';

	wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
	wp_enqueue_style('child-style', get_stylesheet_directory_uri() . '/style.css', array($parent_style));
}

add_action('wp_enqueue_scripts', 'theme_enqueue_styles');
add_action('login_enqueue_scripts', 'theme_enqueue_styles'); // Required for login form

/**
 * Allow mail direction into headers icons.
 * Available to "reactor-primaria-1" and "reactor-serveis-educatius" themes.
 *
 * @author xaviernietosanchez
 * @param $options, $icon_number
 * @return array
 */
function xtec_mail_direction_into_header_icons( $options, $icon_number ){

    $url = parse_url($options['link_icon' . $icon_number]);

    $homeUrl = get_site_url();
    $result = array();

    // Change target link if is the same domain
    if ( strpos( $options['link_icon' . $icon_number], $homeUrl ) !== false ){
        $result['link'] = esc_url($options['link_icon' . $icon_number]);
        $result['target'] = '_self';
    // if the url contains protocol, open in new tab
    }else if ( isset($url['scheme']) && ( ($url['scheme'] == 'https') || ($url['scheme'] == 'http')) ) {
        $result['link'] = esc_url($options['link_icon' . $icon_number]);
        $result['target'] = '_blank';
    } else {
        // Allow include a mail direction instead of a url
        if ( preg_match('/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,4})$/', $options['link_icon' . $icon_number]) ){
            $result['link'] = "mailto:" . sanitize_email($options['link_icon' . $icon_number]);
            $result['target'] = '_self';
        } else {
            // if the url contains dots " . ", open in new tab
            if( strpos($options['link_icon' . $icon_number],'.') !== false ){
                $result['link'] = esc_url('https://' . trim($options['link_icon' . $icon_number]));
                $result['target'] = '_blank';
            } else {
                if ( substr ( trim($options['link_icon' . $icon_number]) , 0 , 1 ) == '/' ){
                    $result['link'] = get_home_url() . trim($options['link_icon' . $icon_number]);
                } else {
                    $result['link'] = get_home_url() . '/' . trim($options['link_icon' . $icon_number]);
                }
                $result['target'] = '_self';
            }
        }
    }

    // return link and target
    return $result;
}
