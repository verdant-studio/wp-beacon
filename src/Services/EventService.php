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

use WPBeacon\Providers\SettingsServiceProvider;

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
	const CRON_BEACON_PUSH_EVENT = 'wp_beacon_push_event';

	/**
	 * Register hooks.
	 */
	public function register(): void
	{
		// Register WP-Cron hooks.
		$this->register_cron_hooks();
	}

	/**
	 * Register WP-Cron hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_cron_hooks(): void
	{
		$this->register_single_cron_hook();
	}

	/**
	 * Trigger a sync event.
	 *
	 * @since 1.0.0
	 */
	public static function trigger_sync(): void
	{
		do_action( 'wp_beacon_trigger_sync', 1 );
	}

	/**
	 * Register a single cron hook to trigger an event.
	 *
	 * @since 1.0.0
	 */
	private function register_single_cron_hook(): void
	{
		add_action( self::CRON_BEACON_PUSH_EVENT, array( self::class, 'trigger_sync' ) );
	}

	/**
	 * Schedule a new wp event unless it already exists.
	 *
	 * @since 1.0.0
	 */
	public static function schedule( $schedule, $recurrence ): void
	{
		$schedules = SettingsServiceProvider::get_cron_schedules();

		// Ensure the recurrence is valid.
		if ( ! isset( $schedules[ $recurrence ] ) ) {
			$recurrence = SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		}

		self::maybe_schedule_event( $schedule, $recurrence );
	}

	/**
	 * Reschedule events.
	 *
	 * @since 1.0.0
	 */
	public static function reschedule(string $hook, string $recurrence ): void
	{
		self::unschedule( $hook );
		self::schedule( $hook, $recurrence );
	}

	/**
	 * Conditionally schedule a WordPress event if the setting is enabled.
	 */
	private static function maybe_schedule_event( string $hook, string $recurrence ): void
	{
		$settings = get_option( 'wp_beacon_settings' );

		if ( self::is_enabled( $settings ) ) {
			self::schedule_event_if_not_exists( $hook, $recurrence );
		}
	}

	/**
	 * Check if a setting is enabled.
	 *
	 * @since 1.0.0
	 */
	private static function is_enabled( ?array $settings ): bool
	{
		return isset( $settings );
	}

	/**
	 * Schedule a WordPress event if it's not already scheduled.
	 *
	 * @since 1.0.0
	 */
	private static function schedule_event_if_not_exists( string $hook, string $recurrence ): void
	{
		if ( ! wp_next_scheduled( $hook ) ) {
			wp_schedule_event( time(), $recurrence, $hook );
		}
	}
	/**
	 * Clear scheduled wp event.
	 *
	 * @since 1.0.0
	 */
	public static function unschedule( string $hook ): void
	{
		$timestamp = wp_next_scheduled( $hook );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, $hook );
		}
	}
}
