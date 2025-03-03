<?php
/**
 * Plugin Name: WordPress Additional Contributors
 * Description: WordPress Additional Contributors allows you to add multiple co-authors / contributors to WordPress posts
 * Plugin URI: https://vansh-kapoor.vercel.app/
 * Version: 1.0.0
 * Author: Vansh Kapoor
 * Author URI: https://vansh-kapoor.vercel.app/
 * Requires at least: 6.7
 * Requires PHP: 7.4
 * License: GPLv2 or later
 * Text Domain: wac
 *
 * @package WordPress_Additional_Contributors
 */

if ( ! defined( constant_name: 'ABSPATH' ) ) {
	exit;
}

define( 'WAC_VERSION', '1.0.0' );
define( 'WAC_PATH', plugin_dir_path( __FILE__ ) );
define( 'WAC_ASSETS', plugin_dir_url( __FILE__ ) . 'assets' );

require_once WAC_PATH . 'plugin-loader.php';
