<?php
/**
 * NocoDB service.
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

use WP_Error;
use WPBeacon\Traits\MetricsTrait;

/**
 * NocoDB service.
 *
 * @since 1.0.0
 */
class NocoDBService
{
	use MetricsTrait;

	private const API_PATH = '/api/v2/tables/';
	private const CONTENT_TYPE = 'application/json';

	private $settings;
	private $site_record;

	public function __construct()
	{
		$this->settings    = get_option( 'wp_beacon_settings' );
		$this->site_record = get_option( 'wp_beacon_site_record' );
	}

	/**
	 * Sync.
	 *
	 * @since 1.0.0
	 */
	public function sync(): void
	{
		if (is_multisite()) {
			$sites = get_sites();

			foreach ($sites as $site) {
				switch_to_blog( $site->blog_id );
				$this->settings = get_option('wp_beacon_settings');
				$this->site_record = get_option('wp_beacon_site_record');
				$this->sync_single_site();
				restore_current_blog();
			}
		} else {
			$this->sync_single_site();
		}
	}

	/**
	 * Sync a single site.
	 *
	 * @since 1.0.0
	 */
	public function sync_single_site()
	{
		if ( ! $this->has_valid_settings()) {
			return new WP_Error(
				'missing_settings',
				esc_html__( 'NocoDB sync error: Missing required settings.', 'wp-beacon' )
			);
		}

		$record_id = $this->site_record ? json_decode($this->site_record)->Id : null;

		if ($record_id && $this->record_exists($record_id)) {
			return $this->update_record();
		} else {
			return $this->create_record();
		}
	}

	/**
	 * Check if the settings are valid.
	 *
	 * @return bool
	 * @since 1.0.0
	 */
	private function has_valid_settings(): bool
	{
		return $this->settings && isset( $this->settings['service_settings']['url'], $this->settings['service_settings']['table_id'], $this->settings['service_settings']['xc_token'] );
	}

	/**
	 * Check if the record exists.
	 *
	 * @since 1.0.0
	 */
	private function record_exists($record_id): bool
	{
		$url = $this->settings['service_settings']['url'] . self::API_PATH . $this->settings['service_settings']['table_id'] . '/records/' . $record_id;

		$response = wp_remote_get(
			$url,
			array(
				'headers' => array(
					'Content-Type' => self::CONTENT_TYPE,
					'xc-token'     => $this->settings['service_settings']['xc_token'],
				),
			)
		);

		if (is_wp_error($response)) {
			return false;
		}

		$response_code = wp_remote_retrieve_response_code($response);
		return $response_code === 200;
	}

	/**
	 * Create the record.
	 *
	 * @since 1.0.0
	 */
	private function create_record()
	{
		$response = $this->send_request('POST', $this->get_request_body());

		return $this->handle_response($response);
	}

	/**
	 * Update the record.
	 *
	 * @since 1.0.0
	 */
	private function update_record()
	{
		$body = $this->get_request_body();
		$body['Id'] = json_decode( $this->site_record )->Id;

		$response = $this->send_request('PATCH', $body);

		return $this->handle_response($response);
	}

	/**
	 * Send the request.
	 *
	 * @return array|WP_Error
	 * @since 1.0.0
	 */
	private function send_request(string $method, array $body)
	{
		$url = $this->settings['service_settings']['url'] . self::API_PATH . $this->settings['service_settings']['table_id'] . '/records';

		return wp_remote_post(
			$url,
			array(
				'method'  => $method,
				'headers' => array(
					'Content-Type' => self::CONTENT_TYPE,
					'xc-token'     => $this->settings['service_settings']['xc_token'],
				),
				'body'    => wp_json_encode($body),
			)
		);
	}

	/**
	 * Get request body.
	 *
	 * @since 1.0.0
	 */
	private function get_request_body(): array
	{
		return array(
			'Site'              => $this->get_site_name(),
			'URL'               => $this->get_site_url(),
			'Health'            => $this->get_site_health_rating(),
			'WP Version'        => $this->get_current_wp_version(),
			'Updates available' => $this->get_amount_of_plugin_updates(),
			'Last sync'         => date( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Handle the response.
	 *
	 * @return array|string|WP_Error
	 * @since 1.0.0
	 */
	private function handle_response(array $response)
	{
		if (is_wp_error( $response )) {
			return new WP_Error(
				'nocodb_sync_error',
				sprintf(
					esc_html__( 'NocoDB sync error: %s', 'wp-beacon' ),
					$response->get_error_message()
				)
			);
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ($response_code >= 200 && $response_code < 300) {
			return update_option( 'wp_beacon_site_record', wp_remote_retrieve_body( $response ) );
		} else {
			return new WP_Error(
				'nocodb_sync_error',
				sprintf(
					esc_html__( 'NocoDB sync error: HTTP %1$d - %2$s', 'wp-beacon' ),
					$response_code,
					wp_remote_retrieve_body( $response )
				)
			);
		}
	}
}
