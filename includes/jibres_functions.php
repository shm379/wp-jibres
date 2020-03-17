<?php 

// define jibres tables
define( 'JIBRES_TABLE', $wpdb->prefix. 'jibres' );
define( 'JIBRES_CTABLE', $wpdb->prefix. 'jibres_check' );


// check jibres information exist or if update argument equal to start_again delete jibres table to start plugin again (reset plugin)
function ch_jibres_store_data( $update = null )
{
	global $wpdb;

	$jibres_table = JIBRES_TABLE;
	if ($update == null) 
	{
		$query = 
		"
			SELECT 
				* 
			FROM 
				$jibres_table 
			WHERE 
				store IS NULL OR 
				apikey IS NULL OR 
				appkey IS NULL
		";
		$store_check = $wpdb->get_results($query);
		if ( ! empty( $store_check ) ) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	elseif ( $update == 'start_again' ) 
	{
		$wpdb->query( "DROP TABLE IF EXISTS $jibres_table" );
	}
}


// if jibres tables not exist create them
function create_jibres_table( $tname = JIBRES_CTABLE )
{
	global $wpdb;
		
	if ( $tname == JIBRES_CTABLE ) 
	{
		$create_ddl = "CREATE TABLE $tname (
					   id int(11) NOT NULL AUTO_INCREMENT,
					   time datetime DEFAULT NOW() NOT NULL,
					   item_id int(11) NOT NULL,
					   type varchar(50) NOT NULL,
					   wers varchar(55) NOT NULL,
					   backuped int(11) DEFAULT 1 NOT NULL,
					   jibres_id int(11) DEFAULT NULL,
					   PRIMARY KEY  (id)
					 ) $charset_collate;";
	}
	elseif( $tname == JIBRES_TABLE )
	{
		$create_ddl = "CREATE TABLE $tname (
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
	}

	$query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $tname ) );
 
	if ( $wpdb->get_var( $query ) == $tname ) 
	{
		return true;
	}
	else
	{
		$charset_collate = $wpdb->get_charset_collate();

		// Didn't find it try to create it..
		$wpdb->query( $create_ddl );
 
		// We cannot directly tell that whether this succeeded!
		if ( $wpdb->get_var( $query ) == $tname ) 
		{
			return true;
		}
		else
		{
			return false;
		}
		// dbDelta( $create_ddl );
	}
}


// insert backuped data in jibres_check table
function insert_in_jibres( $data = [], $tname = JIBRES_CTABLE )
{
	global $wpdb;

	if ( $tname == JIBRES_CTABLE ) 
	{
		$data['wers'] = jibres_wis();
	}
	
	$wpdb->insert( 
		$tname, 
		$data
	);
}



// send data to jibres by api with curl
function send_data_jibres( $where, $data = [], $token = false )
{
	global $wpdb;

	$jibres_table = JIBRES_TABLE;
	$results = $wpdb->get_results( "SELECT * FROM $jibres_table" );
	$arr_results = [];
	foreach ( $results as $key => $val ) 
	{
		foreach ( $val as $key2 => $val2 ) 
		{
			$arr_results[$key2] = $val2;
		}
	}
	$store = $arr_results['store'];
	$appkey = $arr_results['appkey'];
	$apikey = $arr_results['apikey'];
	$headers =  ['Content-Type: application/json', 'appkey: '.$appkey];
	
	if ( $apikey != null ) 
	{
		array_push( $headers, 'apikey: '.$apikey );
	}

	if ( $token == true ) 
	{
		$token = $arr_results['token'];
		array_push( $headers, 'token: '.$token );
	}

	// wordpress curl function
	/*$argus = 
	[
		'method'      => 'POST',
        'timeout'     => 20,
        'headers'     => $headers,
        'body'        => json_encode($data)
	];

	$jibres_req = WP_Http_Curl::request("https://api.jibres.ir/".$store."/v2".$where, $argus);

	var_dump($jibres_req);*/

	// send data with curl
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_URL, "https://api.jibres.ir/".$store."/v2".$where);
	// curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	if (!empty($data)) 
	{
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	}
	//FALSE to stop cURL from verifying the peer's certificate
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);

    //TRUE to return the transfer as a string of the return value of curl_exec() instead of outputting it out directly.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20); // 20 time out of nic-broker
    curl_setopt($ch, CURLOPT_TIMEOUT, 20); // 20 time out of nic-broker

	
	$push_p = curl_exec($ch);

    if(curl_error($ch)) 
    {
		printf('Curl error: ' . curl_error($ch));
		exit();
	}
	else
	{
		$respon = json_decode($push_p, true);
		// var_dump($push_p);
	}

	
	curl_close($ch);
	
	return $respon;
}


// create csv file backups by its name in backup folder
function jibres_create_csv( $cat, $data )
{

	$fname = JIBRES_DIR . 'backup/' . $cat . '.csv';

	if ( file_exists( $fname ) ) 
	{
		$arr = array_values( $data );
		$fp = fopen( $fname, 'a' );
		fputcsv( $fp, $arr );
	} 
	else 
	{
		$fp = fopen( $fname, 'a' );
		$arr = array_keys( $data );
		fputcsv( $fp, $arr );
		$arr = array_values( $data );
		fputcsv( $fp, $arr );
	}
	
	fclose( $fp );

	return true;

}

// sort informations by jibres database design
function jibres_sort_arr( $ch = [], $data = [] )
{
	foreach ( $ch as $key => $value ) 
	{
		$ch[$key] = $data[$value];
	}

	return $ch;
}


// check where plugin back up data and send data to there
function jibres_wis( $item = null, $data = null )
{
	global $wpdb;

	$jibres_table = JIBRES_TABLE;
	$results = $wpdb->get_results("SELECT wis FROM $jibres_table");

	foreach ( $results as $key => $value ) 
	{
		foreach ( $value as $key => $val ) 
		{
			if ( $key == "wis" ) 
			{
				$weris = $val;
			}
		}

	}

	if ( $item == null and $data == null ) 
	{
		return $weris;
	}
	else
	{
		if ( $weris == 'csv' ) 
		{
			return jibres_create_csv( $item, $data );
		}
		elseif ( $weris == 'api' ) 
		{
			return send_data_jibres( $item, $data );
		}
	}
}


function jibres_create_sql_where( $where = [] )
{
	if ( is_array( $where ) ) 
	{
		if ( ! empty( $where ) ) 
		{
			$sqlwhere = "";
			$aw = count( $where );
			$i = 0;
			foreach ( $where as $key => $value ) 
			{
				$i++;
				if ( $i == $aw ) 
				{
					$sqlwhere .= "$key='$value'";
				}
				else
				{
					$sqlwhere .= "$key='$value' AND ";
				}
			}
		}
		else
		{
			$sqlwhere = "1=1";
		}
	}
	else
	{
		$sqlwhere = "$where";
	}

	return $sqlwhere;
}

function jibres_get_not_backuped( $item, $table, $cat, $where = [] )
{
	global $wpdb;

	create_jibres_table();
	$wb = jibres_wis();
	$jibres_ctable = JIBRES_CTABLE;
	$table = $wpdb->prefix. $table;
	$where = jibres_create_sql_where( $where );

	$query = 
	"
		SELECT 
			COUNT($item) 
		FROM 
			$table 
		WHERE 
			$where AND 
			$item NOT IN 
			(
				SELECT item_id FROM $jibres_ctable WHERE type = '$cat' AND backuped = 1 AND wers = '$wb'
			)
	";
	$data = $wpdb->get_results( $query );

	foreach ( $data as $key => $value ) 
	{
		foreach ( $value as $vkey => $val ) 
		{
			$not_b = $val;
		}
	}

	return $not_b;

}


// return which data are backuped
function jibres_informations_b( $item, $table, $cat, $where = [], $first = false )
{
	global $wpdb;

	$wb = jibres_wis();
	$wers = ( $wb == 'csv' ) ? 'to csv file' : 'to your jibres store';
	$jibres_ctable = JIBRES_CTABLE;
	$sdata = jibres_get_not_backuped( $item, $table, $cat, $where );

	$table = $wpdb->prefix. $table;
	$where = jibres_create_sql_where( $where );

	$fdata = $wpdb->get_results( "SELECT COUNT($item) FROM $table WHERE $where" );


	$first = ( $first == false ) ? 'And' : 'You';

	if ( ! empty( $fdata ) ) 
	{
		foreach ( $fdata as $key => $value ) 
		{
			foreach ( $value as $key2 => $val ) 
			{
				$all = $val;
			}
		}
	
		$ex = $first.' have '.$all.' '.$cat;
		
		if ($sdata == '0') 
		{
			$ex .= ' and <a style="color: green;">all of your '.$cat.'s backuped '.$wers.'</a>';
		}
		else
		{
			$ex .= ' and <a style="color: #c80a5a;">'.$sdata.' '.$cat.' not backuped '.$wers.'</a>';
		}
		
	}
	else
	{
		$ex = $first.' have not any '.$cat;
	}

	return $ex;
}

?>