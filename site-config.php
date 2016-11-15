<?php

require_once (dirname(dirname(__FILE__)) . '/config/dblib-mysql.php');

global $school_info;
$centre = getSchoolInfo('nodes');

global $agora, $isAgora, $isBlocs, $diskPercentNodes;

$isAgora = true;
$isBlocs = false;

define('CENTRE', $centre);
define('DB_NAME', $agora['nodes']['userprefix'] . $school_info['id_nodes']);
define('DB_HOST', $school_info['dbhost_nodes']);
define('UPLOADS', 'wp-content/uploads/' . $agora['nodes']['userprefix'] . $school_info['id_nodes']);
define('ENVIRONMENT', $agora['server']['enviroment']);
define('SCHOOL_CODE', $school_info['clientCode']);

// Check for subdomain
if (!empty($school_info['url_type']) && ($school_info['url_type'] == 'subdomain') && !empty($school_info['url_host'])) {
    define('WP_SITEURL', $agora['server']['html']);
} else {
    define('WP_SITEURL', $agora['server']['html'] . $centre . '/');
}

$diskPercentNodes = (isset($school_info['diskPercent_nodes'])) ? $school_info['diskPercent_nodes'] : 0;

if (isset($agora['iseoi']) && $agora['iseoi']) {
    define('XTEC_MAIL_IDAPP', 'AGORAEOI');
} elseif (isset($agora['iseoi'])) {
    define('XTEC_MAIL_IDAPP', 'AGORA');
} else {
    define('XTEC_MAIL_IDAPP', 'XTECBLOCS');
}