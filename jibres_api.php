<?php

/**
 * Connect to Jibres api
 */
class Jibres
{
	
	public static function login($data)
	{
		insert_in_jibres($data, JIBRES_TABLE);
		$get_data = send_data_jibres('/account/token');
		if (isset($get_data['result']['token'])) 
		{
			self::jibres_enter($get_data['result']['token']);
		}
		else
		{
			header("Refresh:0");
		}
	}

	private static function jibres_enter($token)
	{
		global $wpdb;

		$data = ['token' => $token];
		insert_in_jibres($data, JIBRES_TABLE);
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

	public static function verify($code)
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