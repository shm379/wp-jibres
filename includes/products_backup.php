<?php

function send_product($data)
{
	send_data_jibres("/product/add", $data);
}



function product_arr_sort($arr)
{
	$ch = array('title'        => 'post_title',
				'slug'         => '',
				'desc'         => 'post_content',
				'barcode'      => '',
				'barcode2'     => '',
				'buyprice'     => '_regular_price',
				'price'        => '_price',
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
				'status'       => 'onsale',
				'minsale'      => '',
				'maxsale'      => '',
				'salestep'     => '',
				'oversale'     => '',
				'unit'         => '',
				'category'     => '',
				'tag'          => ''
				);
		
	if ($arr["onsale"] == "1") 
	{
		$arr["onsale"] = 'available';
	}
	else
	{
		$arr["onsale"] = 'unavailable';
	}

	$changed = sort_arr($ch, $arr);
	create_csv('products', $changed);
	// send_product($changed);
}

function insert_product_in_jib($id)
{
	$data = array('item_id' => $id, 'type' => 'product');
	insert_in_jibres($data);
}

function get_product_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE 
									post_type = 'product' AND ID NOT IN 
									(SELECT item_id FROM {$wpdb->prefix}jibres_check 
									WHERE type = 'product' AND backuped = 1)");

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
		$i = 0;
		printf('<progress id="pprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress><br><br>');
		foreach ($ids as $value) 
		{
			
			$i++;
			insert_product_in_jib($value);
			$post_results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $value");
			foreach ($post_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			$meta_results = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}wc_product_meta_lookup 
												WHERE product_id = $value");
			foreach ($meta_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			$meta_post = $wpdb->get_results("SELECT * FROM $wpdb->postmeta 
											  WHERE post_id = $value");
			foreach ($meta_post as $key => $val) 
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
			printf('<script>
					 document.getElementById("pprog").value = '.$i.';
					</script>');
			product_arr_sort($arr_results);

		}

		printf("OK Your Products Backuped<br><br>");
	}
	else
	{
		printf("All Products Are Backuped<br><br>");
	}

}

function products_b()
{
	if (create_jibres_table() === true) 
	{
		get_product_data();
	}
}

?>