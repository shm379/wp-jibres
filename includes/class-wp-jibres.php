<?php

/**
 * jibres main class
 */
class jibres_start
{
	
	private static $jibres_t = JIBRES_TABLE;
	

	function __construct()
	{
		$table = self::$jibres_t;
		$strc = "CREATE TABLE $table (
		  		 id int(11) NOT NULL AUTO_INCREMENT,
		  		 time datetime DEFAULT NOW() NOT NULL,
		  		 store varchar(11) DEFAULT NULL,
		  		 token varchar(255) DEFAULT NULL,
		  		 appkey varchar(32) DEFAULT NULL,
		  		 apikey varchar(32) DEFAULT NULL,
		  		 phone_number varchar(20) DEFAULT NULL,
		  		 login tinyint(1) DEFAULT '0',
		  		 wis varchar(55) NOT NULL,
		  		 PRIMARY KEY  (id)
				 ) $charset_collate;";


		if (create_jibres_table($strc, $table) === true) 
		{
			$this->jibres_check_login();
		}
	}


	private function jibres_check_login()
	{
		global $wpdb;

		$table = self::$jibres_t;
		$check_jibres_table = $wpdb->get_results("SELECT * FROM $table");
		$jibres_v = [];

		foreach ( $check_jibres_table as $key => $val ) 
		{
			foreach ( $val as $key2 => $val2 ) 
			{
				$jibres_v[$key2] = $val2;
			}
		}

		if ( $jibres_v['wis'] == 'api' ) 
		{
			if ( $jibres_v['login'] == '1' ) 
			{
				$login = true;
			}
			else
			{
				$login = false;
			}
		}
		elseif ( $jibres_v['wis'] == 'csv' ) 
		{
			$login = true;
		}

		if ( ! empty($check_jibres_table) ) 
		{
			if ( $login == true ) 
			{
				$this->run_jibres();
			}
			else
			{
				ch_jibres_store_data('start_again');
				header("Refresh:0");
			}
		}
		else
		{
			require_once JIBRES_INC. 'jibres_first.php';
		}
	}



	private function run_jibres()
	{
		global $wpdb;

		
		if ( isset($_GET['jibres']) ) 
		{
			require_once JIBRES_INC. 'jibres_backup_class.php';
			if ( $_GET['jibres'] == 'backup_all' ) 
			{
				$this->jibres_backup_all();
			}
			else
			{
				$this->jibres_backup();
			}
		}
		else
		{
			require_once JIBRES_INC. 'jibres_main.php';
		}

	}


	private function jibres_backup_all()
	{
		$packs = array('products', 'orders', 'posts', 'comments', 'categories');
		foreach ($packs as $value) 
		{
			require_once JIBRES_INC . $value.'_backup.php';
			$classname = 'jibres_'.$value;
			$run_class = new $classname();
		}
		printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
	}


	private function jibres_backup()
	{
		require_once JIBRES_INC. $_GET['jibres'].'.php';
		$get_cname = explode("_", $_GET['jibres']);
		$classname = 'jibres_'. $get_cname[0];
		$run_class = new $classname();
		printf('<a href="?page=jibres"><button class="bt">Back Home</button></a>');
	}


}

?>