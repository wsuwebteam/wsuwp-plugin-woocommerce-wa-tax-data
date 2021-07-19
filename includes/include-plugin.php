<?php namespace WSUWP\Plugin\WA_Tax_Query;


class Plugin 
{

	protected static $version = '1.0.1';


	public function __construct() {

		require_once self::get('class_dir') . '/class-tax-query.php';

	}

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
		add_submenu_page('tools.php', 'WA Tax Data', 'WooTaxes', 'administrator', 'wa-tax-data', array(__CLASS__, 'renderPage'), 5);	
	}

	public static function renderPage()
	{
		include Plugin::get('template_dir').'/form.php';
	}

	public function init() 
	{
		add_action('admin_menu', array( __CLASS__, 'addToNav' ));
	}	

}


(new Plugin)->init();