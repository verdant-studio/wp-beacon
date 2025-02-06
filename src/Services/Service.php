<?php
/**
 * Register base service.
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
 * Register base service.
 */
class Service
{
	/**
	 * Register the service.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
	}

	/**
	 * Called when all services are registered.
	 *
	 * @since 1.0.0
	 */
	public function boot(): void
	{
	}
}
