<?php

/**
 * Connect to Jibres api
 */
class Jibres
{

	private static function jibres_get_token()
	{
		global $wpdb;

		$get_data = send_data_jibres('/account/token');
		if (isset($get_data['result']['token'])) 
		{
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'token' => $get_data['result']['token'] ),
							array( 'id' => 1 )
						);
			return true;
		}
		else
		{
			return false;
		}
	}
	


	private static function jibres_get_apikey()
	{
		global $wpdb;

		$data = ['model'=>'WORDPRESS', 'serial'=>'plugin', 'manufacturer'=>'WORDPRESS', 'version'=>'1'];
		$get_data = send_data_jibres('/account/android/add', $data, true);
		if ($get_data['ok'] == true) 
		{
			$apikey = $get_data['result']['apikey'];
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'apikey' => $apikey ),
							array( 'id' => 1 )
						);

			self::jibres_enter();
		}
		else
		{
			header("Refresh:0");
		}
	}

	private static function jibres_enter()
	{
		global $wpdb;

		$jibres_table = JIBRES_TABLE;
		$results = $wpdb->get_results("SELECT * FROM $jibres_table");
		$arr_results = [];
		foreach ($results as $key => $val) 
		{
			foreach ($val as $key2 => $val2) 
			{
				$arr_results[$key2] = $val2;
			}
		}
		self::jibres_get_token();
		$phone_number = $arr_results['phone_number'];
		$data = ['mobile' => $phone_number];
		$get_data = send_data_jibres('/account/enter', $data, true);

		if ($get_data['ok'] == true) 
		{
			printf('<form action method="post">
					<label style="font-weight: bold;">Please Insert Your received code via sms: </label><br><br>
					<input type="number" name="jibresverifycode" placeholder="Your code"><br><br>
					<input type="submit" value="submit" class="bt">
					</form>');
		}
		else
		{
			header("Refresh:0");
		}
	}



	public static function jibres_login($data)
	{
		global $wpdb;

		insert_in_jibres($data, JIBRES_TABLE);
		$get_data = send_data_jibres('/account/token');
		if (self::jibres_get_token() == true) 
		{
			self::jibres_get_apikey();
		}
		else
		{
			header("Refresh:0");
		}
	}



	public static function jibres_verify($code)
	{
		global $wpdb;
		
		$jibres_table = JIBRES_TABLE;
		$results = $wpdb->get_results("SELECT * FROM $jibres_table");
		$arr_results = [];
		foreach ($results as $key => $val) 
		{
			foreach ($val as $key2 => $val2) 
			{
				$arr_results[$key2] = $val2;
			}
		}
		self::jibres_get_token();
		$phone_number = $arr_results['phone_number'];
		$data = ['mobile' => $phone_number, 'verifycode' => $code];
		$get_data = send_data_jibres('/account/enter/verify', $data, true);
		if ($get_data['ok'] == true) 
		{
			$newapikey = $get_data['result']['apikey'];
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'apikey' => $newapikey, 'login' => '1' ),
							array( 'id' => 1 )
						);

			header("Refresh:0");

		}
	}
}

?>