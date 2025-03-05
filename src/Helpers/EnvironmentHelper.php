<?php
/**
 * Environment helper.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon\Helpers;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Environment helper.
 *
 * @since 1.0.0
 */
class EnvironmentHelper
{
	/**
	 * Get the environment key (defaults to production).
	 */
	public static function get_environment_key(): string
	{
		return defined( 'WP_ENVIRONMENT_TYPE' ) ? WP_ENVIRONMENT_TYPE : '';
	}
}
