<?php
/**
 * API controller.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Controllers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPBeacon\Services\EventService;

/**
 * API controller.
 *
 * @since 1.0.0
 */
class ApiController
{
	/**
	 * Get settings.
	 *
	 * @since 1.0.0
	 */
	public static function get_settings(): \WP_REST_Response
	{
		$value = get_option( 'wp_beacon_settings' );

		return new \WP_REST_Response(
			array(
				'success' => true,
				'value'   => $value,
			),
			200
		);
	}

	/**
	 * Update settings.
	 *
	 * @since 1.0.0
	 */
	public static function update_settings( \WP_REST_Request $request ): \WP_REST_Response
	{
		$service          = $request->get_param( 'service' );
		$interval         = $request->get_param( 'interval' );
		$service_settings = $request->get_param( 'service_settings' );

		$settings = array(
			'service'          => $service,
			'interval'         => $interval,
			'service_settings' => $service_settings,
		);

		update_option( 'wp_beacon_settings', $settings );

		EventService::reschedule( $interval );

		return new \WP_REST_Response(
			array(
				'success' => true,
			),
			200
		);
	}

	/**
	 * Check if user may access the endpoints.
	 *
	 * @since 1.0.0
	 */
	public static function get_options_permission()
	{
		if ( ! current_user_can( 'manage_options' )) {
			return new \WP_Error( 'rest_forbidden', esc_html__( 'You do not have permissions to manage options.', 'wp-beacon' ), array( 'status' => 401 ) );
		}

		return true;
	}
}
