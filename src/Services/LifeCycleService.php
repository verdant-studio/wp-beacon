<?php
/**
 * Register life cycle service.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Services;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Register life cycle service.
 *
 * @since 1.0.0
 */
class LifeCycleService extends Service
{
	/**
	 * Register the service.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
		register_deactivation_hook(
			WP_BEACON_FILE,
			array( $this, 'deactivate' )
		);

		register_uninstall_hook(
			WP_BEACON_FILE,
			array( __CLASS__, 'uninstall' )
		);
	}

	/**
	 * Plugin deactivation callback.
	 *
	 * @since 1.0.0
	 */
	public function deactivate(): void
	{
		EventService::unschedule( 'wp_beacon_trigger_sync' );
	}

	/**
	 * Plugin uninstall callback.
	 *
	 * @since 1.0.0
	 */
	public static function uninstall(): void
	{
	}
}
