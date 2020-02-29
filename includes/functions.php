<?php 


function jibres_defines()
{
	global $wpdb;

	define('JIBRES_TABLE', $wpdb->prefix. 'jibres');
	define('JIBRES_CTABLE', $wpdb->prefix. 'jibres_check');
}

function ch_jibres_store_data($update = null)
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
		if (!empty($store_check)) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	elseif ($update == 'start_again') 
	{
		$wpdb->query( "DROP TABLE IF EXISTS $jibres_table" );
	}
}


function create_jibres_table($tstrc = null, $tname = JIBRES_CTABLE)
{
	global $wpdb;
		
	if ($tstrc == null) 
	{
		$create_ddl = "CREATE TABLE $tname (
					   id int(11) NOT NULL AUTO_INCREMENT,
					   time datetime DEFAULT NOW() NOT NULL,
					   item_id int(11) NOT NULL,
					   type varchar(50) NOT NULL,
					   wers varchar(55) NOT NULL,
					   backuped int(11) DEFAULT 1 NOT NULL,
					   PRIMARY KEY  (id)
					 ) $charset_collate;";
	}
	else
	{
		$create_ddl = $tstrc;
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


function insert_in_jibres($data = [], $tname = JIBRES_CTABLE)
{
	global $wpdb;

	if ($tname == JIBRES_CTABLE) 
	{
		$data['wers'] = wis();
	}
	
	$wpdb->insert( 
		$tname, 
		$data
	);
}



function send_data_jibres($where, $data)
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
	$store = $arr_results['store'];
	$apikey = $arr_results['apikey'];
	$appkey = $arr_results['appkey'];

	$ch = curl_init();

	$headers =  array('Content-Type: application/json', 'appkey: '.$appkey, 'apikey: '.$apikey);
	
	curl_setopt($ch, CURLOPT_URL, "https://jibres.com/fa/api/v1/".$store.$where);
	curl_setopt($ch, CURLOPT_HEADER, $headers);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	
	// $push_p = curl_exec($ch);

	if(curl_exec($ch) === false)
	{
		printf('Curl error: ' . curl_error($ch));
	}
	else
	{
		return true;
	}
	
	curl_close($ch);

	// return $push_p;
}


function create_csv($cat, $data)
{

	$fname = JIBRES_DIR . 'backup/' . $cat . '.csv';

	if (file_exists($fname)) 
	{
		$arr = array_values($data);
		$fp = fopen($fname, 'a');
		fputcsv($fp, $arr);
	} 
	else 
	{
		$fp = fopen($fname, 'a');
		$arr = array_keys($data);
		fputcsv($fp, $arr);
		$arr = array_values($data);
		fputcsv($fp, $arr);
	}
	
	fclose($fp);

}

function sort_arr($ch = [], $data = [])
{
	foreach ($ch as $key => $value) 
	{
		$ch[$key] = $data[$value];
	}

	return $ch;
}


function wis($item = null, $data = null)
{
	global $wpdb;

	$jibres_table = JIBRES_TABLE;
	$results = $wpdb->get_results("SELECT wis FROM $jibres_table");

	foreach ($results as $key => $value) 
	{
		foreach ($value as $key => $val) 
		{
			if ($key == "wis") 
			{
				$weris = $val;
			}
		}

	}

	if ($item == null and $data == null) 
	{
		return $weris;
	}
	else
	{
		if ($weris == 'csv') 
		{
			create_csv($item, $data);
		}
		elseif ($weris == 'api') 
		{
			send_data_jibres($item, $data);
		}
	}
}





function informations_b($item, $table, $cat, $wb, $where = [], $first = false)
{
	global $wpdb;


	$wers = ($wb == 'csv') ? 'to csv file' : 'to your jibres store';
	$jibres_ctable = JIBRES_CTABLE;

	$table = $wpdb->$table;
	if (!empty($where)) 
	{
		$i = 0;
		foreach ($where as $key => $value) 
		{
			if ($i == 0) 
			{
				$clm = $key;
				$rw = $value;
			}
			$i++;
		}
		$fdata = $wpdb->get_results("SELECT COUNT($item) FROM $table WHERE $clm = '$rw'");
		$query = 
		"
			SELECT 
				COUNT($item) 
			FROM 
				$table 
			WHERE 
				$clm = '$rw' AND 
				$item NOT IN 
				(
					SELECT item_id FROM $jibres_ctable WHERE type = '$cat' AND backuped = 1 AND wers = '$wb'
				)
		";
		$sdata = $wpdb->get_results($query);
	}
	else
	{
		$fdata = $wpdb->get_results("SELECT COUNT($item) FROM $table");
		$query = 
		"
			SELECT 
				COUNT($item) 
			FROM 
				$table 
			WHERE 
				$item NOT IN 
				(
					SELECT item_id FROM $jibres_ctable WHERE type = '$cat' AND backuped = 1 AND wers = '$wb'
				)
		";
		$sdata = $wpdb->get_results($query);
	}


	$first = ($first == false) ? 'And' : 'You';

	if (!empty($fdata)) 
	{
		foreach ($fdata as $key => $value) 
		{
			foreach ($value as $key2 => $val) 
			{
				$all = $val;
			}
		}
	
		printf($first.' have '.$all.' '.$cat);
		
		if (!empty($sdata)) 
		{
			foreach ($sdata as $key => $value) 
			{
				foreach ($value as $key2 => $val) 
				{
					$not_b = $val;
				}
			}
			if ($not_b == '0') 
			{
				printf(' and <a style="font-weight: bold; color: green;">all of your '.$cat.'s backuped '.$wers.'</a>');
			}
			else
			{
				printf(' and <a style="font-weight: bold; color: #c80a5a;">'.$not_b.' '.$cat.' not backuped '.$wers.'</a>');
			}
		}
		else
		{
			if (create_jibres_table() === false) 
			{
				printf(' and <a style="font-weight: bold; color: #c80a5a;">all of your '.$cat.'s not backuped '.$wers.'</a>');
				header("Refresh:0");
			}
			else
			{
				printf(' and <a style="font-weight: bold; color: green;">all of your '.$cat.'s backuped '.$wers.'</a>');
				header("Refresh:0");
			}
		}
		
	}
	else
	{
		printf($first.' have not any '.$cat);
	}
}

?>