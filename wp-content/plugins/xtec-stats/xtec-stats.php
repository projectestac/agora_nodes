<?php
/**
 * Plugin Name: XTEC Stats
 * Plugin URI: https://github.com/projectestac/agora_nodes/
 * Description: Shows information from the stats table of Nodes in several ways.
 * Version: 1.1
 * Author: Àrea TAC - Departament d'Ensenyament de Catalunya
 * Licence: GPLv3
 * Licence URI: https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: xtec-stats
 */

const DOWN_SUFFIX = 'd-down';
const DATETIME_FIELD_LITERAL = 'wps.datetime DESC';

// Load CSS.
add_action('init', function () {
    wp_enqueue_style('xtec_stats_css', plugins_url('css/xtec-stats.css', __FILE__));
});

// Load javascript.
add_action('admin_head', function () {
    wp_register_script('xtec_stats_js', plugins_url('js/xtec-stats.js', __FILE__), ['jquery'], '1.1', true);
    wp_enqueue_script('xtec_stats_js');
});

// Load language file.
add_action('plugins_loaded', function () {
    load_plugin_textdomain('xtec-stats', false, plugin_basename(__DIR__) . '/languages');
});

// Register widget.
add_action('widgets_init', function () {
    register_widget('xtec_stats_widget');
});

class Xtec_stats_widget extends WP_Widget
{
    // Constructor
    public function __construct()
    {
        $widget_ops = [
                'description' => __('Visits counter', 'xtec-stats'),
                'name' => __('Statistics', 'xtec-stats'),
        ];

        parent::__construct('xtec_stats_widget', 'xtec_stats', $widget_ops);
    }

    /**
     * Outputs the options form on admin.
     *
     * @param array $instance The widget options
     * @return void
     */
    public function form($instance): void
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', 'xtec-stats'); ?>
            </label>
            <br/>
            <input id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"
            />
        </p>
        <?php
    }

    /**
     * Processing widget options on save.
     *
     * @param array $new_instance The new options
     * @param array $old_instance The previous options
     * @return array
     */
    public function update($new_instance, $old_instance): array
    {
        $instance = [];
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags(sanitize_text_field($new_instance['title'])) : '';

        return $instance;
    }

    /**
     * Outputs the content of the widget.
     *
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance): void
    {
        $before_widget = $args['before_widget'];
        $after_widget = $args['after_widget'];

        $visits = get_option('xtec-stats-visits');

        echo $before_widget;

        if (!empty($instance['title'])) {
            $before_title = $args['before_title'];
            $after_title = $args['after_title'];

            echo $before_title . esc_html($instance['title']) . $after_title;
        }

        echo '<div class="xtec-stats">';
        echo $visits;
        echo '</div>';

        echo $after_widget;
    }
}

/**
 * Show data to WP-Stats. To users Admin only shows delete messages. To user xtecadmin shows all data.
 *
 * @author @xaviernietosanchez
 */

// Check over click to export CSV.
add_action('admin_init', 'xtec_stats_generate_csv');

// Generate javascript variables.
function xtec_stats_var_js($limit, $placeholder_username, $placeholder_content): void
{
    echo '<script>
            var xtec_stats_limitResults = ' . $limit . ';
            var xtec_stats_username = "' . $placeholder_username . '";
            var xtec_stats_content = "' . $placeholder_content . '";
          </script>';
}

// Generate Query
function xtec_stats_get_results($whereSQL, $offset, $limit, $searchContent, $field, $searchType)
{
    // Construct Query.
    if (false === $whereSQL) {
        $dataResults = xtec_get_default_stats($offset, $limit);
    } else {
        $dataResults = xtec_get_filtered_stats($offset, $limit, $searchContent, $field, $searchType);
    }

    return $dataResults;
}

/**
 * Get default stats without search filter.
 */
function xtec_get_default_stats($offset, $limit)
{
    global $wpdb;

    return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM wp_stats AS wps ORDER BY wps.datetime DESC LIMIT %d,%d",
            $offset, $limit + 1
    ));
}

/**
 * Get stats filtered by username or content
 */
function xtec_get_filtered_stats($offset, $limit, $searchContent, $field, $searchType)
{
    global $wpdb;

    $where_clause = '';
    $order_clause = "ORDER BY $field";
    $limit_clause = ($limit != -1) ? " LIMIT %d,%d" : '';

    // Search by username.
    if ($searchType == 1) {
        $where_clause = 'WHERE wps.username LIKE %s';
    }
    // Search by content.
    else if ($searchType == 2) {
        $where_clause = 'WHERE wps.content LIKE %s';
    }

    // Build final SQL
    $sql = "SELECT * FROM wp_stats AS wps $where_clause $order_clause $limit_clause";

    if ($limit != -1) {
        return $wpdb->get_results($wpdb->prepare($sql, $searchContent, $offset, $limit + 1));
    }

    return $wpdb->get_results($wpdb->prepare($sql, $searchContent));
}

// Prepare String with data to CSV.
function xtec_stats_compile_csv($dataCSV = null, $dataResults = null): string
{
    if ($dataResults == null) {
        $dataCSV = "\"" . __('Id', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Datetime', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('IP', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('IP Forward', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('IP Client', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('User agent', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Uri', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Uid', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Is admin', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Username', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Email', 'xtec-stats') . "\",";
        $dataCSV .= "\"" . __('Content', 'xtec-stats') . "\",\n";
    } else {
        $dataCSV .= "\"" . $dataResults->stat_id . "\",";
        $dataCSV .= "\"" . $dataResults->datetime . "\",";
        $dataCSV .= "\"" . $dataResults->ip . "\",";
        $dataCSV .= "\"" . $dataResults->ipForward . "\",";
        $dataCSV .= "\"" . $dataResults->ipClient . "\",";
        $dataCSV .= "\"" . $dataResults->userAgent . "\",";
        $dataCSV .= "\"" . $dataResults->uri . "\",";
        $dataCSV .= "\"" . $dataResults->uid . "\",";
        $dataCSV .= "\"" . $dataResults->isadmin . "\",";
        $dataCSV .= "\"" . $dataResults->username . "\",";
        $dataCSV .= "\"" . $dataResults->email . "\",";

        // Catch the message to content field
        $dataContent = explode("content' => '", $dataResults->content);

        if (isset($dataContent[1])) {
            $dataContent = str_replace(["\n", '\'', ','], ['', '', ' '], $dataContent[1]);
            $dataContent = substr($dataContent, 0, -1);
        } else {
            $dataContent = '';
        }

        $dataCSV .= "\"" . $dataContent . "\",\n";
    }

    return $dataCSV;
}

// Generate CSV.
function xtec_stats_generate_csv(): void
{
    if (!empty($_POST) && isset($_POST['action']) && ($_POST['action'] === 'csv')) {

        $searchType = $_POST['search_type'];
        $searchContent = '%' . $_POST['search_content'] . '%';
        $field = DATETIME_FIELD_LITERAL;
        $limit = -1;
        $offset = 0;

        // Initialise to export csv.
        $dataCSV = xtec_stats_compile_csv();

        // Get results to csv
        $dataResults = xtec_stats_get_results(true, $offset, $limit, $searchContent, $field, $searchType);

        foreach ($dataResults as $iValue) {
            $dataCSV = xtec_stats_compile_csv($dataCSV, $iValue);
        }

        $filename = "statsdata_" . date('Ymd') . ".csv";

        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');

        print_r(mb_convert_encoding($dataCSV, 'UTF-16LE', 'UTF-8'));

        exit;
    }
}

// Print results.
function xtec_stats_output_data($offset, $limit, $dataResults, $searchType, $fieldContent, $suffix, $placeholder_username,
                                $placeholder_content, $fieldOrder, $directionArrow): void
{
    ?>
    <div class="wrap">
        <?php xtec_stats_render_tabs(); ?>

        <div id="target_1" class="tab-container
             <?php if (isset($_GET['tab'])) { ?> hidden-container <?php } ?>">
            <br/>
            <?php
            // Show search form.
            xtec_stats_render_search_form($offset, $limit, $searchType, $fieldContent, $suffix, $placeholder_username, $placeholder_content, $fieldOrder, $directionArrow, $dataResults);
            ?>
        </div>

        <div id="target_2" class="tab-container
             <?php if (!isset($_GET['tab'])) { ?> hidden-container <?php } ?>">
            <br>
            <?php
            xtec_stats_render_config_form();
            ?>
        </div>
    </div>
    <?php
}

/**
 * Render the top navigation tabs.
 */
function xtec_stats_render_tabs(): void
{ ?>
    <h2 class="nav-tab-wrapper">
        <a id="tab_1" href="#" class="nav-tab <?php if (!isset($_GET['tab'])) { ?> nav-tab-active <?php } ?>">
            <?php _e('Search options', 'xtec-stats'); ?>
        </a>
        <a id="tab_2" href="#" class="nav-tab <?php if (isset($_GET['tab'])) { ?> nav-tab-active <?php } ?>">
            <?php _e('Configuration options', 'xtec-stats'); ?>
        </a>
    </h2>
    <?php
}

/**
 * Render the search form and results table
 */
function xtec_stats_render_search_form($offset, $limit, $searchType, $fieldContent, $suffix, $placeholder_username,
                                       $placeholder_content, $fieldOrder, $directionArrow, $dataResults): void {
    ?>
    <form method="POST" id="xtec-stats-form-search" name="form_search" action="tools.php?page=xtec_stats">
        <input type="hidden" name="limit" value="<?php echo $offset / $limit ?>">

        <input type="radio" name="search_type" value="1" <?php if ($searchType != 2) { echo 'checked'; } ?>>
        <?php echo __('Username', 'xtec-stats'); ?>&nbsp;&nbsp;&nbsp;&nbsp;

        <input type="radio" name="search_type" value="2" <?php if ($searchType == 2) { echo 'checked'; } ?>>
        <?php echo __('Content', 'xtec-stats'); ?>

        <br/><br/>

        <input type="text"
               id="search_content"
               name="search_content"
               value="<?php echo $fieldContent ?>"
               style="width:300px;"
               placeholder="<?php echo ($searchType == 2) ? $placeholder_content : $placeholder_username; ?>">
        <button type="submit" name="action" value="Search" class="buttonSearch">
            <span class="dashicons dashicons-search"></span>
        </button>
        <br/>

        <?php
        // Print data tables.
        xtec_stats_render_limit_select();
        xtec_stats_render_table($dataResults, $limit, $fieldOrder, $suffix, $directionArrow);
        xtec_stats_render_pagination($offset, $limit, $dataResults);
        xtec_stats_render_export_csv();
        ?>
    </form>
    <?php
}

/**
 * Render the "Number elements to show" dropdown.
 */
function xtec_stats_render_limit_select(): void
{
    ?>
    <div class="xtec-stats-limit">
        <label for="xtec-stats-limitResults">
            <i><?php echo __('Number elements to show', 'xtec-stats') ?></i>
        </label>
        <select id="xtec-stats-limitResults" name="limitResults">
            <option value="25" selected="selected">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="300">300</option>
        </select>
    </div>
    <?php
}

/**
 * Render the results table.
 */
function xtec_stats_render_table($dataResults, $limit, $fieldOrder, $suffix, $directionArrow): void
{
    ?>
    <table class="wp-list-table widefat fixed striped posts xtec-stats-width">
        <thead>
            <?php xtec_stats_render_table_headers($fieldOrder, $suffix, $directionArrow); ?>
        </thead>
        <tbody>
            <?php xtec_stats_render_table_rows($dataResults, $limit); ?>
        </tbody>
    </table>
    <?php
}

/**
 * Render table headers.
 */
function xtec_stats_render_table_headers($fieldOrder, $suffix, $directionArrow): void
{
    $columns = [
            'datetime' => __('Datetime', 'xtec-stats'),
            'username' => __('Username', 'xtec-stats'),
            'uri' => __('Uri', 'xtec-stats'),
            'ip' => __('IP', 'xtec-stats'),
            'content' => __('Content', 'xtec-stats'),
    ];

    foreach ($columns as $field => $label) {
        $arrow_class = ($fieldOrder != $field) ? 'xtec-stats-no-show' : '';
        ?>
        <th <?php if (in_array($field, ['datetime', 'username', 'ip'])) { echo 'style="width:10%"'; } ?>>
            <button type="submit" name="action" value="<?php echo $field . $suffix[$field]; ?>" class="xtec-stats-orderBy">
                <strong><?php echo $label; ?></strong>
                <span class="dashicons dashicons-arrow <?php echo $directionArrow[$field] . ' ' . $arrow_class; ?> xtec-stats-arrow"></span>
            </button>
        </th>
        <?php
    }
}

/**
 * Render table rows.
 */
function xtec_stats_render_table_rows($dataResults, $limit): void
{
    if (count($dataResults) <= 0) {
        echo '<tr><td colspan="5" style="text-align:center;"><i>' . __('Not data found', 'xtec-stats') . '</i></td></tr>';
        return;
    }

    foreach ($dataResults as $i => $iValue) {
        // When there are "$limit+1" elements, only show "$limit" elements and show "Next" button for pagination.
        if ($i === $limit) {
            break;
        }

        $row = $iValue;
        $dataContent = xtec_clean_content($row->content);
        ?>
        <tr>
            <td><?php echo $row->datetime ?></td>
            <td><?php echo $row->username ?></td>
            <td><?php echo $row->uri ?></td>
            <td><?php echo $row->ip ?></td>
            <td><?php echo $dataContent ?></td>
        </tr>
        <?php
    }
}

/**
 * Clean the content field for display.
 */
function xtec_clean_content($content): string
{
    $dataContent = explode("content' => '", $content);

    if (isset($dataContent[1])) {
        $dataContent = str_replace(["\n", "'", ","], ["", "", " "], $dataContent[1]);
        $dataContent = substr($dataContent, 0, -1);
    } else {
        $dataContent = '';
    }

    return $dataContent;
}

/**
 * Render pagination buttons.
 */
function xtec_stats_render_pagination($offset, $limit, $dataResults): void
{
    $next = (count($dataResults) > $limit);
    ?>
    <div class="xtec-stats-pagination">
        <?php
        if (($offset / $limit) > 0) {
        ?>
            <button type="submit" name="action" value="previous" class="xtec-stats-pagination-arrow">
                <span class="dashicons dashicons-arrow-left-alt2"></span>
            </button>
        <?php
        }
        if ($next) {
        ?>
            <button type="submit" name="action" value="next" class="xtec-stats-pagination-arrow">
                <span class="dashicons dashicons-arrow-right-alt2"></span>
            </button>
       <?php
        }
        ?>
    </div>
    <br/><br/>
    <?php
}

/**
 * Render CSV export button.
 */
function xtec_stats_render_export_csv(): void
{
    ?>
    <div class="xtec-stats-pagination">
        <button type="submit" name="action" value="csv" class="xtec-stats-export-csv">
            <span class="dashicons dashicons-download"></span>
            <?php echo __('Export to CSV', 'xtec-stats'); ?>
        </button>
    </div>
    <?php
}

/**
 * Render configuration tab form.
 */
function xtec_stats_render_config_form(): void
{
    $include_admin = get_option('xtec-stats-include-admin'); ?>
    <form method="POST" id="xtec-stats-form-config" name="form_config" action="tools.php?page=xtec_stats&tab=config">
        <input id="exclude_admin" name="exclude_admin" type="checkbox"
               <?php echo (esc_attr($include_admin) === 'on') ? 'checked' : ''; ?>/>
        <?php _e('Count administrators stats', 'xtec-stats'); ?>
        <br/><br/>
        <input type="submit" name="xtec_config" id="action" class="button button-primary button-large"
               value="<?php _e('Save', 'xtec-stats'); ?>">
    </form>
    <?php
}

add_action('admin_menu', function () {
    add_management_page(__('Show logs WP_Stats', 'xtec-stats'), __('Logs', 'xtec-stats'), 'manage_options',
            'xtec_stats', 'get_data_xtec_stats');
});

function get_data_xtec_stats(): void
{
    // Initialize variables
    $field = DATETIME_FIELD_LITERAL;
    $limit = 25;
    $offset = 0;
    $placeholder_username = __('User name', 'xtec-stats');
    $placeholder_content = __('Message content', 'xtec-stats');
    $fieldOrder = 'datetime';

    // Default values
    $searchType = 1;
    $fieldContent = '';
    $searchContent = '';
    $whereSQL = false;

    $suffix = [
        'datetime' => '-up',
        'username' => DOWN_SUFFIX,
        'content' => DOWN_SUFFIX,
        'uri' => DOWN_SUFFIX,
        'ip' => DOWN_SUFFIX,
    ];

    $directionArrow = [
        'datetime' => DOWN_SUFFIX,
        'username' => DOWN_SUFFIX,
        'content' => DOWN_SUFFIX,
        'uri' => DOWN_SUFFIX,
        'ip' => DOWN_SUFFIX,
    ];

    // Check form submit.
    if (!empty($_POST)) {
        xtec_stats_handle_post($limit, $offset, $searchType, $fieldContent, $searchContent,
                $whereSQL, $field, $fieldOrder, $suffix, $directionArrow);
    }

    // Initialize javascript variables.
    xtec_stats_var_js($limit, $placeholder_username, $placeholder_content);

    // Get data.
    $dataResults = xtec_stats_get_results($whereSQL, $offset, $limit, $searchContent, $field, $searchType);

    // Print results.
    xtec_stats_output_data($offset, $limit, $dataResults, $searchType, $fieldContent, $suffix, $placeholder_username,
            $placeholder_content, $fieldOrder, $directionArrow);
}

/**
 * Handle POST request for configuration, CSV export, pagination, and ordering.
 */
function xtec_stats_handle_post(&$limit, &$offset, &$searchType, &$fieldContent, &$searchContent,
                                &$whereSQL, &$field, &$fieldOrder, &$suffix, &$directionArrow): void
{
    // Save configuration.
    if (isset($_POST['xtec_config'])) {
        try {
            $exclude_admin = $_POST['exclude_admin'] ?? '';
            update_option('xtec-stats-include-admin', $exclude_admin);
            echo '<div id="message" class="updated notice notice-success is-dismissible xtec-stats-notice">
                    <p class="xtec-white">' . __('Successfully updated.', 'xtec-stats') . '</p>
                  </div>';
        } catch (\Exception $e) {
            echo '<div id="message" class="error notice notice-error is-dismissible xtec-stats-notice">
                      <p class="xtec-white">' . __('Error: ', 'xtec-stats') . $e->getMessage() . '</p>
                  </div>';
        }
        return;
    }

    // Export CSV.
    if (isset($_POST['action']) && $_POST['action'] === 'csv') {
        xtec_stats_generate_csv();
        return;
    }

    // Handle search.
    $searchType = $_POST['search_type'] ?? 1;
    $fieldContent = $_POST['search_content'] ?? '';
    $searchContent = "%" . $fieldContent . "%";
    $whereSQL = true;

    // Prepare Pagination to Query.
    if (isset($_POST['limitResults'])) {
        $limit = $_POST['limitResults'];
    }

    // Handle action for pagination and ordering.
    if (isset($_POST['action'])) {
        xtec_stats_handle_action($_POST['action'], $limit, $offset, $field, $fieldOrder, $suffix, $directionArrow);
    }
}

/**
 * Handle pagination and ordering actions
 */
function xtec_stats_handle_action($action, $limit, &$offset, &$field, &$fieldOrder, &$suffix, &$directionArrow): void
{
    // Pagination
    if ($action === 'next') {
        $offset = ($_POST['limit'] + 1) * $limit;
    } elseif ($action === 'previous') {
        $offset = ($_POST['limit'] - 1) * $limit;
    }

    // Ordering
    $order_mapping = [
        'datetime-up' => ["wps.datetime ASC", "datetime", "-up", "datetime"],
        'datetime-down' => [DATETIME_FIELD_LITERAL, "datetime", DOWN_SUFFIX, "datetime"],
        'username-up' => ["wps.username ASC", "username", "-up", "username"],
        'username-down' => ["wps.username DESC", "username", "-up", "username"],
        'content-up' => ["wps.content ASC", "content", "-up", "content"],
        'content-down' => ["wps.content DESC", "content", "-up", "content"],
        'uri-up' => ["wps.uri ASC", "uri", "-up", "uri"],
        'uri-down' => ["wps.uri DESC", "uri", "-up", "uri"],
        'ip-up' => ["wps.ip ASC", "ip", "-up", "ip"],
        'ip-down' => ["wps.ip DESC", "ip", "-up", "ip"],
    ];

    if (isset($order_mapping[$action])) {
        [$field, $fieldOrderKey, $suffix_value, $arrowKey] = $order_mapping[$action];
        $fieldOrder = $fieldOrderKey;
        $suffix[$arrowKey] = $suffix_value;
        $directionArrow[$arrowKey] = ($action === $fieldOrderKey . '-up') ? '-up' : DOWN_SUFFIX;
    }
}
