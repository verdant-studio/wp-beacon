<?php
/**
 * Airtable service.
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
use WPBeacon\Helpers\OptionHelper;
use WPBeacon\Traits\MetricsTrait;

/**
 * Airtable service.
 *
 * @since 1.0.0
 */
class AirtableService extends IntegrationService
{
	use MetricsTrait;

	private const API_PATH     = '/v0/';
	private const CONTENT_TYPE = 'application/json';

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	protected function get_config_settings(): array
	{
		return array(
			'service'          => WP_BEACON_SERVICE,
			'schedule'         => WP_BEACON_SCHEDULE,
			'service_settings' => array(
				'url'      => WP_BEACON_AIRTABLE_URL,
				'base_id'  => WP_BEACON_AIRTABLE_BASE_ID,
				'table_id' => WP_BEACON_AIRTABLE_TABLE_ID,
				'api_key'  => WP_BEACON_AIRTABLE_API_KEY,
			),
		);
	}

	/**
	 * @inheritDoc
	 *
	 * @since 1.0.0
	 */
	public function is_config_set(): bool
	{
		return defined( 'WP_BEACON_SERVICE' ) && defined( 'WP_BEACON_SCHEDULE' ) &&
			defined( 'WP_BEACON_AIRTABLE_URL' ) && defined( 'WP_BEACON_AIRTABLE_BASE_ID' ) &&
			defined( 'WP_BEACON_AIRTABLE_TABLE_ID' ) && defined( 'WP_BEACON_AIRTABLE_API_KEY' );
	}

	/**
	 * Sync a single site.
	 *
	 * @since 1.0.0
	 */
	protected function sync_single_site()
	{
		if ( ! $this->has_valid_settings()) {
			return new WP_Error(
				'missing_settings',
				esc_html__( 'Airtable sync error: missing required settings.', 'wp-beacon' )
			);
		}

		$site_record = get_option( OptionHelper::get_site_option_key() );
		$record_id   = null;

		if ($site_record) {
			$decoded_record = json_decode( $site_record, true );
			if (isset( $decoded_record['records'][0]['id'] )) {
				$record_id = $decoded_record['records'][0]['id'];
			}
		}

		if ($record_id && $this->record_exists( $record_id )) {
			return $this->update_record( $record_id );
		} else {
			// If the record does not exist in Airtable, delete the option in WordPress. This is in case the records were deleted in remotely.
			if ($record_id) {
				delete_option( OptionHelper::get_site_option_key() );
			}

			return $this->create_record();
		}
	}

	/**
	 * Check if the settings are valid.
	 *
	 * @since 1.0.0
	 */
	private function has_valid_settings(): bool
	{
		return isset( $this->settings['service_settings']['url'], $this->settings['service_settings']['base_id'], $this->settings['service_settings']['table_id'], $this->settings['service_settings']['api_key'] );
	}

	/**
	 * Check if the record exists.
	 *
	 * @since 1.0.0
	 */
	private function record_exists( $record_id ): bool
	{
		$url = $this->build_url( $record_id );

		$response = wp_remote_get(
			$url,
			array(
				'headers' => $this->get_headers(),
			)
		);

		if (is_wp_error( $response )) {
			return false;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		return 200 === $response_code;
	}

	/**
	 * Create the record.
	 *
	 * @since 1.0.0
	 */
	private function create_record()
	{
		$response = $this->send_request( 'POST', $this->get_request_body( 'POST' ) );
		$result   = $this->handle_response( $response );

		error_log( 'result', print_r( $result, true ) );

		// Only link records if the request was successful and the site is not the main site.
		if ( ! is_wp_error( $result ) && ! is_main_site()) {
			$this->link_records();
		}

		return $result;
	}

	/**
	 * Update the record.
	 *
	 * @since 1.0.0
	 */
	private function update_record( $record_id = null )
	{
		$body     = $this->get_request_body( 'PATCH' );
		$response = $this->send_request( 'PATCH', $body, $record_id );

		return $this->handle_response( $response );
	}

	/**
	 * Send the request.
	 *
	 * @return array|WP_Error
	 * @since 1.0.0
	 */
	private function send_request( string $method, array $body, ?string $record_id = '' )
	{
		if ('POST' === $method) {
			$url = $this->build_url();
		} else {
			$url = $this->build_url( $record_id );
		}

		return wp_remote_post(
			$url,
			array(
				'method'  => $method,
				'headers' => $this->get_headers(),
				'body'    => wp_json_encode( $body ),
			)
		);
	}

	/**
	 * Get request body.
	 *
	 * @since 1.0.0
	 */
	private function get_request_body( string $request_type ): array
	{
		$health = $this->get_site_health_rating();

		$fields = array(
			'Site'              => $this->get_site_name(),
			'URL'               => $this->get_site_url(),
			'Health'            => 0 === $health ? null : (int) $health, // Airtable does not accept a 0 rating value.
			'WP version'        => $this->get_current_wp_version(),
			'Updates available' => (int) $this->get_amount_of_plugin_updates(),
			'Last sync'         => gmdate( 'Y-m-d H:i:s' ),
			'Network'           => array( 'recnHHJFo4Fw1IuOA' ),
		);

		if ('POST' === $request_type) {
			return array(
				'records' => array(
					array(
						'fields' => $fields,
					),
				),
			);
		}

		return array(
			'fields' => $fields,
		);
	}

	/**
	 * Handle the response.
	 *
	 * @return bool|WP_Error
	 * @since 1.0.0
	 */
	private function handle_response( array $response )
	{
		if (is_wp_error( $response )) {
			return new WP_Error(
				'airtable_sync_error',
				sprintf(
					// translators: %s: error message.
					esc_html__( 'Airtable sync error: %s', 'wp-beacon' ),
					$response->get_error_message()
				)
			);
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		if ($response_code >= 200 && $response_code < 300) {
			return update_option( OptionHelper::get_site_option_key(), wp_remote_retrieve_body( $response ) );
		} else {
			return new WP_Error(
				'airtable_sync_error',
				sprintf(
					// translators: %1$d: response code, %2$s: response body.
					esc_html__( 'Airtable sync error: HTTP %1$d - %2$s', 'wp-beacon' ),
					$response_code,
					wp_remote_retrieve_body( $response )
				)
			);
		}
	}

	/**
	 * Build the URL.
	 *
	 * @since 1.0.0
	 */
	private function build_url( ?string $record_id = '' ): string
	{
		$url = $this->settings['service_settings']['url'] . self::API_PATH . $this->settings['service_settings']['base_id'] . '/' . $this->settings['service_settings']['table_id'];

		if ($record_id) {
			$url .= '/' . $record_id;
		}

		return $url;
	}

	/**
	 * Get headers.
	 *
	 * @since 1.0.0
	 */
	private function get_headers(): array
	{
		return array(
			'Content-Type'  => self::CONTENT_TYPE,
			'Authorization' => 'Bearer ' . $this->settings['service_settings']['api_key'],
		);
	}
}
