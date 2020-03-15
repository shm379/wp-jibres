<?php 
/**
 * @author Jibres
 */
class jibres_posts
{

	public static $data;
	

	public static function usas()
	{
		global $wpdb;
		if (isset(self::$data['usas']) and self::$data['usas'] == 'api' and ch_jibres_store_data() == false) 
		{
			ch_jibres_store_data('start_again');
			header("Refresh:0");
		}
		else
		{
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'wis' => self::$data['usas'] ),
							array( 'id' => 1 )
						);
			header("Refresh:0");
		}
	}


	public static function weris()
	{
		if (!empty(self::$data['store']) and !empty(self::$data['appkey']) and !empty(self::$data['phone'])) 
		{
			$data_posted = ['store' => self::$data['store'], 'appkey' => self::$data['appkey'], 'phone_number' => self::$data['phone'], 'wis' => self::$data['weris']];
			require_once JIBRES_DIR. 'jibres_api.php';
			Jibres::jibres_login($data_posted);
		}
		elseif (!empty(self::$data['weris']))
		{
			$data_posted = array('wis' => 'csv');
			insert_in_jibres($data_posted, JIBRES_TABLE);
			header("Refresh:0");
		}
	}

	public static function jibresverifycode()
	{
		require_once JIBRES_DIR. 'jibres_api.php';
		Jibres::jibres_verify(self::$data['jibresverifycode']);
	}


	public static function changit()
	{
		ch_jibres_store_data(self::$data['changit']);
		header("Refresh:0");
	}


	public static function csvdel()
	{
		global $wpdb;
		$del_data = explode("_", self::$data['csvdel']);
		$wpdb->delete( JIBRES_CTABLE, array( 'type' => $del_data[1], 'wers' => 'csv' ) );
		unlink(JIBRES_DIR. 'backup/'. $del_data[0]. '.csv');
		header("Refresh:0");
	}
}



jibres_posts::$data = $_POST;

foreach ($_POST as $key => $value) 
{
	if (method_exists('jibres_posts', $key)) 
	{
		jibres_posts::$key();
	}
}


?>