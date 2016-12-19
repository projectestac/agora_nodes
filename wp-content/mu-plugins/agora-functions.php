<?php
/**
 * Plugin Name: AgoraFunctions
 * Plugin URI: https://github.com/projectestac/agora_nodes
 * Description: Additional functions to customize Àgora-Nodes
 * Version: 1.0
 * Author: Àrea TAC - Departament d'Ensenyament de Catalunya
 */

load_muplugin_textdomain('agora-functions', '/languages');

/**
 * Build a navigation link and add it to the profile main menu
 * @author Nacho Abejaro
 */
function bp_profile_menu_posts() {
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
    $publishCount = get_user_posts_count('publish');
    $pendingCount = get_user_posts_count('pending');
    $draftCount = get_user_posts_count('draft');
    $parent_slug = 'myposts';

    bp_core_new_subnav_item(
            array(
                'name' => __('Published', 'agora-functions') . '<span>' . $publishCount . '</span>',
                'slug' => 'mypublished',
                'parent_url' => bp_displayed_user_domain() . $parent_slug . '/',
                'parent_slug' => $parent_slug,
                'position' => 10,
                'screen_function' => 'mb_author_posts' // the function is declared below
            )
    );

    bp_core_new_subnav_item(
            array(
                'name' => __('Pending Review', 'agora-functions') . '<span>' . $pendingCount . '</span>',
                'slug' => 'myunder-review',
                'parent_url' => bp_displayed_user_domain() . $parent_slug . '/',
                'parent_slug' => $parent_slug,
                'position' => 20,
                'screen_function' => 'mb_author_pending' // the function is declared below
            )
    );

    bp_core_new_subnav_item(
            array(
                'name' => __('Drafts', 'agora-functions') . '<span>' . $draftCount . '</span>',
                'slug' => 'mydrafts',
                'parent_url' => bp_displayed_user_domain() . $parent_slug . '/',
                'parent_slug' => $parent_slug,
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
            the_post_thumbnail('thumbnail');
            echo '</a>';
            echo '</div>';

            echo '<div style="float: left; width: 78%; min-height: 105px; margin-top: 5px; margin-left: 2%; position: relative; margin-bottom: 5px;">';
            echo '<h3 style="font-size: 20px !important; margin-top: 5px !important; margin-bottom: 0.1em !important;">';
            echo '<a href="' . get_edit_post_link() . '">';
            the_title();
            echo '</a>';
            echo '</h3>';

            echo '<span style="width:100%; height:10px; margin-bottom:7px; color:#A0A5A9; font-size:11px; font-style:italic;">' . get_the_date() . '</span>';

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
                echo '<span style="font-size:11px; line-height:28px; float:right; margin:0 0 0 7px; font-style:italic;">';
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
 * @param int $post_id Post ID.
 */
function restrict_post_deletion($post_ID){
	$pagesList = array("Membres", "Pàgines d'inici", "Activitat", "Nodes");
	$restricted_pages = array();

    if (get_option('page_on_front')) {
        // Avoid delete page_on_front because frontpage is not shown if it doesnt exist
        array_push($restricted_pages, get_option('page_on_front'));
    }
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
 * @return string Create forums and docs URL always with HTTPS
 * @author Sara Arjona
 * @author Xavier Nieto
 */
function bbp_get_forum_doc_permalink_filter($permalink) {
	
	$url = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
	
	if ( strpos($url,'forum') !== false || strpos($url,'docs') !== false ) {
		return preg_replace('/^http:/i', 'https:', $permalink);
	}else {
		return preg_replace('/^https:/i', 'http:', $permalink);
	}
}

add_filter('bbp_get_forum_permalink', 'bbp_get_forum_doc_permalink_filter');
add_filter('bbp_get_topic_permalink', 'bbp_get_forum_doc_permalink_filter');
add_filter('bbp_get_reply_permalink', 'bbp_get_forum_doc_permalink_filter');
add_filter('bbp_get_topic_stick_link', 'bbp_get_forum_doc_permalink_filter');
add_filter('bp_get_group_permalink', 'bbp_get_forum_doc_permalink_filter');
add_filter('bp_docs_get_doc_permalink', 'bbp_get_forum_doc_permalink_filter');
add_filter('bp_docs_get_tag_link_url', 'bbp_get_forum_doc_permalink_filter');
add_filter('bp_docs_get_doc_link', 'bbp_get_forum_doc_permalink_filter');

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
	if ( array_key_exists('HTTPS', $_SERVER) && $_SERVER['HTTPS'] == 'on' ) {
		$pageURL .= 's';
	}

	$pageURL .= '://'. $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];

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
            <?php
            $exists_current_cat_image = !empty($cat_meta['image']);
            ?>
            <input type="hidden" size="50" name="Cat_meta[image]" id="Cat_meta[image]" value="<?php echo esc_attr($cat_meta['image']); ?>" readonly>
            <!-- Only display current image if defined -->
            <div id="current_cat_image_layer" style="<?php echo $exists_current_cat_image?'display: block; visibility: visible;':'display: none; visibility: hidden;'; ?>">
                <input type="image" id="current_cat_image" src="<?php echo $cat_meta['image'];?>" height=110 alt="actual image" border=0 >
                <p class="description"><?php _e( 'Current header image', 'agora-functions' ); ?></p>
            </div>
            <br/>
            <!-- Only display upload image button if there is no image -->
            <label id="upload_cat_image_label" for="upload_image" style="<?php echo !$exists_current_cat_image?'display: block; visibility: visible;':'display: none; visibility: hidden;'; ?>" >
                <input id="upload_image_button" class="button" type="button" value="<?php _e( 'Upload image', 'agora-functions' );?>" />
                <p class="description"><?php _e( 'Provide an image for the category', 'agora-functions' ); ?></p>
            </label>
            <!-- Only display remove image button if exists -->
            <input id="remove_image_button" class="button" type="button" value="<?php _e( 'Remove image', 'agora-functions' );?>"  style="<?php echo $exists_current_cat_image?'display: block; visibility: visible;':'display: none; visibility: hidden;'; ?>" />
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
                        $("#current_cat_image").attr('src', attachment.url);
                        $("#current_cat_image_layer").attr('style', 'display:block; visibility:visible;');
                        $("#upload_cat_image_label").attr('style', 'display:none; visibility:hidden;');
                        $("#remove_image_button").attr('style', 'display:block; visibility:visible;');
                    });

                    //Open the uploader dialog
                    custom_uploader.open();
                });

                $('#remove_image_button').click(function(e) {
                     $("#Cat_meta\\[image\\]").val('');
                     $("#current_cat_image_layer").attr('style', 'display:none; visibility:hidden;');
                     $("#remove_image_button").attr('style', 'display:none; visibility:hidden;');
                     $("#upload_cat_image_label").attr('style', 'display:block; visibility:visible;');
                     $("#current_cat_image").attr('src', '');
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

/**
 * Get pages available from email-subscribers plugin
 * @author David Gras
 */
function get_submenu_items_emails_subscribers() {
    return array(
        0 => array('name' => __("Subscribers", 'email-subscribers'), 'link' => "admin.php?page=es-view-subscribers", "page" => "es-view-subscribers"),
        1 => array('name' => __("Compose", 'email-subscribers'), 'link' => "admin.php?page=es-compose", "page" => "es-compose"),
        2 => array('name' => __("Send Email", 'email-subscribers'), 'link' => "admin.php?page=es-sendemail", "page" => "es-sendemail"),
        3 => array('name' => __("Notification", 'email-subscribers'), 'link' => "admin.php?page=es-notification", "page" => "es-notification"),
        4 => array('name' => __("Cron Mail", 'email-subscribers'), 'link' => "admin.php?page=es-cron", "page" => "es-cron"),
        5 => array('name' => __("Settings", 'email-subscribers'), 'link' => "admin.php?page=es-settings", "page" => "es-settings"),
        6 => array('name' => __("Roles", 'email-subscribers'), 'link' => "admin.php?page=es-roles", "page" => "es-roles"),
        7 => array('name' => __("Sent Mails", 'email-subscribers'), 'link' => "admin.php?page=es-sentmail", "page" => "es-sentmail"),
        8 => array('name' => __("Help & Info", 'email-subscribers'), 'link' => "admin.php?page=es-general-information", "target" => "_blank", "page" => "es-general-information"),
    );
}

/**
 * Get pages available from email-subscribers plugin by user role
 * @author David Gras
 */
function get_submenu_items_emails_subscribers_by_role()
{
    $pages = get_submenu_items_emails_subscribers();

    if (!is_xtec_super_admin()) {

        $pages_restricted = array(4, 5, 6);

        foreach ($pages_restricted as $pages_restricted) {
            if (isset($pages[$pages_restricted])) {
                unset($pages[$pages_restricted]);
            }
        }

        $pages[8]['link'] = 'http://agora.xtec.cat/moodle/moodle/mod/glossary/view.php?id=1741&mode=entry&hook=2501';
    }

    return $pages;
}

/**
 * Removes pages available from email-subscribers plugin by user role
 * @author David Gras
 */
function remove_submenu_items_emails_subscribers_by_role()
{
    if (!is_xtec_super_admin()) {

        $pages = get_submenu_items_emails_subscribers();
        $pages_restricted = array(4, 5, 6);

        foreach ($pages_restricted as $pages_restricted) {
            if (isset($pages[$pages_restricted])) {
                remove_submenu_page('email-subscribers', $pages[$pages_restricted]['page']);
            }
        }
    }
}

/**
 * Render available pages from email-subscribers plugin by role
 * @author David Gras
 */
function email_subscribers_options_page()
{
    $pagesEmailSubscribers = get_submenu_items_emails_subscribers_by_role();
    ?>
    <div class="wrap">
        <div style="width:150px; padding:20px; float:left;">
            <h3><?php _e('Options', 'email-subscribers'); ?></h3>
            <?php foreach ($pagesEmailSubscribers as $pageEmailSubscribers) : ?>
                <p>
                    <a href="<?php echo $pageEmailSubscribers['link'] ?>"
                        <?php echo isset($pageEmailSubscribers['target']) ? "target=" . $pageEmailSubscribers['target'] : ''; ?>
                        >
                        <?php echo __($pageEmailSubscribers['name'], 'email-subscribers'); ?>
                    </a>
                </p>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}

/**
 * Build email_subscribers custom menu
 * @author David Gras
 */
add_action('admin_menu', 'rebuild_email_subscribers_menus', 900);
function rebuild_email_subscribers_menus()
{
    global $submenu;

    if (isset($submenu['email-subscribers'])) {
        add_options_page(__('Email subscribers', 'email-subscribers'), __('Email subscribers', 'email-subscribers'), 'manage_options', 'email-subscribers', 'email_subscribers_options_page', '', 10);
        remove_menu_page('email-subscribers');
        remove_submenu_items_emails_subscribers_by_role();
    }
}

/**
 * Displays the option-general menu when you are browsing a page of  email-subscribers  plugin
 * @author David Gras
 */

add_action('contextual_help', 'check_page_from_emails_subscribers_available_by_role', 999);
function check_page_from_emails_subscribers_available_by_role()
{

    if (has_page_from_emails_subscribers_available_by_role()) {
        echo "<script type='text/javascript'>\n";
        echo "
                jQuery(function() {
                      var menuSeetings = jQuery('#menu-settings'),
                      emaiSubscribers = menuSeetings.find( \"a[href='options-general.php?page=email-subscribers']\"),
                      itemEmaiSubscribers = emaiSubscribers.parent('li');

                      menuSeetings.removeAttr('class');
                      menuSeetings.addClass('wp-has-submenu wp-has-current-submenu wp-menu-open menu-top menu-icon-settings menu-top-last menu-top-last menu-top-last');
                      itemEmaiSubscribers.addClass('current');
                });
            ";
        echo "</script>\n";
    }
}

/**
 * Returns if there is a page from plugin emails subscribers and it is available by role
 * @author David Gras
 */
function has_page_from_emails_subscribers_available_by_role()
{
    global $submenu;
    $is_page_from_emails_subscribers = false;

    if (isset($submenu['email-subscribers'])) {
        $current_Screen = get_current_screen();
        $pos = strpos($current_Screen ->id, '_page_');

        if ($pos !== false) {
            $page = substr($current_Screen ->id, $pos +  strlen('_page_'));
            $pagesEmailSubscribers = get_submenu_items_emails_subscribers();

            foreach ($pagesEmailSubscribers as $pageEmailSubscribers) {
                if (strcmp($pageEmailSubscribers['page'], $page) === 0) {
                    $is_page_from_emails_subscribers = true;
                }
            }
        }
    }

    return $is_page_from_emails_subscribers;
}

/**
 * To change accept link (because register page is disabled)
 * @param  string $accept_link accept URL
 * @return string Replaced accept_link for groups link
 * @author Sara Arjona
 */
function change_invite_anyone_accept_url($accept_link) {
    if (! get_option('users_can_register')){
        return site_url('wp-login.php').'?redirect_to='.bp_get_root_domain(). '/' .bp_get_groups_root_slug(). '/';
    }
    return $accept_link;
}
add_filter('invite_anyone_accept_url', 'change_invite_anyone_accept_url');

/**
 * To remove out link (because register page is disabled)
 * @param  string $msg Message with the text and the link
 * @return string New message text to replace the original one
 * @author Sara Arjona
 */
function remove_invite_anyone_opt_out_footer_message($msg) {
    if (! get_option('users_can_register')){
        return '';
    }
    return $msg;
}
add_filter('invite_anyone_opt_out_footer_message', 'remove_invite_anyone_opt_out_footer_message');

/**
 * Fixed upload of images in bbpress using fancy upload when user is not admin nor mod.
 * Taken from: https://bbpress.org/forums/topic/solved-only-keymasters-admin-can-upload-images/
 * @author: aginard
 */
function ml_pre_media_buttons($editor_id) {
    if (!$editor_id == 'bbp_reply_content') {
        return;
    }
    $GLOBALS['post_temp'] = $GLOBALS['post'];
    $GLOBALS['post'] = null;
}

function ml_post_media_buttons($editor_id) {
    if (!$editor_id == 'bbp_reply_content') {
        return;
    }
    $GLOBALS['post'] = $GLOBALS['post_temp'];
}

add_action('media_buttons', 'ml_pre_media_buttons', 1);
add_action('media_buttons', 'ml_post_media_buttons', 20);

/**
 * Replace template if current page is frontpage
 * @param  string $template Path to the current template
 * @return string Template's path
 * @author Sara Arjona
 */
function change_template_for_frontpage($template) {
    if (get_the_ID() == get_option('page_on_front') && strrpos($template, 'front-page') === false) {
        $template = get_template_directory().'/page-templates/front-page.php';
    }
    return $template;
}
add_filter('page_template', 'change_template_for_frontpage');

/**
 * Modify the list of roles in widget visibility
 *
 * @author Toni Ginard
 */
function translate_roles () {
    global $wp_roles;
    $allowed_roles = array ('Administrator', 'Editor', 'Author', 'Contributor', 'Subscriber' );

    foreach ($wp_roles->roles as $role_key => $role) {
        if (in_array($wp_roles->roles[ $role_key ]['name'], $allowed_roles)) {
            $wp_roles->roles[ $role_key ]['name'] = translate_user_role( $wp_roles->roles[ $role_key ]['name'] );
        } else {
            unset($wp_roles->roles[ $role_key ]);
        }
    }

    return ;
}
add_action( 'widget_visibility_roles', 'translate_roles' );

/**
 * Suggest labels to buddypress-docs
 *
 * @author Xavi Nieto
 */
function suggest_label() {
    $term = '%'.strtolower( $_GET['term'] ).'%';
    $suggestions = [];

    global $wpdb;
    $results = $wpdb->get_results($wpdb->prepare("SELECT name, slug FROM wp_terms INNER JOIN wp_term_taxonomy ON wp_terms.term_id = wp_term_taxonomy.term_id  WHERE  name LIKE '%s' AND wp_term_taxonomy.taxonomy = 'bp_docs_tag'",$term));

    foreach( $results as $result ){
        $suggestions[] = $result->name;
    }

    $response = json_encode( $suggestions );
    echo $response;
    exit();
}

add_action( 'wp_ajax_suggest_label', 'suggest_label' );
add_action( 'wp_ajax_nopriv_suggest_label', 'suggest_label' );

/**
 * New setting option to disable/enabled post to home page into buddypress
 * @author Xavi Nieto
 */

/**
 * Your setting main function
 */
function bp_plugin_admin_settings() {

    /* This is how you add a new section to BuddyPress settings */
    add_settings_section(
        /* the id of your new section */
        'bd_admin_node_setting',

        /* the title of your section */
        __( 'Home page configuration',  'agora-functions' ),

        /* the display function for your section's description */
        'bp_plugin_setting_callback_section',

        /* BuddyPress settings */
        'buddypress'
    );

    /* This is how you add a new field to your plugin's section */
    add_settings_field(
        /* the option name you want to use for your plugin */
        'bp-plugin-enabled-post-home',

        /* The title for your setting */
        __( 'Posts to home page', 'agora-functions' ),

        /* Display function */
        'bp_plugin_setting_field_callback',

        /* BuddyPress settings */
        'buddypress',

        /* Your plugin's section id */
        'bd_admin_node_setting'
    );

    /*
       This is where you add your setting to BuddyPress ones
       Here you are directly using intval as your validation function
    */
    register_setting(
        /* BuddyPress settings */
        'buddypress',

        /* the option name you want to use for your plugin */
        'bp-plugin-enabled-post-home',

        /* the validatation function you use before saving your option to the database */
        'intval'
    );

}

/**
 * You need to hook bp_register_admin_settings to register your settings
 */
add_action( 'bp_register_admin_settings', 'bp_plugin_admin_settings' );

/**
 * This is the display function for your section's description
 */
function bp_plugin_setting_callback_section() {}

/**
 * This is the display function for your field
 */
function bp_plugin_setting_field_callback() {
    /* if you use bp_get_option(), then you are sure to get the option for the blog BuddyPress is activated on */
    $bp_plugin_option_value = bp_get_option( 'bp-plugin-enabled-post-home' );
    if( ! is_numeric($bp_plugin_option_value ) ){
        bp_update_option( 'bp-plugin-enabled-post-home', '1' );
        $bp_plugin_option_value = bp_get_option( 'bp-plugin-enabled-post-home' );
    }
    ?>
    <input id="bp-plugin-enabled-post-home" name="bp-plugin-enabled-post-home" type="checkbox" value="1" <?php checked( $bp_plugin_option_value ); ?> />
    <label for="bp-plugin-enabled-post-home"><?php _e( 'Allow to registry users direct post into home page', 'agora-functions' ); ?></label>
    <?php
}

/**
 * Add custom role: 'xtec_teacher'.
 * @author Xavi Nieto
 */
function xtec_booking_add_xtec_teacher_role(){

    global $wpdb;

    $roleTeacher = get_role('xtec_teacher');
    if ( is_null($roleTeacher) ){

        $result = add_role(
            'xtec_teacher',
            __( 'Teacher', 'agora-functions' ),
            array(
                'edit_posts'                => true,
                'read'                      => true,
                'level_1'                   => true,
                'level_0'                   => true,
                'delete_posts'              => true,
                'upload_files'              => true,
                'edit_published_posts'      => true,
                'delete_published_posts'    => true,
                'delete_pages'              => true,
                'delete_pages_bookings'     => true,
                'edit_posts_bookings'       => true,
                'delete_posts_bookings'     => true,
                'publish_posts_bookings'    => true,
            )
        );

        $node = get_option('xtec_principal_node');
        $not_selected = __("Select name to teacher's node",'agora-functions');

        if ( $node != $not_selected ){
            $results = $wpdb->get_results('SELECT * FROM wp_bp_groups_members as WM, wp_bp_groups as WG WHERE  WM.group_id = WG.id and WM.is_confirmed = 1 and WG.name = "'.$node.'"');
        } else {
            $results = $wpdb->get_results('SELECT * FROM wp_bp_groups_members as WM, wp_bp_groups as WG WHERE  WM.group_id = WG.id and WM.is_confirmed = 1 and ( WG.name = "Mestres" or WG.name = "Professorat" )');
        }

        /**
         * Assign role to users can acces 'Mestres' or 'Professorat'
         */
        foreach ($results as $result) {
            $user = new WP_User($result->user_id);

            if ( in_array( 'contributor', (array) $user->roles ) ){
                $user->remove_role('contributor');
                $user->add_role('xtec_teacher');
            }
        }
    }
}
add_action('admin_menu','xtec_booking_add_xtec_teacher_role');

/**
 * Add teacher's role to users when access node
 * @author Xavi Nieto
 */
function xtec_groups_join_group( $membership_user_id, $membership_group_id, $true ) {

    global $wpdb;

    $group_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM wp_bp_groups WHERE id = %d", $membership_group_id) );

    $user = new WP_User($membership_user_id);
    $node = get_option('xtec_principal_node');
    $not_selected = __("Select name to teacher's node",'agora-functions');

    if( in_array('contributor',(array) $user->roles) && ( $group_name == $node ) ){

        $user->remove_role('contributor');
        $user->add_role('xtec_teacher');

    } else if( $node == $not_selected || $node == false ){

        if( in_array('contributor',(array) $user->roles) && ( $group_name == 'Mestres' || $group_name == 'Professorat' ) ) {
            $user->remove_role('contributor');
            $user->add_role('xtec_teacher');
        }
    }

};
add_action( 'groups_membership_accepted', 'xtec_groups_join_group', 10, 3);

/**
 * Remove teacher's role to users when leave node's teacher
 * @author Xavi Nieto
 */
function xtec_member_leave_group( $group_id, $user_id ) {

    global $wpdb;

    $group_name = $wpdb->get_var( $wpdb->prepare( "SELECT name FROM wp_bp_groups WHERE id = %d", $group_id) );

    $user = new WP_User($user_id);
    $node = get_option('xtec_principal_node');
    $not_selected = __("Select name to teacher's node",'agora-functions');

    if( in_array('xtec_teacher',(array) $user->roles) && ( $group_name == $node ) ){

        $user->remove_role('xtec_teacher');
        $user->add_role('contributor');

    } else if( $node == $not_selected || $node == false ){

        if( in_array('xtec_teacher',(array) $user->roles) && ( $group_name == 'Mestres' || $group_name == 'Professorat' ) ) {
            $user->remove_role('xtec_teacher');
            $user->add_role('contributor');
        }

    }

};
add_action( 'groups_remove_member', 'xtec_member_leave_group', 10, 2);
add_action( 'groups_leave_group', 'xtec_member_leave_group', 10, 2);

/**
 * Add parameter to change name teacher's Node.
 * The teacher's node is necessary to add or remove role teacher's to users.
 * @author Xavi Nieto
 */
function xtec_booking_recovery_name(){
    global $wpdb;

    $nodes = $wpdb->get_results("SELECT * FROM wp_bp_groups WHERE status = 'private' ORDER BY name DESC");
    $option = get_option('xtec_principal_node');

    $html = '<select id="xtec_principal_node" name="xtec_principal_node">';
    $html .= '<option val="-1">'.__("Select name to teacher's node",'agora-functions').'</option>';
    foreach( $nodes as $node ){
        if( $option == $node->name ){
            $html .= '<option val="'.$node->id.'" selected>'.$node->name.'</option>';
        } else {
            $html .= '<option val="'.$node->id.'">'.$node->name.'</option>';
        }
    }
    $html .= '</select>';
    $html .= '<p class="description">'.__("Node that containing all user's with teacher's role","agora-functions").'</p>';
    $html .= '<p class="timezone-info">'.__('New members of this group, if they have Contributor role will be automatically promoted to the role teachers. This will have access to the reservations of spaces and equipment. This change does not affect users who already belong to the node, or those who have other roles.','agora-functions').'</p>';

    echo $html;
}

function xtec_booking_nodes_name() {

    add_settings_field(
        'xtec_principal_node',
        __("Teacher's group","agora-functions"),
        'xtec_booking_recovery_name',
        'general'
    );

     register_setting(
        'general',
        'xtec_principal_node'
    );
}
add_action('admin_init', 'xtec_booking_nodes_name');

/**
 * Add custom tool box Exportador Horaris
 */
function add_custom_box_exportador_horaris(){
?>
    <style>
        div.card{
            max-width: 450px;
            margin-right: 20px;
            float: left;
            min-height: 520px;
        }

        div.wrap > div.card:nth-child(3) {
            display: none;
        }
    </style>
    <div class="card">
        <h2 class="title"><?php _e("Exporter schedule","agora-functions"); ?></h2>
        <p><?php _e("From a Google Spreadsheet, this tool allows you to export to web format schedules for each group, teacher, subject or classroom. The generated code can easily insert to any page to Nodes and paste the code in the text tab.","agora-functions"); ?></p>
        <p><a href="http://blocs.xtec.cat/coordinaciotac/2015/09/16/exportador-dhoraris-web/" target="_blank"><img src="../wp-content/themes/reactor/custom-tac/imatges/export_hora.png" title="<?php _e("Exporter schedule","agora-functions"); ?>" style="max-width:320px;border:1px solid black"></a></p>
        <p><a href="http://blocs.xtec.cat/coordinaciotac/2015/09/16/exportador-dhoraris-web/" target="_blank"><?php _e("Link to exporter schedule","agora-functions"); ?></a></p>
        <p><?php _e("Developed by: Felix Tejero","agora-functions"); ?></p>
    </div>
<?php
}
add_action('tool_box','add_custom_box_exportador_horaris');

/**
 * Add custom tool box Gestor d'esdeveniments
 */
function add_custom_box_gestor_esdeveniments(){
?>
    <div class="card">
        <h2 class="title"><?php _e("Calendar manager","agora-functions"); ?></h2>
        <p><?php _e("From a Google Spreadsheet, this tool allows you to upgrade the course calendars with one click. However, if you add new events to the calendar, joining the spreadsheet.","agora-functions"); ?></p>
        <p><a href="http://blocs.xtec.cat/coordinaciotac/2015/09/21/gestio-dels-esdeveniments-del-curs/" target="_blank"><img src="../wp-content/themes/reactor/custom-tac/imatges/img_gestor_esdeveniments.png" title="<?php _e("Calendar manager","agora-functions"); ?>"></a></p>
        <p><a href="http://blocs.xtec.cat/coordinaciotac/2015/09/21/gestio-dels-esdeveniments-del-curs/" target="_blank"><?php _e("Link to calendar manager","agora-functions"); ?></a></p>
        <p><?php _e("Developed by: Pepi Garrote","agora-functions"); ?></p>
    </div>
<?php
    remove_action('tool_box','bp_core_admin_available_tools_page');
}
add_action('tool_box','add_custom_box_gestor_esdeveniments');

/**
 * Remove box tool buddypress 'bp_core_admin_available_tools_intro'
 */
function test_box(){
    remove_action('tool_box','bp_core_admin_available_tools_intro');
}
add_action('admin_init','test_box');

/**
 * Add a summary image automatically when publishing content: gallery image,
 * video youtube (shortcode + iframe), video vimeo (shortcode + iframe),
 * url image, carousel, gallery.io
 *
 * @param $content
 * @author Xavier Nieto
 *
 * @return bool
 */
function xtec_image_summary($content){

    global $post;

    $currentFirstImage = explode( "alt=", $content );
    $currentFirstImage = str_ireplace("'", "\"", $currentFirstImage[1] );
    $currentFirstImage = str_ireplace( "\\", "", $currentFirstImage );
    $currentFirstImage = explode( '"', $currentFirstImage );

    if( count($currentFirstImage) > 1 ){

        $attached_image = get_posts( array(
            'post_type' => 'attachment',
            'posts_per_page' => -1,
        ) );

        foreach ($attached_image as $attachment) {
            if ( $attachment->post_title == $currentFirstImage[1] ){
                set_post_thumbnail( $post->ID, $attachment->ID );
                return true;
            }
        }

        return false;

    } else {

        $currentFirstImage = explode( "src=", $content );
        $currentFirstImage = str_ireplace( "'", "\"", $currentFirstImage[1] );
        $currentFirstImage = str_ireplace( "\\", "", $currentFirstImage );
        $currentFirstImage = explode( '"', $currentFirstImage );

        $summaryImage = trim($currentFirstImage[1]);

        $ch = curl_init ( $summaryImage );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_exec ( $ch );

        $imageBytes = curl_getinfo( $ch, CURLINFO_SIZE_DOWNLOAD );

        if( $imageBytes < 2097152 ) { // 2 MB

            add_action( 'add_attachment', 'xtec_video_thumbnail_attachment' );
            media_sideload_image( $summaryImage, $post->ID );
            remove_action( 'add_attachment', 'xtec_video_thumbnail_attachment' );
            return true;

        }

        return false;
    }
}

function xtec_video_image_summary_y( $content ){

    global $post;

    if ( preg_match( '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $content, $result ) ) {

        $summaryImage = 'https://img.youtube.com/vi/'.$result[1].'/0.jpg';

        if ( stripos( $summaryImage, 'iframe' ) !== false ){

            preg_match( '/youtube.com\/embed\/([a-zA-Z0-9]*)/', $result[0], $result );
            $summaryImage = 'https://img.youtube.com/vi/'.$result[1].'/0.jpg';

        }

        add_action( 'add_attachment', 'xtec_video_thumbnail_attachment' );
        media_sideload_image( $summaryImage, $post->ID );
        remove_action( 'add_attachment', 'xtec_video_thumbnail_attachment' );

        return true;

    }

    return false;
}

function xtec_video_image_summary_v( $content ){

    global $post;

    $url = '';

    if ( preg_match("/src=(\"|')\S*(\"|')/", $content, $result ) ){

        $result = str_ireplace( "src=", "", $result[0] );
        $result = str_ireplace( "'", "\"", $result );
        $result = str_ireplace( "\\", "", $result );
        $result = str_ireplace( "\"", "", $result );

        $url = "https://vimeo.com/api/oembed.json?url=". $result;

    } else if ( preg_match( '/(http|https):\/\/player.vimeo.com\/video\/([0-9]*)/i', $content, $result ) ){

        $url = "https://vimeo.com/api/oembed.json?url=". $result[0];

    } else if ( preg_match( "/vimeo\.com\/(\w+\s*\/?)([0-9]+)*/i", $content, $result ) ){

        $url = "https://vimeo.com/api/oembed.json?url=https%3A//vimeo.com/". $result[1];

    }

    if( $url != "" ){

        $url = trim($url);

        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt( $ch, CURLOPT_URL, $url );
        $result = curl_exec( $ch );
        curl_close( $ch );

        $summaryImage = json_decode( $result, true );

        if ( preg_match( "/_[0-9]*x[0-9]*.jpg/i", $summaryImage['thumbnail_url'], $result ) ){
            $summaryImage['thumbnail_url'] = str_replace($result,'_480x360.jpg',$summaryImage['thumbnail_url']);
        }

        add_action( 'add_attachment','xtec_video_thumbnail_attachment' );
        media_sideload_image( $summaryImage['thumbnail_url'], $post->ID );
        remove_action( 'add_attachment','xtec_video_thumbnail_attachment' );

        return true;

    }

    return false;
}

function xtec_shortcode_image_summary( $shortCode ){

    $shortCode = str_ireplace( '\\','',$shortCode );
    $htmlCode = apply_filters( 'the_content',$shortCode );

    if ( $htmlCode !== '' ){

        global $post;

        $currentFirstImage = explode( "src=",$htmlCode );
        $currentFirstImage = str_ireplace( "'","\"",$currentFirstImage[1] );
        $currentFirstImage = str_ireplace( "\\","",$currentFirstImage );
        $currentFirstImage = explode( '"',$currentFirstImage );

        if( count( $currentFirstImage ) > 1 ){

            $attached_image = get_posts( array(
                'post_type' => 'attachment',
                'posts_per_page' => -1,
            ) );

            foreach ( $attached_image as $attachment ) {
                if ( $attachment->guid == $currentFirstImage[1] ){
                    set_post_thumbnail( $post->ID, $attachment->ID );
                    return true;
                }
            }
            return false;
        }
    }
    return false;
}

function xtec_video_thumbnail_attachment( $att_id ){
    global $post;
    set_post_thumbnail( $post->ID, $att_id );
}

function automatic_summary_image() {

    $post = $_POST;

    if ( $post['post_type'] == 'post' ){

        $already_has_thumb = has_post_thumbnail($post['post_ID']);
        if ( ! $already_has_thumb ){
            $content = $post['content'];

            $content = str_ireplace("/><img","/>\n<img",$content);
            $content = str_ireplace("/><iframe","/>\n<iframe",$content);

            $pattern = '/<img.*>|youtu|vimeo|\[.*\]/';
            preg_match_all($pattern,$content,$matches);

            foreach ( $matches[0] as $match ) {

                if ( stripos($match,'[slideshow_deploy') !== false ){
                    $shortCode = $match;
                    $match = 'shortcode';
                }

                switch ( $match ) {
                    case 'youtu':
                        $thumbnail = xtec_video_image_summary_y( $content );
                        break;
                    case 'vimeo':
                        $thumbnail = xtec_video_image_summary_v( $content );
                        break;
                    case 'shortcode':
                        $thumbnail = xtec_shortcode_image_summary( $shortCode );
                        break;
                    default:
                        $thumbnail = xtec_image_summary( $match );
                        break;
                }

                if ( $thumbnail == true ){
                    break;
                }
            }
        }
    }
}
add_action('draft_to_publish', 'automatic_summary_image');
add_action('new_to_publish', 'automatic_summary_image');
add_action('pending_to_publish', 'automatic_summary_image');
add_action('future_to_publish', 'automatic_summary_image');

// Buddypress-docs

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
 * Users with Contributor or suscriptor role can't create documents into bpdocs plugin
 *
 * @author Xavier Nieto
 *
 * @return string
 */
function xtec_caps_bpdocs($caps, $cap, $user_id, $args){
    $bpuser_docs = get_user_by( 'ID', $user_id );

    if ( false !== $bpuser_docs) { // If there's no logged user, get_user_by() returns null
        $bproles_docs = (array) $bpuser_docs->roles;

        if ( in_array( 'contributor', $bproles_docs ) || in_array( 'subscriber', $bproles_docs ) ) {
            $caps[] = 'do_not_allow';
        }

        return $caps;
    } else {
        return false;
    }
}
add_filter('bp_docs_map_meta_caps','xtec_caps_bpdocs', 10, 4);

/**
 * Don't allow change of bbpress role to xtecadmin. Forced to be keymaster.
 *
 * @author Xavier Nieto
 *
 * @return string
 */
function xtec_filter_bbp_set_user_role( $new_role, $user_id, $user ) {
    if( $user->data->user_login == get_xtecadmin_username() ) {
        foreach ( $user->roles as $role ) {
            if( substr( $role, 0, 4 ) == 'bbp_' ) {
                $user->remove_role( $new_role ); // At this point the role was already changed, so it must be removed
                $user->add_role( 'bbp_keymaster' );
            }
        }
    }
    return $new_role;
}
add_filter( 'bbp_set_user_role', 'xtec_filter_bbp_set_user_role', 10, 3 );

/**
 * Hidden some metaboxes in nav-menus for buddypress-docs
 *
 * @author Xavier Nieto
 */
function remove_nav_menu_metaboxes_nodes( $metaboxes ) {
    if ( !is_xtec_super_admin() ) {
        remove_meta_box( 'add-bp_docs_folder_in_user', 'nav-menus', 'side' );
        remove_meta_box( 'add-bp_docs_folder_in_group', 'nav-menus', 'side' );
        remove_meta_box( 'add-bp_docs_access', 'nav-menus', 'side' );
    }
}
add_action( 'admin_head-nav-menus.php', 'remove_nav_menu_metaboxes_nodes', 10, 1 );
