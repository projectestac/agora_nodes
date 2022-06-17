<?php

require_once (dirname(__FILE__, 2) . '/config/dblib-mysql.php');

global $school_info;
$centre = getSchoolInfo('nodes');

global $agora, $isAgora, $isBlocs, $diskPercentNodes;

if (isset($school_info['state_nodes']) && ($school_info['state_nodes'] === '-5')) {
    setcookie($agora['server']['cookie'], '', time(), '/'); // Force cookie to expire
    header('Location: ' . WWWROOT . 'error.php?s=&migrating=' . $centre);
    exit();
}

if (isset($school_info['state_nodes']) && ($school_info['state_nodes'] === '-6')) {
    setcookie($agora['server']['cookie'], '', time(), '/'); // Force cookie to expire
    header('Location: ' . WWWROOT . 'error.php?s=&migrated=' . $centre);
    exit();
}

if (isset($school_info['state_nodes']) && ($school_info['state_nodes'] === '-7')) {
    setcookie($agora['server']['cookie'], '', time(), '/'); // Force cookie to expire
    header('Location: ' . WWWROOT . 'error.php?s=&saturated=' . $centre);
    exit();
}

$isAgora = true;
$isBlocs = false;

define('CENTRE', $centre);
define('DB_NAME', $agora['nodes']['userprefix'] . $school_info['id_nodes']);
define('DB_HOST', $school_info['dbhost_nodes']);
define('UPLOADS', 'wp-content/uploads/' . $agora['nodes']['userprefix'] . $school_info['id_nodes']);
define('ENVIRONMENT', $agora['server']['enviroment']);
define('SCHOOL_CODE', $school_info['clientCode']);

// Check for subdomain
if (!empty($school_info['url_type']) && ($school_info['url_type'] === 'subdomain') && !empty($school_info['url_host'])) {
    define('WP_SITEURL', $agora['server']['html']);
    define('WP_HOME', $agora['server']['html']);
} else {
    define('WP_SITEURL', $agora['server']['html'] . $centre . '/');
    define('WP_HOME', $agora['server']['html'] . $centre . '/');
}

$diskPercentNodes = $school_info['diskPercent_nodes'] ?? 0;

if (isset($agora['iseoi']) && $agora['iseoi']) {
    define('XTEC_MAIL_IDAPP', 'AGORAEOI');
} elseif (isset($agora['iseoi'])) {
    define('XTEC_MAIL_IDAPP', 'AGORA');
} else {
    define('XTEC_MAIL_IDAPP', 'XTECBLOCS');
}

// Load only the plugin files associated with the current type of school to improve the performance.
if (isset($agora['isProjecte']) && !$agora['isProjecte']) {
    $agora['nodes']['plugins_to_remove'] = [
        'astra-addon',
        'astra-sites',
        'wpforms-lite',
    ];
}