<?php

// BEGIN iThemes Security - Do not modify or remove this line
// iThemes Security Config Details: 2
define( 'DISALLOW_FILE_EDIT', true ); // Disable File Editor - Security > Settings > WordPress Tweaks > File Editor
// END iThemes Security - Do not modify or remove this line

define( 'WP_CACHE', true );

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'iUePqzH1' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          'roD?JuUJ#,m}Rj1CebjP*RTMpg[0ow}uv9^N8*@?ZN!_f8s?hhvsJnc:`+FiXI#w' );
define( 'SECURE_AUTH_KEY',   't$-u%0OB7OLTRoAq2@WX7NnMA$ G5977m1JMY=o rAT&2[Yu[wA$MxY}F1m}F$83' );
define( 'LOGGED_IN_KEY',     'a|&Kk6vXyj/L[KBk*y/e4~L^ZH?6ie%= 7da9OmqLqlp]-C2b=t1|<yU]KwYG8<b' );
define( 'NONCE_KEY',         '9Lhd/?3:dJsjI.WOhqbdY67yv_Xn9)_Y-,eJW3[a~ :bpF0uFv]&ekL{aq%#A_r4' );
define( 'AUTH_SALT',         'cruJ(6Om3IR!.9G<$u5TU3k`3WOrxW(@;W tc^DU<>SM9uy*|s?p4oi@5u/x<&O5' );
define( 'SECURE_AUTH_SALT',  'Wzn+T-W8)T,>B,s!GCpMlN0:tcO+fq)h*pXNw^EZdP&S;ra(##9_T<oLb8PiA711' );
define( 'LOGGED_IN_SALT',    'QrtP<b(H_bfH6YkgBff^`7b3VvG?hI]np~JAl?*/u4}ZS=%4{oHPJ4^Tw.S(N$8j' );
define( 'NONCE_SALT',        '&5lA.A=a}y +0yA& (FcamQ) ~>L=S%=*bAIE-v5LgXPf$Tg6N@`V=UBC9U*;P2-' );
define( 'WP_CACHE_KEY_SALT', 'Nq~2 -?57~F9NAAp$QHRm/zQL(X:_ga bFejZ4Yq[~6LF-OTujs4@X> 0UWUCa3S' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
