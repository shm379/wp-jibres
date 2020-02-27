<?php
/**
 * @package           wp-jibres
 * @author            Shahb2
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


function menus() 
{
	add_menu_page(
		'Jibres',
		'Jibres',
		'manage_options',
		'jibres',
		'jibres',
		plugin_dir_url(__FILE__) . 'admin/images/Jibres-Logo-icon-black-32.png',
		30
	);
}

add_action( 'admin_menu', 'menus' );

function jib_css() 
{
	require_once(dirname( __FILE__ ) . '/admin/css/style.php');
}

add_action( 'admin_head', 'jib_css' );


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
