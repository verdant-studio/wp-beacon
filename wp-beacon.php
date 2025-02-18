<?php
/**
 * @package WP_Beacon
 * @author  Verdant Studio
 *
 * Plugin Name: WP Beacon
 * Plugin URI: https://www.verdant.studio/plugins/wp-beacon
 * Description: Synchronize your sites metrics to a no-code databases.
 * Version: 1.0.0
 * Author: Verdant Studio
 * Author URI: https://www.verdant.studio
 * License: GPLv2 or later
 * Text Domain: wp-beacon
 * Domain Path: /languages
 * Requires at least: 6.0
 * Network: true
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

const WP_BEACON_VERSION = '1.0.0';
const WP_BEACON_REQUIRED_WP_VERSION = '6.0';
const WP_BEACON_FILE = __FILE__;

define( 'WP_BEACON_DIR_PATH', plugin_dir_path( WP_BEACON_FILE ) );
define( 'WP_BEACON_PLUGIN_URL', plugins_url( '/', WP_BEACON_FILE ) );

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor-prefixed/autoload.php' ) ) {
	require_once __DIR__ . '/vendor-prefixed/autoload.php';
}

require_once __DIR__ . '/src/autoload.php';
require_once __DIR__ . '/src/Bootstrap.php';

add_action(
	'plugins_loaded',
	function () {
		$init = new WPBeacon\Bootstrap();
	}
);
