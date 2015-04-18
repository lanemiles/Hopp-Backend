<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'i801374_wp1');

/** MySQL database username */
define('DB_USER', 'i801374_wp1');

/** MySQL database password */
define('DB_PASSWORD', 'Q@L*WHwVIJ05[]4');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6uqyhPebIEe9rpcT8qTd57crPfH3wU1MuGcfwbiU7nEwNG6NoF54Z7aMJi83kNNi');
define('SECURE_AUTH_KEY',  'hTtUlScwUgtzNiAepywx6GEV6n8HOM5EOyAJdXStFBizBvuFz2pPQ3NUSf6FXTii');
define('LOGGED_IN_KEY',    'imdTSjPxBcFtRhj12cFsVH3O3nvWpLKcWBejTcAd5JyLXaZvoRLxkZNb6u5eIgJr');
define('NONCE_KEY',        'OL9TCeKgdN0r8g0ep0pxqM6mUgagXq8BUt7Y6QsQcocXzMpzrpTuBx2YBvgPtdch');
define('AUTH_SALT',        '1SJ8IPUU2p9ilbgavSRYhbSj1FbgwxnbrwAEb1jvKYrAQh4gff8ptACeNkVSIt7G');
define('SECURE_AUTH_SALT', 'YMkHYgmQqNB9FhzCH0qLcTl6m663LenVkjhThc3LCt5p68nuXTfLnZL2oKnUDFzW');
define('LOGGED_IN_SALT',   'XZPUTIU51ydd7oD6jUWQgFWX5EuVdJHeNNyAO9bBGw4ubXEQ95DKqrEqsmLtN9fg');
define('NONCE_SALT',       'yzmUPFb4Ydn0Xvnwc0h9reMOmU3SifYSfSEBAhTwwiFo36wjEnBa6dRV58ahLg8F');

/**
 * Other customizations.
 */
define('FS_METHOD','direct');define('FS_CHMOD_DIR',0755);define('FS_CHMOD_FILE',0644);
define('WP_TEMP_DIR',dirname(__FILE__).'/wp-content/uploads');

/**
 * Turn off automatic updates since these are managed upstream.
 */
define('AUTOMATIC_UPDATER_DISABLED', true);


/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
