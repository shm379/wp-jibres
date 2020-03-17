<?php
/**
 * jibres main class
 */
class run_jibres
{
	

	function __construct()
	{

		if (create_jibres_table(JIBRES_TABLE) === true) 
		{
			$this->jibres_check_login();
		}

	}


	private function jibres_check_login()
	{
		global $wpdb;

		$table = JIBRES_TABLE;
		$check_jibres_table = $wpdb->get_results("SELECT * FROM $table WHERE id = '1'");

		if ( ! empty($check_jibres_table) ) 
		{
			
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
			
			if ( $login == true ) 
			{
				$this->start_jibres();
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



	private function start_jibres()
	{
		
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

		$packs = array( 'products', 'orders', 'posts', 'comments', 'categories' );

		foreach ( $packs as $value ) 
		{
			require_once JIBRES_INC . $value.'_backup.php';
			$classname = 'jibres_'.$value;
			$run_class = new $classname();
		}

		printf('<a href="?page=jibres"><button class="button">Back Home</button></a>');

	}


	private function jibres_backup()
	{

		require_once JIBRES_INC. $_GET['jibres'].'.php';

		$get_cname = explode("_", $_GET['jibres']);
		$classname = 'jibres_'. $get_cname[0];
		$run_class = new $classname();

		printf('<a href="?page=jibres"><button class="button">Back Home</button></a>');
		
	}


}

?>