<?php
/**
 * Register wp_schedule_event service.
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
 * Register wp_schedule_event service.
 *
 * @since 1.0.0
 */
class EventService extends Service
{
	/**
	 * The name of the push event.
	 *
	 * @since 1.0.0
	 */
	const PUSH_EVENT = 'wp_beacon_push_event';

	/**
	 * Schedule a new wp event unless it already exists.
	 *
	 * @since 1.0.0
	 *
	 * @param string $interval | hourly, twicedaily, daily, weekly
	 */
	public static function schedule( string $interval, string $event_name = null ): void
	{
		$args = $event_name ? array( $event_name ) : array();

		if ( ! wp_next_scheduled( self::PUSH_EVENT, $args )) {
			wp_schedule_event( time(), $interval, self::PUSH_EVENT, $args );
		}
	}

	/**
	 * Reschedule event when the settings have changed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $interval | hourly, twicedaily, daily, weekly
	 */
	public static function reschedule( string $interval, string $event_name = null ): void
	{
		self::unschedule( $event_name );
		self::schedule( $interval, $event_name );
	}

	/**
	 * Clear scheduled wp event.
	 *
	 * @since 1.0.0
	 */
	public static function unschedule( string $event_name = null ): void
	{
		$args = $event_name ? array( $event_name ) : array();

		if ( wp_next_scheduled( self::PUSH_EVENT, $args ) ) {
			wp_clear_scheduled_hook( self::PUSH_EVENT, $args );
		}
	}
}
