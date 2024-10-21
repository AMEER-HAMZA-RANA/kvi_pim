<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'new-kvi-pim' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'ZOKmrpcQB4VXFblVqnrb31TcJxlhc9DXxY8fVl5vni8nsHaPGM8FLorRhXkunwfi' );
define( 'SECURE_AUTH_KEY',  '0Co0D3n2VIbZf727raHYv8CxNLgT6OSUlg8r3GCDa5Sttv4FtZfAbSxFN44wMLrR' );
define( 'LOGGED_IN_KEY',    'XGF76JlCqv18aoMEH99FXBDadQKFy2yY3RKw1ZxLeh5PP3zo3IACmqwyMjEcORsi' );
define( 'NONCE_KEY',        '5PELt6OoDQuXcS1AO8hJ9XbNXN77xist2blGksHE2kML2jHYWepAWbfENx9EQESB' );
define( 'AUTH_SALT',        'DqKsQ9WjD2QKMDIRFcJkvrygbObekCnQbMZBA2KlQKjhr4YmVtYGcnj4ufVWai7l' );
define( 'SECURE_AUTH_SALT', '6ItTCBHnrAE9iwuEJEYsiqAjLh94h9DjHrPsfO2yFJLvdKKg0kVSP8C3dee5K3My' );
define( 'LOGGED_IN_SALT',   '1rkmnL7C3ZkslGMNxcv9gvqbPMNy4GTFpnI4RIbAw52jeUEXrVCb8jflCHP5CCfc' );
define( 'NONCE_SALT',       '0YzkVtok1ZLXNnHjvBUohIp45fU7ZEP71I3febIsoUW7aqHvisPPN3H9KcKAjgqe' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', true );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';