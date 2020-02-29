<?php 

function menus() 
{
	add_menu_page(
		'Jibres',
		'Jibres',
		'manage_options',
		'jibres',
		'jibres',
		plugin_dir_url(__FILE__) . 'admin/images/logo-black.svg',
		30
	);
}
add_action( 'admin_menu', 'menus' );

function jib_css() 
{
	printf('<style type="text/css">');
	require_once(JIBRES_DIR . 'admin/css/style.css');
	printf('</style>');
}
add_action( 'admin_head', 'jib_css' );


?>