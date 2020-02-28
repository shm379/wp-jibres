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

function jibres_two_instance_notice()
{
	echo '<div class="error"><p>' . __( 'You are using two instances of Jibres plugin at same time! Please deactive one of them.', 'jibres' ) . '</p></div>';
}
// check whether another instance of Master Slider is activated or not
if(defined('JIBRES_VERSION'))
{
	add_action( 'admin_notices', 'jibres_two_instance_notice' );
	return;
}

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) )
{
	echo "Hi there! I'm just a plugin, not much I can do when called directly.";
	exit;
}


define('JIBRES_DIR', dirname(__FILE__). DIRECTORY_SEPARATOR);
define('JIBRES_VERSION', '1.1');


require_once(JIBRES_DIR. 'requirements.php');

function admin_jibres()
{
	global $wpdb;

	if ($_GET['page'] == 'jibres')
	{

		require_once(JIBRES_DIR. 'includes/functions.php');
		require_once(JIBRES_DIR. 'header.php');

		printf('<div class="jibres"><br>');

		if ($_POST)
		{
			require_once(JIBRES_DIR. 'if_posts.php');
		}

		require_once(JIBRES_DIR. 'gets.php');

		printf('</div>');


	}
}
add_action( 'admin_notices', 'admin_jibres' );

?>