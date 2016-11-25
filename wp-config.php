<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'test_training');

/** MySQL database username */
define('DB_USER', 'dileep');

/** MySQL database password */
define('DB_PASSWORD', 'Indpro123');

/** MySQL hostname */
define('DB_HOST', '192.168.1.18');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '^t/huB.b;}@Lik$6{,;l}6l.|5c$G?Lz73_`2>)ZMv+3 ?=nS*a]=#]]ubG&5[_n');
define('SECURE_AUTH_KEY',  'vXvFC??-g5NW>`]r6/~hB^!v2wW%@knREmXcy;i[1+$DJ1vx1wPK Xoij6E#W8=)');
define('LOGGED_IN_KEY',    'LB}j-};oFD2x$M}2&s l@,kY;doeOH_Rx7[}(B2eJ,2%pLmk,R[O|:efFt90[=!I');
define('NONCE_KEY',        'Y&[2r1CvYN;/YXLYj&|~L:]i] U0.gS~foLzX1.l}BZnDqdUICGW?JLCs1YU:%cX');
define('AUTH_SALT',        'XCZ~X()ztQPz>*J/H23ugo?]xq-6,N*6,v5#PM5=~V8R[2uP8l>4Mb>S?3~cwh*i');
define('SECURE_AUTH_SALT', '#*aO0#TD`83TC=>Thgz|rIP^*T{q8@UDKJ:C|)U?I2X%s9sz&;r65I0Y^UBJryI)');
define('LOGGED_IN_SALT',   'QBkUeX1 I|++]t4;jl6|5lRA{Lh`2{!{]Hf;Y;/f>h  mfcw.=^fXh*SL_(UJyuh');
define('NONCE_SALT',       '%F%CN+pUWv<F>>Ui];|VP~Rgi]{%7CQ,vefImV+,X(+|8H>K|)?Is{!#ai|.*1h$');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
