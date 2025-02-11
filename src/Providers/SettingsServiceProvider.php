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

	public const DEFAULT_CRON_SCHEDULE = 'wp_beacon_12_hour';

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

		add_filter( 'cron_schedules', array( $this, 'register_cron_schedules' ) );
	}

	/**
	 * Register custom cron schedule intervals.
	 *
	 * @since 1.0.0
	 */
	public static function register_cron_schedules(array $schedules ): array
	{
		$custom_schedules = self::get_cron_schedules();

		return array_merge( $schedules, $custom_schedules );
	}

	/**
	 * Define custom cron schedule intervals.
	 *
	 * @since 1.0.0
	 */
	public static function get_cron_schedules(): array
	{
		return array(
			'wp_beacon_1_day'   => array(
				'interval' => 86400,
				'display'  => __( 'Every day', 'wp-beacon' ),
			),
			'wp_beacon_12_hour' => array(
				'interval' => 43200,
				'display'  => __( 'Every 12 hours', 'wp-beacon' ),
			),
			'wp_beacon_6_hour'  => array(
				'interval' => 21600,
				'display'  => __( 'Every 6 hours', 'wp-beacon' ),
			),
			'wp_beacon_4_hour'  => array(
				'interval' => 14400,
				'display'  => __( 'Every 4 hours', 'wp-beacon' ),
			),
			'wp_beacon_2_hour'  => array(
				'interval' => 7200,
				'display'  => __( 'Every 2 hours', 'wp-beacon' ),
			),
			'wp_beacon_1_hour'  => array(
				'interval' => 3600,
				'display'  => __( 'Every hour', 'wp-beacon' ),
			),
		);
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
