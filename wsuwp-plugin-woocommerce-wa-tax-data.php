<?php namespace WSUWP\Plugin\WA_Tax_Query;
/**
 * Plugin Name: WSUWP plugin woocommerce WA tax data
 * Plugin URI: https://github.com/wsuwebteam/wsuwp-plugin-woocommerce-wa-tax-data
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
/* 
*
*	Was hoping this would show me I'm looking in the right place. The above is like the examples but acts like nothing happens.
* 	echo(__DIR__ . '/includes/include-plugin.php');
*
*/

