<?php
/**
 * Plugin Name: Booking Classrooms and resources plugin
 * Plugin Uri:
 * Description: Allow booking classrooms and diferents resources into bootstrap calendar
 * Version: 1.0
 * Author: Xavier Nieto
 * Author URI:
 * License: GPLv2
 **/

include(plugin_dir_path(__FILE__) . 'includes/resources.php');
include(plugin_dir_path(__FILE__) . 'includes/booking.php');
include(plugin_dir_path(__FILE__) . 'includes/calendar.php');
include(plugin_dir_path(__FILE__) . 'includes/actions_calendar.php');
include(plugin_dir_path(__FILE__) . 'includes/cron.php');

const XTEC_RESOURCES_STATUS_KEY = 'a_xtec_resources_status';
const XTEC_BOOKING_RESOURCE_KEY = 'a_xtec-booking-resource';
const XTEC_BOOKING_START_TIME_KEY = 'a_xtec-booking-start-time';
const XTEC_BOOKING_FINISH_TIME_KEY = 'a_xtec-booking-finish-time';
const XTEC_BOOKING_DATA_KEY = 'a_xtec-booking-data';

// Load language file.
add_action('init', function (): void {
    load_plugin_textdomain('xtec-booking', false, plugin_basename(__DIR__ . '/i18n'));
});

// CREATE CUSTOM POST TYPE
function create_post_type(): void
{
    $labels = [
            'name' => __('Bookings', 'xtec-booking'),
            'singular_name' => __('Bookings', 'xtec-booking'),
            'menu_name' => __('Bookings', 'xtec-booking'),
            'parent_item_colon' => __('Parent Booking', 'xtec-booking'),
            'all_items' => __('All Bookings', 'xtec-booking'),
            'view_item' => __('View Booking', 'xtec-booking'),
            'add_new_item' => __('Add New Booking', 'xtec-booking'),
            'add_new' => __('Add New', 'xtec-booking'),
            'edit_item' => __('Edit Booking', 'xtec-booking'),
            'update_item' => __('Update Booking', 'xtec-booking'),
            'search_items' => __('Search Booking', 'xtec-booking'),
            'not_found' => __('Not Found', 'xtec-booking'),
            'not_found_in_trash' => __('Not found in Trash', 'xtec-booking'),
    ];

    $user = wp_get_current_user();

    $capabilities = [
            'delete_pages' => 'delete_pages_bookings',
            'edit_posts' => 'edit_posts_bookings',
            'delete_posts' => 'delete_posts_bookings',
            'publish_posts' => 'publish_posts_bookings',
            'edit_published_posts' => 'edit_published_posts_bookings',
            'delete_published_posts' => 'delete_published_posts_bookings',
    ];

    if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)) {
        $supports = ['title', 'editor', 'author'];
    } else {
        $supports = ['title', 'editor'];
    }

    // Set other options for Custom Post Type
    $args = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => false,
            'show_ui' => true,
            'capability_type' => 'post',
            'capabilities' => $capabilities,
            'map_meta_cap' => true,
            'menu_position' => 28,
            'menu_icon' => 'dashicons-book',
            'label' => __('Booking', 'xtec-booking'),
            'description' => __('Resources to booking', 'xtec-booking'),
            'register_meta_box_cb' => 'xtec_add_metabox_booking',
    ];

    register_post_type('calendar_booking', $args);

    // Resources only show to administrator users.
    if (in_array('administrator', $user->roles)) {

        // Set UI labels for Custom Post Type
        $labels = [
                'name' => __('Add Resource', 'xtec-booking'),
                'singular_name' => __('Add Resource', 'xtec-booking'),
                'menu_name' => __('Add Resources', 'xtec-booking'),
                'parent_item_colon' => __('Parent Booking', 'xtec-booking'),
                'all_items' => __('Resources', 'xtec-booking'),
                'view_item' => __('View Resource', 'xtec-booking'),
                'add_new_item' => __('Add New Resource', 'xtec-booking'),
                'add_new' => __('Add New', 'xtec-booking'),
                'edit_item' => __('Edit Resource', 'xtec-booking'),
                'update_item' => __('Update Resource', 'xtec-booking'),
                'search_items' => __('Search Resource', 'xtec-booking'),
                'not_found' => __('Not Found', 'xtec-booking'),
                'not_found_in_trash' => __('Not found in Trash', 'xtec-booking'),
        ];

        // Set other options for Custom Post Type
        $args = [
                'labels' => $labels,
                'supports' => ['title', 'editor', 'thumbnail'],
                'public' => false,
                'show_ui' => true,
                'capability_type' => 'page',
                'show_in_menu' => 'edit.php?post_type=calendar_booking',
                'register_meta_box_cb' => 'xtec_add_metabox_resource',
        ];

        register_post_type('calendar_resources', $args);
    }
}

add_action('init', 'create_post_type');

// ADD CALENDAR PAGE
function xtec_booking_calendar_page(): void
{
    $user = wp_get_current_user();
    if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)
            || in_array('author', $user->roles) || in_array('contributor', $user->roles)
            || in_array('xtec_teacher', $user->roles)) {
        add_submenu_page(
                'edit.php?post_type=calendar_booking',
                'Custom Post Type Admin',
                __('Calendar', 'xtec-booking'),
                'edit_posts',
                basename(__FILE__),
                'xtec_booking_show_calendar_page'
        );
    }
}

add_action('admin_menu', 'xtec_booking_calendar_page');

// ERROR MESSAGE
add_filter('wp_update_post', 'xtec_save');

function xtec_post_location_filter($location): string
{
    remove_filter('redirect_post_location', __FUNCTION__, 99);
    return add_query_arg('message', 99, $location);
}

add_filter('post_updated_messages', 'xtec_updated_messages_filter');
function xtec_updated_messages_filter($messages)
{
    if (isset($_GET['message']) && (int)$_GET['message'] === 99) {
        ?>
        <div id="message" class="notice notice-error is-dismissible xtec-red">
            <p class="xtec-white">
                <?php
                _e('Booking not allowed. The resource is not allowed to data selected. Please, try other data.',
                        'xtec-booking'); ?>
            </p>
        </div>
        <?php
        return '';
    }

    $messages['post'][99] =
            __('Booking not allowed. The resource is not allowed to data selected. Please, try other data.',
                    'xtec-booking');

    return $messages;
}

// SAVE INPUTS CALENDARS AND STATUS
function xtec_save_post_meta($data)
{
    if (isset($_POST[XTEC_RESOURCES_STATUS_KEY])) {
        // Add field post meta - Status Resource
        xtec_save_resource_status($_POST['post_ID']);
    }

    if (isset($_POST[XTEC_BOOKING_RESOURCE_KEY])) {
        $data = xtec_handle_booking($data);
    }

    return $data;
}

/**
 * Add field post meta - Status Resource
 */
function xtec_save_resource_status($post_id): void
{
    update_post_meta($post_id, XTEC_RESOURCES_STATUS_KEY, $_POST[XTEC_RESOURCES_STATUS_KEY]);
    update_post_meta($post_id, '_xtec_resources_color', $_POST['_xtec_resources_color']);
}

/**
 * Handle booking logic
 */
function xtec_handle_booking($data)
{
    // start_time and finish_time must have correct values; if not, booking couldn't be published
    if (xtec_booking_times_invalid()) {
        $data['post_status'] = 'pending';
        add_filter('redirect_post_location', 'xtec_post_location_filter', 99, 2);
    }

    $current_user_label = xtec_get_current_user_label();
    $dataBooking = xtec_build_booking_data($current_user_label);

    // CHECK AVAILABLE
    if (!check_avalailable_dates($dataBooking, $_POST['post_ID'])) {
        $data['post_status'] = 'pending';
        add_filter('redirect_post_location', 'xtec_post_location_filter', 99, 2);
    }

    $idBooking = $_POST['ID'];
    update_post_meta($idBooking, XTEC_BOOKING_RESOURCE_KEY, $_POST[XTEC_BOOKING_RESOURCE_KEY]);
    update_post_meta($idBooking, XTEC_BOOKING_DATA_KEY, $dataBooking);

    return $data;
}

/**
 * Validate booking start and finish times
 */
function xtec_booking_times_invalid(): bool
{
    return empty($_POST[XTEC_BOOKING_START_TIME_KEY])
            || $_POST[XTEC_BOOKING_START_TIME_KEY] === '__:__'
            || empty($_POST[XTEC_BOOKING_FINISH_TIME_KEY]);
}

/**
 * Get current user label for post title
 */
function xtec_get_current_user_label(): string
{
    global $current_user;

    if ($current_user->user_firstname && $current_user->user_lastname) {
        return '(' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ')';
    }

    return '(' . $current_user->user_login . ')';
}

/**
 * Build booking data array
 */
function xtec_build_booking_data($user_label): array
{
    return [
            'post_title' => $_POST['post_title'] . ' ' . $user_label,
            'content' => $_POST['post_content'],
            XTEC_BOOKING_RESOURCE_KEY => $_POST[XTEC_BOOKING_RESOURCE_KEY],
            '_xtec-booking-start-date' => $_POST['_xtec-booking-start-date'],
            '_xtec-booking-finish-date' => $_POST['_xtec-booking-finish-date'],
            '_xtec-booking-day-monday' => isset($_POST['_xtec-booking-day-monday']),
            '_xtec-booking-day-tuesday' => isset($_POST['_xtec-booking-day-tuesday']),
            '_xtec-booking-day-wednesday' => isset($_POST['_xtec-booking-day-wednesday']),
            '_xtec-booking-day-thursday' => isset($_POST['_xtec-booking-day-thursday']),
            '_xtec-booking-day-friday' => isset($_POST['_xtec-booking-day-friday']),
            '_xtec-booking-day-saturday' => isset($_POST['_xtec-booking-day-saturday']),
            '_xtec-booking-day-sunday' => isset($_POST['_xtec-booking-day-sunday']),
            XTEC_BOOKING_START_TIME_KEY => $_POST[XTEC_BOOKING_START_TIME_KEY],
            XTEC_BOOKING_FINISH_TIME_KEY => $_POST[XTEC_BOOKING_FINISH_TIME_KEY],
    ];
}

add_filter('wp_insert_post_data', 'xtec_save_post_meta');

// DELETE POST META
function xtec_delete_save_post($postid)
{
    $data = get_post_meta($postid);

    if (isset($data[XTEC_BOOKING_RESOURCE_KEY], $data[XTEC_BOOKING_DATA_KEY])) {
        delete_post_meta($postid, XTEC_BOOKING_RESOURCE_KEY);
        delete_post_meta($postid, XTEC_BOOKING_DATA_KEY);
        wp_delete_post($postid, true);
    }

    return $postid;
}

add_action('wp_trash_post', 'xtec_delete_save_post');

// LOAD CSS AND JS FILES
function xtec_booking_load_css_js(): void
{
    global $post;
    $current_url = $_SERVER['REQUEST_URI'];

    $check_calendar_booking = strpos($current_url, 'post_type=calendar_booking&page=xtec-booking.php');
    $check_xtec_booking = strpos($current_url, 'xtec-booking.php');

    if (!empty($post) || $check_calendar_booking !== false || $check_xtec_booking !== false) {

        if ((isset($post->post_type) &&
                ($post->post_type === 'calendar_booking' || $post->post_type === 'calendar_resources'))
                || $check_calendar_booking !== false || $check_xtec_booking !== false) {

            wp_register_script('xtec-booking-js', plugins_url() . '/xtec-booking/js/xtec-booking.js',
                    ['jquery'], '1.1', true);

            // Get language WordPress.
            $language_locale = ['locale' => get_locale()];

            // Add js variable with language
            wp_localize_script('xtec-booking-js', 'locale', $language_locale);

            wp_enqueue_script('xtec-booking-js');
            wp_enqueue_style('style-xtec-booking', plugins_url() . '/xtec-booking/css/xtec-booking.css');

            // LOAD JAVASCRIPT VARIABLES TEXT
            add_action('admin_footer', 'xtec_booking_text_javascript');
        }

        if (isset($post->post_type) && $post->post_type === 'calendar_resources') {
            wp_register_script('xtec-booking-resources-js', plugins_url() .
                    '/xtec-booking/js/xtec-booking-resources.js', ['jquery'], '1.1', true);
            wp_enqueue_script('xtec-booking-resources-js');
        }

        if (isset($post->post_type) && $post->post_type === 'calendar_booking') {
            xtec_booking_calendar_libraries('booking');
        } else {
            if ($check_calendar_booking || $check_xtec_booking) {
                xtec_booking_calendar_libraries();
            }
        }
    }
}

add_action('admin_head', 'xtec_booking_load_css_js');


// LOAD JAVASCRIPT VARIABLES TEXT
function xtec_booking_text_javascript()
{
    $script = '
		<script>
			var dies_reserva = "' . __('You must choose which day of booking.', 'xtec-booking') . '";
			var message_resources = "
				' . __('This resource has associated reserves. To remove it, you must first remove their reservations.',
                    'xtec-booking') . '";
			var confirmText = "' . __('Do you want to permanently delete bookings selected?', 'xtec-booking') . '";
			var textSelectDelete = "' . __('Remove', 'xtec-booking') . '";
			var confirmTextInd = "' . __('Do you want to permanently delete current booking?', 'xtec-booking') . '";
			var days = ["' . __('mo.', 'xtec-booking') . '","' . __('tu.', 'xtec-booking') . '","' .
            __('we.', 'xtec-booking') . '","' . __('th.', 'xtec-booking') . '","' .
            __('fr.', 'xtec-booking') . '","' . __('sa.', 'xtec-booking') . '","' . __('su.', 'xtec-booking') . '"];
		</script>';

    echo $script;
}

// ORDER CUSTOM COLUMNS
function xtec_booking_custom_columns_booking($columns)
{
    $n_columns = [];
    $before = 'date'; // move before this

    foreach ($columns as $key => $value) {
        if ($key == $before) {
            $n_columns['description'] = __('Description', 'xtec-booking');
            $n_columns['author'] = __('Author', 'xtec-booking');
            $n_columns['resource'] = __('Resource', 'xtec-booking');
        }
        $n_columns[$key] = $value;
    }

   return $n_columns;
}

add_filter('manage_edit-calendar_booking_columns', 'xtec_booking_custom_columns_booking');

// ADD CUSTOM COLUMNS
function xtec_booking_custom_columns_resources($columns): array
{
    $n_columns = [];
    $before = 'date'; // move before this

    foreach ($columns as $key => $value) {
        if ($key == $before) {
            $n_columns['description'] = __('Description', 'xtec-booking');
            $n_columns['status'] = __('Status', 'xtec-booking');
        }
        $n_columns[$key] = $value;
    }
    return $n_columns;
}

add_filter('manage_edit-calendar_resources_columns', 'xtec_booking_custom_columns_resources');

// FILL CUSTOM COLUMNS
function xtec_custom_columns($name)
{
    global $post;
 
    switch ($name) {
 
        case 'resource':
            $views = get_post_meta($post->ID, XTEC_BOOKING_RESOURCE_KEY, true);
            $status = get_post_meta($views, XTEC_RESOURCES_STATUS_KEY, true);
            $views = get_post($views);
            $wpStatus = get_post_status($views);
            if ($status === 'inactive') {
                echo $views->post_title . '&nbsp;&nbsp;&nbsp;&nbsp;<br><small style="color:red;font-size:10px">
					 <strong>' . __('Not available resource', 'xtec-booking') . '</strong></small>';
            } else if ($wpStatus === 'private') {
                $roles = wp_get_current_user();
                if (!in_array('administrator', $roles->roles)) {
                    echo $views->post_title . '&nbsp;&nbsp;&nbsp;&nbsp;<br>
                     <small style="color:red;font-size:10px">
                     <strong>' . __('Only admin users', 'xtec-booking') . '</strong></small>';
                } else {
                    echo $views->post_title;
                }
            } else {
                echo $views->post_title;
            }
            break;

        case 'status':
            $views = get_post_meta($post->ID, XTEC_RESOURCES_STATUS_KEY, true);
            switch ($views) {
                case 'all_users':
                    _e('Available', 'xtec-booking');
                    break;
                case 'inactive':
                    _e('Not available', 'xtec-booking');
                    break;
                default:
                    break;
            }
            break;

        case 'description':
            echo strip_tags($post->post_content);
            break;

        default:
            break;
    }
}

add_action('manage_posts_custom_column', 'xtec_custom_columns');

// PLUGIN ACTIVATION
register_activation_hook(__FILE__, static function (): void {
    xtec_booking_active_plugin();
});

// PLUGIN DEACTIVATION
register_deactivation_hook(__FILE__, static function (): void {
    xtec_booking_deactive_plugin();
});

// AJAX FUNCTION TO CHECK BOOKINGS
function resource_booking($postid): void
{
    if (isset($_REQUEST['data'])) {

        $postid = $_REQUEST['data'];

        global $wpdb;

        $result = $wpdb->get_results(
                $wpdb->prepare("SELECT * FROM wp_postmeta
                                      WHERE meta_key = XTEC_BOOKING_RESOURCE_KEY
                                      AND meta_value = %d", $postid));

        if (count($result) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }

        die();
    }
}

add_action('wp_ajax_resource_booking', 'resource_booking');
add_action('wp_ajax_nopriv_resource_booking', 'resource_booking');

function xtec_booking_admin_posts_filter_restrict_manage_posts()
{
    global $wpdb;
    $type = $_GET['post_type'] ?? 'post';

    if ('calendar_resources' === $type) {
        ?>
        <select id="admin_filter" name="ADMIN_FILTER_FIELD_VALUE" class="postform">
            <option value=""><?php
                _e('Filter by status', 'xtec-booking'); ?></option>
            <option value="all_users"><?php
                _e('Available', 'xtec-booking'); ?></option>
            <option value="inactive"><?php
                _e('Not available', 'xtec-booking'); ?></option>
        </select>
        <?php
    }

    if ('calendar_booking' === $type) {
        $user = wp_get_current_user();

        if (in_array('administrator', $user->roles) || in_array('editor', $user->roles)) {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'calendar_resources'
										 and ( post_status = 'publish' OR post_status = 'private' )
										 ORDER BY post_title ASC");
        } else {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'calendar_resources'
										 and post_status = 'publish'
										 ORDER BY post_title ASC");
        }
        ?>
        <select id="admin_filter" name="ADMIN_FILTER_FIELD_VALUE" class="postform">
            <option value=""><?php
                _e('All resources', 'xtec-booking'); ?></option>
            <?php
            foreach ($posts as $post) {
                if (isset($_GET['ADMIN_FILTER_FIELD_VALUE']) && $_GET['ADMIN_FILTER_FIELD_VALUE'] == $post->ID) {
                    ?>
                    <option value="<?php
                    echo $post->ID ?>" selected><?php
                        echo $post->post_title ?></option>
                    <?php
                } else {
                    ?>
                    <option value="<?php
                    echo $post->ID ?>"><?php
                        echo $post->post_title ?></option>
                    <?php
                }
            }
            ?>
        </select>
        <?php
    }

}

add_action('restrict_manage_posts', 'xtec_booking_admin_posts_filter_restrict_manage_posts');

function xtec_booking_posts_filter($query)
{
    global $pagenow;
    $type = 'post';
    if (isset($_GET['post_type'])) {
        $type = $_GET['post_type'];
    }

    if ('calendar_resources' == $type && is_admin() && $pagenow == 'edit.php'
            && isset($_GET['ADMIN_FILTER_FIELD_VALUE'])
            && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = XTEC_RESOURCES_STATUS_KEY;
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }

    if ('calendar_booking' == $type && $pagenow == 'edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE'])
            && $_GET['ADMIN_FILTER_FIELD_VALUE'] != '') {
        $query->query_vars['meta_key'] = XTEC_BOOKING_RESOURCE_KEY;
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
}

add_filter('parse_query', 'xtec_booking_posts_filter');

// CUSTOMIZE MESSAGES LISTS CUSTOM POST TYPE
function xtec_booking_updated_messages($messages)
{
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));

    if (strpos($current_url, 'post_type=calendar_booking')) {

        $messages['post'] = [
                'updated' => '%s ' . __("booking updated", "xtec-booking"),
                'locked' => '%s ' . __("booking not updated, somebody is editing them", "xtec-booking"),
                'deleted' => __("Deleted permanently booking.", "xtec-booking"),
                'trashed' => __("Deleted permanently booking.", "xtec-booking"),
        ];

        // HIDEN PERMALINK TO MESSAGE UNTRASH ACTION
        echo '
                <style>
                    #message p a{
                        display: none;
                    }
                </style>
            ';

    } else if (strpos($current_url, 'post_type=calendar_resources')) {

        $messages['post'] = [
                'updated' => '%s ' . __("resources updated", "xtec-booking"),
                'locked' => '%s ' . __("resources not updated, somebody is editing them", "xtec-booking"),
                'deleted' => __("Deleted permanently resources", "xtec-booking"),
                'trashed' => __("Deleted resources", "xtec-booking"),
        ];

    }

    return $messages;
}

add_filter('bulk_post_updated_messages', 'xtec_booking_updated_messages');

// CUSTOMIZE MESSAGES CREATE/EDIT CUSTOM POST TYPE
function codex_book_updated_messages($messages)
{
    global $post;

    if ($post->post_type == 'calendar_booking') {

        $messages['post'][1] = __("Book updated", "xtec-booking");
        $messages['post'][4] = __("Book updated", "xtec-booking");
        $messages['post'][6] = __("Book published", "xtec-booking");
        $messages['post'][7] = __("Book saved", "xtec-booking");
        $messages['post'][8] = __("Book sended", "xtec-booking");
        $messages['post'][10] = __("Updated draft of the book", "xtec-booking");

    }

    if ($post->post_type == 'calendar_resources') {

        $messages['post'][1] = __("Resource updated", "xtec-booking");
        $messages['post'][4] = __("Resource updated", "xtec-booking");
        $messages['post'][6] = __("Resource published", "xtec-booking");
        $messages['post'][7] = __("Resource saved", "xtec-booking");
        $messages['post'][8] = __("Resource sended", "xtec-booking");
        $messages['post'][10] = __("Updated draft of the resource", "xtec-booking");

    }

    return $messages;
}

add_filter('post_updated_messages', 'codex_book_updated_messages');

// AJAX FUNCTION TO GET RESOURCES
function resource_selected($postid)
{
    $requests = xtec_booking_get_events($_REQUEST['data']);
    echo $requests;

    die();
}

add_action('wp_ajax_resource_selected', 'resource_selected');
add_action('wp_ajax_nopriv_resource_selected', 'resource_selected');


// AJAX FUNCTION TO GET RESOURCES EVENT CALENDAR
function get_event_modal($postid)
{
    $requests = xtec_booking_get_event($_REQUEST['data']);
    echo $requests;

    die();
}

add_action('wp_ajax_get_event_modal', 'get_event_modal');
add_action('wp_ajax_nopriv_get_event_modal', 'get_event_modal');

// AJAX FUNCTION TO GET RESOURCES EVENT CALENDAR
function get_thumbnail_resource($postid)
{
    $requests = xtec_booking_get_thumbnail_resource($_REQUEST['data']);
    print_r($requests);

    die();
}

add_action('wp_ajax_get_thumbnail_resource', 'get_thumbnail_resource');
add_action('wp_ajax_nopriv_get_thumbnail_resource', 'get_thumbnail_resource');

// REMOVE EDIT IN LINE ACTION
function my_action_row($actions, $post)
{
    if ($post->post_type == 'calendar_booking') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}

add_filter('post_row_actions', 'my_action_row', 10, 2);

// MODIFY TITLE CALENDAR PAGE
function xtec_booking_calendar_title($title)
{
    $current_url = $_SERVER['REQUEST_URI'];
    if (strpos($current_url, 'post_type=calendar_booking&page=xtec-booking.php')) {
        $title = __('Booking calendar', 'xtec-booking');
    }

    return $title;
}

add_action('admin_title', 'xtec_booking_calendar_title');

// CRON TO REMOVE OLD BOOKINGS
add_action('cron_xtec_booking', 'check_old_bookings');
