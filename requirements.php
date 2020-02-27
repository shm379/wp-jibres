<?php 

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


?>