<?php
/**
 * @package           jibres
 * @author            Shahb2
 * @copyright         2020 Jibres
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       jibres
 * Plugin URI:        https://jibres.com
 * Description:       Backup of your data on jibres.
 */


add_action( 'admin_menu', 'menus' );
function menus() 
{
    add_menu_page(
        'JIBRES',
        'Jibres',
        'manage_options',
        'jibres',
        'jibres',
        plugin_dir_url(__FILE__) . 'admin/images/Jibres-Logo-icon-black-32.png',
        30
    );
}

function not()
{
    if ($_GET['page'] == 'jibres') 
    {
        echo "<h1>Jibres</h1>";
    }
}
add_action( 'admin_notices', 'not' );


function jib_css() {
	echo "
	<style type='text/css'>
	.jibres {

	}
	</style>
	";
}

add_action( 'admin_head', 'jib_css' );


function create_csv($data)
{
	$f = pathinfo(__FILE__);
	$fname = $f['dirname'] . '/backup/' . date("Y-m-d") . '.csv';

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


function send_product($data)
{
    $store = '';
    $apikey = '';
    $appkey = '';

    $ch = curl_init();

    $headers =  array('Content-Type: application/json', 'appkey: '.$appkey, 'apikey: '.$apikey);
    
    curl_setopt($ch, CURLOPT_URL, "https://jibres.com/fa/api/v1/".$store."/product/add");
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



function arr_sort($arr)
{
	$ch = array('title' 	   => 'post_title',
				'slug'         => '',
				'desc'         => 'post_content',
				'barcode'      => '',
				'barcode2'     => '',
				'buyprice'     => 'max_price',
				'price'        => 'min_price',
				'discount'     => '',
				'vat'          => '',
				'sku'          => 'sku',
				'infinite'     => '',
				'gallery'      => '',
				'weight'       => '',
				'weightunit'   => '',
				'seotitle'     => '',
				'type'         => 'post_type',
				'seodesc'      => '',
				'saleonline'   => '',
				'saletelegram' => '',
				'saleapp'      => '',
				'company'      => '',
				'scalecode'    => '',
				'status'       => '',
				'minsale'      => '',
				'maxsale'      => '',
				'salestep'     => '',
				'oversale'     => '',
				'unit'         => '',
				'category'     => '',
				'tag'          => ''
                );
    foreach ($ch as $key => $value) 
    {
        if ($key == 'status' and $arr["onsale"] == "1") 
        {
            $ch[$key] = 'available';
        }
        else
        {
            $ch[$key] = $arr[$value];
        }
    }
    create_csv($ch);
}

function insert_in_jib($id)
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'jibres_check';
	
	$wpdb->insert( 
		$table_name, 
		array( 
			'product_id' => $id
		) 
	);
}

function get_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'product'");

    $arr_results = array();
    $ids = array();
	foreach ($results as $key => $value) 
	{
		foreach ($value as $key => $val) 
	    {
            if ($key == "ID") 
            {
            	array_push($ids, $val);
            }
	    }

	}

	foreach ($ids as $value) 
	{
		$check_ex = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}jibres_check WHERE product_id = $value AND backuped = 1");
       	if (empty($check_ex)) 
       	{
       		insert_in_jib($value);
       		$post_results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $value");
       		foreach ($post_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				$arr_results[$key2] = $val2;
       			}
       		}

       		$meta_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id = $value");
       		foreach ($meta_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				$arr_results[$key2] = $val2;
       			}
       		}

    		arr_sort($arr_results);
       	}
	}

    printf("ok<br><br>");
	printf('<a href="?page=jibres"><button>back</button></a>');
}

function ch_jib_table()
{
    global $wpdb;
 		
 	$table_name = $wpdb->prefix . 'jibres_check';	

    $query = $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->esc_like( $table_name ) );
 
    if ( $wpdb->get_var( $query ) == $table_name ) 
    {
        get_data();
    }
 	else
 	{
 		$charset_collate = $wpdb->get_charset_collate();

		$create_ddl = "CREATE TABLE $table_name (
		  id int(11) NOT NULL AUTO_INCREMENT,
		  time datetime DEFAULT NOW() NOT NULL,
		  product_id int(11) NOT NULL,
		  backuped int(11) DEFAULT 1 NOT NULL,
		  PRIMARY KEY  (id)
		) $charset_collate;";

 		// Didn't find it try to create it..
    	$wpdb->query( $create_ddl );
 
    	// We cannot directly tell that whether this succeeded!
    	if ( $wpdb->get_var( $query ) == $table_name ) 
    	{
    	    get_data();
    	}
    	// dbDelta( $create_ddl );
 	}
}

function admin_jib() 
{
    global $wpdb;
    
	if ($_GET['page'] == 'jibres') 
    {
    	printf('<div class="jibres">');
    	if ($_GET['jibres'] == 'backup') 
    	{
	    	ch_jib_table();
    	}
    	else
    	{
    		 printf('<a href="?page=jibres&jibres=backup"><button>Backup</button></a>');
    	}
    	printf('</div>');
    	
	    
    }
}



add_action( 'admin_notices', 'admin_jib' );
?>
