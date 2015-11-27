<?php

/*
  Plugin Name: AgoraFunctions
  Plugin URI: https://github.com/projectestac/agora_nodes
  Description: A pluggin to include specific functions which affects only to Àgora-Nodes
  Version: 1.0
  Author: Àrea TAC - Departament d'Ensenyament de Catalunya
 */

load_muplugin_textdomain('agora-functions', '/languages');

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
                'name' => __('My Articles', 'agora-functions'),
                'slug' => 'myposts',
                'position' => 100,
                'default_subnav_slug' => 'published',
                'screen_function' => 'mb_author_posts'
            )
    );
}

add_action('bp_setup_nav', 'bp_profile_menu_posts', 301);

/**
 * Build two sub menu items, the first is showing by default
 * @author Nacho Abejaro
 */
function bp_profile_submenu_posts() {
    global $bp;

    $publishCount = get_user_posts_count('publish');
    $pendingCount = get_user_posts_count('pending');
    $draftCount = get_user_posts_count('draft');

    bp_core_new_subnav_item(
            array(
                'name' => __('Published', 'agora-functions') . '<span>' . $publishCount . '</span>',
                'slug' => 'mypublished',
                'parent_url' => bp_displayed_user_domain() . $bp->bp_nav['myposts']['slug'] . '/',
                'parent_slug' => $bp->bp_nav['myposts']['slug'],
                'position' => 10,
                'screen_function' => 'mb_author_posts' // the function is declared below
            )
    );

    bp_core_new_subnav_item(
            array(
                'name' => __('Pending Review', 'agora-functions') . '<span>' . $pendingCount . '</span>',
                'slug' => 'myunder-review',
                'parent_url' => bp_displayed_user_domain() . $bp->bp_nav['myposts']['slug'] . '/',
                'parent_slug' => $bp->bp_nav['myposts']['slug'],
                'position' => 20,
                'screen_function' => 'mb_author_pending' // the function is declared below
            )
    );

    bp_core_new_subnav_item(
            array(
                'name' => __('Drafts', 'agora-functions') . '<span>' . $draftCount . '</span>',
                'slug' => 'mydrafts',
                'parent_url' => bp_displayed_user_domain() . $bp->bp_nav['myposts']['slug'] . '/',
                'parent_slug' => $bp->bp_nav['myposts']['slug'],
                'position' => 30,
                'screen_function' => 'mb_author_drafts' // the function is declared below
            )
    );
}

add_action('bp_setup_nav', 'bp_profile_submenu_posts', 302);

/**
 * Manage the first sub item
 * First function is the screen_function
 * Second function displays the content
 * @author Nacho Abejaro
*/
function mb_author_posts() {
    add_action('bp_template_content', 'mb_show_posts');
    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function mb_show_posts() {
	$user_id = bp_displayed_user_id();
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
    add_action('bp_template_content', 'mb_show_drafts');
    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function mb_show_drafts() {
	$user_id = bp_displayed_user_id();
    $query = "author=$user_id&post_status=draft&orderby=title&order=ASC";
    myTemplate($query);
}

/**
 * Manage the third sub item
 * First function is the screen_function
 * Second function displays the content
 * @author Nacho Abejaro
 */
function mb_author_pending() {
	add_action('bp_template_content', 'mb_show_pending');
	bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
}

function mb_show_pending() {
	$user_id = bp_displayed_user_id();
	$query = "author=$user_id&post_status=pending&orderby=title&order=ASC";
	myTemplate($query);
}

/**
 * Create a template for load author posts
 * @param $query Contains the parameters
 */
function myTemplate($query) {

    // Launch the query
    query_posts($query);

    if (have_posts()) {
        while (have_posts()) {
            the_post();

            $allCategories = array();
            $categories = get_the_category();
            for ($i = 0; $i < count($categories); $i++) {
                $allCategories[] = '<a href="' . get_category_link($categories[$i]->cat_ID) . '" >' . $categories[$i]->cat_name . '</a>';
            }

            echo '<div style="width: 100%; float: left; position: relative; margin-bottom: 5px;">';
            echo '<div style="width: 100%; min-height: 90px; float: left; border-bottom: 2px solid #F2F0F0; background: white;	margin-left: 0px; position: relative;">';
            echo '<div style="max-width: 100px; max-height: 100px; float: left; margin-top: 10px; position: relative; width: 200%;" >';
            echo '<a href="' . get_edit_post_link() . '">';
            the_post_thumbnail(thumbnail);
            echo '</a>';
            echo '</div>';

            echo '<div style="float: left; width: 78%; min-height: 105px; margin-top: 5px; margin-left: 2%; position: relative; margin-bottom: 5px;">';
            echo '<h3 style="font-size: 20px !important; margin-top: 5px !important; margin-bottom: 0.1em !important;">';
            echo '<a href="' . get_edit_post_link() . '">';
            the_title();
            echo '</a>';
            echo '</h3>';

            echo '<span style="float: left; width: 100%; height: 10px; margin-bottom: 5px; color: #A0A5A9; font-size: 11px; font-style: italic; font: 08px "Droid Serif", Georgia, "Times New Roman", Times, serif; text-align: right; ">' . get_the_date() . '</span>';

            echo '<div class="excerpt">';
            echo limit_text(get_the_excerpt(), 30);
            echo '</div>';
            echo '</div>';

            echo '<div id="article-footer">';
            echo '<div style="width: 50%; height: 30px; float: left; margin-bottom: 2px; font-size:11px; line-height: 30px;">';
            echo __('Categories', 'agora-functions') . implode(" | ", $allCategories);
            echo '</div>';

            if (get_comments_number()) {
                echo '<div style="width: 40%; float: right; color: #1fa799 !important;">';
                echo '<span style="float: right; margin: 0 0 0 7px; font-size: 26px; font-weight: bold; line-height: 1; text-shadow: 0 1px 0 white; font-style: italic;">' . get_comments_number() . '</span>';
                echo '<span style="font-size:11px; line-height: 28px; float: right; margin: 0 0 0 7px; font: italic 11px "Droid Serif",Georgia,"Times New Roman",Times,serif;">';
                echo __('Comments', 'agora-functions');
                echo "</span>";
                echo '</div>';
            }
            echo '</div>';

            echo '<div style="clear:both"></div>';

            echo '</div>';
            echo '</div>';
        }
    } else {
        echo __('Article not found', 'agora-functions');
    }

    //Reset Query
    wp_reset_query();
}

/**
 * Return the post number from a user
 * @author Nacho Abejaro
 */
function get_user_posts_count($status) {
    $args = array();
    $args['post_status'] = $status;
    $args['author'] = bp_displayed_user_id();
    $args['fields'] = 'ids';
    $args['posts_per_page'] = "-1";
    $args['post_type'] = 'post';
    $ps = get_posts($args);
    return count($ps);
}

/**
 * Limits the text
 * @author Nacho Abejaro
 */
function limit_text($text, $limitwrd) {
    if (str_word_count($text) > $limitwrd) {
        $words = str_word_count($text, 2);
        if ($words > $limitwrd) {
            $pos = array_keys($words);
            $text = substr($text, 0, $pos[$limitwrd]) . ' [...]';
        }
    }
    return $text;
}

/**
 * If user is not logged in, redirects at login screen on BuddyPress tabs
 * @author Nacho Abejaro
 */
function members_logged_in_only() {
    $uid = get_current_user_id();

    if ($uid == 0) {
        //Instead of using wp_redirect, echo location using meta
        $location = home_url() . "/wp-login.php";
        echo "<meta http-equiv='refresh' content='0;url=$location'/>";
    }
}

add_filter('bp_before_member_home_content', 'members_logged_in_only');

/**
 * Disable gravatar.com calls on buddypress.
 * @author Víctor Saavedra (vsaavedr@xtec.cat)
 */
add_filter( 'bp_core_fetch_avatar_no_grav', '__return_true' );

/**
 * Remove screen options from posts to simplify user experience
 * @author Sara Arjona
 */
function agora_remove_post_meta_boxes() {
    remove_meta_box('formatdiv', 'post', 'normal');
    remove_meta_box('formatdiv', 'post', 'side');
}

add_action('do_meta_boxes', 'agora_remove_post_meta_boxes');

/**
 * Control disk percent usage when upload a file
 * @author Nacho Abejaro
 */
add_action('wp_handle_upload', 'quota_control');
function quota_control($results) {
	if (isset($GLOBALS['diskPercentNodes'])&&($GLOBALS['diskPercentNodes'] >= 100)){
		$file['error'] = __('You have exceeded your disk quota limit', 'agora-functions');
		return $file;
	}else {
		return $results;
	}
}

/**
 * Call action remove_old_stats when the cron to remove old stats has been launched
 * @author Nacho Abejaro
 */
add_action('remove_stats', 'remove_old_stats');

/**
 *  Avoid delete this pages: Activitat, Membres, Nodes and Initial Page
 * @param unknown $post_ID
 */
function restrict_post_deletion($post_ID){

	$pagesList = array("Membres", "Pàgines d'inici", "Activitat", "Nodes");
	$restricted_pages = array();

	if (!is_xtec_super_admin()) {
		foreach ($pagesList as $pageTitle){
			$page = get_page_by_title($pageTitle);
			if ($page->ID) {
				array_push($restricted_pages, $page->ID);
			}
		}

		if (in_array($post_ID, $restricted_pages)) {
			$msg = __('The page you were trying to delete is protected.', 'agora-functions');
			wp_die($msg);
		}
	}

}
add_action('wp_trash_post', 'restrict_post_deletion');
add_action('before_delete_post', 'restrict_post_deletion');

/**
 * Prevents the creation of restricted pages
 * @author Nacho Abejaro
 */

function force_post_title_init() {
	wp_enqueue_script('jquery');
}

function force_post_title() {
	$msgError = __('The page name you were trying to create is protected.', 'agora-functions');
	$msgNull = __('Page name is required.', 'agora-functions');
	echo "<script type='text/javascript'>\n";
	echo "
    jQuery('#publish').click(function(){
		var title = jQuery('[id^=\"titlediv\"]').find('#title');
		if (title.val() != '') {
			var blackPagesList = ['moodle', 'moodle2', 'intranet'];

			if (jQuery.inArray(title.val().toLowerCase(), blackPagesList) !== -1) {
				alert ('".$msgError."');
				return false;
			}else {
				// Title ok, do nothing
				return true;
			}
        }else {
			alert ('".$msgNull."');
			return false;
		}
    });
  ";
	echo "</script>\n";
}

add_action('edit_form_advanced', 'force_post_title');
add_action('edit_page_form', 'force_post_title');

/**
 * To avoid error uploading files from HTTP pages
 * @return string Create forums URL always with HTTPS
 * @author Sara Arjona
 */
function bbp_get_forum_permalink_filter($permalink) {
	
	$url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	
	if ( strpos($url,'forum') !== false) {
		return preg_replace('/^http:/i', 'https:', $permalink);
	}else {
		return preg_replace('/^https:/i', 'http:', $permalink);
	}
}

add_filter('bbp_get_forum_permalink', 'bbp_get_forum_permalink_filter');
add_filter('bbp_get_topic_permalink', 'bbp_get_forum_permalink_filter');
add_filter('bbp_get_reply_permalink', 'bbp_get_forum_permalink_filter');
add_filter('bbp_get_topic_stick_link', 'bbp_get_forum_permalink_filter');
add_filter('bp_get_group_permalink', 'bbp_get_forum_permalink_filter');

/**
 * Disable Add_To_Any Module widgets if user is not xtecadmin
 * @author Nacho Abejaro 
 */
function unregister_AddToAny_widgets() {
	if (!is_xtec_super_admin()) {
		unregister_widget( 'A2A_SHARE_SAVE_Widget' );
		unregister_widget( 'A2A_Follow_Widget' );
	}
}
add_action('widgets_init', 'unregister_AddToAny_widgets', 11);

/**
 * Exclude URL access to bbpress module options
 * @author Nacho Abejaro
 */
function remove_general_bbpress_options(){

	$restrictedPage = 'options-general.php?page=bbpress';

	// Get current URL
	$pageURL = 'http';
	if ($_SERVER["HTTPS"] == "on") {
		$pageURL .= "s";
	}

	$pageURL .= "://". $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];

	// Check if url contains the restricted page
	$pos = strpos($pageURL, $restrictedPage);

	if ( !is_xtec_super_admin() && ($pos !== false) ) {
		wp_die(__('You do not have permission to do that.'));
	}
}

add_action('parse_query', 'remove_general_bbpress_options');

/**
 * Adedd new extrafields for categories (images and articles_fila)
 * This function replaced the old version located on functions.php of the themes
 * 2015.12.04 @author Xavier Meler & Nacho Abejaro
 */
function extra_category_fields( $tag ) {    //check for existing featured ID
    $t_id = $tag->term_id;
    $cat_meta = get_option( "category_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="articles_fila"><?php _e( 'Articles by row', 'agora-functions' ); ?></label></th>
        <td>
            <input type="text" name="Cat_meta[articles_fila]" id="Cat_meta[articles_fila]" size="25" style="width:60%;" value="<?php echo $cat_meta['articles_fila'] ? $cat_meta['articles_fila'] : ''; ?>"><br />
            <p class="description"><?php _e('Articles to be displayed per row on category page (between 1 and 4)', 'agora-functions'); ?></p>
        </td>
    </tr>

    <tr class="form-field form-required term-name-wrap">
        <th scope="row" valign="top">
            <label for="term_meta[term_meta]">
                <?php _e( 'Image', 'agora-functions' ); ?>
            </label>
        </th>
        <!-- Extra field to load an image into category -->
        <td>
            <label for="upload_image">
                <input id="upload_image_button" class="button" type="button" value="<?php _e( 'Upload image', 'agora-functions' );?>" />
                <input type="text" size="50" name="Cat_meta[image]" id="Cat_meta[image]" value="<?php echo esc_attr( $cat_meta['image'] ) ? esc_attr( $cat_meta['image'] ) : ''; ?>" readonly>
                <p class="description"><?php _e( 'Provide an image for the category', 'agora-functions' ); ?></p>
            </label>
            <br/>
            <?php
            if (!empty($cat_meta['image'])) {
                ?>
                <input type=image src="<?php echo $cat_meta['image'];?>" height=110 width=160 alt="actual image" border=0 >
                <p class="description"><?php _e( 'Current header image', 'agora-functions' ); ?></p>
                <?php
            }
            ?>
        </td>

        <!-- Open the WP gallery and insert the URL image into text button (term_meta)-->
        <script>
            jQuery(document).ready(function($){
                var custom_uploader;
                $('#upload_image_button').click(function(e) {
                    e.preventDefault();
                    //If the uploader object has already been created, reopen the dialog
                    if (custom_uploader) {
                        custom_uploader.open();
                        return;
                    }
                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media({
                        title: 'Selecciona una imatge',
                        button: {
                            text: 'Selecciona una imatge'
                        },
                        multiple: false
                    });

                    //When a file is selected, grab the URL and set it as the text field's value
                    custom_uploader.on('select', function() {
                        console.log(custom_uploader.state().get('selection').toJSON());
                        attachment = custom_uploader.state().get('selection').first().toJSON();
                        $('#upload_image').val(attachment.url);

                        // Save img URL into input Cat_meta[image] field
                        $("#Cat_meta\\[image\\]").val(attachment.url);
                    });

                    //Open the uploader dialog
                    custom_uploader.open();
                });
            });
        </script>
    </tr>
<?php }

add_action ( 'edit_category_form_fields', 'extra_category_fields');

/**
 * Save category extra fields callback function
 * This function replaced the old version located on functions.php of the themes
 * 2015.12.04 @author Xavier Meler & Nacho Abejaro
 */
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

/**
 * Gets image field from a category
 * 2015.12.04 @author Nacho Abejaro
 */
function get_category_image (){
    $categoria = get_query_var('cat');
    $cat_meta = get_option("category_$categoria");
    $image = $cat_meta ['image'];

    return $image;
}