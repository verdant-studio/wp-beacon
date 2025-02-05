<?php
/**
 * Api service provider.
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

use WPBeacon\Controllers\ApiController;

/**
 * Api service provider.
 *
 * @since 1.0.0
 */
class ApiServiceProvider extends ServiceProvider
{
	private ApiController $controller;

	/**
	 * The rest base path.
	 *
	 * @since 1.0.0
	 */
	const REST_BASE = 'wp-beacon/v1';


	public function __construct()
	{
		$this->controller = new ApiController();
	}

	/**
	 * Register the Api actions.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
		add_action( 'rest_api_init', array( $this, 'register_endpoints' ) );
	}

	/**
	 * Register endpoints.
	 *
	 * @since 1.0.0
	 */
	public function register_endpoints(): void
	{
		register_rest_route(
			self::REST_BASE,
			'/settings',
			array(
				array(
					'methods'             => \WP_REST_Server::READABLE,
					'callback'            => array( $this->controller, 'get_settings' ),
					'permission_callback' => array( $this->controller, 'get_options_permission' ),
				),
				array(
					'methods'             => \WP_REST_Server::EDITABLE,
					'callback'            => array( $this->controller, 'update_settings' ),
					'permission_callback' => array( $this->controller, 'get_options_permission' ),
				),
			)
		);
	}
}
