<?php

require_once 'agora_script_base.class.php';

class script_upgrade extends agora_script_base {

    public $title = "Actualitza els espais d'Àgora-Nodes";
    public $info = "Crida l'script wp-admin/upgrade.php de cada espai per dur a terme l'actualització estàndard del WordPress";

    protected function _execute($params = array()) {
        
        global $wp_db_version;

        echo 'Versió de la base dades: ' . get_option('db_version') . '<br />';
        echo 'Versió dels fitxers: ' . $wp_db_version . '<br />';

        if ((get_option('db_version') != $wp_db_version) || !is_blog_installed()) {
            $_GET['step'] = 1;
            require_once ABSPATH . 'wp-admin/upgrade.php';
        } else {
            echo '<br />';
            echo 'El WordPress ja estava actualitzat!';
            echo '<br />';
        }

        return true;
    }

}
