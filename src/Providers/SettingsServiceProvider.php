<?php
/**
 * Register settings service provider.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPBeacon\Controllers\SettingsController;

/**
 * Register settings service provider.
 *
 * @since 1.0.0
 */
class SettingsServiceProvider extends ServiceProvider
{
	/**
	 * @var SettingsController;
	 */
	private SettingsController $controller;

	public const DEFAULT_CRON_SCHEDULE = 'daily';

	public function __construct( SettingsController $controller )
	{
		$this->controller = $controller;
	}

	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu', array( $this, 'add_plugin_menu' ) );
	}

	/**
	 * Add plugin menu.
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_menu(): void {
		$parent     = is_multisite() ? 'settings.php' : 'options-general.php';
		$capability = is_multisite() ? 'manage_network_options' : 'manage_options';

		add_submenu_page(
			$parent,
			esc_html__( 'WP Beacon', 'wp-beacon' ),
			esc_html_x( 'WP Beacon', 'Menu item', 'wp-beacon' ),
			$capability,
			'wp-beacon',
			array( $this->controller, 'render_page' )
		);
	}
}
