<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'TWBB_VERSION', '1.3.319' );
define( 'TWBB_PREFIX', 'twbb' );
define( 'TWBB_DIR', dirname( __FILE__ ) );
define( 'TWBB_URL', plugins_url( plugin_basename( dirname( __FILE__ ) ) ) );
define( 'TWBB_DEV', FALSE );
define( 'TWBB_DEBUG', FALSE );
define( 'TWBB_ELEMENTOR_MIN_VERSION', '3.5.0' );

if(!defined('TENWEB_PREFIX')){
    define('TENWEB_PREFIX', 'tenweb');
}

if(!defined('TENWEB_VERSION')){
    define('TENWEB_VERSION', 'two-123.131.32');
}

// in seconds
if (!defined('TW_IN_PROGRESS_LOCK')) {
    define('TW_IN_PROGRESS_LOCK', 300);
}

if (!defined('TENWEB_MANAGER_ID')) {
    define('TENWEB_MANAGER_ID', 51);
}

//TODO check or change White label
//if ( class_exists( '\Tenweb_Manager\Helper' ) && method_exists( '\Tenweb_Manager\Helper', 'get_company_name' ) && strtolower( \Tenweb_Manager\Helper::get_company_name() ) !== '10web' ) {
//	define( 'TENWEB_WHITE_LABEL', TRUE );
//} else {
//	define( 'TENWEB_WHITE_LABEL', FALSE );
//}

define( 'TENWEB_WHITE_LABEL', TRUE );

if (!function_exists('get_plugins')) {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
if (!defined('TW_HOSTED_ON_10WEB')) {
    $mu_plugins = get_mu_plugins();
    define('TW_HOSTED_ON_10WEB', TRUE);
}

require_once 'env.php';
