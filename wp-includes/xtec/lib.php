<?php

/*
 * This file contains extra functions for Agora and XTECBlocs services
 */

/**
 * Decide if user has full access. In Àgora must be xtecadmin and 
 * in XTECBlocs must be network admin.
 * 
 * @global boolean $isAgora
 * @global boolean $isBlocs
 * @return boolean
 */
function is_xtec_super_admin() {

    global $isAgora, $isBlocs;
    
    if ($isAgora && is_xtecadmin()) {
        return true;
    }
    
    if ($isBlocs && is_super_admin()) {
        return true;
    }
    
    return false;
}

/*
 * Check if current logged user is xtecadmin
 */
function is_xtecadmin() {

    global $current_user;

    if (isset($current_user->user_login) && ($current_user->user_login == 'xtecadmin')) {
        return true;
    } else {
        return false;
    }
}

/*
 * Get the ID of xtecadmin user
 *
 * return int ID of xtecadmin
 */
function get_xtecadmin_id() {

    return get_user_by('login', 'xtecadmin')->ID;
}

/*
 * Get the username of xtecadmin
 *
 * return string username of xtecadmin
 */
function get_xtecadmin_username() {

    return 'xtecadmin';
}

/*
 * Collect basic statistical and security information.
 */
function save_stats() {

    global $current_user, $table_prefix, $wpdb;

    $table = $table_prefix . 'stats';

    // time() return time referred to GMT. Changing the time zone fixes this.
    date_default_timezone_set('Europe/Madrid');
    $datetime = date('Y-m-d H:i:s', time());

    $ip = $ipForward = $ipClient = $userAgent = '';

    // Usage of filter_input() guarantees that info is clean
    if (isset($_SERVER['REMOTE_ADDR']) && !empty($_SERVER['REMOTE_ADDR'])) {
        $ip = filter_input(INPUT_SERVER, 'REMOTE_ADDR', FILTER_SANITIZE_STRING);
    }

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ipForward = filter_input(INPUT_SERVER, 'HTTP_X_FORWARDED_FOR', FILTER_SANITIZE_STRING);
    }

    if (isset($_SERVER['HTTP_CLIENT_IP']) && !empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ipClient = filter_input(INPUT_SERVER, 'HTTP_CLIENT_IP', FILTER_SANITIZE_STRING);
    }

    if (isset($_SERVER['HTTP_USER_AGENT']) && !empty($_SERVER['HTTP_USER_AGENT'])) {
        $userAgent = filter_input(INPUT_SERVER, 'HTTP_USER_AGENT', FILTER_SANITIZE_STRING);
    }

    if (isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI'])) {
        $uri = filter_input(INPUT_SERVER, 'REQUEST_URI', FILTER_SANITIZE_STRING);
    }

    $uid = $current_user->ID;
    $username = $current_user->user_login;
    $email = $current_user->user_email;

    $isadmin = current_user_can('manage_options');

    $data = array(
        'datetime' => $datetime,
        'ip' => $ip,
        'ipForward' => $ipForward,
        'ipClient' => $ipClient,
        'userAgent' => $userAgent,
        'uri' => $uri,
        'uid' => $uid,
        'isadmin' => $isadmin,
        'username' => $username,
        'email' => $email
    );

    $wpdb->insert($table, $data, array('%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s', '%s'));
}

/**
 * 
 * @global type $wpdb
 * @author Toni Ginard
 */
function remove_old_stats() {
    
    global $wpdb;

    $datetime = date('Y-m-d H:i:s', time() - 31104000); // 31104000 = 6 * 30 * 24 * 60 * 60 * 2 (12 month)
    
    $wpdb->query( "DELETE FROM $wpdb->stats WHERE datetime < '$datetime'");
    
}


function parse_cli_args() {
    global $cliargs;
    $cliargs = array();
    $rawoptions = $_SERVER['argv'];

    if (($key = array_search('--', $rawoptions)) !== false) {
        $rawoptions = array_slice($rawoptions, 0, $key);
    }

    unset($rawoptions[0]);
    foreach ($rawoptions as $raw) {
        if (substr($raw, 0, 2) === '--') {
            $value = substr($raw, 2);
            $parts = explode('=', $value);
            if (count($parts) == 1) {
                $key   = reset($parts);
                $value = true;
            } else {
                $key = array_shift($parts);
                $value = implode('=', $parts);
            }
            $cliargs[$key] = $value;

        } else if (substr($raw, 0, 1) === '-') {
            $value = substr($raw, 1);
            $parts = explode('=', $value);
            if (count($parts) == 1) {
                $key   = reset($parts);
                $value = true;
            } else {
                $key = array_shift($parts);
                $value = implode('=', $parts);
            }
            $cliargs[$key] = $value;
        }
    }
}

function get_cli_arg($arg){
    global $cliargs;
    if (empty($cliargs)) {
        parse_cli_args();
    }
    if (isset($cliargs[$arg])) {
        return $cliargs[$arg];
    }
    return false;
}