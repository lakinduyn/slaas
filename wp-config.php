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
define('DB_NAME', 'slaas_2016');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

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
define('AUTH_KEY',         'q!X2NMX9SUPU%Bp3%|;)j)-=ex+.TDy4]B@Dzf=RyhH1Ru-Xl&.k+|FnEU]yHG@k');
define('SECURE_AUTH_KEY',  '+$=5<cp. ~i6%b9-lsUZ#YK|wQ(#+/twr[-.<NMi@9B`-aj}v!K@+q1h|2|@Icjn');
define('LOGGED_IN_KEY',    'y2*IU+d@4FvK39%G(+6^o%/C[|A`B[Pb+@eX#sSlKw!!I+dz;yG`*m++~u``Q+HY');
define('NONCE_KEY',        ' {Gmo a#kw$`i(Bp6]#{g+(wiH6;|.UTlEP3gV3&1#U}av:6YD10`=,+,i^T<J#e');
define('AUTH_SALT',        'gFyHXr^rQLmORv79V5S1+Ne<9)t&FCF,q,*0mU5|X,/vWko@esy8,WM+i4:HDtFb');
define('SECURE_AUTH_SALT', 'G+kSw;M#m f4eFEd)dl3*b>mx|}232iQo:nY7u-o?9G7;7)L{eYj/SLO{$y_^w])');
define('LOGGED_IN_SALT',   '67~g s;)PUt!b.TU%630L$wqUH`,m,:d!9`.=lz}vnqiR[4tqUkWw[YGZ_-5uQ+j');
define('NONCE_SALT',       'Q>JP+t[$6L DuEl|gY_irG0BM[ul_44Dpq*Wj$drN|%{e`fi.9vH+?+X&|p>;Ypw');

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

define('WP_HOME', 'http://localhost/slaas/');
define('WP_SITEURL', 'http://localhost/slaas/');
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
