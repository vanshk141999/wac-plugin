<?php
/**
 * Class Admin
 *
 * @package WAC\Tests\Admin
 */

use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests Plugin Initialization.
 */
class Admin extends TestCase {
	/**
	 * Testing if all required constants are defined.
	 *
	 * @return void
	 */
	public function test_constants() {
		$this->assertTrue( defined( 'WAC_VERSION' ) );
	}
}
