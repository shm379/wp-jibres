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
    	printf('<div class="jibres"><br>');
    	if ($_GET['jibres'] == 'backup_products') 
    	{
    		require_once dirname( __FILE__ ) . '/includes/products_backup.php';
	    	products_b();
            printf('<a href="?page=jibres"><button>back</button></a>');
    	}
    	elseif ($_GET['jibres'] == 'backup_orders') 
    	{
    		require_once dirname( __FILE__ ) . '/includes/orders_backup.php';
            orders_b();
            printf('<a href="?page=jibres"><button>back</button></a>');
    	}
        elseif ($_GET['jibres'] == 'backup_posts') 
        {
            require_once dirname( __FILE__ ) . '/includes/posts_backup.php';
            posts_b();
            printf('<a href="?page=jibres"><button>back</button></a>');
        }
        elseif ($_GET['jibres'] == 'backup_comments') 
        {
            require_once dirname( __FILE__ ) . '/includes/comments_backup.php';
            comments_b();
            printf('<a href="?page=jibres"><button>back</button></a>');
        }
    	else
    	{
    		printf('<a href="?page=jibres&jibres=backup_products"><button>Backup Your Products</button></a><br><br>');
    		printf('<a href="?page=jibres&jibres=backup_orders"><button>Backup Your Orders</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=backup_posts"><button>Backup Your Posts</button></a><br><br>');
            printf('<a href="?page=jibres&jibres=backup_comments"><button>Backup Your Comments</button></a>');
    	}
    	printf('</div>');
    	
	    
    }
}



add_action( 'admin_notices', 'admin_jib' );
?>
