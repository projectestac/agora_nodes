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
	 */
    function form ($instance){
        $title = !empty($instance['title']) ? $instance['title'] : '';
        // @aginard: 'include_admin' is an unique value for all the widgets, so its value is stored in options table 
        $include_admin = get_option('xtec-stats-include-admin');

		?>
        <p><?php _e('Title:', 'xtec-stats');?><br />
        <input id="<?php echo $this->get_field_id('title'); ?>"
               name="<?php echo $this->get_field_name('title'); ?>"
               type="text" value="<?php echo esc_attr($title); ?>"
               />
        </p>
        <p>
        <input id="<?php echo $this->get_field_id('include_admin'); ?>"
               name="<?php echo $this->get_field_name('include_admin'); ?>"
               type="checkbox"
               <?php echo (esc_attr($include_admin) == 'on') ? 'checked' : ''; ?>
               />
        <?php _e('Count administrators stats', 'xtec-stats');?>
        </p>
        <?php
	}

    /**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
    function update($new_instance, $old_instance){
		$instance = array();
		$instance['title'] = (!empty($new_instance['title'])) ? strip_tags(sanitize_text_field($new_instance['title'])) : '';
        $include_admin = (!empty($new_instance['include_admin'])) ? strip_tags(sanitize_text_field($new_instance['include_admin'])) : 'off';
        // @aginard: 'include_admin' is stored in widget data to make possible to be
        // changed in the widget form, but the value used is in options table
		$instance['include_admin'] = $include_admin;

        // 'include_admin' is an unique value for all the widgets, so its value is stored separatedly
        update_option('xtec-stats-include-admin', $include_admin);
        
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