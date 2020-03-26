<?php 

// define jibres tables
define( 'JIBRES_TABLE', $wpdb->prefix. 'jibres' );
define( 'JIBRES_CTABLE', $wpdb->prefix. 'jibres_check' );


function dump_json($_data)
{
  @header("Content-Type: application/json; charset=utf-8");
  if(is_array($_data))
  {
    $_data = json_encode($_data, JSON_UNESCAPED_UNICODE);
  }
  echo $_data;
  exit();
}


function jibres_mail_backup( $file_name )
{
	global $wpdb;

	$table = $wpdb->prefix . "users";
	$results = $wpdb->get_results( "SELECT user_email FROM $table WHERE ID = 1", 'ARRAY_N' );
	
	$mailTo = $results[0][0];

    $message = "Csv file of " . $file_name . "backup";
    $subject    = "wp-jibres " . $file_name . " backup";
	$fromName = "Jibres";
	$fromMail = "info@jibres.com";
    $replyTo    = "no-reply";
    $filePath = JIBRES_DIR . "backup/" . $file_name . ".csv";
    $LE  = "\r\n";
    $uid = md5(uniqid(time()));
    $withAttachment = ( $filePath !== NULL && file_exists( $filePath ) );

    if( $withAttachment )
    {
        $fileName   = basename($filePath);
        $fileSize   = filesize($filePath);
        $handle     = fopen($filePath, "r");
        $content    = fread($handle, $fileSize);
        fclose($handle);
        $content = chunk_split(base64_encode($content));
    }

    $header = "From: ".$fromName." <".$fromMail.">$LE";
    $header .= "Reply-To: ".$replyTo."$LE";
    $header .= "MIME-Version: 1.0$LE";
    $header .= "Content-Type: multipart/mixed; boundary=\"".$uid."\"$LE$LE";
    $header .= "This is a multi-part message in MIME format.$LE";
    $header .= "--".$uid."$LE";
    $header .= "Content-type:text/html; charset=UTF-8$LE";
    $header .= "Content-Transfer-Encoding: 7bit$LE$LE";
    $header .= $message."$LE$LE";

    if( $withAttachment )
    {
        $header .= "--".$uid."$LE";
        $header .= "Content-Type: application/octet-stream; name=\"".$fileName."\"$LE";
        $header .= "Content-Transfer-Encoding: base64$LE";
        $header .= "Content-Disposition: attachment; filename=\"".$fileName."\"$LE$LE";
        $header .= $content."$LE$LE";
        $header .= "--".$uid."--";
    }

    return mail( $mailTo, $subject, "", $header );
}


function jibres_auto_mail()
{
	global $wpdb;

	$table = JIBRES_TABLE;
	$check_auto_mail_res = $wpdb->get_results( "SELECT a_mail FROM $table WHERE id = 1", 'ARRAY_N' );
	$check_auto_mail = $check_auto_mail_res[0][0];
	$auto_mail = ( $check_auto_mail == '1' ) ? true : false;

	return $auto_mail;
}


// jibres error logging to error_log.txt
function jibres_error_log( $where, $er )
{
	$error = 'JIBRES ERROR: [' . date("Y-m-d H:i:s") . '] > ' . ' «' . $where . '» ' . $er . "\n";
	file_put_contents( JIBRES_DIR . 'error_log.txt', $error, FILE_APPEND );
}

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
		  		 	   a_mail tinyint(1) DEFAULT '0',
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
	$results = $wpdb->get_results( "SELECT * FROM $jibres_table", 'ARRAY_A' );
	$arr_results = [];
	foreach ( $results[0] as $key => $value ) 
	{
		$arr_results[$key] = $value;
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

	curl_setopt($ch, CURLOPT_URL, "https://api.jibres.com/".$store."/v2".$where);
	// curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, TRUE);
	if (!empty($data)) 
	{
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
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
	$results = $wpdb->get_results( "SELECT wis FROM $jibres_table", 'ARRAY_N' );

	$weris = $results[0][0];

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
	$data = $wpdb->get_results( $query, 'ARRAY_N' );

	$not_b = $data[0][0];

	return $not_b;

}


// return which data are backuped
function jibres_informations_b( $item, $table, $cat, $where = [] )
{
	global $wpdb;

	$wb = jibres_wis();
	$jibres_ctable = JIBRES_CTABLE;
	$sdata = jibres_get_not_backuped( $item, $table, $cat, $where );

	$table = $wpdb->prefix. $table;
	$where = jibres_create_sql_where( $where );

	$fdata = $wpdb->get_results( "SELECT COUNT($item) FROM $table WHERE $where", 'ARRAY_N' );
	$all = $fdata[0][0];

	$j_date = $wpdb->get_results( "SELECT time FROM $jibres_ctable WHERE type='$cat' AND wers='$wb' ORDER BY id DESC LIMIT 1", 'ARRAY_N' );
	$j_date = $j_date[0][0];


	$exp['all'] = $all;
	$exp['cat'] = $cat;
	$exp['not_becked_up'] = $sdata;
	$exp['datetime'] = $j_date;
	
	if ( $all != '0' ) 
	{
		
		if ($sdata == '0') 
		{
			$exp['status'] = '<a style="color: green;">all of your '.$cat.'s backed up</a>';
		}
		else
		{
			$exp['status'] = '<a style="color: #c80a5a;">'.$sdata.' not backed up</a>';
		}
		
	}
	else
	{
		$exp['status'] = 'You have not any '.$cat;
	}

	return $exp;
}

?>