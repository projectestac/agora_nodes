<?php
/*
Plugin Name: XTEC Stats
Plugin URI: https://github.com/projectestac/agora_nodes
Description: Shows information from the stats table of Nodes in several ways
Version: 1.0
Author: Àrea TAC - Departament d'Ensenyament de Catalunya
*/

// Register plugin
add_action('widgets_init','xtec_stats_register_widgets');

function xtec_stats_register_widgets(){
	register_widget('xtec_stats_widget');
}

// Load pugin css
add_action('wp_enqueue_scripts', 'xtec_stats_css');

function xtec_stats_css() {
	wp_enqueue_style( 'style-xtec-stats', plugins_url().'/xtec-stats/css/xtec-stats.css' );
}

load_plugin_textdomain('xtec-stats', false, plugin_basename(dirname(__FILE__)). '/languages');

class Xtec_stats_widget extends WP_Widget {

	// Constructor
	function Xtec_stats_widget(){
		$widget_ops =array (
			'description' => __('Visits counter', 'xtec-stats'),
			'name' => __('Statistics', 'xtec-stats'),
		);

		$this->WP_Widget('xtec_stats_widget', 'xtec_stats', $widget_ops);
	}

	// outputs the options form on admin
	function form ($instance){
		$instance = wp_parse_args((array) $instance);
		$title = !empty($instance['title'] ) ? $instance['title'] : '';
		?>
        <p><?php _e("Title:","xtec-stats");?><br>
        <input id=  "<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>"
               type="text" value="<?php echo esc_attr($title); ?>"
               >
        </p>
        <?php
	}

	//Save options
	function update($new_instance, $old_instance){
		$instance = $old_instance;
		$instance['title'] = sanitize_text_field($new_instance['title']);
        $instance['title'] = strip_tags($new_instance['title']);

        return $instance;
	}

	// Get DDBB results
	function getStats($instance) {
		global $wpdb;

		$query = "SELECT count(*) AS Total FROM wp_stats
				WHERE uri NOT LIKE('%wp-cron%')
				AND uri NOT LIKE('%wp-admin%')";

		$result = $wpdb->get_results($query);
		$total = $result[0]->Total;

		return $total;
	}

	// outputs the content of the widget
	function widget($args, $instance) {
		extract( $args );
		echo $before_widget;

		$total = $this->getStats($instance);

		if (!empty($instance['title'])){
			echo $before_title . esc_html($instance['title']) . $after_title;
		}
		echo '<div class="xtec-stats">';
		echo $total;
		echo '</div>';
		echo $after_widget;
	}
}