<?php
/**
 * Plugin helpers.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */
if ( ! function_exists( 'wp_beacon_prefix' )) {
	function wp_beacon_prefix( $name ): string
	{
		return 'wp-beacon-' . $name;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */
if ( ! function_exists( 'wp_beacon_url' )) {
	function wp_beacon_url( string $path ): string
	{
		return WP_BEACON_PLUGIN_URL . $path;
	}
}

/**
 * Add prefix for the given string.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */
if ( ! function_exists( 'wp_beacon_asset_url' )) {
	function wp_beacon_asset_url( string $path ): string
	{
		return wp_beacon_url( 'dist/' . $path );
	}
}

/**
 * Render a view.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 *
 * @throws Exception - if file not found throw exception
 * @throws Exception - if data is not array throw exception
 */
if ( ! function_exists( 'wp_beacon_render_view' )) {
	function wp_beacon_render_view( string $file_path, $data = array() )
	{
		$file = WP_BEACON_DIR_PATH . 'src/Views/' . $file_path;

		if ( ! file_exists( $file )) {
			throw new Exception( 'File not found' );
		}
		if ( ! is_array( $data )) {
			throw new Exception( 'Expected array as data' );
		}

		extract($data, EXTR_PREFIX_SAME, 'todo');	// @phpcs:ignore

		return require_once $file;
	}
}
