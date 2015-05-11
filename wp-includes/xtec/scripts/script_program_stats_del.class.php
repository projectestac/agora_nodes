<?php

require_once('agora_script_base.class.php');

class script_program_stats_del extends agora_script_base {

    public $title = 'Program stats deletion';
    public $info = 'Programs the deletion of the old stats in WP cron';

    protected function _execute($params = array()) {

        add_action('remove_stats', 'remove_old_stats');

        if (!wp_next_scheduled('remove_stats')) {
            wp_schedule_event(time(), 'daily', 'remove_stats');
        }
        
        return true;
    }

}
