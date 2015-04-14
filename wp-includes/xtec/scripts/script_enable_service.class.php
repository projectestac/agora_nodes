<?php

require_once('agora_script_base.class.php');

class script_enable_service extends agora_script_base {

    public $title = 'Activa el servei Àgora-Nodes';
    public $info = "Fa els passos necessàris per activar Wordpress i deixar-lo a punt per començar";

    public function params() {
        $params = array();
        $params['password'] = ""; //Admin password en md5
        $params['clientName'] = "";
        $params['clientAddress'] = "";
        $params['clientCity'] = "";
        $params['clientPC'] = ""; // Postal Code
        $params['clientDNS'] = "";
        $params['clientCode'] = "";

        $params['URLNodesModelBase'] = "";
        $params['shortcodes'] = "";
        $params['DBNodesModel'] = "";

        return $params;
    }

    protected function _execute($params = array()) {
        global $agora, $wpdb;

        $urlModelBase = $params['URLNodesModelBase'];
        $shortcodes = explode(',', $params['shortcodes']);
        foreach ($shortcodes as $scode) {
            $urlModels[] = $urlModelBase . $scode . '/';
        }

        // Get the params
        $clientName = $params['clientName'];
        $clientAddress = $params['clientAddress'];
        $clientCity = $params['clientCity'];
        $clientPC = $params['clientPC']; // Post Code
        $clientDNS = $params['clientDNS'];
        $clientCode = $params['clientCode'];
        $dbModels = explode(',', $params['DBNodesModel']);

        update_option('blogname', $clientName);
        update_option('blogdescription', 'Espai del centre ' . $clientName);

        $siteURL = WP_SITEURL;
        update_option('siteurl', $siteURL);
        update_option('home', $siteURL);
        update_option('wsl_settings_redirect_url', $siteURL);

        update_option('admin_email', $clientCode . '@xtec.cat');

        $user = get_user_by('login', 'admin');
        $user_id = wp_update_user(array(
            'ID' => $user->id,
            'user_email' => $clientCode.'@xtec.cat',
            'user_registered' => time()
        ));
        if ( is_wp_error( $user_id ) ) {
            echo 'Error actualitzant usuari admin';
            return false;
        }
        $wpdb->update($wpdb->users, array('user_pass' => $params['password']), array('ID' => $user_id) );

        $user = get_user_by('login', 'xtecadmin');
        $user_id = wp_update_user(array(
            'ID' => $user->id,
            'user_email' => $agora['xtecadmin']['mail']
        ));
        if ( is_wp_error( $user_id ) ) {
            echo 'Error actualitzant usuari xtecadmin';
            return false;
        }
        $wpdb->update($wpdb->users, array('user_pass' => $agora['xtecadmin']['password']), array('ID' => $user_id) );

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
                foreach ($urlModels as $string) {
                    if (!$this->replace_sql($tablename, $fieldname, $string, $siteURL, $and)) {
                        return false;
                    }
                }
            }
        }

        echo "Replace database names\n";
        $replacedb = array('bp_activity' => array('content'),
                        'posts' => array('post_content', 'guid')
                    );
        foreach ($replacedb as $tablename => $fields) {
            foreach ($fields as $fieldname) {
                foreach ($dbModels as $dbModel) {
                    if (!$this->replace_sql($tablename, $fieldname, '/'.$dbModel.'/', '/'.DB_NAME.'/')) {
                        return false;
                    }
                }
            }
        }

        echo "Reset stats table\n";
        if (!$this->execute_sql('TRUNCATE '.$wpdb->prefix.'stats')) {
            return false;
        }

        echo "Update serialized wp_options fields\n";
        $options = array ('my_option_name', 'widget_text', 'reactor_options', 'widget_socialmedia_widget', 'widget_xtec_widget', 'widget_grup_classe_widget');
        foreach ($options as $option) {
            $value = get_option($option);

            // Update URL recursively
            foreach ($urlModels as $string) {
                $value = $this->replaceTree($string, $siteURL, $value);
            }

            // Update user database recursively
            foreach ($dbModels as $dbModel) {
                $value = $this->replaceTree(trim($dbModel), DB_NAME, $value);
            }

            if ($option == 'reactor_options') {
                // Update school name and address
                $value['nomCanonicCentre'] = $clientName;
                $value['direccioCentre'] = $clientAddress;
                $value['cpCentre'] = $clientPC . ' ' . $clientCity;
                $value['nomCanonicCentre'] = $clientName;
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
