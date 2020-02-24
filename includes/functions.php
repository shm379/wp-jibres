<?php 

function create_jibres_table($tname, $strc)
{
    global $wpdb;
 		
 	$table_name = $tname;	

    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
 
    if ( $wpdb->get_var( $query ) == $table_name ) 
    {
        return true;
    }
 	else
 	{
 		$charset_collate = $wpdb->get_charset_collate();

 		// Didn't find it try to create it..
    	$wpdb->query( $strc );
 
    	// We cannot directly tell that whether this succeeded!
    	if ( $wpdb->get_var( $query ) == $table_name ) 
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


function insert_in_jibres($tname, $data = array())
{
	global $wpdb;

	$table_name = $wpdb->prefix . $tname;
	
	$wpdb->insert( 
		$table_name, 
		$data
	);
}



function send_data_jibres($where, $data)
{
    $store = '';
    $apikey = '';
    $appkey = '';

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
        printf('OK');
    }
    
    curl_close($ch);

    // return $push_p;
}


function create_csv($cat, $data)
{

    $fname = dirname( __DIR__ ) . '/backup/' . $cat . '.csv';

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

function sort_arr($ch = array(), $data = array())
{
    foreach ($ch as $key => $value) 
    {
        $ch[$key] = $data[$value];
    }

    return $ch;
}


?>