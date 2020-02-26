<?php
/**
 * @package           jibres
 * @author            Shahb2
 * @copyright         2020 Jibres
 * @license           GPL-3.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       jibres
 * Plugin URI:        https://jibres.com
 * Description:       Backup of your data on jibres.
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
        printf("<h1>Jibres</h1>");

    	require_once(dirname( __FILE__ ) . '/includes/functions.php');
    	printf('<div class="jibres"><br>');
    	if ($_GET['jibres'] and $_GET['jibres'] == 'backup_all') 
    	{
    		$packs = array('products', 'orders', 'posts', 'comments');
    		foreach ($packs as $value) 
    		{
    			require_once dirname( __FILE__ ) . '/includes/'.$value.'_backup.php';
    			$open_func = $value.'_b';
    			$open_func();
    		}
    		printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
    	}
    	elseif ($_GET['jibres']) 
    	{
    		require_once dirname( __FILE__ ) . '/includes/'.$_GET['jibres'].'.php';
    		$get_func = explode("_", $_GET['jibres']);
    		$open_func = $get_func[0]."_b";
    		$open_func();
    		printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
    	}
    	else
    	{
    		printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a><br><br>');
    		printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a>');
    	}
    	printf('</div>');
    	
	    
    }
}



add_action( 'admin_notices', 'admin_jib' );
?>
