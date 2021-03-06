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

require_once dirname(dirname(__FILE__)) . '/config/env-config.php';
include_once dirname(__FILE__) . '/wp-includes/xtec/lib.php';

global $agora;

// Load multisite params for Agora
include_once('site-config.php');

// ** MySQL settings ** //
define('DB_USER', $agora['nodes']['username']);
define('DB_PASSWORD', $agora['nodes']['userpwd']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

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
define('FORCE_SSL_ADMIN', true);
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
    $_SERVER['HTTPS'] = 'on';
}

// Force usage of MySQLi instead of MySQL
define ('WP_USE_EXT_MYSQL', false);

// Block any kind of filesystem change, automatic or by users
define('DISALLOW_FILE_MODS', true);

// Completely disable all types of automatic updates, core or otherwise
define('AUTOMATIC_UPDATER_DISABLED', true);

// Disable asynchronous default cron
define('DISABLE_WP_CRON', 'true');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'q$ATvJpV<JO`Xa)7hv!4)u%<wSdmtE?519V>8X#ds~|Pp9|jduz1[Cc:1??K%ty:');
define('SECURE_AUTH_KEY',  ']|R`MQ?~b$|e`)R^},O1FFT|H_x!ukyhEXr:bOO2Fq;a_..iRNG;b,tzYNFx|i&v');
define('LOGGED_IN_KEY',    'o[E|Hg}oL89sDHB9,cPm(+bd!klzS}R=e7o]N=6N$@^s]u?z@{ SA032!@c-2lzj');
define('NONCE_KEY',        'Y.ojKh*XW<&*bZ?0]b/g/_?klFzk}O&J-}$-#>}x7Ye<E[U`,4ih@& 5MqUo}L~:');
define('AUTH_SALT',        '(Ym[t///b5JXmf9W(g..Fv2L@qF5473IQ|F4C/|},Y[F{SRhlL?ADKWa/@8ts&-s');
define('SECURE_AUTH_SALT', '#m=Q-MjIv4v}B7Ewp3IG@~N!DwE=eUoyK><[V^CoZ6A[ltJ0jrvD??L-,^J;DA~6');
define('LOGGED_IN_SALT',   'Rr& 6>;jA1?K?xW}zWsvxE@i6lS4*+f|RH0W[}.:egl|RYQ*Y2FVLqvx^~}GiO8%');
define('NONCE_SALT',       'N+=]R(*9^T2<ja;$x61F}tq6XDNO%v[iL.?3#;h9Y@lJ2-X%72>- 6!3B8R}g:uN');
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
define('WP_DEBUG', false);

// Deactivate the loading of compressed Javascript and CSS files
define('SCRIPT_DEBUG', false);

// Activates the saving of database queries in an array ($wpdb->queries).
// Set to true and uncomment code in theme reactor-primaria-1 in footer.php
//  and in wp-admin/admin-footer.php for admin pages, to use it.
define('SAVEQUERIES', false);

// Default blog creation theme.
define('WP_DEFAULT_THEME', 'NODES');

// Autosave time
define('AUTOSAVE_INTERVAL', 300); // 5 minuts
// Number of revisions of each post
define('WP_POST_REVISIONS', 3);

// Reduce the number of requests
const CONCATENATE_SCRIPTS = true;

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') ) {
	define('ABSPATH', dirname(__FILE__) . '/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

// Call to XTEC function to collect basic stats info
save_stats();
