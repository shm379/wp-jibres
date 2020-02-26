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


		if ($_POST['store']) 
		{
			$data_posted = array('store' => $_POST['store'], 'apikey' => $_POST['apikey'], 'appkey' => $_POST['appkey']);
			insert_in_jibres('jibres', $data_posted);
		}

		$table_name = $wpdb->prefix . 'jibres';
		$strc =  "CREATE TABLE $table_name (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  time datetime DEFAULT NOW() NOT NULL,
				  store varchar(11) NOT NULL,
				  apikey varchar(32) NOT NULL,
				  appkey varchar(32) NOT NULL,
				  PRIMARY KEY  (id)
				) $charset_collate;";
		if (create_jibres_table('jibres', $strc) === true) 
		{
			$check_jib = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jibres");
			if (!empty($check_jib)) 
			{
				if ($_GET['jibres'] and $_GET['jibres'] == 'backup_all') 
				{
					$packs = array('products', 'orders', 'posts', 'comments', 'categories');
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
					printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br>');
					printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a><br><br>');
					printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a><br><br>');
					printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a><br><br>');
					printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a><br><br>');
					printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a>');
				}
			}
			else
			{
				printf('<p>Welcome to the official Jibres plugin for worpress</p>');
				printf('<p>For backup your data like products, orders, posts, comments and categories into your store in Jibres use this plugin</p>');
				printf('<p>Create your store in Jibres Sell & Enjoy...</p>');
				printf('<p>More informations in <a href="https://jibres.com" target="_blank" style="font-weight: bold;">Jibres</a></p>');
				printf('<p>For connect to Jibres api fill out the following information</p>');
				printf('<form action method="post">
						<label style="font-weight: bold;">Please Insert Your Jibres Informations: </label><br><br>
						<input type="text" name="store" placeholder="store"><br><br>
						<input type="text" name="apikey" placeholder="apikey"><br><br>
						<input type="text" name="appkey" placeholder="appkey"><br><br>
						<input type="submit" value="submit" class="bt">
						</form>');
			}
		}

		printf('</div>');
		
		
	}
}



add_action( 'admin_notices', 'admin_jib' );
?>
