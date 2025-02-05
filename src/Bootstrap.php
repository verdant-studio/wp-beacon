<?php
/**
 * Bootstrap providers and containers.
 *
 * @package WP_Beacon
 * @author  Verdant Studio
 * @since   1.0.0
 */

namespace WPBeacon;

/**
 * Exit when accessed directly.
 */
if ( ! defined( 'ABSPATH' )) {
	exit;
}

use WPBeacon\Providers\ApiServiceProvider;
use WPBeacon\Providers\AssetsServiceProvider;
use WPBeacon\Providers\SettingsServiceProvider;
use WPBeacon\Vendor_Prefixed\DI\ContainerBuilder;
use WPBeacon\Vendor_Prefixed\Psr\Container\ContainerInterface;

require_once __DIR__ . '/helpers.php';

/**
 * Bootstrap providers and containers.
 */
final class Bootstrap
{
	/**
	 * Dependency Injection container.
	 *
	 * @since 1.0.0
	 *
	 * @var ContainerInterface
	 */
	private static ContainerInterface $container;

	/**
	 * Dependency providers.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	private array $providers;

	/**
	 * Plugin constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->container = $this->build_container();
		$this->providers = $this->get_providers();
		$this->register_providers();
		$this->boot_providers();
	}

	/**
	 * Gets all providers
	 *
	 * @since 1.0.0
	 */
	protected function get_providers(): array
	{
		$providers = array(
			ApiServiceProvider::class,
			AssetsServiceProvider::class,
			SettingsServiceProvider::class,
		);
		foreach ( $providers as &$provider ) {
			$provider = $this->container->get( $provider );
		}
		return $providers;
	}

	/**
	 * Registers all providers.
	 *
	 * @since 1.0.0
	 */
	protected function register_providers(): void
	{
		foreach ( $this->providers as $provider ) {
			$provider->register();
		}
	}

	/**
	 * Boots all providers.
	 *
	 * @since 1.0.0
	 */
	protected function boot_providers(): void
	{
		foreach ( $this->providers as $provider ) {
			$provider->boot();
		}
	}

	/**
	 * Builds the container.
	 *
	 * @since 1.0.0
	 */
	protected function build_container(): ContainerInterface
	{
		$builder = new ContainerBuilder();

		// Use DIRECTORY_SEPARATOR to ensure the path works on both Windows and Unix-like systems.
		$config_path = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'php-di.php';

		// Add definitions using the correct path.
		$builder->addDefinitions( $config_path );
		$builder->useAnnotations( true );
		return $builder->build();
	}
}
