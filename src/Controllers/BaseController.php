<?php
/**
 * Base controller.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Controllers;

use Exception;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Base controller.
 *
 * @since 1.0.0
 */
class BaseController
{
	/**
	 * Register hooks callback.
	 *
	 * @since 1.0.0
	 */
	public function register(): void
	{
	}

	/**
	 * Render view file and pass data to the file.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception - if file not found throw exception
	 * @throws Exception - if data is not array throw exception
	 */
	public function render(string $file_path, array $data = array(), bool $buffer = false ): int
	{
		if ( ! $buffer) {
			return wp_beacon_render_view( $file_path, $data );
		}
		ob_start();
		wp_beacon_render_view( $file_path, $data );
		return ob_get_clean();
	}
}
