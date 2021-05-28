<?php namespace WSUWP\Plugin\WA_Tax_Query;


class Plugin 
{

	protected static $version = '1.0.1';

	/****************************
	 * 
	 *  Function to provide GET access to properties.
	 * 
	 ***************************/
	public static function get( $property ) 
	{

		switch ( $property ) {

			case 'version':
				return self::$version;

			case 'plugin_dir':
				return plugin_dir_path( dirname( __FILE__ ) );

			case 'plugin_url':
				return plugin_dir_url( dirname( __FILE__ ) );

			case 'template_dir':
				return plugin_dir_path( dirname( __FILE__ ) ) . '/template-parts';

			case 'class_dir':
				return plugin_dir_path( dirname( __FILE__ ) ) . '/classes';

			default:
				return '';

		}

	}

	public static function addToNav() 
	{
		/***
		 * 
		 * 		This call appears to do nothing at all. I see nothing in the menu.*/
		  		add_menu_page('WA Tax Data', 'WooTaxes', 'administrator', array(__CLASS__, 'renderPage'), '', '', 5);
		 /* 
		 * 		add_options_page( 'WA Tax Data', 'WooTaxes', 'Administrator', 'wsuwp-plugin-woocommerce-wa-tax-data/template-parts/form.php');	*/	
	}

	public static function renderPage()
	{
		//include (__CLASS__) ->get('template_dir').'/form.php';
	}

	public function init() 
	{
		add_action('admin_menu', array( __CLASS__, 'addToNav' ));

		/* NOTE: Here's where everything starts. You probably don't need to include/require additional files here 
		* and just do everything in this class - for a more complex plugin you might want to separate functionality into it's own files. 
		* You may only need one action here that calls a class method.
		*
		* I'd start by adding it as a menu page and then trying to add it under WooCommerce later (that can be a real pain).
		* 
		* Here's how to add a menu page https://developer.wordpress.org/reference/functions/add_menu_page/
		*
		* If you check out the notes you'll see they are using the 'admin_menu' action i.e. add_action( 'admin_menu', .....)
		* https://developer.wordpress.org/reference/functions/add_menu_page/#user-contributed-notes
		* Basically this just means that when WP gets to rendereing the the admin menu we are going to do something.
		* 
		* The action would go inside this method (init) and point to another (static) method (see example below) where you would call the add_menu_page function inside of it.
		* Actions always take the form of add_action( 'action_name', callback, numerical priority (default 10), Number of args the callback is expecting (default 1) ).
		* 
		* When using static class the callbacks will look like array( __CLASS__, 'methodname' ). __CLASS__ is a magic constant in php https://www.php.net/manual/en/language.constants.magic.php.
		* You'll likely need to use this same format for the callback in add_menu_page.
		*/

		// Example:
		/*
		add_action( 'someactionpoint', array( __CLASS__, 'myactionpointmethod') );
		*/

		
		//require_once __DIR__ . '/include-post-query.php';

		//require_once __DIR__ . '/include-save-post.php';

		//require_once __DIR__ . '/include-post-content.php';
		//if ( is_admin() ) {

			//require_once __DIR__ . '/include-metabox.php';

		//}


	}	

}


(new Plugin)->init();