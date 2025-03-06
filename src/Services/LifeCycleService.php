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

use WPBeacon\Helpers\OptionHelper;

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
		if ( is_multisite() ) {
			delete_site_option( OptionHelper::get_settings_option_key() );

			$sites = get_sites();
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
				delete_option( OptionHelper::get_site_option_key() );
				restore_current_blog();
			}
		} else {
			delete_option( OptionHelper::get_site_option_key() );
			delete_option( OptionHelper::get_settings_option_key() );
		}
	}
}
