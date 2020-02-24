<?php

function send_order($data)
{
	send_data_jibres("/cart/add", $data);
}



function order_arr_sort($arr)
{
	$ch = array('product' => 'order_item_id',
				'count'   => 'order_id'
                );
    
    $changed = sort_arr($ch, $arr);
    create_csv('orders', $changed);
}

function insert_order_in_jib($id)
{
	$data = array('item_id' => $id, 'type' => 'order');
	insert_in_jibres($data);
}

function get_order_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id NOT IN (SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'order' AND backuped = 1)");

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
       	
       		insert_order_in_jib($value);
       		$post_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = $value");
       		foreach ($post_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				$arr_results[$key2] = $val2;
       			}
       		}

    		order_arr_sort($arr_results);

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

 	if (create_jibres_table() === true) 
 	{
 		get_order_data();
 	}

}

?>