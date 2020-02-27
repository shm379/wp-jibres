<?php
/**
 * @package           wp-jibres
 * @author            sarbazk
 * @copyright         2020 Jibres
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       wp-jibres
 * Plugin URI:        https://github.com/jibres/wp-jibres
 * Description:       Backup of your data on jibres.
 * Version: 		  1.0
 * Author:            Jibres
 * Author URI:        https://jibres.com
 */

require_once(dirname( __FILE__ ) . '/requirements.php');

function admin_jib() 
{
	global $wpdb;
	
	if ($_GET['page'] == 'jibres') 
	{

		require_once(dirname( __FILE__ ) . '/includes/functions.php');
		require_once(dirname( __FILE__ ) . '/header.php');

		printf('<div class="jibres"><br>');

		if ($_POST) 
		{
			require_once(dirname( __FILE__ ) . '/if_posts.php');
		}

		require_once(dirname( __FILE__ ) . '/gets.php');

		printf('</div>');
		
		
	}
}
add_action( 'admin_notices', 'admin_jib' );

?>
