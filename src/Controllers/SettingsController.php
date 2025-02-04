<?php
/**
 * Settings controller.
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
 * Settings controller.
 *
 * @since 1.0.0
 */
class SettingsController extends BaseController
{
	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 *
	 * @throws Exception - if file not found throw exception
	 * @throws Exception - if data is not array throw exception
	 */
	public function render_page(): void
	{
		$this->render( 'settings-page.php' );
	}
}
