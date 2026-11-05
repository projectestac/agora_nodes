<?php
/**
 * Plugin Name: Booking Classrooms and resources
 * Plugin URI:
 * Description: Allow booking classrooms and diferents resources into bootstrap calendar
 * Version: 1.2
 * Author: Xavier Nieto
 * Author: Toni Ginard
 * Author URI:
 * Licence: GPLv2
 */

// Version of the plugin's own JS and CSS files. Increase it whenever they change, so browsers don't use cached copies.
const XTEC_BOOKING_ASSETS_VERSION = '1.3';

include_once plugin_dir_path(__FILE__) . 'includes/resources.php';
include_once plugin_dir_path(__FILE__) . 'includes/booking.php';
include_once plugin_dir_path(__FILE__) . 'includes/calendar.php';
include_once plugin_dir_path(__FILE__) . 'includes/actions_calendar.php';
include_once plugin_dir_path(__FILE__) . 'includes/cron.php';

// Load language file.
add_action('init', function (): void {
    $plugin_rel_path = plugin_basename(__DIR__ . '/i18n');
    load_plugin_textdomain('xtec-booking', false, $plugin_rel_path);
});

// Create custom post type for bookings and resources.
add_action('init', function (): void {
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

    // Set other options for Custom post type.
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

    // Resources are only shown to administrator users.
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

        // Set other options for Custom post type.
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
});

// Add calendar page to menu.
add_action('admin_menu', function (): void {
    $user = wp_get_current_user();
    $allowed_roles = ['administrator', 'editor', 'author', 'contributor', 'xtec_teacher'];

    if (array_intersect($allowed_roles, $user->roles)) {
        // Keep the hook suffix of the calendar page to load its scripts only there. It can't be hardcoded because it
        // depends on the translated menu title of the post type.
        $GLOBALS['xtec_booking_calendar_hook'] = add_submenu_page(
                'edit.php?post_type=calendar_booking',
                'Custom Post Type Admin',
                __('Calendar', 'xtec-booking'),
                'edit_posts',
                basename(__FILE__),
                'xtec_booking_show_calendar_page'
        );
    }
});

// Filter to show message when booking is not allowed.
function xtec_post_location_filter($location): string
{
    remove_filter('redirect_post_location', __FUNCTION__, 99);
    return add_query_arg('message', 99, $location);
}

// Filter to show message when booking is not allowed.
add_filter('post_updated_messages', function () {
    if (isset($_GET['message']) && (int)$_GET['message'] === 99) {
        ?>
        <div id="message" class="notice notice-error is-dismissible xtec-red">
            <p class="xtec-white"><?php
                _e('Booking not allowed. The resource is not allowed to data selected. Please, try other data.', 'xtec-booking'); ?></p>
        </div>
        <?php
    }
});

/**
 * Return the value if it is a valid date in format dd-mm-yyyy, or an empty string otherwise.
 */
function xtec_booking_sanitize_date(mixed $value): string
{
    if (!is_string($value) || !preg_match('/^\d{2}-\d{2}-\d{4}$/', $value)) {
        return '';
    }

    $date = DateTime::createFromFormat('!d-m-Y', $value);

    return ($date && $date->format('d-m-Y') === $value) ? $value : '';
}

/**
 * Return the value if it is a valid time in format hh:mm (24h), or an empty string otherwise.
 */
function xtec_booking_sanitize_time(mixed $value): string
{
    return (is_string($value) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value)) ? $value : '';
}

// Save post meta for resources and bookings
add_filter('wp_insert_post_data', function ($data, $postarr) {
    $post_id = (int)($postarr['ID'] ?? 0);
    $post_type = $data['post_type'] ?? '';

    // Only act when saving a resource or a booking that already exists (the edit form works on an auto-draft) and the
    // current user is allowed to edit it. Any other call to wp_insert_post() must not touch the meta of any post.
    if (!in_array($post_type, ['calendar_resources', 'calendar_booking'], true) || $post_id === 0 ||
        !current_user_can('edit_post', $post_id)) {
        return $data;
    }

    if ($post_type === 'calendar_resources' && isset($_POST[XTEC_RESOURCES_STATUS])) {
        $allowed_status = ['all_users', 'inactive', 'admin_users'];
        $allowed_colors = ['red', 'yellow', 'blue', 'black', 'green', 'purple', 'orange', 'pink', 'brown', 'light_blue'];

        $status = sanitize_key(wp_unslash($_POST[XTEC_RESOURCES_STATUS]));
        $color = sanitize_key(wp_unslash($_POST['_xtec_resources_color'] ?? ''));

        update_post_meta($post_id, XTEC_RESOURCES_STATUS, in_array($status, $allowed_status, true) ? $status : 'all_users');
        update_post_meta($post_id, '_xtec_resources_color', in_array($color, $allowed_colors, true) ? $color : 'blue');
    }

    if ($post_type === 'calendar_booking' && isset($_POST[XTEC_BOOKING_RESOURCE])) {

        // Only store IDs of resources (an inactive one is kept, so the booking still shows it while it is pending).
        $resource_id = absint(wp_unslash($_POST[XTEC_BOOKING_RESOURCE]));
        if (get_post_type($resource_id) !== 'calendar_resources') {
            $resource_id = 0;
        }
        $start_date = xtec_booking_sanitize_date(wp_unslash($_POST['_xtec-booking-start-date'] ?? ''));
        $finish_date = xtec_booking_sanitize_date(wp_unslash($_POST['_xtec-booking-finish-date'] ?? ''));
        $start_time = xtec_booking_sanitize_time(wp_unslash($_POST[XTEC_BOOKING_START_TIME] ?? ''));
        $finish_time = xtec_booking_sanitize_time(wp_unslash($_POST[XTEC_BOOKING_FINISH_TIME] ?? ''));

        // The resource must be one the user can book, and dates and times must be valid. If not, the booking can't be
        // published. Invalid values are not stored.
        if (!xtec_booking_user_can_book_resource($resource_id) ||
            $start_date === '' || $finish_date === '' || $start_time === '' || $finish_time === '') {
            if ($data['post_status'] === 'publish') {
                $data['post_status'] = 'pending';
            }
            add_filter('redirect_post_location', 'xtec_post_location_filter', 99, 2);
        }

        global $current_user;

        if ($current_user->user_firstname !== '' && $current_user->user_lastname !== '') {
            $user = '(' . $current_user->user_firstname . ' ' . $current_user->user_lastname . ')';
        } else {
            $user = '(' . $current_user->user_login . ')';
        }

        $dataBooking = [
                // $data is already unslashed and filtered by WordPress (kses for users without unfiltered_html).
                'post_title' => $data['post_title'] . ' ' . $user,
                'content' => $data['post_content'],
                XTEC_BOOKING_RESOURCE => (string)$resource_id,
                '_xtec-booking-start-date' => $start_date,
                '_xtec-booking-finish-date' => $finish_date,
                '_xtec-booking-day-monday' => isset($_POST['_xtec-booking-day-monday']),
                '_xtec-booking-day-tuesday' => isset($_POST['_xtec-booking-day-tuesday']),
                '_xtec-booking-day-wednesday' => isset($_POST['_xtec-booking-day-wednesday']),
                '_xtec-booking-day-thursday' => isset($_POST['_xtec-booking-day-thursday']),
                '_xtec-booking-day-friday' => isset($_POST['_xtec-booking-day-friday']),
                '_xtec-booking-day-saturday' => isset($_POST['_xtec-booking-day-saturday']),
                '_xtec-booking-day-sunday' => isset($_POST['_xtec-booking-day-sunday']),
                XTEC_BOOKING_START_TIME => $start_time,
                XTEC_BOOKING_FINISH_TIME => $finish_time,
        ];

        // Check availability whenever the booking is going to be published, regardless of the visibility.
        if ($data['post_status'] === 'publish' && !check_available_dates($dataBooking, $post_id)) {
            $data['post_status'] = 'pending';
            add_filter('redirect_post_location', 'xtec_post_location_filter', 99, 2);
        }

        update_post_meta($post_id, XTEC_BOOKING_RESOURCE, (string)$resource_id);
        update_post_meta($post_id, '_xtec-booking-data', $dataBooking);
    }

    return $data;

}, 10, 2);

/**
 * Load scripts and styles only in the screens of the plugin, each one with the files it needs:
 *  - Calendar page: common files and calendar (with the Bootstrap CSS).
 *  - New/edit booking: common files, date/time picker and calendar widget.
 *  - New/edit resource: common files and the script to check the bookings before deleting the resource.
 *  - Lists of bookings and resources: common files (checks before moving to trash).
 */
add_action('admin_enqueue_scripts', function (string $hook_suffix): void {
    $screen = get_current_screen();
    $screen_id = $screen->id ?? '';

    $is_calendar_page = !empty($GLOBALS['xtec_booking_calendar_hook']) && $hook_suffix === $GLOBALS['xtec_booking_calendar_hook'];
    $is_booking_edit = ($screen_id === 'calendar_booking');
    $is_resource_edit = ($screen_id === 'calendar_resources');
    $is_list = in_array($screen_id, ['edit-calendar_booking', 'edit-calendar_resources'], true);

    if (!$is_calendar_page && !$is_booking_edit && !$is_resource_edit && !$is_list) {
        return;
    }

    $plugin_url = plugins_url() . '/xtec-booking';

    // Common files.
    wp_register_script('xtec-booking-js', $plugin_url . '/js/xtec-booking.js', ['jquery'], XTEC_BOOKING_ASSETS_VERSION, true);
    wp_localize_script('xtec-booking-js', 'locale', ['locale' => get_locale()]);
    wp_localize_script('xtec-booking-js', 'xtecBookingAjax', ['nonce' => wp_create_nonce('xtec-booking')]);
    wp_enqueue_script('xtec-booking-js');
    wp_enqueue_style('style-xtec-booking', $plugin_url . '/css/xtec-booking.css', [], XTEC_BOOKING_ASSETS_VERSION);

    // Texts used by the scripts.
    add_action('admin_footer', 'xtec_booking_text_javascript');

    if ($is_resource_edit) {
        wp_enqueue_script('xtec-booking-resources-js', $plugin_url . '/js/xtec-booking-resources.js', ['jquery'], XTEC_BOOKING_ASSETS_VERSION, true);
    }

    if ($is_booking_edit) {
        // Date/time picker. It is a jQuery plugin: it uses the jQuery of WordPress.
        $datetimepicker_url = $plugin_url . '/includes/vendor/datetimepicker';
        wp_enqueue_script('datetimepicker-js', $datetimepicker_url . '/build/jquery.datetimepicker.full.min.js', ['jquery'], '2.5.4', true);
        wp_enqueue_style('datetime-css', $datetimepicker_url . '/jquery.datetimepicker.css', [], '2.5.4');

        xtec_booking_calendar_libraries('booking');
    }

    if ($is_calendar_page) {
        xtec_booking_calendar_libraries();
    }
});

// Load javascript variables with texts for calendar page.
function xtec_booking_text_javascript(): void
{
    echo '
        <script>
            var dies_reserva = "' . __('You must choose which day of booking.', 'xtec-booking') . '";
            var message_resources = "' . __('This resource has associated reserves. To remove it, you must first remove their reservations.', 'xtec-booking') . '";
            var confirmText = "' . __('Do you want to permanently delete bookings selected?', 'xtec-booking') . '";
            var textSelectDelete = "' . __('Remove', 'xtec-booking') . '";
            var confirmTextInd = "' . __('Do you want to permanently delete current booking?', 'xtec-booking') . '";
            var days = ["' . __('mo.', 'xtec-booking') . '","' . __('tu.', 'xtec-booking') . '","' . __('we.', 'xtec-booking') . '","' . __('th.', 'xtec-booking') . '","' . __('fr.', 'xtec-booking') . '","' . __('sa.', 'xtec-booking') . '","' . __('su.', 'xtec-booking') . '"];
        </script>
        ';
}

// Add custom columns to booking list table.
add_filter('manage_edit-calendar_booking_columns', function ($columns) {
    $n_columns = [];
    $before = 'date'; // move before this

    foreach ($columns as $key => $value) {
        if ($key === $before) {
            $n_columns['description'] = __('Description', 'xtec-booking');
            $n_columns['author'] = __('Author', 'xtec-booking');
            $n_columns['resource'] = __('Resource', 'xtec-booking');
        }
        $n_columns[$key] = $value;
    }

    return $n_columns;
});

// Add custom columns to resources list table.
add_filter('manage_edit-calendar_resources_columns', function ($columns) {
    $n_columns = [];
    $before = 'date'; // move before this

    foreach ($columns as $key => $value) {
        if ($key === $before) {
            $n_columns['description'] = __('Description', 'xtec-booking');
            $n_columns['status'] = __('Status', 'xtec-booking');
        }
        $n_columns[$key] = $value;
    }

    return $n_columns;
});

// Fill custom columns for booking and resources list tables.
add_action('manage_posts_custom_column', function ($name) {
    global $post;

    switch ($name) {
        case 'resource':
            $views = get_post_meta($post->ID, XTEC_BOOKING_RESOURCE, true);
            $status = get_post_meta($views, XTEC_RESOURCES_STATUS, true);
            $views = get_post($views);
            $wpStatus = get_post_status($views);

            if ($status === 'inactive') {
                echo esc_html($views->post_title) . '&nbsp;&nbsp;&nbsp;&nbsp;<br><small style="color:red;font-size:10px"><strong>' . __('Not available resource', 'xtec-booking') . '</strong></small>';
            } elseif ($wpStatus === 'private') {
                $roles = wp_get_current_user();
                if (!in_array('administrator', $roles->roles)) {
                    echo esc_html($views->post_title) . '&nbsp;&nbsp;&nbsp;&nbsp;<br><small style="color:red;font-size:10px"><strong>' . __('Only admin users', 'xtec-booking') . '</strong></small>';
                } else {
                    echo esc_html($views->post_title);
                }
            } else {
                echo esc_html($views->post_title);
            }
            break;

        case 'status':
            $views = get_post_meta($post->ID, XTEC_RESOURCES_STATUS, true);
            if ($views === 'inactive') {
                _e('Not available', 'xtec-booking');
            } elseif ($views === 'all_users') {
                _e('Available', 'xtec-booking');
            }
            break;

        case 'description':
            echo esc_html(wp_strip_all_tags($post->post_content));
            break;

        default:
            break;
    }
});

// Plugin activation.
register_activation_hook(__FILE__, static function () {
    xtec_booking_active_plugin();
});

// Plugin deactivation.
register_deactivation_hook(__FILE__, static function () {
    xtec_booking_deactive_plugin();
});

/**
 * Common checks for all the AJAX requests of the plugin: valid nonce and a user allowed to see the calendar. Only
 * logged-in users can reach these functions, as they are not registered with wp_ajax_nopriv_*.
 */
function xtec_booking_ajax_check(): void
{
    check_ajax_referer('xtec-booking', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_die('', '', ['response' => 403]);
    }
}

/**
 * Get the first ID from the AJAX request parameter 'data', which can be a scalar or an array.
 */
function xtec_booking_ajax_get_id(): int
{
    $data = wp_unslash($_REQUEST['data'] ?? 0);

    if (is_array($data)) {
        $data = reset($data);
    }

    return absint($data);
}

// AJAX FUNCTION TO CHECK BOOKINGS
function resource_booking(): void
{
    xtec_booking_ajax_check();

    global $wpdb;

    $count = (int)$wpdb->get_var($wpdb->prepare(
        "SELECT COUNT(*) FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %d",
        XTEC_BOOKING_RESOURCE,
        xtec_booking_ajax_get_id()
    ));

    echo ($count > 0) ? 'false' : 'true';

    die();
}

add_action('wp_ajax_resource_booking', 'resource_booking');

add_action('restrict_manage_posts', function () {
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
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'calendar_resources' and ( post_status = 'publish' OR post_status = 'private' ) ORDER BY post_title ASC");
        } else {
            $posts = $wpdb->get_results("SELECT * FROM wp_posts WHERE post_type = 'calendar_resources' and post_status = 'publish' ORDER BY post_title ASC");
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
                    echo (int)$post->ID ?>" selected><?php
                        echo esc_html($post->post_title) ?></option>
                    <?php
                } else {
                    ?>
                    <option value="<?php
                    echo (int)$post->ID ?>"><?php
                        echo esc_html($post->post_title) ?></option>
                    <?php
                }
            }
            ?>
        </select>
        <?php
    }

});

add_filter('parse_query', function ($query) {
    global $pagenow;
    $type = $_GET['post_type'] ?? 'post';

    if ('calendar_resources' === $type && $pagenow === 'edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) &&
            $_GET['ADMIN_FILTER_FIELD_VALUE'] !== '' && is_admin()) {
        $query->query_vars['meta_key'] = XTEC_RESOURCES_STATUS;
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }

    if ('calendar_booking' === $type && $pagenow === 'edit.php' && isset($_GET['ADMIN_FILTER_FIELD_VALUE']) &&
            $_GET['ADMIN_FILTER_FIELD_VALUE'] !== '') {
        $query->query_vars['meta_key'] = XTEC_BOOKING_RESOURCE;
        $query->query_vars['meta_value'] = $_GET['ADMIN_FILTER_FIELD_VALUE'];
    }
});

// CUSTOMIZE MESSAGES LISTS CUSTOM POST TYPE
add_filter('bulk_post_updated_messages', function ($messages) {
    global $wp;
    $current_url = add_query_arg($wp->query_string, '', home_url($wp->request));

    if (strpos($current_url, 'post_type=calendar_booking')) {

        $messages['post'] = [
                'updated' => '%s ' . __("booking updated", "xtec-booking"),
                'locked' => '%s ' . __("booking not updated, somebody is editing them", "xtec-booking"),
                'deleted' => __("Deleted permanently booking.", "xtec-booking"),
                'trashed' => __("Deleted permanently booking.", "xtec-booking"),
        ];

        // HIDDEN PERMALINK TO MESSAGE UNTRASH ACTION
        echo '
                <style>
                    #message p a{
                        display: none;
                    }
                </style>
            ';

    } elseif (strpos($current_url, 'post_type=calendar_resources')) {
        $messages['post'] = [
                'updated' => '%s ' . __("resources updated", "xtec-booking"),
                'locked' => '%s ' . __("resources not updated, somebody is editing them", "xtec-booking"),
                'deleted' => __("Deleted permanently resources", "xtec-booking"),
                'trashed' => __("Deleted resources", "xtec-booking"),
        ];
    }

    return $messages;
});

// CUSTOMIZE MESSAGES CREATE/EDIT CUSTOM POST TYPE
add_filter('post_updated_messages', function ($messages) {
    global $post;

    if ($post->post_type === 'calendar_booking') {
        $messages['post'][1] = __("Book updated", "xtec-booking");
        $messages['post'][4] = __("Book updated", "xtec-booking");
        $messages['post'][6] = __("Book published", "xtec-booking");
        $messages['post'][7] = __("Book saved", "xtec-booking");
        $messages['post'][8] = __("Book sended", "xtec-booking");
        $messages['post'][10] = __("Updated draft of the book", "xtec-booking");
    }

    if ($post->post_type === 'calendar_resources') {
        $messages['post'][1] = __("Resource updated", "xtec-booking");
        $messages['post'][4] = __("Resource updated", "xtec-booking");
        $messages['post'][6] = __("Resource published", "xtec-booking");
        $messages['post'][7] = __("Resource saved", "xtec-booking");
        $messages['post'][8] = __("Resource sended", "xtec-booking");
        $messages['post'][10] = __("Updated draft of the resource", "xtec-booking");
    }

    return $messages;
});

// AJAX FUNCTION TO GET RESOURCES
function resource_selected(): void
{
    xtec_booking_ajax_check();

    $data = wp_unslash($_REQUEST['data'] ?? 'no-events');

    // Either 'no-events' or a list of resource IDs. xtec_booking_get_events() compares the IDs as strings.
    if ($data !== 'no-events') {
        $data = array_values(array_filter(array_map(static fn($id) => (string)absint($id), (array)$data)));
        if (empty($data)) {
            $data = 'no-events';
        }
    }

    echo xtec_booking_get_events($data);
    die();
}

add_action('wp_ajax_resource_selected', 'resource_selected');

// AJAX FUNCTION TO GET RESOURCES EVENT CALENDAR
function get_event_modal(): void
{
    xtec_booking_ajax_check();

    $event = xtec_booking_get_event(xtec_booking_ajax_get_id());

    if ($event === null) {
        wp_die('', '', ['response' => 404]);
    }

    echo $event;
    die();
}

add_action('wp_ajax_get_event_modal', 'get_event_modal');

// AJAX FUNCTION TO GET RESOURCES EVENT CALENDAR
function get_thumbnail_resource(): void
{
    xtec_booking_ajax_check();

    $resource_id = xtec_booking_ajax_get_id();

    if (!xtec_booking_user_can_see_resource($resource_id)) {
        wp_die('', '', ['response' => 404]);
    }

    echo xtec_booking_get_thumbnail_resource($resource_id);
    die();
}

add_action('wp_ajax_get_thumbnail_resource', 'get_thumbnail_resource');

// REMOVE EDIT IN LINE ACTION
add_filter('post_row_actions', function ($actions, $post) {
    if ($post->post_type === 'calendar_booking') {
        unset($actions['inline hide-if-no-js']);
    }
    return $actions;
}, 10, 2);

// MODIFY TITLE CALENDAR PAGE
add_action('admin_title', function ($title) {
    $current_url = $_SERVER['REQUEST_URI'];
    if (strpos($current_url, 'post_type=calendar_booking&page=xtec-booking.php')) {
        $title = __('Booking calendar', 'xtec-booking');
    }

    return $title;
});

// CRON TO REMOVE OLD BOOKINGS
add_action('cron_xtec_booking', 'check_old_bookings');
