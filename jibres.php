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


add_action( 'admin_menu', 'menus' );
function menus() 
{
    add_menu_page(
        'JIBRES',
        'Jibres',
        'manage_options',
        'jibres',
        'jibres',
        plugin_dir_url(__FILE__) . 'admin/images/Jibres-Logo-icon-black-32.png',
        30
    );
}

function not()
{
    if ($_GET['page'] == 'jibres') 
    {
        echo "<h1>Jibres</h1>";
    }
}
add_action( 'admin_notices', 'not' );


function jib_css() {
	echo "
	<style type='text/css'>
	.jibres {

	}
	</style>
	";
}

add_action( 'admin_head', 'jib_css' );


function admin_jib() 
{
    global $wpdb;
    
	if ($_GET['page'] == 'jibres') 
    {
    	require_once(dirname( __FILE__ ) . '/includes/functions.php');
    	printf('<div class="jibres"><br>');
    	if ($_GET['jibres'] and $_GET['jibres'] == 'backup_all') 
    	{
    		require_once dirname( __FILE__ ) . '/includes/products_backup.php';
    		require_once dirname( __FILE__ ) . '/includes/orders_backup.php';
    		require_once dirname( __FILE__ ) . '/includes/posts_backup.php';
    		require_once dirname( __FILE__ ) . '/includes/comments_backup.php';
    		products_b();
    		orders_b();
    		posts_b();
    		comments_b();
    		printf('<a href="?page=jibres"><button>Back</button></a>');
    	}
    	elseif ($_GET['jibres']) 
    	{
    		require_once dirname( __FILE__ ) . '/includes/'.$_GET['jibres'].'.php';
    		$get_func = explode("_", $_GET['jibres']);
    		$open_func = $get_func[0]."_b";
    		$open_func();
    		printf('<a href="?page=jibres"><button>Back</button></a>');
    	}
    	else
    	{
    		printf('<a href="?page=jibres&jibres=products_backup"><button>Backup Your Products</button></a><br><br>');
    		printf('<a href="?page=jibres&jibres=orders_backup"><button>Backup Your Orders</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=posts_backup"><button>Backup Your Posts</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=comments_backup"><button>Backup Your Comments</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=backup_all"><button>Backup All Data</button></a>');
    	}
    	printf('</div>');
    	
	    
    }
}



add_action( 'admin_notices', 'admin_jib' );
?>
