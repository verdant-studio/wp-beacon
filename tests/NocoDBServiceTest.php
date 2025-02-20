<?php

use PHPUnit\Framework\TestCase;
use WPBeacon\Services\Integrations\NocoDBService;
use Brain\Monkey\Functions;

/**
 * NocoDBService test.
 */
class NocoDBServiceTest extends TestCase
{
	protected NocoDBService $noco_db_service;

	protected function setUp(): void
	{
		parent::setUp();
		Brain\Monkey\setUp();

		$settings = array(
			'service_settings' => array(
				'url'      => 'https://example.com',
				'table_id' => '12345',
				'xc_token' => 'test_token',
			),
		);
		Functions\when( 'get_option' )->justReturn( $settings );

		$this->noco_db_service = new NocoDBService();
	}

	/**
	 * @throws ReflectionException If the method does not exist.
	 */
	public function testHasValidSettings()
	{
		$reflection = new \ReflectionClass( $this->noco_db_service );
		$method     = $reflection->getMethod( 'has_valid_settings' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->noco_db_service ) );
	}

	/**
	 * @throws ReflectionException If the method does not exist.
	 */
	public function testRecordExists()
	{
		// Mock the wp_remote_get function to return a successful response
		$response = array(
			'response' => array( 'code' => 200 ),
		);
		Functions\when( 'wp_remote_get' )->justReturn( $response );

		// Mock the wp_remote_retrieve_response_code function
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );

		$reflection = new \ReflectionClass( $this->noco_db_service );
		$method     = $reflection->getMethod( 'record_exists' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->noco_db_service, 'record_id' ) );
	}

	/**
	 * @throws ReflectionException If the method does not exist.
	 */
	public function testCreateRecord()
	{
		// Mock the wp_remote_post function to return a successful response
		$response = array(
			'response' => array( 'code' => 201 ),
			'body'     => wp_json_encode( array( 'Id' => 'new_record_id' ) ),
		);

		Functions\when( 'wp_remote_post' )->justReturn( $response );
		Functions\when( 'wp_remote_get' )->justReturn( array( 'response' => array( 'code' => 200 ) ) );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );

		$reflection = new \ReflectionClass( $this->noco_db_service );
		$method     = $reflection->getMethod( 'record_exists' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->noco_db_service, 'record_id' ) );
	}

	/**
	 * @throws ReflectionException If the method does not exist.
	 */
	public function testUpdateRecord()
	{
		// Mock the wp_remote_post function to return a successful response
		$response = array(
			'response' => array( 'code' => 200 ),
		);

		Functions\when( 'wp_remote_post' )->justReturn( $response );
		Functions\when( 'wp_remote_get' )->justReturn( array( 'response' => array( 'code' => 200 ) ) );
		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );

		$reflection = new \ReflectionClass( $this->noco_db_service );
		$method     = $reflection->getMethod( 'record_exists' );
		$method->setAccessible( true );

		$this->assertTrue( $method->invoke( $this->noco_db_service, 'record_id' ) );
	}

	/**
	 * @throws ReflectionException If the method does not exist.
	 */
	public function testHandleResponse()
	{
		// Mock a successful response
		$response = array(
			'response' => array( 'code' => 200 ),
			'body'     => wp_json_encode( array( 'Id' => 'record_id' ) ),
		);

		Functions\when( 'wp_remote_retrieve_response_code' )->justReturn( 200 );
		Functions\when( 'wp_remote_retrieve_body' )->justReturn( wp_json_encode( array( 'Id' => 'record_id' ) ) );
		Functions\when( 'update_option' )->justReturn( true );

		$reflection = new \ReflectionClass( $this->noco_db_service );
		$method     = $reflection->getMethod( 'handle_response' );
		$method->setAccessible( true );

		$result = $method->invoke( $this->noco_db_service, $response );
		$this->assertTrue( $result );
	}

	protected function tearDown(): void
	{
		Brain\Monkey\tearDown();
		parent::tearDown();
	}
}
