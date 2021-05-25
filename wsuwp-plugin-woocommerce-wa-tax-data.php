<?php
/**
 * Plugin Name: WSUWP plugin woocommerce WA tax data
 * Plugin URI: https://web.wsu.edu/
 * Description: Allows for finding all the WA tax codes and amounts for a given time frame.
 * Version: 1.0.0
 * Requires PHP: 7.0
 * Author: Washington State University, Jeff Hanson
 * Author URI: https://web.wsu.edu/
 * Text Domain: WSUWP plugin woocommerce WA tax data
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Initiate plugin
require_once __DIR__ . '/includes/include-plugin.php';

