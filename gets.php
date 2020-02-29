<?php 

$table_name = JIBRES_TABLE;
$strc =  "CREATE TABLE $table_name (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT NOW() NOT NULL,
		  store varchar(11) DEFAULT NULL,
		  apikey varchar(32) DEFAULT NULL,
		  appkey varchar(32) DEFAULT NULL,
		  wis varchar(55) NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";
if (create_jibres_table($strc, JIBRES_TABLE) === true) 
{
	$check_jib = $wpdb->get_results("SELECT * FROM $table_name");
	if (!empty($check_jib)) 
	{
		if (isset($_GET['jibres']) and $_GET['jibres'] == 'backup_all') 
		{
			$packs = array('products', 'orders', 'posts', 'comments', 'categories');
			foreach ($packs as $value) 
			{
				require_once JIBRES_INC . $value.'_backup.php';
				$classname = 'jibres_'.$value;
				$run_class = new $classname;
			}
			printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
		}
		elseif (isset($_GET['jibres'])) 
		{
			require_once JIBRES_INC. $_GET['jibres'].'.php';
			$get_cname = explode("_", $_GET['jibres']);
			$classname = 'jibres_'. $get_cname[0];
			$run_class = new $classname();
			printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
		}
		else
		{
			require_once(JIBRES_DIR. 'main.php');
		}
	}
	else
	{
		require_once(JIBRES_DIR. 'first_jibres.php');
	}
}

?>