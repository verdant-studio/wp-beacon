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
		return get_bloginfo( 'name' ) ?? 'N.A.';
	}

	/**
	 * Get the site url.
	 *
	 * @since 1.0.0
	 */
	public function get_site_url(): ?string
	{
		return get_bloginfo( 'url' ) ?? 'N.A.';
	}

	/**
	 * Get the current WP version installed on the site.
	 *
	 * @since 1.0.0
	 */
	public function get_current_wp_version(): ?string
	{
		return get_bloginfo( 'version' ) ?? 'N.A.';
	}

	/**
	 * Get the site health and turn it into a rating.
	 *
	 * @since 1.0.0
	 */
	public function get_site_health_rating(): int
	{
		return 5;
	}

	/**
	 * Get amount of plugin updates.
	 *
	 * @since 1.0.0
	 */
	public function get_amount_of_plugin_updates(): int
	{
		return 9;
	}
}
