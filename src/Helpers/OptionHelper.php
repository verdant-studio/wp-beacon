<?php
/**
 * Option helper.
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
 * Option helper.
 *
 * @since 1.0.0
 */
class OptionHelper
{
	/**
	 * Get the option key based on the given prefix.
	 */
	private static function get_option_key( string $prefix ): string
	{
		$env_key = EnvironmentHelper::get_environment_key();

		return $env_key ? $prefix . '_' . $env_key : $prefix;
	}

	/**
	 * Get the site option key.
	 */
	public static function get_site_option_key(): string
	{
		return self::get_option_key( 'wp_beacon_site' );
	}

	/**
	 * Get the settings option key.
	 */
	public static function get_settings_option_key(): string
	{
		return self::get_option_key( 'wp_beacon_settings' );
	}
}
