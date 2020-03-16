<?php
/**
 * @package           Jibres
 * @copyright         2020 Jibres
 * @link              https://Jibres.com
 *
 * Plugin Name:       Jibres
 * Plugin URI:        https://github.com/jibres/wp-jibres
 * Description:       Backup of all of your wordpress data and woocommerce into Jibres. Anytime you want you can transfer to Jibres. #1 World Sales Engineering System. Sell & Enjoy
 * Version:           1.1
 * Author:            Jibres
 * Author URI:        https://Jibres.com
 * Text Domain:       jibres
 * Domain Path:       /languages
*/

// If this file is called directly, abort.
if (!defined('WPINC'))
{
	die('Hey There! No Naughty Business Please!');
}

// Abort loading if WordPress is upgrading
if (defined('WP_INSTALLING') && WP_INSTALLING)
{
	return;
}

// check whether another instance of Master Slider is activated or not
if(defined('JIBRES_VERSION'))
{
	function jibres_two_instance_notice()
	{
		echo '<div class="error"><p>' . __( 'You are using two instances of Jibres plugin at same time! Please deactive one of them.', 'jibres' ) . '</p></div>';
	}
	add_action( 'admin_notices', 'jibres_two_instance_notice' );
	return;
}

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) )
{
	die("Hi there! I'm just a plugin, not much I can do when called directly.");
}

// define basic variables
define('JIBRES_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR);
require_once JIBRES_DIR . 'includes/define.php';


require_once JIBRES_INC . 'jibres_requirements.php';

function jibres_admin()
{
	global $wpdb;

	if ( is_admin() ) 
	{
		
		if (isset( $_GET['page']) and $_GET['page'] == 'jibres' )
		{

			// include the global jibres functions
			require_once JIBRES_INC . 'jibres_functions.php';

			printf('<div class="wrap"><br>');

			// include jibres headers
			require_once JIBRES_INC . 'jibres_header.php';


			// if post sent to this plugin page include posts page
			if ( ! empty( $_POST ) )
			{
				require_once JIBRES_INC . 'jibres_posts.php';
				exit();
			}

			// load jibres
			require_once JIBRES_INC . 'class-wp-jibres.php';
			new run_jibres();

			printf('</div>');


		}
	}
}
add_action( 'admin_notices', 'jibres_admin' );

?>