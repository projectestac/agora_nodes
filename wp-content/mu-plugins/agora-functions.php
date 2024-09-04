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
 * Load agora-functions javascript file
 */
function agora_functions_load_js() {
    wp_register_script('agora-functions-js', plugins_url('/javascript/agora-functions.js', __FILE__), ['jquery']);
    wp_register_script('cookie-consent-js', plugins_url('/javascript/cookie-consent.min.js', __FILE__));
    wp_enqueue_script('agora-functions-js');
    wp_enqueue_script('cookie-consent-js');
}
add_action('wp_enqueue_scripts', 'agora_functions_load_js');

/**
 * Load script to show a bottom bar with a message to accept the usage of cookies.
 */
function bps_cookie_script() {
    echo '
        <script type="text/javascript">
            window.cookieconsent_options = {"message":"En aquest web es fan servir galetes. En cas que continuïs navegant, entendrem que acceptes la instal·lació d’aquestes tal com es detalla a la ","dismiss":"Accepta","learnMore":"política de galetes.","link":"https://projectes.xtec.cat/digital/politica-de-galetes/","theme":"dark-bottom"};
        </script>
    ';
}
add_action('wp_head', 'bps_cookie_script');

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

    // When called from CLI, $_SERVER['SERVER_NAME'] is not defined. Furthermore, this function doesn't need to be called.
    if (defined('CLI_SCRIPT')) {
        return ;
    }

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
                <image id="current_cat_image" src="<?php echo $cat_meta['image'];?>" height=110 alt="actual image" border=0 >
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
    return $cat_meta['image'] ?? '';
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
    // Exception for Astra theme
    if ('Astra' == wp_get_theme()->Name) {
        return $template;
    } elseif (get_the_ID() == get_option('page_on_front') && strrpos($template, 'front-page') === false) {
        $template = get_template_directory() . '/page-templates/front-page.php';
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

    if ( isset($post['post_type']) && $post['post_type'] === 'post' ) {

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
                        // 2022-02-16 @aginard
                        // Commented out to avoid the automatic thumbnail generation for images, which doesn't work well.
                        // $thumbnail = xtec_image_summary( $match );
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

    if ( false !== $bpuser_docs ) { // If there's no logged user, get_user_by() returns null
        $bproles_docs = (array) $bpuser_docs->roles;
        if ( ( in_array( 'contributor', $bproles_docs ) || in_array( 'subscriber', $bproles_docs ) ) && $cap == 'bp_docs_create' ) {
            $caps[] = 'do_not_allow';
            echo "<script type='text/javascript'>\n";
            echo "jQuery(document).ready(function() {
                jQuery('#bp-create-doc-button').hide();
            });";
            echo "</script>\n";
        }
    }

    return $caps;
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

/**
 * Show metaboxes in $visible_metaboxes as default.
 * @author adriagarrido
 */
function default_meta_box() {
    $current_metaboxes = get_user_option( 'metaboxhidden_nav-menus' );
    $visible_metaboxes = array( 'add-post-type-page', 'add-post-type-post', 'add-custom-links', 'add-category', 'add-post-type-bp_doc' );
    $result = array_diff($current_metaboxes, $visible_metaboxes);
    $user = wp_get_current_user();
	update_user_option( $user->ID, 'metaboxhidden_nav-menus', $result, true );
}
add_action( 'admin_head-nav-menus.php', 'default_meta_box' );

/**
 * In themes list (wp-admin/themes.php), show only the theme already in use in that site
 *
 * @param $prepared_themes array
 * @return mixed
 * @author Xavier Nieto
 * @author Toni Ginard
 */
function xtec_check_themes_to_show(array $prepared_themes) {
    // xtecadmin can change the theme
    if (is_xtec_super_admin()) {
        return $prepared_themes;
    }

    // Remove all themes but the active
    foreach ($prepared_themes as $theme) {
        if ($theme['active'] !== true) {
            unset($prepared_themes[$theme['id']]);
        }
    }

    return $prepared_themes;
}
add_filter( 'wp_prepare_themes_for_js', 'xtec_check_themes_to_show', 10, 1 );

/** Polylang customizations */
function xtec_polylang_load_language_file() {
    // Load polylang translation
    load_plugin_textdomain( 'polylang', false, basename( POLYLANG_DIR ) . '/languages' );

    // Load translation of some extensions that polylang blocks (Yes, It's duplicated intentionally!)
    load_plugin_textdomain( 'grup-classe', false, 'grup-classe/languages' );
}

function xtec_polylang_check() {
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    if( is_plugin_active( 'polylang/polylang.php' )) {

        // Load polylang translation
        load_plugin_textdomain( 'polylang', false, 'polylang/languages' );

        // Reload translation of some extensions that polylang blocks
        load_plugin_textdomain( 'slideshow-jquery-image-gallery', false, 'slideshow-jquery-image-gallery/languages' );
        load_plugin_textdomain( 'google-calendar-events', false, 'google-calendar-events/i18n' );
        load_plugin_textdomain( 'xtec-stats', false, 'xtec-stats/languages' );
        load_plugin_textdomain( 'buddypress', false, 'buddypress/bp-languages' );
        load_plugin_textdomain( 'bp-docs', false, 'buddypress-docs/languages' );
        load_plugin_textdomain( 'invite-anyone', false, 'invite-anyone/languages' );
        load_plugin_textdomain( 'email-subscribers', false, 'email-subscribers/languages' );
        load_plugin_textdomain( 'grup-classe', false, 'grup-classe/languages' );
        load_plugin_textdomain( 'add-to-any', false, 'add-to-any/languages' );
        load_plugin_textdomain( 'tinymce-advanced', false, 'tinymce-advanced/langs' );
        load_plugin_textdomain( 'wordpress-social-login', false, 'wordpress-social-login/languages' );

        // Reload translation of polylang and other plugins (TODO: Load language files only once)
        add_action( 'pll_language_defined', 'xtec_polylang_load_language_file' );
    }
}
add_action ( 'init', 'xtec_polylang_check', 1 );

/**
 * BuddyPress and bbpress moderation feature. Code begins here.
 * @author Toni Ginard
 */

/**
 * Add a report button to buddypress stream activity
 */
function xtec_bp_report_button() {
    $id = bp_get_activity_id();

    echo '<a
          id = "xtec_bp_report-' . $id . '"
          class="button item-button bp-secondary-action xtec-report"
          href=""
          title="' . __( 'Report to administrators', 'agora-functions' ) . '"
          ><span class="fa fa-flag-o"></span></a>';
}
add_action( 'bp_activity_entry_meta', 'xtec_bp_report_button' );

/**
 * Add a report button to bbpress posts
 *
 * @param $retval
 * @param $r
 * @return string
 */
function xtec_bbpress_report_button( $retval, $r ) {

    $id = bbp_get_reply_id();

    $r['links']['report'] = '<a
          id = "xtec_bbpress_report-' . $id . '"
          class="bbp-report-link xtec-report"
          href=""
          title="' . __( 'Report to administrators', 'agora-functions' ) . '"
          >' . __( 'Report this', 'agora-functions' ) . '</a>';

    $links = implode( $r['sep'], array_filter( $r['links'] ) );
    $retval = $r['before'] . $links . $r['after'];

    return $retval;
}
add_filter( 'bbp_get_topic_admin_links', 'xtec_bbpress_report_button', 10, 2 );
add_filter( 'bbp_get_reply_admin_links', 'xtec_bbpress_report_button', 10, 2 );

/**
 * Create custom post type xtec_report
 */
function xtec_create_post_type_report() {
    // XTEC *** 603 - Add filter to show only for admin and xtecadmin - 2018.07.13 @adriagarrido
    global $current_user;
    if (is_xtec_super_admin() || (isset($current_user->roles[0]) && $current_user->roles[0] == 'administrator')) {
        $show_in_menu_value = 'xtec-bp-options';
    } else {
        $show_in_menu_value = false;
    }
    register_post_type( 'xtec_report',
        array(
            'labels' => array(
                'name' => __( 'Reports', 'agora-functions' ),
                'singular_name' => __( 'Report', 'agora-functions' ),
            ),
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => $show_in_menu_value,
            'publicly_queryable' => false,
            'exclude_from_search' => true,
            'show_in_nav_menus' => false,
            'supports' => array(
                'title',
                'author',
            ),
        )
    );
}
add_action( 'init', 'xtec_create_post_type_report' );

/**
 * Add report to wp_posts (post type xtec_report) and send an e-mail to site e-mail address
 */
function xtec_report() {
    $id = $_POST['id'];
    $plugin = $_POST['plugin'];
    $user_login = $user_display_name = $permalink = $post_content = $post_date = '';

    if ( 'buddypress' == $plugin ) {
        $activity = bp_activity_get_specific( array( 'activity_ids' => $id ) )['activities'][0];
        $user_login = $activity->user_login;
        $user_display_name = $activity->display_name;
        $post_content = $activity->content;
        $post_date = $activity->date_recorded;

        // General wall
        if ( 0 == $activity->item_id ) {
            global $bp;
            $permalink = trailingslashit( bp_get_root_domain() . '/' . $bp->activity->root_slug );
        } else {
            $group = groups_get_group( array( 'group_id' => $activity->item_id ) );
            $permalink = trailingslashit( bp_get_root_domain() . '/' . bp_get_groups_root_slug() . '/' . $group->slug . '/' );
        }
    }
    elseif ( 'bbpress' == $plugin ) {
        // The reported post can be a topic or a reply. Need to support both possibilities
        $reply = bbp_get_reply( $id );
        $topic = bbp_get_topic( $id );

        if ( null == $reply ) {
            $post_content = $topic->post_content;
            $permalink = $topic->guid;
            $user_login = get_user_by( 'id', $topic->post_author )->data->user_login;
            $user_display_name = get_user_by( 'id', $topic->post_author )->data->display_name;
        }
        elseif ( null == $topic ) {
            $post_content = $reply->post_content;
            $permalink = $reply->guid;
            $user_login = get_user_by( 'id', $reply->post_author )->data->user_login;
            $user_display_name = get_user_by( 'id', $reply->post_author )->data->display_name;
        }

        $post_date = $reply->post_date;
    }

    $title = $user_login
        . ' (' . $user_display_name . ') '
        . __( 'posted on' , 'agora-functions' ) . ' '
        . date( 'd-m-Y H:i:s', strtotime( $post_date ) );

    $content = '<a href="' . $permalink . '" target="_blank">'
        . __( 'See it in its context', 'agora-functions')
        . '</a><br /><hr />'
        . $post_content;

    // Save report to wp_posts
    wp_insert_post( array(
        'post_type' => 'xtec_report',
        'post_title' => $title,
        'post_content' => $content,
        'post_status' => 'publish',
        'comment_status' => 'closed',
        'ping_status' => 'closed',
    ));

    // Update the button seen by the user
    _e( 'Notified to admins', 'agora-functions' );

    // Send the e-mail to site address
    $user_reporter = wp_get_current_user();
    $admin_email = get_bloginfo('admin_email');
    $subject = '[' . get_bloginfo('name') . '] ' . __( 'Activity report in social network', 'agora-functions' );
    $email_content = '<p>' . sprintf(
            __( 'The user <strong>%1$s (%2$s)</strong> has reported the following content published by <strong>%3$s (%4$s)</strong>:', 'agora-functions' ),
            $user_reporter->data->user_login,
            $user_reporter->data->display_name,
            $user_login,
            $user_display_name)
        . '</p><br />'
        . '<blockquote><hr style="color: #ccc; height: 1px; border-style: dashed;" />'
        . '<p>' . $post_content . '</p><br />'
        . '<hr style="color: #ccc; height: 1px; border-style: dashed;" /></blockquote>'
        . '<p>'
        . '<a href="' . $permalink . '" target="_blank">' . __( 'See it in its context', 'agora-functions') . '</a><br />'
        . '<a href="' . admin_url( 'edit.php?post_type=xtec_report' ) . '" target="_blank">' . __( 'Go to the report admin', 'agora-functions') . '</a>'
        . '</p>'
        . '<p>' . __( 'P.S.: This is an automatic notification. Please do not reply', 'agora-functions' ) .'</p><br />';

    wp_mail( $admin_email, $subject, $email_content );

    // This function is called via ajax
    wp_die();
}
add_action( 'wp_ajax_xtec_report', 'xtec_report' );

/**
 * Adds 'Content' column to xtec_report posts screen
 *
 * @param $columns
 * @return array
 */
function add_xtec_report_columns($columns) {
    $part_before = array_slice($columns, 0, 2, true);
    $part_after = array_slice($columns, 2, null, true);

    return $input = $part_before + array('content' => __( 'Content', 'agora-functions' )) + $part_after;
}
add_filter('manage_xtec_report_posts_columns', 'add_xtec_report_columns');

/**
 * Populate column 'Content' in xtec_report posts screen
 *
 * @param $column
 * @param $post_id
 */
function custom_columns($column, $post_id) {
    if ('content' == $column) {
        echo get_post($post_id)->post_content;
    }
}
add_action('manage_posts_custom_column', 'custom_columns', 10, 2);

/**
 * Hide links that cannot be removed in xtec_report posts screen
 */
function hide_xtec_report_actions() {
    global $post_type;

    if ($post_type == 'xtec_report') {
        echo '<style>
            span.edit, span.inline, a.page-title-action { display:none; }
            a.row-title { pointer-events: none; cursor: default; }
            </style>';
    }
}
add_action('admin_head', 'hide_xtec_report_actions');

/**
 * Remove option for massive edition of post type xtec_report in xtec_report posts screen
 *
 * @param $actions
 * @return mixed
 */
function remove_xtec_report_bulk_actions( $actions ){
    unset( $actions['edit'] );
    return $actions;
}
add_filter('bulk_actions-edit-xtec_report','remove_xtec_report_bulk_actions');

/**
 * BuddyPress and bbpress moderation feature. Code ends here.
 * @author Toni Ginard
 */

/**
 * Activate the user, join into group and login into his page instead
 * of sending activation email.
 *
 * @param $array
 * @return void
 * @author adriagarrido
 */
function xtec_validate_user_to_bdpress( $array ) {
    // Check email type
    $email_type = $array->get( 'type' );
    switch ( $email_type ) {
        case 'core-user-registration':
            // Get data from the email.
            $tokens = $array->get_tokens();
            session_start();
            $_SESSION['invited_user'][$tokens['user.id']] = null;
            session_write_close();
            // Activate user
            $user_id = bp_core_activate_signup( $tokens['key'] );
            // Login the user to avoid login screen.
            if ( $user_id != null ) {
                $user = get_user_by( 'id', $user_id );
                $username = $user->get( 'user_login' );
                wp_set_current_user( $user_id, $username );
                wp_set_auth_cookie( $user_id );
                do_action( 'wp_login', $username );
                // redirect to group page
                session_start();
                $slug = $_SESSION['invited_user'][$user_id];
                session_write_close();
                if ( $slug != null ) {
                    session_start();
                    unset( $_SESSION['invited_user'][$user_id] );
                    session_write_close();
                    wp_redirect( get_home_url() . "/nodes/$slug" );
                } else {
                    // or to user page
                    wp_redirect( get_home_url() . "/membres/$username" );
                }
                exit;
            }
            break;
        case 'groups-invitation':
            // Check if this email is from an invited user.
            $to = $array->get( 'to' );
            $user_id = $to[0]->get_user()->get( 'id' );
            session_start();
            $invited_user = $_SESSION['invited_user'];
            session_write_close();
            if ( array_key_exists( $user_id, $invited_user ) ) {
                $tokens = $array->get( 'tokens' );
                $group = $tokens['group'];
                groups_join_group( $group->id, $user_id );
                BP_Notifications_Notification::delete( array( 'user_id' => $user_id, 'component_action' => 'group_invite' ) );
                session_start();
                $_SESSION['invited_user'][$user_id] = $group->slug;
                session_write_close();
            }
            break;
    }
}
add_action( 'bp_send_email', 'xtec_validate_user_to_bdpress' );

/**
 * Configure PHPMailer object to use SMTP instead of default sendmail. The parameter is
 * passed by reference, so it is not necessary to return it
 *
 * @param $phpmailer PHPMailer object (passed by reference)
 * @return void
 * @author Toni Ginard
 */
function xtec_configure_mailer($phpmailer) {

    global $agora;

    $phpmailer->IsSMTP(); // use SMTP
    $phpmailer->SMTPDebug = 0;
    $phpmailer->Debugoutput = 'html';
    $phpmailer->Host = $agora['mail']['server'];
    $phpmailer->Port = 587;
    $phpmailer->SMTPSecure = 'tls';
    $phpmailer->SMTPAuth = true;
    $phpmailer->Username = $agora['mail']['username'];
    $phpmailer->Password = $agora['mail']['userpwd'];
    $phpmailer->From = $agora['mail']['reply'];
    $phpmailer->FromName = html_entity_decode(get_option('blogname'), ENT_QUOTES);
    $phpmailer->Subject = html_entity_decode($phpmailer->Subject, ENT_QUOTES);

}
add_action('phpmailer_init', 'xtec_configure_mailer');

/**
 * Modify default cache time of 12 hours down to 1 hour for all feeds
 *
 * @author Nacho Abejaro
 */
function custom_change_feed_cache_transient_lifetime() {
    return 3600;
}
add_filter('wp_feed_cache_transient_lifetime', 'custom_change_feed_cache_transient_lifetime', 2);

/*
 * Ginys al tauler d'incidències i inventari
 */
function extensions_dashboard_widgets() {
    wp_add_dashboard_widget('extensions_widget_dashboard', 'Extensions (plugins)', 'extensions_widget_dashboard');

    // Amaga el giny de RSS de agora.xtec.cat/nodes
    remove_meta_box('widget_rss_nodes', 'dashboard', 'normal');
}

add_action('wp_dashboard_setup', 'extensions_dashboard_widgets');

function extensions_widget_dashboard() {

    if (current_user_can('activate_plugins')) {

        echo '
            <h3><span class="dashicons dashicons-laptop"></span> Gestor d\'incidències</h3>
            <p>Permet que docents, alumnat i PAS puguin notificar les incidències informàtiques en un sistema senzill i centralitzat. 
            <a target="_blank" href="https://projectes.xtec.cat/coordinaciodigital/gestio-equips-serveis/gestio-incidencies/ri/">Més informació</a>.</p> 
            <form action="' . admin_url('admin-post.php') . '" method="post">
            ';

        if (!is_plugin_active('nodes-incidencies/incidencies-informatiques.php')) {
            echo '
                <input type="hidden" name="action" value="activate_incidencies" />
                <input type="submit" class="button button-primary" value="Activa" />
                ';
        } else {
            echo '
                <input type="hidden" name="action" value="deactivate_incidencies" />
                <input type="submit" class="button" value="Desactiva" />
                <a href="post-new.php?post_type=nodes_incidencies" class="button button-primary">Obre una incidència</a>
                ';
        }

        echo '</form><hr /><br />';

    } else if (is_plugin_active('nodes-incidencies/incidencies-informatiques.php')) {
        echo '<a href="post-new.php?post_type=nodes_incidencies" class="button button-primary">Obre una incidència</a><hr>';
    }

    if (current_user_can('manage_options')) {
        echo '
            <h3><span class="dashicons dashicons-list-view"></span> Gestor d\'inventari digital</h3>
            <p>Permet crear un inventari dels equips i dispositius digitals del centre (excepte equips del PEDC, que es gestionen a INDIC). 
            <a target="_blank" href="https://projectes.xtec.cat/coordinaciodigital/gestio-equips-serveis/maquinari/inventari-digital/">Més informació</a>.</p>
            <form action="' . admin_url('admin-post.php') . '" method="post">
            ';

        if (!is_plugin_active('nodes-inventari/inventari-digital.php')) {
            echo '
                <input type="hidden" name="action" value="activate_inventari" />
                <input type="submit" class="button button-primary" value="Activa" />
                ';
        } else {
            echo '
                <input type="hidden" name="action" value="deactivate_inventari" />
                <input type="submit" class="button" value="Desactiva" />
                <a href="post-new.php?post_type=nodes_inventari" class="button button-primary">Afegeix un element nou</a>
                ';
        }

        echo '</form>';
    }

}

function activate_incidencies() {
    activate_plugin('nodes-incidencies/incidencies-informatiques.php');
    wp_redirect('index.php');
}

function deactivate_incidencies() {
    deactivate_plugins('nodes-incidencies/incidencies-informatiques.php');
    wp_redirect('index.php');
}

function activate_inventari() {
    activate_plugins('nodes-inventari/inventari-digital.php');
    wp_redirect('index.php');
}

function deactivate_inventari() {
    deactivate_plugins('nodes-inventari/inventari-digital.php');
    wp_redirect('index.php');
}

add_action('admin_post_activate_incidencies', 'activate_incidencies');
add_action('admin_post_deactivate_incidencies', 'deactivate_incidencies');
add_action('admin_post_activate_inventari', 'activate_inventari');
add_action('admin_post_deactivate_inventari', 'deactivate_inventari');


// Filtre demanat pel Xavi Meler
function myfeed_request($qv) {
    if (isset($qv['feed']) && !isset($qv['post_type'])) {
        $qv['post_type'] = [
            'post',
            'nodes_reporters',
            'nodes_edupress',
        ];
    }
    return $qv;
}

add_filter('request', 'myfeed_request');

/**
 * Enqueue the javascript code to disable Gutenberg blocks.
 *
 * @return void
 */
function disable_gutenberg_blocks(): void {
    wp_enqueue_script(
        'deny-list-blocks',
        WPMU_PLUGIN_URL . '/javascript/disable_gutenberg_blocks.js',
        ['wp-blocks', 'wp-dom-ready', 'wp-edit-post']
    );
}

add_action('enqueue_block_editor_assets', 'disable_gutenberg_blocks');

/**
 * Enqueue the javascript code to configure the editor views section.
 *
 * @return void
 */
function configure_block_editor() {
    $script = "window.onload = function() {
        const isFullscreenMode = wp.data.select('core/edit-post').isFeatureActive('fullscreenMode');
        const isFocusModeEnabled = wp.data.select('core/edit-post').isFeatureActive('focusMode');
        const isTopToolbarEnabled = wp.data.select('core/edit-post').isFeatureActive('topToolbar');
        if (isFullscreenMode) { wp.data.dispatch('core/edit-post').toggleFeature('fullscreenMode'); }
        if (!isFocusModeEnabled) { wp.data.dispatch('core/edit-post').toggleFeature('focusMode'); }
        if (!isTopToolbarEnabled) { wp.data.dispatch('core/edit-post').toggleFeature('topToolbar'); }
        }";
    wp_add_inline_script('wp-blocks', $script);
}

add_action('enqueue_block_editor_assets', 'configure_block_editor');

// Widgets: Use legacy widgets configuration.
add_action('after_setup_theme', 'nodes_remove_theme_support');
function nodes_remove_theme_support(): void {
    remove_theme_support('widgets-block-editor');
}

// Admin menu: Add Buddypress custom login link if plugin is active and not logged in.
add_action('admin_bar_menu', function ($wp_admin_bar) {
    if (!is_plugin_active('buddypress/bp-loader.php') && !is_user_logged_in()) {
        $wp_admin_bar->add_node([
            'parent' => 'top-secondary',
            'id' => 'login-link-admin-bar',
            'title' => __('Log in'),
            'href' => wp_login_url($_SERVER['REQUEST_URI']),
        ]);

        echo '<style>
        #wpadminbar li#wp-admin-bar-login-link-admin-bar > .ab-item::before {
            content: "";
            margin-top: 4px;
        }
        </style>';
    }
});

/* Theme migration stuff begins here */

/**
 * Do all the necessary actions when there is a theme change. Ensure that the
 * records required by Nodes are created and initialized.
 *
 * @param string $new_theme The name of theme that is being activated.
 */
add_action('switch_theme', function ($new_theme) {

    if ($new_theme === 'Astra') {
        initialize_theme_mod();
        initialize_palettes();
        initialize_astra_settings();

        // Remove the welcome panel from the dashboard. This call hides the panel immediately after
        // the theme is activated. Otherwise, the panel shows up at least once.
        add_action('admin_init', function () {
            remove_action('welcome_panel', 'wp_welcome_panel');
        }, 1);
    }

    // When action switch_theme is triggered, the function get_user_by() is not available.
    if (!function_exists('get_user_by')) {
        include_once ABSPATH . 'wp-includes/pluggable.php';
    }

    update_admin_colors($new_theme);

}, 10, 1);

/**
 * Create default values for the element 'astra_nodes_options' in the 'theme_mods_astra'
 * record of the table wp_options.
 *
 * @return void
 */
function initialize_theme_mod(): void {

    $default_values = default_theme_mod();
    $astra_nodes_options = get_theme_mod('astra_nodes_options');

    if (empty($astra_nodes_options)) {
        $astra_nodes_options = $default_values;
    } else {
        foreach ($default_values as $key => $value) {
            if (!array_key_exists($key, $astra_nodes_options)) {
                $astra_nodes_options[$key] = $value;
            }
        }
    }

    set_theme_mod('astra_nodes_options', $astra_nodes_options);

}

/**
 * Definition of the default values for the 'astra_nodes_options' element in the
 * 'theme_mods_astra' record.
 *
 * @return array
 */
function default_theme_mod(): array {

    $reactor_options = get_option('reactor_options');
    $carousel_id = $reactor_options['carrusel'];

    if ($carousel_id) {
        $post_meta = get_post_meta($carousel_id);
        $slides = maybe_unserialize($post_meta['slides'][0]);

        $filtered_slides = array_filter($slides, static function ($slide) {
            return $slide['type'] === 'attachment';
        });

        $processed_slides = array_slice($filtered_slides, 0, 5);

        foreach ($processed_slides as &$slide) {
            $slide['image_url'] = wp_get_attachment_url($slide['postId']);
        }
    }

    migrate_favicon($reactor_options['favicon_image']);
    $logo = migrate_logo($reactor_options['logo_image']);

    $origin_icons = get_option('my_option_name');
    $organism_logo = stripos($reactor_options['cpCentre'], 'barcelona') ? 'ceb' : 'departament';

    $icons = array_filter($origin_icons, static function ($key) {
        return strpos($key, 'icon') === 0;
    }, ARRAY_FILTER_USE_KEY);

    $icons = convert_header_icons($icons);

    $title_cards = get_cards_titles();
    $image_cards = register_image_in_media_library();

    // Translation note: When action switch_theme is triggered, the text domain is not loaded. That's why the
    // texts are in catalan.
    return [
        'custom_logo' => $logo ?? 0,
        'pre_blog_name' => '',
        'postal_address' => $reactor_options['direccioCentre'] ?? '',
        'postal_code_city' => $reactor_options['cpCentre'] ?? '',
        'phone_number' => $reactor_options['telCentre'] ?? '',
        'link_to_map' => $reactor_options['googleMaps'] ?? '',
        'contact_page' => $reactor_options['emailCentre'] ?? '',
        'email_address' => '',
        'header_icon_1_classes' => $icons['icon11'] ?? $icons['icon1'] ?? '',
        'header_icon_1_text' => $origin_icons['title_icon11'] ?? $origin_icons['title_icon1'] ?? 'Icona 1',
        'header_icon_1_link' => $origin_icons['link_icon11'] ?? $origin_icons['link_icon1'] ?? '',
        'header_icon_1_open_in_new_tab' => true,
        'header_icon_2_classes' => $icons['icon12'] ?? $icons['icon2'] ?? '',
        'header_icon_2_text' => $origin_icons['title_icon12'] ?? $origin_icons['title_icon2'] ?? 'Icona 2',
        'header_icon_2_link' => $origin_icons['link_icon12'] ?? $origin_icons['link_icon2'] ?? '',
        'header_icon_2_open_in_new_tab' => true,
        'header_icon_3_classes' => $icons['icon3'] ?? '',
        'header_icon_3_text' => $origin_icons['title_icon3'] ?? '',
        'header_icon_3_link' => $origin_icons['link_icon3'] ?? '',
        'header_icon_3_open_in_new_tab' => true,
        'header_icon_4_classes' => $icons['icon21'] ?? $icons['icon4'] ?? '',
        'header_icon_4_text' => $origin_icons['title_icon21'] ?? $origin_icons['title_icon4'] ?? 'Icona 4',
        'header_icon_4_link' => $origin_icons['link_icon21'] ?? $origin_icons['link_icon4'] ?? '',
        'header_icon_4_open_in_new_tab' => true,
        'header_icon_5_classes' => $icons['icon22'] ?? $icons['icon5'] ?? '',
        'header_icon_5_text' => $origin_icons['title_icon22'] ?? $origin_icons['title_icon5'] ?? 'Icona 5',
        'header_icon_5_link' => $origin_icons['link_icon22'] ?? $origin_icons['link_icon5'] ?? '',
        'header_icon_5_open_in_new_tab' => true,
        'header_icon_6_classes' => '',
        'header_icon_6_text' => '',
        'header_icon_6_link' => '',
        'header_icon_6_open_in_new_tab' => true,
        'front_page_notice_enable' => true,
        'front_page_notice_layout' => 'image_text',
        'front_page_notice_image' => $image_cards[4] ?? '',
        'front_page_notice_url' => '',
        'front_page_notice_open_in_new_tab' => true,
        'front_page_notice_background_color' => '',
        'front_page_notice_pre_title' => 'Informació de servei',
        'front_page_notice_title' => 'Carta d\'inici de curs i calendari',
        'front_page_notice_content' => 'Benvolgudes famílies us facilitem la Carta d\'inici de curs per a totes les famílies de l\'escola.
                                        <br/><br/>Carta d\'inici de curs per a famílies d\'educació infantil.<br/>Carta d\'inici de curs per a
                                        famílies de primària.<br/>Calendari en PDF.',
        'front_page_cards_enable' => true,
        'front_page_card_1_title' => $title_cards[0],
        'front_page_card_1_image' => $image_cards[0] ?? '',
        'front_page_card_1_url' => '',
        'front_page_card_2_title' => $title_cards[1],
        'front_page_card_2_image' => $image_cards[1] ?? '',
        'front_page_card_2_url' => '',
        'front_page_card_3_title' => $title_cards[2],
        'front_page_card_3_image' => $image_cards[2] ?? '',
        'front_page_card_3_url' => '',
        'front_page_card_4_title' => $title_cards[3],
        'front_page_card_4_image' => $image_cards[3] ?? '',
        'front_page_card_4_url' => '',
        'front_page_slider_enable' => true,
        'front_page_slider_arrows' => 'yes',
        'front_page_slider_dots' => 'yes',
        'front_page_slider_min_height' => 500,
        'front_page_slider_autoplay' => true,
        'front_page_slider_image_1' => $processed_slides[0]['image_url'] ?? '',
        'front_page_slider_link_1' => $processed_slides[0]['url'] ?? '',
        'front_page_slider_open_in_new_tab_1' => true,
        'front_page_slider_heading_1' => $processed_slides[0]['title'] ?? '',
        'front_page_slider_text_1' => $processed_slides[0]['description'] ?? '',
        'front_page_slider_image_2' => $processed_slides[1]['image_url'] ?? '',
        'front_page_slider_link_2' => $processed_slides[1]['url'] ?? '',
        'front_page_slider_open_in_new_tab_2' => true,
        'front_page_slider_heading_2' => $processed_slides[1]['title'] ?? '',
        'front_page_slider_text_2' => $processed_slides[1]['description'] ?? '',
        'front_page_slider_image_3' => $processed_slides[2]['image_url'] ?? '',
        'front_page_slider_link_3' => $processed_slides[2]['url'] ?? '',
        'front_page_slider_open_in_new_tab_3' => true,
        'front_page_slider_heading_3' => $processed_slides[2]['title'] ?? '',
        'front_page_slider_text_3' => $processed_slides[2]['description'] ?? '',
        'front_page_slider_image_4' => $processed_slides[3]['image_url'] ?? '',
        'front_page_slider_link_4' => $processed_slides[3]['url'] ?? '',
        'front_page_slider_open_in_new_tab_4' => true,
        'front_page_slider_heading_4' => $processed_slides[3]['title'] ?? '',
        'front_page_slider_text_4' => $processed_slides[3]['description'] ?? '',
        'front_page_slider_image_5' => $processed_slides[4]['image_url'] ?? '',
        'front_page_slider_link_5' => $processed_slides[4]['url'] ?? '',
        'front_page_slider_open_in_new_tab_5' => true,
        'front_page_slider_heading_5' => $processed_slides[4]['title'] ?? '',
        'front_page_slider_text_5' => $processed_slides[4]['description'] ?? '',
        'front_page_news_enable' => true,
        'front_page_news_number' => 20,
        'front_page_news_category' => 29,
        'front_page_layout' => 'sidebar_boxes',
        'pages_sidebar' => 'menu',
        'organism_logo' => $organism_logo,
    ];

}

function migrate_favicon($image_url): void {

    $reactor_favicon_url = $image_url;
    $reactor_favicon_id = attachment_url_to_postid($reactor_favicon_url);

    update_option('site_icon', $reactor_favicon_id);

}

function migrate_logo($image_url): int {

    $reactor_logo_url = $image_url;
    $reactor_logo_id = attachment_url_to_postid($reactor_logo_url);

    set_theme_mod('custom_logo', $reactor_logo_id);

    if (function_exists('astra_update_option')) {
        astra_update_option('custom_logo', $reactor_logo_id);
    }

    return $reactor_logo_id;

}

function convert_header_icons($icons): array {

    $conversion_map = [
        'dashicons-format-gallery' => 'fa-regular fa-images',
        'dashicons-groups' => 'fa-solid fa-people-group',
        'dashicons-search' => 'fa-solid fa-magnifying-glass',
        'dashicons-carrot' => 'fa-solid fa-carrot',
        'dashicons-format-chat' => 'fa-regular fa-comments',
        'dashicons-menu' => 'fa-solid fa-bars',
        'dashicons-clock' => 'fa-regular fa-clock',
        'dashicons-welcome-learn-more' => 'fa-solid fa-graduation-cap',
        'dashicons-calendar' => 'fa-regular fa-calendar-days',
        'dashicons-admin-home' => 'fa-solid fa-house',
        'dashicons-portfolio' => 'fa-solid fa-briefcase',
        'dashicons-admin-users' => 'fa-solid fa-users',
        'dashicons-book' => 'fa-solid fa-book',
        'dashicons-welcome-write-blog' => 'fa-regular fa-pen-to-square',
        'dashicons-cloud' => 'fa-solid fa-cloud',
        'dashicons-location' => 'fa-solid fa-location-dot',
    ];

    $converted_icons = [];

    foreach ($icons as $key => $value) {
        if (isset($conversion_map['dashicons-' . $value])) {
            $converted_icons[$key] = $conversion_map['dashicons-' . $value];
        } else {
            $converted_icons[$key] = '';
        }
    }

    return $converted_icons;

}

/**
 * Get the default text that will be shown in the title area of the cards, according to the type of school.
 *
 * @return array
 */
function get_cards_titles(): array {

    switch (SCHOOL_TYPE) {

        case '1': // Escola.
        case '11': // ZER.
        case '14': // Llar d'infants.
            $titles = [
                0 => 'Petits',
                1 => 'Mitjans',
                2 => 'Grans',
                3 => 'Serveis',
            ];
            break;

        case '2': // Institut.
        case '3': // Institut-Escola.
        case '9': // Centre concertat.
            $titles = [
                0 => 'ESO',
                1 => 'Batxillerat',
                2 => 'FP',
                3 => 'Serveis',
            ];
            break;

        default:
            $titles = [
                0 => 'Biblioteca',
                1 => 'Menjador',
                2 => 'Famílies',
                3 => 'Serveis',
            ];
            break;

    }

    return $titles;

}

/**
 * Registers images in the media library.
 *
 * @return array An array of uploaded images with their URLs.
 */
function register_image_in_media_library(): array {

    $image_directory = 'wp-content/mu-plugins/astra-nodes/images/';
    $image_files = [
        'card_demo_0.jpg',
        'card_demo_1.jpg',
        'card_demo_2.jpg',
        'card_demo_3.jpg',
        'news_demo.jpg',
    ];

    $uploaded_images = [];

    foreach ($image_files as $image_file) {

        $image_path = ABSPATH . $image_directory . $image_file;

        if (!file_exists($image_path)) {
            continue;
        }

        // Check if the image already exists in the media library
        $existing_attachment = get_posts([
            'name' => sanitize_title($image_file),
            'post_type' => 'attachment',
            'post_mime_type' => 'image/jpeg',
            'post_status' => 'inherit',
            'posts_per_page' => 1,
        ]);

        if (empty($existing_attachment)) {

            // When action switch_theme is triggered, the function wp_get_current_user() is not available.
            if (!function_exists('wp_get_current_user')) {
                include_once ABSPATH . 'wp-includes/pluggable.php';
            }

            $image_data = file_get_contents($image_path);
            $upload = wp_upload_bits($image_file, null, $image_data);

            if (!$upload['error']) {

                $file_path = $upload['file'];
                $file_url = $upload['url'];
                $file_type = wp_check_filetype($file_path);

                $attachment = [
                    'guid' => $file_url,
                    'post_mime_type' => $file_type['type'],
                    'post_name' => sanitize_file_name($image_file),
                    'post_title' => sanitize_file_name($image_file),
                    'post_content' => '',
                    'post_status' => 'inherit',
                ];

                $attach_id = wp_insert_attachment($attachment, $file_path);

                if (!is_wp_error($attach_id)) {

                    // When action switch_theme is triggered, the function wp_generate_attachment_metadata() is not available.
                    if (!function_exists('wp_generate_attachment_metadata')) {
                        include_once ABSPATH . 'wp-admin/includes/image.php';
                    }

                    $attach_data = wp_generate_attachment_metadata($attach_id, $file_path);
                    wp_update_attachment_metadata($attach_id, $attach_data);
                    $uploaded_images[] = wp_get_attachment_url($attach_id);

                }

            }
        } else {

            $attach_id = $existing_attachment[0]->ID;
            $uploaded_images[] = wp_get_attachment_url($attach_id);

        }
    }

    return $uploaded_images;

}

/**
 * Ensure the record 'astra-color-palettes' in the table wp_options is set and has
 * contents. If it does not exist, it is created with the default values. Otherwise,
 * any missing values are added.
 *
 * In case the record 'astra-color-palettes' does not exist, pick the current
 * palette from the theme reactor configuration.
 *
 * @return void
 */
function initialize_palettes(): void {

    $default_palettes = get_default_palettes();
    $astra_color_palettes = get_option('astra-color-palettes');
    $palettes = [];

    if (empty($astra_color_palettes) || !isset($astra_color_palettes['palettes'])) {

        $default_palettes['currentPalette'] = get_reactor_palette();
        $palettes = $default_palettes;

        $astra_settings = get_option('astra-settings');

        if (is_array($astra_settings)) {
            $astra_settings['global-color-palette']['palette'] = $palettes['palettes'][$default_palettes['currentPalette']];
            update_option('astra-settings', $astra_settings);
        } else {
            add_option('astra-settings', [
                    'global-color-palette' => [
                            'palette' => $palettes['palettes'][$default_palettes['currentPalette']]
                    ]
            ]);
        }

    } else {

        foreach ($default_palettes['palettes'] as $key => $value) {
            if (!array_key_exists($key, $astra_color_palettes['palettes'])) {
                $palettes['palettes'][$key] = $value;
            }
        }

    }

    if ($astra_color_palettes !== false) {
        update_option('astra-color-palettes', $palettes);
    } else {
        add_option('astra-color-palettes', $palettes);
    }

}

/**
 * Get the current palette from the configuration of the theme reactor.
 *
 * @return string
 */
function get_reactor_palette(): string {

    $reactor_options = get_option('reactor_options');

    // Take the value from the theme reactor configuration.
    if (!empty($reactor_options['paleta_colors'])) {
        $palette = $reactor_options['paleta_colors'];

        include_once get_theme_root() . '/reactor/custom-tac/colors_nodes.php';
        global $colors_nodes;

        return $colors_nodes[$palette]['nom'];
    }

    // Safe default value in case no configuration is found.
    return 'Blau clar i blau fosc';

}

/**
 * Define the default palettes for the theme. Generates the content for the
 * record 'astra-color-palettes' in the table wp_options.
 *
 * @return array
 */
function get_default_palettes(): array {

    return [
        'palettes' => [
            'Vermell i blau' => [
                0 => '#FF3257', // Primary
                1 => '#4C86A6', // Secondary
                2 => '', // Tertiary
                3 => '', // Link
                4 => '', // Calendar
                5 => '', // Icon22
                6 => '', // Footer
                7 => '',
                8 => '',
            ],
            'Blau clar i blau fosc' => [
                0 => '#0EB1FF',
                1 => '#087EB6',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Groc i blau' => [
                0 => '#FFA00C',
                1 => '#3C7C80',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Groc i lila' => [
                0 => '#FFA00C',
                1 => '#B03BBA',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Groc i verd' => [
                0 => '#FEA200',
                1 => '#0F6333',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Groc i vermell' => [
                0 => '#FCB535',
                1 => '#E04B35',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Verd i blau' => [
                0 => '#92AE01',
                1 => '#0988A9',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Rosa i gris' => [
                0 => '#FF3257',
                1 => '#6C6C6C',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Roses' => [
                0 => '#FF2189',
                1 => '#A41159',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Sienna' => [
                0 => '#CA5F5F',
                1 => '#763333',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Taronges' => [
                0 => '#FF4C00',
                1 => '#C42300',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Taronja i gris' => [
                0 => '#FF4C00',
                1 => '#7D7D7D',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Taronja i verd' => [
                0 => '#FF5A26',
                1 => '#1FA799',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Turqueses' => [
                0 => '#40C3C4',
                1 => '#2F5D5D',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Verd i marró' => [
                0 => '#00B36B',
                1 => '#752800',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Rosa i verd' => [
                0 => '#FC3B56',
                1 => '#2CA698',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Verd clar i verd fosc' => [
                0 => '#55CD00',
                1 => '#418000',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Vermell i taronja' => [
                0 => '#FF2A2A',
                1 => '#FF5A26',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Vermell i verd' => [
                0 => '#ff2A2A',
                1 => '#00854E',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Xocolata' => [
                0 => '#521A09',
                1 => '#923917',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Taronja i blau' => [
                0 => '#FF5A26',
                1 => '#087EB6',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Verd clar i lila' => [
                0 => '#92AE01',
                1 => '#5E3A73',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Bordeus' => [
                0 => '#B5196E',
                1 => '#770E4B',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Taronja i oliva' => [
                0 => '#EA6A00',
                1 => '#768703',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Lila i vermell' => [
                0 => '#9068BE',
                1 => '#E62739',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Blau fosc i taronja' => [
                0 => '#3A5863',
                1 => '#D86E3E',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
            'Personalitzada' => [
                0 => '#00688B',
                1 => '#B03BBA',
                2 => '',
                3 => '',
                4 => '',
                5 => '',
                6 => '',
                7 => '',
                8 => '',
            ],
        ],
        'flag' => true,
    ];

}

/**
 * Ensure the record 'astra-settings' in the table wp_options is set and contains
 * the configuration for Nodes. If it does not exist, it is created with default
 * values. Otherwise, any missing values are added.
 *
 * @return void
 */
function initialize_astra_settings(): void {

    $astra_settings = get_option('astra-settings', false);
    $astra_settings_db = $astra_settings;

    if (false === $astra_settings || !is_array($astra_settings)) {
        $astra_settings = get_default_astra_settings();
    } else {
        $default_settings = get_default_astra_settings();
        foreach ($default_settings as $key => $value) {
            if (!array_key_exists($key, $astra_settings)) {
                $astra_settings[$key] = $value;
            }
        }
    }

    if ($astra_settings_db) {
        update_option('astra-settings', $astra_settings);
    } else {
        add_option('astra-settings', $astra_settings);
    }

}

/**
 * Definition of the default values for the 'astra-settings' record. These values generate
 * the Nodes configuration.
 *
 * @return array
 */
function get_default_astra_settings(): array {

    return unserialize(
        'a:164:{s:50:"ast-callback-notice-header-transparent-header-logo";s:0:"";s:55:"ast-callback-notice-header-transparent-header-logo-link";s:0:"";s:22:"ast-header-retina-logo";s:0:"";s:18:"mobile-header-logo";s:0:"";s:51:"ast-callback-notice-header-transparent-meta-enabled";s:0:"";s:55:"ast-callback-notice-header-transparent-header-meta-link";s:0:"";s:23:"transparent-header-logo";s:0:"";s:30:"transparent-header-retina-logo";s:0:"";s:22:"is_theme_queue_running";b:0;s:24:"astra-addon-auto-version";s:5:"4.1.5";s:28:"is_astra_addon_queue_running";b:0;s:18:"theme-auto-version";s:6:"4.6.12";s:20:"header-desktop-items";a:5:{s:5:"popup";a:1:{s:13:"popup_content";a:1:{i:0;s:11:"mobile-menu";}}s:5:"above";a:5:{s:10:"above_left";a:0:{}s:17:"above_left_center";a:0:{}s:12:"above_center";a:0:{}s:18:"above_right_center";a:0:{}s:11:"above_right";a:0:{}}s:7:"primary";a:5:{s:12:"primary_left";a:2:{i:0;s:4:"logo";i:1;s:6:"html-3";}s:19:"primary_left_center";a:0:{}s:14:"primary_center";a:0:{}s:20:"primary_right_center";a:0:{}s:13:"primary_right";a:2:{i:0;s:6:"html-1";i:1;s:6:"html-2";}}s:5:"below";a:5:{s:10:"below_left";a:1:{i:0;s:6:"menu-1";}s:17:"below_left_center";a:0:{}s:12:"below_center";a:0:{}s:18:"below_right_center";a:0:{}s:11:"below_right";a:1:{i:0;s:6:"search";}}s:4:"flag";b:0;}s:11:"custom_logo";i:8244;s:13:"header-html-1";s:0:"";s:13:"header-html-3";s:0:"";s:37:"site-layout-outside-bg-obj-responsive";a:3:{s:7:"desktop";a:11:{s:16:"background-color";s:25:"var(--ast-global-color-4)";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:5:"color";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"tablet";a:11:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:0:"";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:11:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:0:"";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:25:"content-bg-obj-responsive";a:3:{s:7:"desktop";a:11:{s:16:"background-color";s:25:"var(--ast-global-color-5)";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:5:"color";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"tablet";a:11:{s:16:"background-color";s:25:"var(--ast-global-color-5)";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:5:"color";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:11:{s:16:"background-color";s:25:"var(--ast-global-color-5)";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:15:"background-type";s:5:"color";s:16:"background-media";s:0:"";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:19:"site-content-layout";s:12:"page-builder";s:16:"body-font-family";s:18:"\'Lato\', sans-serif";s:17:"body-font-variant";s:3:"400";s:14:"font-size-body";a:6:{s:7:"desktop";i:18;s:6:"tablet";i:17;s:6:"mobile";i:17;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:20:"headings-font-family";s:21:"\'Poppins\', sans-serif";s:21:"headings-font-variant";s:3:"600";s:12:"font-size-h1";a:6:{s:7:"desktop";i:36;s:6:"tablet";i:36;s:6:"mobile";i:32;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:12:"font-size-h2";a:6:{s:7:"desktop";i:40;s:6:"tablet";i:30;s:6:"mobile";i:26;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:12:"font-size-h3";a:6:{s:7:"desktop";i:26;s:6:"tablet";i:25;s:6:"mobile";i:22;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:12:"font-size-h4";a:6:{s:7:"desktop";i:24;s:6:"tablet";i:20;s:6:"mobile";i:18;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:12:"font-size-h5";a:6:{s:7:"desktop";i:20;s:6:"tablet";i:17;s:6:"mobile";i:15;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:12:"font-size-h6";a:6:{s:7:"desktop";i:17;s:6:"tablet";i:15;s:6:"mobile";i:13;s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:20:"theme-button-padding";a:6:{s:7:"desktop";a:4:{s:3:"top";i:10;s:5:"right";i:20;s:6:"bottom";i:10;s:4:"left";i:20;}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:37:"theme-button-border-group-border-size";a:4:{s:3:"top";i:0;s:5:"right";i:0;s:6:"bottom";i:0;s:4:"left";i:0;}s:20:"button-radius-fields";a:6:{s:7:"desktop";a:4:{s:3:"top";i:30;s:5:"right";i:30;s:6:"bottom";i:30;s:4:"left";i:30;}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:11:"site-layout";s:21:"ast-full-width-layout";s:9:"blog-grid";s:1:"4";s:13:"blog-date-box";b:1;s:19:"blog-date-box-style";s:6:"circle";s:15:"blog-pagination";s:8:"infinite";s:26:"single-page-content-layout";s:7:"default";s:26:"single-page-sidebar-layout";s:12:"left-sidebar";s:32:"ast-header-responsive-logo-width";a:3:{s:7:"desktop";i:200;s:6:"tablet";s:0:"";s:6:"mobile";s:0:"";}s:12:"wp-blocks-ui";s:7:"comfort";s:20:"title_tagline-margin";a:6:{s:7:"desktop";a:4:{s:3:"top";s:1:"0";s:5:"right";s:1:"0";s:6:"bottom";s:1:"0";s:4:"left";s:1:"0";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:26:"header-menu1-submenu-width";i:399;s:18:"site-content-width";i:1200;s:15:"ast-author-info";b:0;s:20:"global-color-palette";a:2:{s:7:"palette";a:9:{i:0;s:7:"#b2cca7";i:1;s:7:"#3c4c51";i:2;s:0:"";i:3;s:7:"#00A68B";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";i:8;s:0:"";}s:4:"flag";b:0;}s:29:"global-color-palette[palette]";a:9:{i:0;s:7:"#92AE01";i:1;s:7:"#0988A9";i:2;s:0:"";i:3;s:0:"";i:4;s:0:"";i:5;s:0:"";i:6;s:0:"";i:7;s:0:"";i:8;s:0:"";}s:59:"astra-sidebar-widgets-header-widget-1-visibility-responsive";a:3:{s:7:"desktop";i:1;s:6:"tablet";i:1;s:6:"mobile";i:1;}s:28:"hbb-header-bg-obj-responsive";a:3:{s:7:"desktop";a:10:{s:16:"background-color";s:7:"#ffffff";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";s:15:"background-type";s:5:"color";}s:6:"tablet";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:28:"hba-header-bg-obj-responsive";a:3:{s:7:"desktop";a:10:{s:16:"background-color";s:7:"#ffffff";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";s:15:"background-type";s:5:"color";}s:6:"tablet";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:10:"blog-width";s:7:"default";s:19:"blog-post-structure";a:5:{i:0;s:5:"title";i:1;s:10:"title-meta";i:2;s:5:"image";i:3;s:7:"excerpt";i:4;s:9:"read-more";}s:9:"blog-meta";a:1:{i:0;s:4:"date";}s:21:"blog-meta-date-format";s:5:"d/m/Y";s:27:"archive-post-sidebar-layout";s:12:"left-sidebar";s:22:"ast-archive-post-title";b:1;s:26:"single-post-sidebar-layout";s:12:"left-sidebar";s:20:"enable-related-posts";b:0;s:19:"site-sidebar-layout";s:10:"no-sidebar";s:18:"site-sidebar-width";i:30;s:20:"footer-desktop-items";a:10:{s:5:"above";a:6:{s:7:"above_1";a:0:{}s:7:"above_2";a:0:{}s:7:"above_3";a:0:{}s:7:"above_4";a:0:{}s:7:"above_5";a:0:{}s:7:"above_6";a:0:{}}s:7:"primary";a:6:{s:9:"primary_1";a:1:{i:0;s:6:"html-1";}s:9:"primary_2";a:1:{i:0;s:8:"widget-1";}s:9:"primary_3";a:1:{i:0;s:8:"widget-2";}s:9:"primary_4";a:0:{}s:9:"primary_5";a:0:{}s:9:"primary_6";a:0:{}}s:5:"below";a:6:{s:7:"below_1";a:0:{}s:7:"below_2";a:0:{}s:7:"below_3";a:0:{}s:7:"below_4";a:0:{}s:7:"below_5";a:0:{}s:7:"below_6";a:0:{}}s:5:"popup";a:1:{s:13:"popup_content";a:0:{}}s:4:"flag";b:0;s:5:"group";s:36:"astra-settings[footer-desktop-items]";s:4:"rows";a:3:{i:0;s:5:"above";i:1;s:7:"primary";i:2;s:5:"below";}s:5:"zones";a:3:{s:5:"above";a:6:{s:7:"above_1";s:15:"Above Section 1";s:7:"above_2";s:15:"Above Section 2";s:7:"above_3";s:15:"Above Section 3";s:7:"above_4";s:15:"Above Section 4";s:7:"above_5";s:15:"Above Section 5";s:7:"above_6";s:15:"Above Section 6";}s:7:"primary";a:6:{s:9:"primary_1";s:17:"Primary Section 1";s:9:"primary_2";s:17:"Primary Section 2";s:9:"primary_3";s:17:"Primary Section 3";s:9:"primary_4";s:17:"Primary Section 4";s:9:"primary_5";s:17:"Primary Section 5";s:9:"primary_6";s:17:"Primary Section 6";}s:5:"below";a:6:{s:7:"below_1";s:15:"Below Section 1";s:7:"below_2";s:15:"Below Section 2";s:7:"below_3";s:15:"Below Section 3";s:7:"below_4";s:15:"Below Section 4";s:7:"below_5";s:15:"Below Section 5";s:7:"below_6";s:15:"Below Section 6";}}s:7:"layouts";a:3:{s:5:"above";a:2:{s:6:"column";s:1:"2";s:6:"layout";a:3:{s:7:"desktop";s:7:"2-equal";s:6:"tablet";s:7:"2-equal";s:6:"mobile";s:4:"full";}}s:7:"primary";a:2:{s:6:"column";i:3;s:6:"layout";a:4:{s:6:"mobile";s:4:"full";s:6:"tablet";s:7:"3-equal";s:7:"desktop";s:7:"3-cwide";s:4:"flag";b:1;}}s:5:"below";a:2:{s:6:"column";s:1:"1";s:6:"layout";a:4:{s:7:"desktop";s:4:"full";s:6:"tablet";s:4:"full";s:6:"mobile";s:4:"full";s:4:"flag";b:0;}}}s:6:"status";a:3:{s:5:"above";b:1;s:7:"primary";b:1;s:5:"below";b:1;}}s:13:"footer-html-1";s:0:"";s:13:"footer-html-2";s:0:"";s:17:"hbb-footer-column";s:1:"1";s:17:"hbb-footer-layout";a:4:{s:7:"desktop";s:4:"full";s:6:"tablet";s:4:"full";s:6:"mobile";s:4:"full";s:4:"flag";b:0;}s:23:"hbb-footer-layout-width";s:4:"full";s:17:"hbb-footer-height";i:50;s:9:"hbb-stack";a:3:{s:7:"desktop";s:5:"stack";s:6:"tablet";s:5:"stack";s:6:"mobile";s:5:"stack";}s:28:"hbb-footer-bg-obj-responsive";a:3:{s:7:"desktop";a:10:{s:16:"background-color";s:25:"var(--ast-global-color-5)";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";s:15:"background-type";s:5:"color";}s:6:"tablet";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:19:"breadcrumb-position";s:4:"none";s:29:"breadcrumb-separator-selector";s:5:"\\003E";s:29:"hbb-footer-vertical-alignment";s:10:"flex-start";s:18:"breadcrumb-spacing";a:6:{s:7:"desktop";a:4:{s:3:"top";s:1:"0";s:5:"right";s:1:"0";s:6:"bottom";s:1:"0";s:4:"left";s:2:"13";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:20:"breadcrumb-alignment";s:4:"left";s:22:"cloned-component-track";a:13:{s:13:"header-button";i:2;s:13:"footer-button";i:2;s:11:"header-html";i:3;s:11:"footer-html";i:2;s:11:"header-menu";i:3;s:13:"header-widget";i:4;s:13:"footer-widget";i:6;s:19:"header-social-icons";i:1;s:19:"footer-social-icons";i:1;s:14:"header-divider";i:3;s:14:"footer-divider";i:3;s:13:"removed-items";a:0:{}s:4:"flag";b:0;}s:21:"ast-single-page-title";b:1;s:16:"hb-footer-layout";a:4:{s:7:"desktop";s:7:"3-cwide";s:6:"tablet";s:7:"3-equal";s:6:"mobile";s:4:"full";s:4:"flag";b:0;}s:27:"hb-footer-bg-obj-responsive";a:3:{s:7:"desktop";a:10:{s:16:"background-color";s:7:"#f9f9f9";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";s:15:"background-type";s:5:"color";}s:6:"tablet";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:23:"footer-html-2-alignment";a:3:{s:7:"desktop";s:6:"center";s:6:"tablet";s:6:"center";s:6:"mobile";s:6:"center";}s:25:"footer-widget-alignment-1";a:3:{s:7:"desktop";s:6:"center";s:6:"tablet";s:0:"";s:6:"mobile";s:0:"";}s:25:"footer-widget-alignment-2";a:3:{s:7:"desktop";s:5:"right";s:6:"tablet";s:0:"";s:6:"mobile";s:0:"";}s:22:"hb-footer-layout-width";s:4:"full";s:27:"list-block-vertical-spacing";b:0;s:18:"add-hr-styling-css";b:0;s:32:"astra-site-svg-logo-equal-height";b:0;s:23:"ast-site-content-layout";s:20:"full-width-container";s:18:"site-content-style";s:7:"unboxed";s:18:"site-sidebar-style";s:7:"unboxed";s:30:"single-page-ast-content-layout";s:7:"default";s:25:"single-page-content-style";s:5:"boxed";s:25:"single-page-sidebar-style";s:7:"default";s:25:"fullwidth_sidebar_support";b:0;s:23:"v4-2-0-update-migration";b:1;s:29:"v4-2-2-core-form-btns-styling";b:0;s:22:"v4-4-0-backward-option";b:0;s:30:"secondary-button-radius-fields";a:6:{s:7:"desktop";a:4:{s:3:"top";i:30;s:5:"right";i:30;s:6:"bottom";i:30;s:4:"left";i:30;}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:65:"ast-dynamic-single-forum-article-featured-image-position-layout-1";s:4:"none";s:65:"ast-dynamic-single-forum-article-featured-image-position-layout-2";s:4:"none";s:58:"ast-dynamic-single-forum-article-featured-image-ratio-type";s:7:"default";s:65:"ast-dynamic-single-topic-article-featured-image-position-layout-1";s:4:"none";s:65:"ast-dynamic-single-topic-article-featured-image-position-layout-2";s:4:"none";s:58:"ast-dynamic-single-topic-article-featured-image-ratio-type";s:7:"default";s:65:"ast-dynamic-single-reply-article-featured-image-position-layout-1";s:4:"none";s:65:"ast-dynamic-single-reply-article-featured-image-position-layout-2";s:4:"none";s:58:"ast-dynamic-single-reply-article-featured-image-ratio-type";s:7:"default";s:64:"ast-dynamic-single-post-article-featured-image-position-layout-1";s:4:"none";s:64:"ast-dynamic-single-post-article-featured-image-position-layout-2";s:4:"none";s:57:"ast-dynamic-single-post-article-featured-image-ratio-type";s:7:"default";s:64:"ast-dynamic-single-page-article-featured-image-position-layout-1";s:4:"none";s:64:"ast-dynamic-single-page-article-featured-image-position-layout-2";s:4:"none";s:57:"ast-dynamic-single-page-article-featured-image-ratio-type";s:7:"default";s:22:"v4-5-0-backward-option";b:0;s:26:"scndry-btn-default-padding";b:0;s:22:"v4-6-0-backward-option";b:0;s:39:"ast-sub-section-author-box-border-width";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:40:"ast-sub-section-author-box-border-radius";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:39:"ast-sub-section-author-box-border-color";s:0:"";s:28:"single-content-images-shadow";b:0;s:21:"ast-font-style-update";b:0;s:22:"v4-6-2-backward-option";b:0;s:33:"ast-dynamic-single-page-structure";a:1:{i:0;s:29:"ast-dynamic-single-page-title";}s:20:"btn-stylings-upgrade";b:0;s:24:"elementor-headings-style";b:0;s:33:"elementor-container-padding-style";b:0;s:14:"font-extras-h1";a:2:{s:11:"line-height";s:3:"1.4";s:16:"line-height-unit";s:2:"em";}s:14:"font-extras-h2";a:2:{s:11:"line-height";s:3:"1.3";s:16:"line-height-unit";s:2:"em";}s:14:"font-extras-h3";a:2:{s:11:"line-height";s:3:"1.3";s:16:"line-height-unit";s:2:"em";}s:14:"font-extras-h4";a:2:{s:11:"line-height";s:3:"1.2";s:16:"line-height-unit";s:2:"em";}s:14:"font-extras-h5";a:2:{s:11:"line-height";s:3:"1.2";s:16:"line-height-unit";s:2:"em";}s:14:"font-extras-h6";a:2:{s:11:"line-height";s:4:"1.25";s:16:"line-height-unit";s:2:"em";}s:34:"global-headings-line-height-update";b:1;s:37:"single_posts_pages_heading_clear_none";b:0;s:21:"elementor-btn-styling";b:0;s:52:"remove_single_posts_navigation_mobile_device_padding";b:1;s:27:"hb-header-bg-obj-responsive";a:3:{s:7:"desktop";a:10:{s:16:"background-color";s:7:"#fffefe";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";s:15:"background-type";s:5:"color";}s:6:"tablet";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}s:6:"mobile";a:9:{s:16:"background-color";s:0:"";s:16:"background-image";s:0:"";s:17:"background-repeat";s:6:"repeat";s:19:"background-position";s:13:"center center";s:15:"background-size";s:4:"auto";s:21:"background-attachment";s:6:"scroll";s:12:"overlay-type";s:0:"";s:13:"overlay-color";s:0:"";s:16:"overlay-gradient";s:0:"";}}s:18:"hb-header-main-sep";i:0;s:38:"section-primary-header-builder-padding";a:6:{s:7:"desktop";a:4:{s:3:"top";s:1:"0";s:5:"right";s:1:"0";s:6:"bottom";s:1:"0";s:4:"left";s:1:"0";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:37:"section-primary-header-builder-margin";a:6:{s:7:"desktop";a:4:{s:3:"top";s:2:"16";s:5:"right";s:2:"16";s:6:"bottom";s:2:"16";s:4:"left";s:2:"16";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:20:"hbb-header-separator";i:0;s:36:"section-below-header-builder-padding";a:6:{s:7:"desktop";a:4:{s:3:"top";s:1:"0";s:5:"right";s:0:"";s:6:"bottom";s:1:"0";s:4:"left";s:0:"";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:35:"section-below-header-builder-margin";a:6:{s:7:"desktop";a:4:{s:3:"top";s:1:"0";s:5:"right";s:2:"16";s:6:"bottom";s:0:"";s:4:"left";s:2:"16";}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:16:"body-font-weight";s:7:"inherit";s:16:"body-font-extras";a:6:{s:11:"line-height";s:4:"1.55";s:16:"line-height-unit";s:2:"em";s:14:"letter-spacing";s:0:"";s:19:"letter-spacing-unit";s:2:"px";s:14:"text-transform";s:0:"";s:15:"text-decoration";s:0:"";}s:14:"font-family-h1";s:21:"\'Poppins\', sans-serif";s:14:"font-weight-h1";s:3:"600";s:27:"header-menu1-submenu-border";a:4:{s:3:"top";s:1:"0";s:6:"bottom";i:0;s:4:"left";i:0;s:5:"right";i:0;}s:40:"header-menu1-submenu-container-animation";s:10:"slide-down";s:13:"header-html-2";s:0:"";s:24:"header-search-icon-space";a:3:{s:7:"desktop";i:28;s:6:"tablet";i:18;s:6:"mobile";i:18;}s:19:"header-search-width";a:3:{s:7:"desktop";i:600;s:6:"tablet";s:0:"";s:6:"mobile";s:0:"";}s:29:"header-search-box-placeholder";s:5:"Cerca";s:22:"header-search-box-type";s:11:"full-screen";s:19:"button-preset-style";s:9:"button_03";s:29:"secondary-button-preset-style";s:9:"button_03";s:30:"secondary-theme-button-padding";a:6:{s:7:"desktop";a:4:{s:3:"top";i:10;s:5:"right";i:20;s:6:"bottom";i:10;s:4:"left";i:20;}s:6:"tablet";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:6:"mobile";a:4:{s:3:"top";s:0:"";s:5:"right";s:0:"";s:6:"bottom";s:0:"";s:4:"left";s:0:"";}s:12:"desktop-unit";s:2:"px";s:11:"tablet-unit";s:2:"px";s:11:"mobile-unit";s:2:"px";}s:47:"secondary-theme-button-border-group-border-size";a:4:{s:3:"top";i:0;s:5:"right";i:0;s:6:"bottom";i:0;s:4:"left";i:0;}s:14:"font-family-h2";s:21:"\'Poppins\', sans-serif";s:14:"font-weight-h2";s:3:"600";s:11:"blog-layout";s:13:"blog-layout-4";s:26:"archive-post-content-style";s:5:"boxed";s:33:"header-menu1-menu-hover-animation";s:4:"zoom";s:32:"header-menu1-submenu-item-border";b:1;s:16:"hb-footer-column";s:1:"3";}',
        ['allowed_classes' => true]
    );

}

/**
 * Change the color scheme of all users on a theme change. Color scheme is different for xtecadmin
 * and the other users. The general case is having a grey theme for Astra and black for the others.
 *
 * @param string $new_theme
 * @return void
 */
function update_admin_colors(string $new_theme): void {

    $color_scheme_users = ($new_theme === 'Astra') ? 'light' : 'fresh';
    $color_scheme_super_admin = 'sunrise';

    $users = get_users();

    foreach ($users as $user) {
        if ($user->data->user_login === get_xtecadmin_username()) {
            update_user_meta($user->ID, 'admin_color', $color_scheme_super_admin);
        } else {
            update_user_meta($user->ID, 'admin_color', $color_scheme_users);
        }
    }

}

/* Theme migration stuff ends here */

/**
 * Add SVG support to WordPress Media
 */
add_filter('upload_mimes', function ($file_types) {

    $new_filetypes = [];
    $new_filetypes['svg'] = 'image/svg+xml';

    return array_merge($file_types, $new_filetypes);

});

/**
 * Fixed compatibility issue between buddypress-docs and Astra theme:
 *   https://wordpress.org/support/topic/astra-theme-issue-3/
 */
add_filter('get_the_archive_description', function ($description) {

    if (!is_post_type_archive('bp_doc')) {
        return $description;
    }
    return 'Docs directory';

});

/**
 * Force the configuration of the front page to use always a page, so the option of using the
 * latest posts is disallowed.
 *
 * @author Xavi Meler
 */
add_filter('pre_option_show_on_front', function () {
    return 'page';
});

// Admin: Remove Koko Analytics from dashboard menu.
add_action('admin_menu', function () {
    remove_submenu_page('index.php', 'koko-analytics');
}, 11);

// Admin: Add Koko Analytics to Options menu.
add_action('admin_menu', function () {

    add_options_page(
        __('Koko Analytics', 'koko-analytics'),
        __('Koko Analytics', 'koko-analytics'),
        'manage_options',
        'koko-analytics',
        static function () {
            if (class_exists('KokoAnalytics\Admin')) {
                $admin = new KokoAnalytics\Admin();
                $admin->show_dashboard_page();
            }
        },
        5
    );

});

// Gutenberg: Change default configuration.
add_filter('block_editor_settings_all', function ($settings) {

    // Disable the Openverse Media Category.
    $settings['enableOpenverseMediaCategory'] = false;

    // Don't create tabs in the block inspector.
    $settings['blockInspectorTabs'] = ['default' => false];

    return $settings;

});

// Gutenberg: Disable the block directory (a new way for block editor users to discover, test and
//            install new blocks on their website).
remove_action('enqueue_block_editor_assets', 'wp_enqueue_editor_block_directory_assets');

// Gutenberg: Disable the remote block patterns. This reduces drastically the number block patterns.
//add_filter('should_load_remote_block_patterns', '__return_false');

/**
 * Static page to display some plugin's important links
 *
 * @author J. Alejandro Escobar
 */
add_action('admin_menu', 'plugin_links');
add_action('admin_menu', 'remove_plugin_menus', 999);

function plugin_links(): void {

    include_once ABSPATH . 'wp-admin/includes/plugin.php';

    add_menu_page(
        __('Plugins'),
        __('Plugins'),
        'manage_options',
        'xtec-plugins-options',
        'plugin_options_page',
        '',
        65
    );

}

function plugin_options_page(): void {

    $plugins = [
        [
            'title' => 'H5P',
            'description' => 'Permet inserir contingut interactiu atractiu.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/h5p-integrat/',
            'links' => [
                'Configuració' => 'options-general.php?page=h5p_settings',
                'Llista de H5P creats' => 'options-general.php?page=h5p',
                'Resultats' => 'options-general.php?page=h5p_results',
                'Afegeix nou' => 'options-general.php?page=h5p_new',
            ],
            'plugin_file' => 'h5p/h5p.php',
        ],
        [
            'title' => 'WP Telegram',
            'description' => 'Aquesta extensió permet enviar publicacions a Telegram quan es publica un article o pàgina nova.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/canal-de-telegram/',
            'links' => [
                'Configuració' => 'options-general.php?page=wptelegram',
            ],
            'plugin_file' => 'wordpress-telegram/wptelegram.php',
        ],
        [
            'title' => 'Gtranslate',
            'description' => 'Permet incorporar un selector d\'idioma per fer la traducció automàtica del web a una gran quantitat d\'idiomes.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/g-translate/',
            'links' => [
                'Configuració' => 'options-general.php?page=gtranslate_options',
            ],
            'plugin_file' => 'gtranslate/gtranslate.php',
        ],
        [
            'title' => 'Getwid',
            'description' => 'Permet configurar blocs extra com Instagram, que podeu inserir a pàgines i articles.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/editor-gutenberg/bloc-instagram/',
            'links' => [
                'Configuració' => 'options-general.php?page=getwid',
            ],
            'plugin_file' => 'getwid/getwid.php',
        ],
        [
            'title' => 'WP Social Login',
            'description' => 'Permet habilitar l\'accés dels usuaris mitjançant Google o Moodle. També permet restringir els accessos.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/wp-social-login/',
            'links' => [
                'Configuració' => 'options-general.php?page=wordpress-social-login',
            ],
            'plugin_file' => 'wordpress-social-login/wp-social-login.php',
        ],
        [
            'title' => 'AddToAny',
            'description' => 'Afegeix botons als articles i pàgines per facilitar la compartició dels continguts a les xarxes socials.',
            'more_info' => 'https://projectes.xtec.cat/digital/serveis-digitals/nodes/add-to-any/',
            'links' => [
                'Configuració' => 'options-general.php?page=addtoany',
            ],
            'plugin_file' => 'add-to-any/add-to-any.php',
        ],
    ];

    echo '<div class="wrap" style="display:flex; flex-wrap:wrap;">';

    foreach ($plugins as $plugin) {
        if (is_plugin_active($plugin['plugin_file'])) {
            echo '<div style="width: 250px; height: auto; min-height: 200px; padding: 15px; margin: 10px; box-sizing: border-box; border: 1px solid #dddddd; border-radius: 5px;">';
            echo '<h3 style="height: 25px;">' . esc_html($plugin['title']) . '</h3>';
            echo '<p style="margin-bottom: 20px;">' . esc_html($plugin['description']) . '</p>';
            echo '<p style="margin: 3px 0 3px 0;"><a href="' . esc_url($plugin['more_info']) . '">' . esc_html(__('More information', 'agora-functions')) . '</a></p>';
            foreach ($plugin['links'] as $link_text => $link_url) {
                echo '<p style="margin: 3px 0 3px 0;"><a href="' . esc_url(admin_url($link_url)) . '">' . esc_html($link_text) . '</a></p>';
            }
            echo '</div>';
        }
    }

    echo '</div>';

}

function remove_plugin_menus(): void {

    // Remove H5P options.
    remove_submenu_page('options-general.php', 'h5p');
    remove_submenu_page('options-general.php', 'h5p_settings');
    remove_submenu_page('options-general.php', 'h5p_new');
    remove_submenu_page('options-general.php', 'h5p_libraries');
    remove_submenu_page('options-general.php', 'h5p_results');

    // Remove WP Telegram menu.
    remove_menu_page('wptelegram');

    // Remove Gtranslate option.
    remove_submenu_page('options-general.php', 'gtranslate_options');

    // Remove Getwid option.
    remove_submenu_page('options-general.php', 'getwid');

    // Remove WP Social Login option.
    remove_submenu_page('options-general.php', 'wordpress-social-login');

    // Remove AddToAny option.
    remove_submenu_page('options-general.php', 'addtoany');

    // Remove One Click Accessibility option.
    if (!is_xtec_super_admin()) {
        remove_menu_page('accessibility-settings');
    }
}

// On plugin xtec-booking, remove meta boxes from the page to create a new calendar.
add_action('do_meta_boxes', function () {

    remove_meta_box('astra_settings_meta_box', 'calendar', 'side');
    remove_meta_box('postcustom', 'calendar', 'normal');

});
