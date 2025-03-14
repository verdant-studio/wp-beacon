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

use WPBeacon\Helpers\OptionHelper;
use WPBeacon\Providers\SettingsServiceProvider;
use WPBeacon\Services\Integrations\AirtableService;
use WPBeacon\Services\Integrations\NocoDBService;

/**
 * Register wp_schedule_event service.
 *
 * @since 1.0.0
 */
class EventService extends Service
{
	private AirtableService $airtable_service;
	private NocoDBService $noco_db_service;

	public function __construct(
		AirtableService $airtable_service,
		NocoDBService $noco_db_service
	) {
		$this->airtable_service = $airtable_service;
		$this->noco_db_service  = $noco_db_service;
	}

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
	public function trigger_sync(): void
	{
		if (defined( 'WP_BEACON_SERVICE' )) {
			$service = WP_BEACON_SERVICE;
		} else {
			$settings = get_option( OptionHelper::get_settings_option_key() );
			$service  = $settings['service'] ?? null;
		}

		if ($service) {
			switch ($service) {
				case 'airtable':
					$this->airtable_service->sync();
					break;
				case 'nocodb':
					$this->noco_db_service->sync();
					break;
				default:
					break;
			}
		}
	}

	/**
	 * Register a single cron hook to trigger an event.
	 *
	 * @since 1.0.0
	 */
	private function register_single_cron_hook(): void
	{
		add_action( self::CRON_BEACON_PUSH_EVENT, array( $this, 'trigger_sync' ) );
	}

	/**
	 * Schedule a new wp event unless it already exists.
	 *
	 * @since 1.0.0
	 */
	public static function schedule( string $hook, string $recurrence ): void
	{
		$schedules = wp_get_schedules();

		// Ensure the recurrence is valid.
		if ( ! isset( $schedules[ $recurrence ] ) ) {
			$recurrence = SettingsServiceProvider::DEFAULT_CRON_SCHEDULE;
		}

		self::maybe_schedule_event( $hook, $recurrence );
	}

	/**
	 * Reschedule events.
	 *
	 * @since 1.0.0
	 */
	public static function reschedule( string $hook, string $recurrence ): void
	{
		self::unschedule( $hook );
		self::schedule( $hook, $recurrence );
	}

	/**
	 * Conditionally schedule a WordPress event if the setting is enabled.
	 */
	private static function maybe_schedule_event(string $hook, string $recurrence ): void
	{
		if (defined( 'WP_BEACON_SERVICE' ) && defined( 'WP_BEACON_SCHEDULE' )) {
			$settings = array(
				'service'  => WP_BEACON_SERVICE,
				'schedule' => WP_BEACON_SCHEDULE,
			);
		} else {
			$settings = get_option( OptionHelper::get_settings_option_key() );
		}

		if (self::is_enabled( $settings )) {
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
