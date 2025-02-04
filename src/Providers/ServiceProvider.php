<?php
/**
 * Register service provider.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Providers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Register service provider.
 *
 * @since 1.0.0
 */
class ServiceProvider
{
	protected array $services = array();

	/**
	 * Registers the services.
	 */
	public function register(): void
	{
		foreach ( $this->services as $service ) {
			$service->register();
		}
	}

	/**
	 * Boots the services.
	 */
	public function boot(): void
	{
		foreach ( $this->services as $service ) {
			if ( false === $service ) {
				continue;
			}
			$service->boot();
		}
	}
}
