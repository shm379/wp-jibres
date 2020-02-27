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

		require_once(dirname( __FILE__ ) . '/includes/functions.php');
		if (wis() == 'csv') 
		{
			if (ch_jibres_store_data() == true) 
			{
				printf('<h1>Jibres | 
						<form id="fwis" action method="post" style="display: inline;">
						<input type="hidden" name="usas" value="api">
						<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to my store in Jibres</a>
						</form></h1>');
			}
			else
			{
				printf('<h1>Jibres | 
						<form id="fwis" action method="post" style="display: inline;">
						<input type="hidden" name="usas" value="api">
						<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">I want to use jibres api</a>
						</form></h1>');
			}
		}
		elseif (wis() == 'api') 
		{
			
			printf('<h1>Jibres | 
					<form id="fwis" action method="post" style="display: inline;">
					<input type="hidden" name="usas" value="csv">
					<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to csv file</a>
					</form></h1>');
			
		}
		printf('<script>
					document.getElementById("subm").onclick = function() {
    					document.getElementById("fwis").submit();
					}
				</script>');

		printf('<div class="jibres"><br>');


		if ($_POST['weris']) 
		{
			if ($_POST['store'] and !empty($_POST['store'])) 
			{
				$data_posted = array('store' => $_POST['store'], 'apikey' => $_POST['apikey'], 'appkey' => $_POST['appkey'], 'wis' => $_POST['weris']);
			}
			else
			{
				$data_posted = array('wis' => $_POST['weris']);
			}
			insert_in_jibres($data_posted, 'jibres');
			header("Refresh:0");
		}

		if ($_POST['usas']) 
		{
				
			if ($_POST['usas'] == 'api' and ch_jibres_store_data() == false) 
			{
				ch_jibres_store_data('start_again');
				header("Refresh:0");
			}
			else
			{
				$wpdb->update(
								$wpdb->prefix . 'jibres',
								array( 'wis' => $_POST['usas'] ),
								array( 'id' => 1 )
							);
				header("Refresh:0");
			}
		}

		$table_name = $wpdb->prefix . 'jibres';
		$strc =  "CREATE TABLE $table_name (
				  id int(11) NOT NULL AUTO_INCREMENT,
				  time datetime DEFAULT NOW() NOT NULL,
				  store varchar(11) DEFAULT NULL,
				  apikey varchar(32) DEFAULT NULL,
				  appkey varchar(32) DEFAULT NULL,
				  wis varchar(55) NOT NULL,
				  PRIMARY KEY  (id)
				) $charset_collate;";
		if (create_jibres_table($strc, 'jibres') === true) 
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
				printf('<h1>Jibres</h1>');
				printf('<p>Welcome to the official Jibres plugin for worpress.</p>');
				printf('<p>For backup your data like products, orders, posts, comments and categories into your store in Jibres use this plugin.</p>');
				printf('<p>For backup your data like said examples into csv files again use this plugin.</p>');
				printf('<p>Create your store in Jibres Sell and Enjoy :)</p>');
				printf('<p>More informations in <a href="https://jibres.com" target="_blank" style="font-weight: bold;">Jibres</a>.</p>');
				printf('<p>For connect to Jibres api fill out the following information but if you want to backup into csv files without use jibres api only click submit.</p>');
				printf('<p>Csv files path: wp-content/plugins/this plugin folder(wp-jibres)/backup</p>');
				printf('<form action method="post">
						<label style="font-weight: bold;">Please Insert Your Jibres Informations: </label><br><br>
						<input type="text" name="store" placeholder="store"><br><br>
						<input type="text" name="apikey" placeholder="apikey"><br><br>
						<input type="text" name="appkey" placeholder="appkey"><br><br>
						<p>Where you want to save your backups?</p>
  						<input type="radio" id="csv" name="weris" value="csv" checked>
  						<label for="csv">csv file</label><br>
  						<input type="radio" id="api" name="weris" value="api">
  						<label for="api">your jibres store with api</label><br><br>
						<input type="submit" value="submit" class="bt">
						</form>');
			}
		}

		printf('</div>');
		
		
	}
}



add_action( 'admin_notices', 'admin_jib' );
?>
