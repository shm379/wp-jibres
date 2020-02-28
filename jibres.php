<?php
/**
 * @package           Jibres
 * @copyright         2020 Jibres
 *
 * Plugin Name:       Jibres
 * Plugin URI:        https://github.com/jibres/wp-jibres
 * Description:       Backup of all of your wordpress data and woocommerce into Jibres. Anytime you want you can transfer to Jibres. #1 World Sales Engineering System. Sell & Enjoy
 * Version: 		  		1.1
 * Author:            Jibres
 * Author URI:        https://Jibres.com
*/

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo "Hi there! I'm just a plugin, not much I can do when called directly.";
	exit;
}
if(defined('JIBRES_DIR'))
{
	echo "wooow! What's happening!";
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