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
		$settings = get_option( 'wp_beacon_settings' );
		$schedules = wp_get_schedules();

		// Show only the schedules defined for this plugin.
		$schedules = array_filter(
			$schedules,
			static fn ( $key ) => str_starts_with( $key, 'wp_beacon_' ),
			ARRAY_FILTER_USE_KEY
		);
		uasort(
			$schedules,
			static fn ( array $a, array $b ): int => (int) $b['interval'] - (int) $a['interval']
		);
		$schedules = array_map(
			static fn ( array $schedule ): string => $schedule['display'],
			$schedules
		);

		return new \WP_REST_Response(
			array(
				'success' => true,
				'value'   => [
					'schedules' => $schedules,
					'settings' => $settings,
				],
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
		$schedule         = $request->get_param( 'schedule' );
		$service          = $request->get_param( 'service' );
		$service_settings = $request->get_param( 'service_settings' );

		$settings = array(
			'schedule'         => $schedule,
			'service'          => $service,
			'service_settings' => $service_settings,
		);

		update_option( 'wp_beacon_settings', $settings );

		EventService::reschedule( EventService::CRON_BEACON_PUSH_EVENT, $schedule );

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
