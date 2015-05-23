<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link https://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

define('WP_CACHE', true); //Added by WP-Cache Manager
define( 'WPCACHEHOME', '/Applications/MAMP/htdocs/sexik.ee/wp-content/plugins/wp-super-cache/' ); //Added by WP-Cache Manager
define( 'WP_MEMORY_LIMIT', '96M' );

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'd51014sd96054');

/** MySQL database username */
define('DB_USER', 'd51014sa94112');

/** MySQL database password */
define('DB_PASSWORD', 'Roland112');

/** MySQL hostname */
define('DB_HOST', 'd51014.mysql.zone.ee');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_HOME', 'http://localhost/sexik.ee/');
define('WP_SITEURL', 'http://localhost/sexik.ee/');
define( 'WPLANG', 'en_GB' );
/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '3+zKy`hyJi%~*B6zp4M)Nv>FH5wtbT,k94Em#!kp=LN[_5FT[j(?-JZ!2~P|{-{O');
define('SECURE_AUTH_KEY',  '$]$ZNcYG|;nO~#6b}Q7w;s 2DN@(I|Sc56i}|(eC_*HZ(5 tk,a-pwqMAT*D X{#');
define('LOGGED_IN_KEY',    '5DAid5>$jTCeI|lDi)ij&S*9#;-GE|+h/$gg`=WYW^UcXHZ|TJBVN&PMdDrM<b`-');
define('NONCE_KEY',        '1YKNMOyQ`G0|SK_yTs+ule?,[!)IwzPU%H(hHDU:.O)Y>G8p8b/$(XUtO&daA#*O');
define('AUTH_SALT',        'nf-x>a?t$J<3&ZW7!|7pa1V@{N}Wf#bV_J8K6j:iGJtIyWYOb1KbTzo=w:dg@S%1');
define('SECURE_AUTH_SALT', 'MNBjl`^f# g-S*xW@{OlM-nDp7YV O):B.Smc4Z7LWCT|MzS6evUo]cM0}J{#d+9');
define('LOGGED_IN_SALT',   '9U|z`~V)q}+m;PuA:-KcQl e)<q+:5}!L4MN]I9]NQk*4r_7Z>wgg21X?ZuyyBq%');
define('NONCE_SALT',       '.9v.?]N<hW[57|t/~X7z67_+L8M|ByFCCOY!;E6[%$%@U!v,N[,{B|+wI?E3>2px');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_sex';

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
