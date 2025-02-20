<?php
/**
 * Metrics trait.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Traits;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Metrics trait.
 *
 * @since 1.0.0
 */
trait MetricsTrait
{
	/**
	 * Get the site name.
	 *
	 * @since 1.0.0
	 */
	public function get_site_name(): ?string
	{
		return get_bloginfo( 'name' ) ?? __( 'N/A', 'wp-beacon' );
	}

	/**
	 * Get the site url.
	 *
	 * @since 1.0.0
	 */
	public function get_site_url(): ?string
	{
		return get_bloginfo( 'url' ) ?? __( 'N/A', 'wp-beacon' );
	}

	/**
	 * Get the current WP version installed on the site.
	 *
	 * @since 1.0.0
	 */
	public function get_current_wp_version(): ?string
	{
		return get_bloginfo( 'version' ) ?? __( 'N/A', 'wp-beacon' );
	}

	/**
	 * Get the site health and turn it into a rating.
	 *
	 * @since 1.0.0
	 */
	public function get_site_health_rating(): int
	{
		$summary = get_transient( 'health-check-site-status-result' );

		// If the transient returns false, handle it appropriately.
		if (false === $summary) {
			return 0;
		}

		$summary = json_decode( $summary, true );

		if (is_array( $summary )) {
			$good        = $summary['good'] ?? 0;
			$recommended = $summary['recommended'] ?? 0;
			$critical    = $summary['critical'] ?? 0;

			// Calculate the rating based on the counts.
			$total = $good + $recommended + $critical;

			if (0 === $total) {
				return 0;
			}

			// Example calculation: 5 stars for all good, 3 stars for all recommended, 1 star for all critical.
			$rating = ( 5 * $good + 3 * $recommended + 1 * $critical ) / $total;
			return (int) round( $rating );
		}

		return 0;
	}

	/**
	 * Get amount of plugin updates.
	 *
	 * @since 1.0.0
	 */
	public function get_amount_of_plugin_updates(): int
	{
		if ( ! function_exists( 'wp_update_plugins' )) {
			require_once ABSPATH . WPINC . '/update.php';
		}

		wp_update_plugins();
		$update_plugins = get_site_transient( 'update_plugins' );

		$count_outdated = 0;

		if (isset( $update_plugins->response )) {
			foreach ($update_plugins->response as $update_plugin) {
				++$count_outdated;
			}
		}

		return $count_outdated;
	}
}
