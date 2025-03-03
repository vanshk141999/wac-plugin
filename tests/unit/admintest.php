<?php
/**
 * PHPUnit Test Case for Admin Class
 *
 * @package WAC\Tests
 */

namespace WAC\Tests;

use Yoast\PHPUnitPolyfills\TestCases\TestCase;
use WAC\Includes\Admin;

/**
 * Class AdminTest
 *
 * @package WAC\Tests
 */
class AdminTest extends TestCase {
    /**
     * Admin instance
     *
     * @var Admin
     */
    protected $admin;

    /**
     * Set up the test environment.
     */
    public function setUp(): void {
        parent::setUp();
        
        // Create instance of Admin class.
        $this->admin = new Admin();
    }

    /**
     * Test if settings link is added correctly.
     */
    public function test_add_settings_link() {
        $links = ['<a href="#">Deactivate</a>'];
        $file = 'wp-additional-contributors/wp-additional-contributors.php';
        
        $updated_links = $this->admin->add_settings_link($links, $file);
        
        $this->assertCount(2, $updated_links);
    }

    /**
     * Test if settings page is registered correctly.
     */
    public function test_register_settings() {
        global $wp_settings_sections;

        $this->admin->register_settings();

        $this->assertArrayHasKey('wac-display-settings', $wp_settings_sections);
    }


    /**
     * Test if meta box is added.
     */
    public function test_add_contributors_metabox() {
        global $wp_meta_boxes;
        
        $this->admin->add_contributors_metabox();
        
        $this->assertArrayHasKey('wp_contributors_metabox', $wp_meta_boxes['post']['side']['high']);
    }

    /**
     * Test if display options are sanitized correctly.
     */
    public function test_sanitize_display_options() {
        $input = [
            'show_avatar' => '1',
            'show_name' => '0',
            'element_order' => 'name,bio,website'
        ];
        
        $sanitized = $this->admin->sanitize_display_options($input);
        
        $this->assertIsArray($sanitized);
        $this->assertEquals(1, $sanitized['show_avatar']);
        $this->assertEquals(1, $sanitized['show_name']);
        $this->assertContains('name', $sanitized['element_order']);
    }
}
