<?php

require_once('agora_script_base.class.php');

class script_replace_url extends agora_script_base {

    public $title = 'Reemplaça la URL base d\'Àgora-Nodes';
    public $info = "La URL de destí serà WP_SITEURL, add_ccentre é sun booleà que defineix si a la origin_url se li afegeix el nom del centre al final. origin_url i origin_bd poden ser llistes separades per comes (sense espais)";

    public function params() {
        $params = array();
        $params['origin_url'] = false;
        $params['origin_bd'] = false;
        $params['add_ccentre'] = false;
        return $params;
    }

    protected function _execute($params = array()) {
        global $agora, $wpdb;


        // If this is specified, only replace URLs
        if ($params['origin_url']) {
            $replaceURL = explode(',', $params['origin_url']);
            if ($params['add_ccentre']) {
                foreach($replaceURL as $i => $url) {
                    $replaceURL[$i] .= CENTRE.'/';
                }
            }
        } else {
            $replaceURL = false;
        }

        if ($params['origin_bd']) {
            $replaceDB = explode(',', $params['origin_bd']);
        } else {
            $replaceDB = false;
        }

        $siteURL = WP_SITEURL;
        update_option('siteurl', $siteURL);
        update_option('home', $siteURL);
        update_option('wsl_settings_redirect_url', $siteURL);

        if ($replaceURL) {
            echo "Replace URL's\n";
            $replace = array('bp_activity' => array('action' => false,
                                                    'content' => false,
                                                    'primary_link' => false),
                            'posts' => array('post_content' => false,
                                            'post_excerpt' => false,
                                            'guid' => false),
                            'postmeta' => array('meta_value' => "meta_key = '_menu_item_url'")
                        );
            foreach ($replace as $tablename => $fields) {
                foreach ($fields as $fieldname => $and) {
                    foreach ($replaceURL as $string) {
                        if (!$this->replace_sql($tablename, $fieldname, $string, $siteURL, $and)) {
                            return false;
                        }
                    }
                }
            }
        }

        if ($replaceDB) {
            echo "Replace database names\n";
            $replacedb = array('bp_activity' => array('content'),
                            'posts' => array('post_content', 'guid')
                        );
            foreach ($replacedb as $tablename => $fields) {
                foreach ($fields as $fieldname) {
                    foreach ($replaceDB as $dbModel) {
                        if (!$this->replace_sql($tablename, $fieldname, '/'.$dbModel.'/', '/'.DB_NAME.'/')) {
                            return false;
                        }
                    }
                }
            }
        }

        echo "Update serialized wp_options fields\n";
        $options = array ('my_option_name', 'widget_text', 'reactor_options', 'widget_socialmedia_widget', 'widget_xtec_widget', 'widget_grup_classe_widget');
        foreach ($options as $option) {
            $value = get_option($option);

            // Update URL recursively
            if ($replaceURL) {
                foreach ($replaceURL as $string) {
                    $value = $this->replaceTree($string, $siteURL, $value);
                }
            }

            if ($replaceDB) {
                // Update user database recursively
                foreach ($replaceDB as $dbModel) {
                    $value = $this->replaceTree(trim($dbModel), DB_NAME, $value);
                }
            }

            update_option($option, $value);
        }

        return true;
    }

    private function execute_sql($sql) {
        global $wpdb;
        $wpdb->hide_errors();
        if (is_wp_error($wpdb->query($sql))) {
            $wpdb->print_error();
            return false;
        }
        $wpdb->show_errors();
        return true;
    }

    private function replace_sql($table, $field, $search, $replace, $and = false) {
        global $wpdb;
        $tablename = $wpdb->prefix.$table;
        $sql = "UPDATE $tablename SET `$field` = REPLACE (`$field` , '$search', '$replace')
                WHERE `$field` like '%$search%'";
        if ($and) {
            $sql .= "AND $and";
        }
        if (!$this->execute_sql($sql)) {
            return false;
        }
        return true;
    }

    private function replaceTree($search = '', $replace = '', $array = false) {

        if (!is_array($array)) {
            // Regular replace
            return str_replace($search, $replace, $array);
        }

        $newArray = array();
        foreach ($array as $k => $v) {
            // Recursive call
            $newArray[$k] = $this->replaceTree($search, $replace, $v);
        }

        return $newArray;
    }
}
