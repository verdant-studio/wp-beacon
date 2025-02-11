<?php
/**
 * App service provider (registers general plugins functionality).
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

use WPBeacon\Services\EventService;
use WPBeacon\Services\LifeCycleService;

/**
 * App service provider (registers general plugins functionality).
 *
 * @since 1.0.0
 */
class AppServiceProvider extends ServiceProvider
{
	public function __construct(
		EventService $event_service,
		LifeCycleService $life_cycle_service
	) {
		$this->services = array(
			'event'      => $event_service,
			'life_cycle' => $life_cycle_service,
		);
	}
}
