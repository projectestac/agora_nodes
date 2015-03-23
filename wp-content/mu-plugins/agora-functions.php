<?php
/*
Plugin Name: AgoraFunctions
Plugin URI: https://github.com/projectestac/agora_nodes
Description: A pluggin to include specific functions which affects only to Àgora-Nodes
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/



/**
 * To avoid error uploading files from HTTP pages
 * @param  string $url create docs URL
 * @return string Create docs URL always with HTTPS
 * @author Sara Arjona
 */
function bp_docs_get_create_link_filter($url) {
	return preg_replace('/^http:/i', 'https:', $url);
}
add_filter('bp_docs_get_create_link', 'bp_docs_get_create_link_filter');

/**
 * Build a navigation link and add it to the profile main menu
 * @author Nacho Abejaro
 */
function bp_profile_menu_posts() {
	global $bp;

	bp_core_new_nav_item(
		array(
			'name' => 'Els meus articles',
			'slug' => 'posts',
			'position' => 100,
			'default_subnav_slug' => 'published',
			'screen_function' => 'mb_author_posts'
		)
	);
}
add_action('bp_setup_nav', 'bp_profile_menu_posts', 301 );

/**
 * Build two sub menu items, the first is showing by default
 * @author Nacho Abejaro
*/
function bp_profile_submenu_posts() {
	global $bp;
	if(!is_user_logged_in()) return '';

	bp_core_new_subnav_item(
		array(
			'name' => 'Publicats',
			'slug' => 'published',
			'parent_url' => $bp->loggedin_user->domain . $bp->bp_nav['posts']['slug'] . '/' ,
			'parent_slug' => $bp->bp_nav['posts']['slug'],
			'position' => 10,
			'screen_function' => 'mb_author_posts' // the function is declared below
		)
	);

	bp_core_new_subnav_item(
		array(
			'name' => 'Esborranys',
			'slug' => 'drafts',
			'parent_url' => $bp->loggedin_user->domain . $bp->bp_nav['posts']['slug'] . '/' ,
			'parent_slug' => $bp->bp_nav['posts']['slug'],
			'position' => 20,
			'screen_function' => 'mb_author_drafts' // the function is declared below
		)
	);
}
add_action('bp_setup_nav', 'bp_profile_submenu_posts', 302 );

/**
 * Manage the first sub item
 * First function is the screen_function
 * Second function displays the content
 * @author Nacho Abejaro
*/
function mb_author_posts(){
	add_action( 'bp_template_content', 'mb_show_posts' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function mb_show_posts() {
	$user_id = bp_displayed_user_id();
	if (!$user_id) {
		return '';
	}
	$query = "author=$user_id&orderby=title&order=ASC";
	myTemplate($query);
}

/**
 * Manage the second sub item
 * First function is the screen_function
 * Second function displays the content
 * @author Nacho Abejaro
 */
function mb_author_drafts() {
	add_action( 'bp_template_content', 'mb_show_drafts' );
	bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
}

function mb_show_drafts() {
	$user_id = bp_displayed_user_id();
	if (!$user_id) {
		return '';
	}
	$query = "author=$user_id&post_status=draft&orderby=title&order=ASC";
	myTemplate($query);
}

/**
 * Create a template for load author posts
 * @param $query Contains the parameters
 */
function myTemplate($query) {

	// Launch the query
	query_posts($query);

	if ( have_posts() ) : while ( have_posts() ) : the_post();
		echo '<div class="activity" role="main">';
			echo '<ul id="activity-stream" class="activity-list item-list">';
				echo '<li class="groups bp_doc_created activity-item mini">';
					echo '<div class="bp-widget base">';
						echo '<table border="0" cellspacing="0" cellpadding="0">';
							echo '<tr>';
								echo '<td>';
									echo '<a href="' . get_edit_post_link() . '">';
									the_post_thumbnail(thumbnail);
									echo '</a>';
								echo '</td>';

								echo '<td>';
									echo '<h2 class="title">';
									echo '<a href="' . get_edit_post_link() . '">';
									the_title();
									echo '</a>';
									echo '</h3>';

									echo '<p>' .get_the_author(). ' el ' ;
									echo '<span class="date">' . get_the_date() . '</span>';

									echo '<div class="excerpt">';
									the_excerpt();
									echo '</div>';
								echo '</td>';
							echo '</tr>';
						echo '</table>';
					echo '</div>';

				echo '</li>';
			echo '</ul>';
		echo '</div>';
	endwhile;

	else:
		echo "Cap publicació que mostrar";
	endif;

	//Reset Query
	wp_reset_query();
}

/**
 * Disable gravatar.com calls on buddypress.
 * @author Víctor Saavedra (vsaavedr@xtec.cat)
 */
add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );