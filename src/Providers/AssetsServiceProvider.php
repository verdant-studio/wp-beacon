<?php
/**
 * Assets service provider.
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

use Error;
use WPBeacon\Services\Integrations\NocoDBService;

/**
 * Register assets service provider.
 *
 * @since 1.0.0
 */
class AssetsServiceProvider extends ServiceProvider
{
	/**
	 * @inheritDoc
	 */
	public function register(): void
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );
	}

	/**
	 * Register the admin scripts.
	 *
	 * @since 1.0.0
	 *
	 * @throws Error Run npm build;
	 */
	public function register_admin_scripts( string $hook_suffix ): void
	{
		// Only load the scripts on the plugin settings page.
		if ('settings_page_wp-beacon' !== $hook_suffix) {
			return;
		}

		$script_asset_path = WP_BEACON_DIR_PATH . 'dist/settings.asset.php';

		if ( ! file_exists( $script_asset_path )) {
			throw new Error(
				'You need to run `npm run watch` or `npm run build` to be able to use this plugin first.'
			);
		}

		$script_asset = require $script_asset_path;

		wp_enqueue_style(
			wp_beacon_prefix( 'settings-css' ),
			wp_beacon_asset_url( 'settings.css' ),
			array( 'wp-components' ),
			$script_asset['version']
		);

		wp_register_script(
			wp_beacon_prefix( 'settings-js' ),
			wp_beacon_asset_url( 'settings.js' ),
			$script_asset['dependencies'],
			$script_asset['version'],
			true
		);

		wp_enqueue_script( wp_beacon_prefix( 'settings-js' ) );

		$noco_db_service = new NocoDBService();

		wp_localize_script(
			wp_beacon_prefix( 'settings-js' ),
			'wpBeaconSettings',
			array(
				'nonce'      => wp_create_nonce( 'wp_rest' ),
				'ajax_base'  => esc_url_raw( rest_url( 'wp-beacon/v1' ) ),
				'config_set' => $noco_db_service->is_config_set(),
			)
		);
	}
}
