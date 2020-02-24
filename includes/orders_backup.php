<?php

require_once(dirname( __FILE__ ) . '/functions.php');

function send_product($data)
{
	send_data_jibres("/cart/add", $data);
}



function arr_sort($arr)
{
	$ch = array('product' => 'order_item_id',
				'count'   => 'order_id'
                );
    
    $changed = sort_arr($ch, $arr);
    create_csv('orders', $changed);
}

function insert_in_jib($id)
{
	$data = array('order_item_id' => $id);
	insert_in_jibres('jibres_order_check', $data);
}

function get_order_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id NOT IN (SELECT order_item_id FROM {$wpdb->prefix}jibres_order_check WHERE backuped = 1)");

    $arr_results = array();
    $ids = array();

	foreach ($results as $key => $value) 
	{
		foreach ($value as $key => $val) 
	    {
            if ($key == "order_item_id") 
            {
            	array_push($ids, $val);
            }
	    }

	}

	if (!empty($results)) 
    {
    	foreach ($ids as $value) 
		{
       	
       		insert_in_jib($value);
       		$post_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $value");
       		foreach ($post_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				$arr_results[$key2] = $val2;
       			}
       		}

    		arr_sort($arr_results);

		}

		printf("ok<br><br>");
    }
    else
    {
       	printf("All orders are backuped<br><br>");
    }

}


function orders_b()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'jibres_order_check';
	$create_ddl = "CREATE TABLE $table_name (
				   id int(11) NOT NULL AUTO_INCREMENT,
				   time datetime DEFAULT NOW() NOT NULL,
				   order_item_id int(11) NOT NULL,
				   backuped int(11) DEFAULT 1 NOT NULL,
				   PRIMARY KEY  (id)
				 ) $charset_collate;";

	
 	if (create_jibres_table($table_name, $create_ddl) === true) 
 	{
 		get_order_data();
 	}

}

?>