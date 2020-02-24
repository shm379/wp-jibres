<?php

require_once(dirname( __FILE__ ) . '/functions.php');

function send_product($data)
{
	send_data_jibres("/product/add", $data);
}



function arr_sort($arr)
{
	$ch = array('title' 	   => 'post_title',
				'slug'         => '',
				'desc'         => 'post_content',
				'barcode'      => '',
				'barcode2'     => '',
				'buyprice'     => 'regular_price',
				'price'        => 'sale_price',
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
}

function insert_in_jib($id)
{
	$data = array('product_id' => $id);
	insert_in_jibres('jibres_product_check', $data);
}

function get_product_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = 'product' AND ID NOT IN (SELECT product_id FROM {$wpdb->prefix}jibres_product_check WHERE backuped = 1)");

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

       		$post_mata_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = $value AND meta_key = '_regular_price'");
       		foreach ($post_mata_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				if ($key2 == "meta_value") 
       				{
	       				$arr_results["regular_price"] = $val2;
       				}
       			}	
       		}

       		$post_mata_results = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE post_id = $value AND meta_key = '_price'");
       		foreach ($post_mata_results as $key => $val) 
       		{
       			foreach ($val as $key2 => $val2) 
       			{
       				if ($key2 == "meta_value") 
       				{
	       				$arr_results["sale_price"] = $val2;
       				}
       			}	
       		}

    		arr_sort($arr_results);

		}

		printf("ok<br><br>");
    }
    else
    {
       	printf("All products are backuped<br><br>");
    }

}

function products_b()
{
	global $wpdb;

	$table_name = $wpdb->prefix . 'jibres_product_check';
	$create_ddl = "CREATE TABLE $table_name (
				   id int(11) NOT NULL AUTO_INCREMENT,
				   time datetime DEFAULT NOW() NOT NULL,
				   product_id int(11) NOT NULL,
				   backuped int(11) DEFAULT 1 NOT NULL,
				   PRIMARY KEY  (id)
				 ) $charset_collate;";

	
 	if (create_jibres_table($table_name, $create_ddl) === true) 
 	{
 		get_product_data();
 	}

}

?>