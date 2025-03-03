<?php
/**
 * PHPUnit Test Case for Frontend Class
 *
 * @package WAC\Tests
 */

namespace WAC\Tests;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WAC\Includes\Frontend;
use WP_Query;

/**
 * Class FrontendTest
 *
 * @package WAC\Tests
 */
class FrontendTest extends TestCase {
    /**
     * Frontend instance
     *
     * @var Frontend
     */
    protected $frontend;

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();
        
        // Create instance of Frontend class.
        $this->frontend = new Frontend();
    }

}
