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
	public function sync()
	{
		$settings = $this->settings;

		if ( ! $settings || ! isset( $settings['service_settings']['url'], $settings['service_settings']['table_id'], $settings['service_settings']['xc_token'] )) {
			return new WP_Error(
				'missing_settings',
				esc_html__( 'NocoDB sync error: Missing required settings.', 'wp-beacon' )
			);
		}

		if ($this->site_record && isset( json_decode( $this->site_record )->Id )) {
			return $this->update_record();
		} else {
			return $this->create_record();
		}
	}

	/**
	 * Create the record.
	 *
	 * @return string|WP_Error
	 * @since 1.0.0
	 */
	private function create_record()
	{
		$url      = $this->settings['service_settings']['url'];
		$table_id = $this->settings['service_settings']['table_id'];
		$xc_token = $this->settings['service_settings']['xc_token'];

		$response = wp_remote_post(
			"$url/api/v2/tables/$table_id/records",
			array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'xc-token'     => $xc_token,
				),
				'body'    => wp_json_encode(
					array(
						'Site'              => $this->get_site_name(),
						'URL'               => $this->get_site_url(),
						'Health'            => $this->get_site_health_rating(),
						'WP Version'        => $this->get_current_wp_version(),
						'Updates available' => $this->get_amount_of_plugin_updates(),
					)
				),
			)
		);

		if (is_wp_error( $response )) {
			return new WP_Error(
				'nocodb_sync_error',
				sprintf(
					// translators: %s: error message.
					esc_html__( 'NocoDB sync error: %s', 'wp-beacon' ),
					$response->get_error_message()
				)
			);
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );
			if ($response_code >= 200 && $response_code < 300) {
				return update_option( 'wp_beacon_site_record', wp_remote_retrieve_body( $response ) );
			} else {
				return new WP_Error(
					'nocodb_sync_error',
					sprintf(
						// translators: %1$d: response code, %2$s: response body.
						esc_html__( 'NocoDB sync error: HTTP %1$d - %2$s', 'wp-beacon' ),
						$response_code,
						wp_remote_retrieve_body( $response )
					)
				);
			}
		}
	}

	/**
	 * @return bool|WP_Error
	 */
	private function update_record()
	{
		$url      = $this->settings['service_settings']['url'];
		$table_id = $this->settings['service_settings']['table_id'];
		$xc_token = $this->settings['service_settings']['xc_token'];

		$response = wp_remote_post(
			"$url/api/v2/tables/$table_id/records",
			array(
				'method'  => 'PATCH',
				'headers' => array(
					'Content-Type' => 'application/json',
					'xc-token'     => $xc_token,
				),
				'body'    => wp_json_encode(
					array(
						'Id'                => json_decode( $this->site_record )->Id,
						'Site'              => $this->get_site_name(),
						'URL'               => $this->get_site_url(),
						'Health'            => $this->get_site_health_rating(),
						'WP Version'        => $this->get_current_wp_version(),
						'Updates available' => 2,
					)
				),
			)
		);

		if (is_wp_error( $response )) {
			return new WP_Error(
				'nocodb_sync_error',
				esc_html__( 'NocoDB sync error: ' . $response->get_error_message(), 'wp-beacon' )
			);
		} else {
			$response_code = wp_remote_retrieve_response_code( $response );

			if ($response_code >= 200 && $response_code < 300) {
				return update_option( 'wp_beacon_site_record', wp_remote_retrieve_body( $response ) );
			} else {
				return new WP_Error(
					'nocodb_sync_error',
					esc_html__( 'NocoDB sync error: HTTP ' . $response_code . ' - ' . wp_remote_retrieve_body( $response ), 'wp-beacon' )
				);
			}
		}
	}
}
