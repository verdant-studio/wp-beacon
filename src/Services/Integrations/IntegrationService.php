<?php
/**
 * Integration service.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Services\Integrations;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPBeacon\Services\EventService;

/**
 * Integration service.
 *
 * @since 1.0.0
 */
abstract class IntegrationService
{
	protected $settings;
	protected $ui_settings;

	public function __construct()
	{
		$this->ui_settings = get_option( 'wp_beacon_settings' );

		if ($this->is_config_set()) {
			$this->settings = $this->get_config_settings();
		} else {
			$this->settings = $this->ui_settings;
		}

		if ($this->has_schedule_changed()) {
			$this->reschedule_cron_job();
		}
	}

	/**
	 * Get the configuration settings.
	 *
	 * @since 1.0.0
	 */
	abstract protected function get_config_settings(): array;

	/**
	 * Check if the file configuration is set.
	 *
	 * @since 1.0.0
	 */
	abstract protected function is_config_set(): bool;

	/**
	 * Get the environment key (defaults to production).
	 */
	private function get_environment_key(): string
	{
		return defined( 'WP_ENVIRONMENT_TYPE' ) ? WP_ENVIRONMENT_TYPE : 'production';
	}

	/**
	 * Get the option key.
	 */
	protected function get_option_key(): string
	{
		return 'wp_beacon_site_' . $this->get_environment_key();
	}

	/**
	 * Check if the schedule has changed.
	 *
	 * @since 1.0.0
	 */
	public function has_schedule_changed(): bool
	{
		if ($this->is_config_set()) {
			$last_schedule = get_transient( 'wp_beacon_last_schedule' );
			return $last_schedule !== $this->settings['schedule'];
		}

		$current_settings = get_option( 'wp_beacon_settings' );
		return $current_settings['schedule'] !== $this->settings['schedule'];
	}

	/**
	 * Reschedule the cron job.
	 *
	 * @since 1.0.0
	 */
	public function reschedule_cron_job(): void
	{
		if ($this->settings && isset( $this->settings['schedule'] )) {
			set_transient( 'wp_beacon_last_schedule', $this->settings['schedule'] );
			EventService::reschedule( EventService::CRON_BEACON_PUSH_EVENT, $this->settings['schedule'] );
		}
	}
}
