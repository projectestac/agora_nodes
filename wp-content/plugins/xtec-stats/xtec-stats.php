<?php
/*
Plugin Name: XTEC Stats
Plugin URI: https://github.com/projectestac/agora_nodes
Description: Shows information from the stats table of Nodes in several ways
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

function xtec_stats_init() {
    // Load javascript
    wp_register_script( 'xtec_stats_js', plugins_url( 'js/xtec-stats.js', __FILE__ ), array( 'jquery' ), '1.1', true );
    wp_enqueue_script( 'xtec_stats_js' );

    // Load CSS
    wp_enqueue_style( 'xtec_stats_css', plugins_url( 'css/xtec-stats.css', __FILE__ ) );

    // Load language file
    load_plugin_textdomain( 'xtec-stats', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'xtec_stats_init' );

// Register widget
function xtec_stats_register_widgets() {
    register_widget( 'xtec_stats_widget' );
}
add_action( 'widgets_init', 'xtec_stats_register_widgets' );

class Xtec_stats_widget extends WP_Widget {

	// Constructor
	function __construct() {
		$widget_ops =array (
			'description' => __('Visits counter', 'xtec-stats'),
			'name' => __('Statistics', 'xtec-stats'),
		);

		parent::__construct('xtec_stats_widget', 'xtec_stats', $widget_ops);
	}

    /**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
     * @return void
	 */
    function form ($instance){
        $title = !empty($instance['title']) ? $instance['title'] : '';
        ?>
        <p><?php _e('Title:', 'xtec-stats');?><br />
        <input id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>"
               type="text" value="<?php echo esc_attr($title); ?>"
               />
        </p>
        <?php
	}

    /**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
     * @return array
	 */
    function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags(sanitize_text_field($new_instance['title'])) : '';
        
        return $instance;
	}

    /**
     * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
     */
    function widget($args, $instance) {

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
        
        return ;
    }
}

/**
 * Show data to WP-Stats. To users Admin only show delete messages. To users xtecadmin show all data.
 *
 * @author @xaviernietosanchez
 */

// Check over click to export CSV
add_action('admin_init','xtec_stats_generate_csv');

// Generate javascript variables
function xtec_stats_var_js($limit,$placeholder_username,$placeholder_content){
    echo '<script> var xtec_stats_limitResults = '.$limit.'; var xtec_stats_username = "'.$placeholder_username.'"; var xtec_stats_content = "'.$placeholder_content.'";</script>';
}

// Generate Query
function xtec_stats_get_results($whereSQL,$offset,$limit,$searchContent,$field,$searchType){

    global $wpdb;

    // Construct Query
    if ( false === $whereSQL ) {
        if ( is_xtec_super_admin() ){
            $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps ORDER BY wps.datetime DESC LIMIT %d,%d", $offset, $limit+1));
        } else {
            $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content is not null ORDER BY wps.datetime DESC LIMIT %d,%d", $offset, $limit+1));
        }
    } else {
        if ( $searchType == 1 ){ // Search to username
            if ($limit != -1 ){
                if ( is_xtec_super_admin() ){
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.username LIKE %s ORDER BY ".$field." LIMIT %d,%d", $searchContent, $offset, $limit+1));
                }else{
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content is not null and wps.username LIKE %s ORDER BY ".$field." LIMIT %d,%d", $searchContent, $offset, $limit+1));
                }
            } else {
                if ( is_xtec_super_admin() ){
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.username LIKE %s ORDER BY ".$field, $searchContent));
                }else{
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content is not null and wps.username LIKE %s ORDER BY ".$field, $searchContent));
                }
            }
        } else if ( $searchType == 2 ){   // Search to content
            if ( $limit != -1 ){
                if ( is_xtec_super_admin() ){
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content LIKE %s ORDER BY ".$field." LIMIT %d,%d", $searchContent, $offset, $limit+1));
                } else {
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content is not null and wps.content LIKE %s ORDER BY ".$field." LIMIT %d,%d", $searchContent, $offset, $limit+1));
                }
            }else{
                if ( is_xtec_super_admin() ){
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content LIKE %s ORDER BY ".$field, $searchContent));
                } else {
                    $dataResults = $wpdb->get_results($wpdb->prepare("SELECT * FROM wp_stats AS wps WHERE wps.content is not null and wps.content LIKE %s ORDER BY ".$field, $searchContent));
                }
            }
        }
    }

    return $dataResults;
}

// Prepare String with data to CSV
function xtec_stats_compile_csv( $dataCSV = null, $dataResults = null ){

    if( $dataResults == null ){
        $dataCSV = "\"".__("Id","xtec-stats")."\",";
        $dataCSV .= "\"".__("Datetime","xtec-stats")."\",";
        $dataCSV .= "\"".__("IP","xtec-stats")."\",";
        $dataCSV .= "\"".__("IP Forward","xtec-stats")."\",";
        $dataCSV .= "\"".__("IP Client","xtec-stats")."\",";
        $dataCSV .= "\"".__("User agent","xtec-stats")."\",";
        $dataCSV .= "\"".__("Uri","xtec-stats")."\",";
        $dataCSV .= "\"".__("Uid","xtec-stats")."\",";
        $dataCSV .= "\"".__("Is admin","xtec-stats")."\",";
        $dataCSV .= "\"".__("Username","xtec-stats")."\",";
        $dataCSV .= "\"".__("Email","xtec-stats")."\",";
        $dataCSV .= "\"".__("Content","xtec-stats")."\",\n";
    } else {
        $dataCSV .= "\"".$dataResults->stat_id."\",";
        $dataCSV .= "\"".$dataResults->datetime."\",";
        $dataCSV .= "\"".$dataResults->ip."\",";
        $dataCSV .= "\"".$dataResults->ipForward."\",";
        $dataCSV .= "\"".$dataResults->ipClient."\",";
        $dataCSV .= "\"".$dataResults->userAgent."\",";
        $dataCSV .= "\"".$dataResults->uri."\",";
        $dataCSV .= "\"".$dataResults->uid."\",";
        $dataCSV .= "\"".$dataResults->isadmin."\",";
        $dataCSV .= "\"".$dataResults->username."\",";
        $dataCSV .= "\"".$dataResults->email."\",";

        // Catch the message to content field
        $dataContent = explode("content' => '",$dataResults->content);

        if ( isset( $dataContent[1] ) ) {
            $dataContent = str_replace( "\n", "", $dataContent[1] );
            $dataContent = str_replace( '\'', '', $dataContent );
            $dataContent = str_replace( ',', ' ', $dataContent );
            $dataContent = substr( $dataContent, 0, strlen( $dataContent ) - 1 );
        } else {
            $dataContent = '';
        }

        $dataCSV .= "\"".$dataContent."\",\n";
    }

    return $dataCSV;

}

// Generate CSV
function xtec_stats_generate_csv(){

    if ( ! empty( $_POST ) && isset( $_POST['action'] ) && ( $_POST['action'] == 'csv' )) {

        $searchType = $_POST['search_type'];
        $searchContent = "%".$_POST['search_content']."%";
        $whereSQL = true;
        $field = "wps.datetime DESC";
        $limit = -1;
        $offset = 0;

        // Initialize to export csv
        $dataCSV = xtec_stats_compile_csv();

        // Get results to csv
        $dataResults = xtec_stats_get_results($whereSQL,$offset,$limit,$searchContent,$field,$searchType);

        for ( $i=0; $i<count($dataResults); $i++ ){
            $dataCSV = xtec_stats_compile_csv($dataCSV,$dataResults[$i]);
        }

        $filename = "statsdata_".date('Ymd').".csv";

        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=".$filename);
        header("Pragma: no-cache");
        header("Expires: 0");

        print_r(mb_convert_encoding($dataCSV, 'UTF-16LE', 'UTF-8'));

        exit;
    }

}

// Print results
function xtec_stats_output_data($offset,$limit,$dataResults,$searchType,$fieldContent,$suffix,$placeholder_username,$placeholder_content,$fieldOrder,$directionArrow){

?>
    <div class="wrap">
        <h2 class="nav-tab-wrapper">
            <a id="tab_1" href="#" class="nav-tab <?php if( ! isset($_GET['tab']) ){ ?> nav-tab-active <?php } ?>"><?php _e('Search options','xtec-stats'); ?></a>
            <a id="tab_2" href="#" class="nav-tab <?php if( isset($_GET['tab']) ){ ?> nav-tab-active <?php } ?>"><?php _e('Configuration options','xtec-stats'); ?></a>
        </h2>

        <div id="target_1" class="tab-container <?php if( isset($_GET['tab']) ){ ?> hidden-container <?php } ?>">
            <br>
        <?php // Show search form ?>
            <form method="POST" id="xtec-stats-form-search" name="form_search" action="tools.php?page=xtec_stats">
                <input type="hidden" name="limit" value="<?php echo ($offset/$limit) ?>">
                <input type="radio" name="search_type" value="1" checked> <?php echo __('Username','xtec-stats'); ?>&nbsp;&nbsp;&nbsp;&nbsp;
                <input type="radio" name="search_type" value="2" <?php if ( $searchType == 2 ){ ?> checked <?php } ?>> <?php echo __('Content','xtec-stats'); ?>
                <br><br>
                <input type="text" id="search_content" name="search_content" value="<?php echo $fieldContent ?>" style="width:300px" placeholder="<?php if ( $searchType == 2 ){ echo $placeholder_content; } else { echo $placeholder_username; } ?>">
                <a><button type="submit" name="action" value="Search" label="Buscar" class="buttonSearch"><span class="dashicons dashicons-search"></span></button></a>
                <br>
                <?php // Print data tables ?>
                <div class="xtec-stats-limit">
                    <label><i><?php echo __('Number elements to show','xtec-stats') ?></i></label>
                    <select id="xtec-stats-limitResults" name="limitResults">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                    </select>
                </div>
                <table class="wp-list-table widefat fixed striped posts xtec-stats-width">
                    <thead>
                        <tr>
                            <th style="width:11%;">
                                <a>
                                    <button id="xtec-stats-datetime" type="submit" name="action" value="datetime<?php echo $suffix['datetime']; ?>" class="xtec-stats-orderBy">
                                        <strong><?php echo __('Datetime','xtec-stats'); ?></strong>
                                        <span class="dashicons dashicons-arrow<?php echo $directionArrow['datetime']; ?> <?php if ( $fieldOrder != 'datetime' ){?>xtec-stats-no-show <?php } ?>xtec-stats-arrow"></span>
                                    </button>
                                </a>
                            </th>
                            <th style="width:12%;">
                                <a>
                                    <button id="xtec-stats-username" type="submit" name="action" value="username<?php echo $suffix['username']; ?>" class="xtec-stats-orderBy">
                                        <strong><?php echo __('Username','xtec-stats'); ?></strong>
                                        <span class="dashicons dashicons-arrow<?php echo $directionArrow['username']; ?> <?php if ( $fieldOrder != 'username' ){ ?>xtec-stats-no-show <?php } ?>xtec-stats-arrow"></span>
                                    </button>
                                </a>
                            </th>
                            <th style="width:30%;">
                                <a>
                                    <button id="xtec-stats-content" type="submit" name="action" value="content<?php echo $suffix['content']; ?>" class="xtec-stats-orderBy">
                                        <strong><?php echo __('Content','xtec-stats'); ?></strong>
                                        <span class="dashicons dashicons-arrow<?php echo $directionArrow['content']; ?> <?php if ( $fieldOrder != 'content' ){ ?>xtec-stats-no-show <?php } ?>xtec-stats-arrow"></span>
                                    </button>
                                </a>
                            </th>
                            <th style="width:10%;">
                                <a>
                                    <button id="xtec-stats-ip" type="submit" name="action" value="ip<?php echo $suffix['ip']; ?>" class="xtec-stats-orderBy">
                                        <strong><?php echo __('IP','xtec-stats'); ?></strong>
                                        <span class="dashicons dashicons-arrow<?php echo $directionArrow['ip']; ?> <?php if ( $fieldOrder != 'ip' ){ ?>xtec-stats-no-show <?php } ?>xtec-stats-arrow"></span>
                                    </button>
                                </a>
                            </th>
        <?php
                            if ( is_xtecadmin() ){
        ?>
                            <th style="width:20%;">
                                <a>
                                    <button id="xtec-stats-ip" type="submit" name="action" value="ip<?php echo $suffix['ip']; ?>" class="xtec-stats-orderBy">
                                        <strong><?php echo __('Uri','xtec-stats'); ?></strong>
                                        <span class="dashicons dashicons-arrow<?php echo $directionArrow['uri']; ?> <?php if ( $fieldOrder != 'uri' ){ ?>xtec-stats-no-show <?php } ?>xtec-stats-arrow"></span>
                                    </button>
                                </a>
                            </th>
        <?php
                            }
        ?>
                        </tr>
                    </thead>
                    <tbody>

        <?php
                if( count($dataResults) <= 0 ){
                    $colspan = 4;
                    if ( is_xtecadmin() ){ $colspan = 5; }
        ?>
                        <tr><td colspan="<?php echo $colspan; ?>" style="text-align:center"><i><?php echo __('Not data found','xtec-stats'); ?></i></td></tr>
        <?php
                } else {
                    for( $i=0; $i<count($dataResults); $i++ ){
                        // When I have "$limit+1" elements, only show "$limit" elements and show "Next" button for de pagination.
                        if( $i != $limit ){
        ?>
                            <tr>
                            <td><?php echo $dataResults[$i]->datetime ?></td>
                            <td><?php echo $dataResults[$i]->username ?></td>
        <?php
                            $dataContent = explode("content' => '",$dataResults[$i]->content);

                            if ( isset( $dataContent[1] ) ) {
                                $dataContent = str_replace("\n","",$dataContent[1]);
                                $dataContent = str_replace('\'','',$dataContent);
                                $dataContent = str_replace(',',' ',$dataContent);
                                $dataContent = substr($dataContent, 0, strlen($dataContent) - 1);
                            } else {
                                $dataContent = '';
                            }
        ?>
                            <td><?php echo $dataContent ?></td>
                            <td><?php echo $dataResults[$i]->ip ?></td>
        <?php
                            if ( is_xtecadmin() ){
        ?>
                            <td><?php echo $dataResults[$i]->uri ?></td>
        <?php
                            }
        ?>
                            </tr>
        <?php
                        }else{
                            $next = true;
                        }
                    }
                }
        ?>
                    </tbody>
                </table>
                <br>
                <div class="xtec-stats-pagination">
        <?php
                // Show "Previous" button to return show forward results
                if ( ($offset/$limit) > 0 ){
        ?>
                    <a><button type="submit" name="action" value="previous" class="xtec-stats-pagination-arrow"><span class="dashicons dashicons-arrow-left-alt2"></span></button></a>
        <?php
                }

                // Show "Next" button to show more results
                if ( $next === true ){
        ?>
                    <a><button type="submit" name="action" value="next" class="xtec-stats-pagination-arrow"><span class="dashicons dashicons-arrow-right-alt2"></span></button></a>
        <?php
                }
        ?>
                </div>
                <br><br>
                <div class="xtec-stats-pagination">
                    <a>
                        <button type="submit" name="action" value="csv" class="xtec-stats-export-csv">
                            <span class="dashicons dashicons-download"></span>
                            <?php echo __('Export to CSV','xtec-stats'); ?>
                        </button>
                    </a>
                </div>
            </form>
        </div>
        <div id="target_2" class="tab-container <?php if( ! isset($_GET['tab']) ){ ?> hidden-container <?php } ?>">
            <br>
            <?php
                $include_admin = get_option('xtec-stats-include-admin');
            ?>
            <form method="POST" id="xtec-stats-form-config" name="form_config" action="tools.php?page=xtec_stats&tab=config">
                <input id="exclude_admin" name="exclude_admin" type="checkbox" <?php echo (esc_attr($include_admin) == 'on') ? 'checked' : ''; ?>/>
                <?php _e('Count administrators stats', 'xtec-stats');?>
                <br><br>
                <input type="submit" name="xtec_config" id="action" class="button button-primary button-large" action="config" value="<?php _e('Save','xtec-stats'); ?>">
            </form>
        </div>
    </div>
<?php
}

function show_xtec_stats_create_menu(){
    add_management_page( __('Show logs WP_Stats', 'xtec-stats'),__('Logs', 'xtec-stats'),'manage_options','xtec_stats','get_data_xtec_stats','/images/wordpress.png');
}
add_action('admin_menu','show_xtec_stats_create_menu');

function get_data_xtec_stats(){

    // Initialize variables
    $field = "wps.datetime DESC";
    $limit = 10;
    $offset = 0;
    $placeholder_username = __('User name','xtec-stats');
    $placeholder_content = __('Message content','xtec-stats');
    $fieldOrder = 'datetime';

    // Default values
    $searchType = 1;
    $fieldContent = '';
    $searchContent = '';
    $whereSQL = false;

    $suffix = array (
        'datetime' => '-up',
        'username' => '-down',
        'content' => '-down',
        'uri' => '-down',
        'ip' => '-down',
    );

    $directionArrow = array (
        'datetime' => '-down',
        'username' => '-down',
        'content' => '-down',
        'uri' => '-down',
        'ip' => '-down',
    );

    // Check form submit
    if( ! empty($_POST) ){

        if( isset($_POST['xtec_config']) ){
            try {
                $exclude_admin = ( isset( $_POST['exclude_admin'] )) ? $_POST[ 'exclude_admin' ] : false;
                update_option( 'xtec-stats-include-admin', $exclude_admin );
                ?>
                <div id="message" class="updated notice notice-success is-dismissible xtec-stats-notice">
                    <p class="xtec-white"><?php _e('Successfully updated.','xtec-stats'); ?></p>
                </div>
                <?php
            } catch (\Exception $e){
                ?>
                <div id="message" class="error notice notice-error is-dismissible xtec-stats-notice">
                    <p class="xtec-white"><?php _e($e); ?></p>
                </div>
                <?php
            }
        } else if( isset($_POST['action'] ) && ( $_POST['action'] == 'csv' ) ) {
            xtec_stats_generate_csv();
        } else {
            $searchType = $_POST['search_type'];
            $fieldContent = $_POST['search_content'];
            $searchContent = "%".$_POST['search_content']."%";
            $whereSQL = true;

            // Prepare Pagination to Query
            if ( isset($_POST['limitResults']) ){$limit = $_POST['limitResults']; }

            if ( isset( $_POST[ 'action' ] )) {
                // Pagination
                if ( $_POST['action'] == 'next' ){ $offset = ($_POST['limit']+1)*$limit; }
                if ( $_POST['action'] == 'previous'){ $offset = ($_POST['limit']-1)*$limit; }
                // Order by
                if ( $_POST['action'] == 'datetime-up' ){ $field = "wps.datetime ASC"; $fieldOrder = "datetime"; $suffix['datetime'] = "-down"; $directionArrow['datetime'] = "-up"; }
                if ( $_POST['action'] == 'datetime-down' ){ $field = "wps.datetime DESC"; $directionArrow['datetime'] = "-down"; $fieldOrder = "datetime"; }
                if ( $_POST['action'] == 'username-up' ){ $field = "wps.username ASC"; $fieldOrder = "username"; $directionArrow['username'] = "-up"; }
                if ( $_POST['action'] == 'username-down' ){ $field = "wps.username DESC"; $suffix['username'] = "-up"; $directionArrow['username'] = "-down"; $fieldOrder = "username"; }
                if ( $_POST['action'] == 'content-up' ){ $field = "wps.content ASC"; $fieldOrder = "content"; $directionArrow['content'] = "-up"; }
                if ( $_POST['action'] == 'content-down' ){ $field = "wps.content DESC"; $suffix['content'] = "-up"; $directionArrow['content'] = "-down"; $fieldOrder = "content"; }
                if ( $_POST['action'] == 'uri-up' ){ $field = "wps.uri ASC"; $fieldOrder = "uri"; $directionArrow['uri'] = "-up"; }
                if ( $_POST['action'] == 'uri-down' ){ $field = "wps.uri DESC"; $suffix['uri'] = "-up"; $directionArrow['ùri'] = "-down"; $fieldOrder = "uri"; }
                if ( $_POST['action'] == 'ip-up' ){ $field = "wps.ip ASC"; $fieldOrder = "ip"; $directionArrow['ip'] = "-up";}
                if ( $_POST['action'] == 'ip-down' ){ $field = "wps.ip DESC"; $suffix['ip'] = "-up"; $directionArrow['ip'] = "-down"; $fieldOrder = "ip"; }
            }
        }
    }

    // Initialize javascripts variables
    xtec_stats_var_js($limit,$placeholder_username,$placeholder_content);

    // Get data
    $dataResults = xtec_stats_get_results($whereSQL,$offset,$limit,$searchContent,$field,$searchType);

    // Print results
    xtec_stats_output_data($offset,$limit,$dataResults,$searchType,$fieldContent,$suffix,$placeholder_username,$placeholder_content,$fieldOrder,$directionArrow);

}
