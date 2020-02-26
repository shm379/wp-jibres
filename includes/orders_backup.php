<?php

function send_order($data)
{
	send_data_jibres("/cart/add", $data);
}



function order_arr_sort($arr)
{
	$ch = array('key'            => '_order_key',
				'paid_date'      => '_paid_date',
				'completed_date' => '_completed_date',
				'currency'       => '_order_currency',
				'customer'       => '_customer_user'
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

	$results = $wpdb->get_results("SELECT ID FROM $wpdb->posts 
									WHERE post_type = 'shop_order' AND ID NOT IN 
									(SELECT item_id FROM {$wpdb->prefix}jibres_check 
									WHERE type = 'order' AND backuped = 1)");

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

	if (!empty($results)) 
	{
		foreach ($ids as $value) 
		{
			
			insert_order_in_jib($value);
			$order_results = $wpdb->get_results("SELECT * FROM $wpdb->posts 
												 WHERE ID = $value");
			foreach ($order_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			$meta_order = $wpdb->get_results("SELECT * FROM $wpdb->postmeta 
											  WHERE post_id = $value");
			foreach ($meta_order as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					if ($key2 == 'meta_key') 
					{
						$this_key = $val2;
					}
					if ($key2 == 'meta_value') 
					{
						$arr_results[$this_key] = $val2;
					}
				}	
			}


			order_arr_sort($arr_results);

		}

		printf("OK Your Orders Backuped<br><br>");
	}
	else
	{
		printf("All Orders Are Backuped<br><br>");
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