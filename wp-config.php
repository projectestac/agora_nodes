<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

require_once dirname(__FILE__, 2) . '/config/env-config.php';
include_once __DIR__ . '/wp-includes/xtec/lib.php';

global $agora;

// Load multisite params for Agora
include_once 'site-config.php';

// ** MySQL settings ** //
define('DB_USER', $agora['nodes']['username']);
define('DB_PASSWORD', $agora['nodes']['userpwd']);
const DB_CHARSET = 'utf8';
const DB_COLLATE = '';

// Proxy configuration
if (!empty($agora['proxy']['host'])) {
    define('WP_PROXY_HOST', $agora['proxy']['host']);
    define('WP_PROXY_PORT', $agora['proxy']['port']);
}
if (!empty($agora['proxy']['user'])) {
    define('WP_PROXY_USERNAME', $agora['proxy']['user']);
    define('WP_PROXY_PASSWORD', $agora['proxy']['pass']);
}

// Force https on login
const FORCE_SSL_ADMIN = true;
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Force usage of MySQLi instead of MySQL
const WP_USE_EXT_MYSQL = false;

// Block any kind of filesystem change, automatic or by users
const DISALLOW_FILE_MODS = true;

// Completely disable all types of automatic updates, core or otherwise
const AUTOMATIC_UPDATER_DISABLED = true;

// Disable asynchronous default cron
const DISABLE_WP_CRON = true;

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', $agora['nodes']['auth_key']);
define('SECURE_AUTH_KEY', $agora['nodes']['secure_auth_key']);
define('LOGGED_IN_KEY', $agora['nodes']['logged_in_key']);
define('NONCE_KEY', $agora['nodes']['nonce_key']);
define('AUTH_SALT', $agora['nodes']['auth_salt']);
define('SECURE_AUTH_SALT', $agora['nodes']['secure_auth_salt']);
define('LOGGED_IN_SALT', $agora['nodes']['logged_in_salt']);
define('NONCE_SALT', $agora['nodes']['nonce_salt']);
/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = $agora['nodes']['prefix'] . '_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
const WP_DEBUG = false;
const WP_DEBUG_DISPLAY = false;
const WP_DEBUG_LOG = false;

// Deactivate the loading of compressed Javascript and CSS files
const SCRIPT_DEBUG = false;

// Activates the saving of database queries in an array ($wpdb->queries).
// Set to true and uncomment code in theme reactor-primaria-1 in footer.php
//  and in wp-admin/admin-footer.php for admin pages, to use it.
const SAVEQUERIES = false;

// Default blog creation theme.
const WP_DEFAULT_THEME = 'NODES';

// Autosave time
const AUTOSAVE_INTERVAL = 300; // 5 minutes

// Number of revisions of each post
const WP_POST_REVISIONS = 3;

// Reduce the number of requests in admin pages
const CONCATENATE_SCRIPTS = true;

// Performance tweaks
const COMPRESS_SCRIPTS = true;
const COMPRESS_CSS = true;

// Param for Astra templates
const FS_METHOD = 'direct';

const WP_LOCAL_DEV = false;

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') ) {
    define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

// Call to XTEC function to collect basic stats info
save_stats();
