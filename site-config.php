<?php

require_once (dirname(dirname(__FILE__)) . '/config/dblib-mysql.php');

global $school_info;
$centre = getSchoolInfo('nodes');

global $agora, $isAgora, $isBlocs;

$isAgora = true;
$isBlocs = false;

define('DB_NAME', $agora['nodes']['userprefix'] . $school_info['id_nodes']);
define('DB_HOST', $school_info['dbhost_nodes']);
define('WP_SITEURL', $agora['server']['html'] . $centre . '/');
define('UPLOADS', 'wp-content/uploads/' . $agora['nodes']['userprefix'] . $school_info['id_nodes']);

define('ENVIRONMENT', $agora['server']['enviroment']);
define('SCHOOL_CODE',$school_info["clientCode"]);
