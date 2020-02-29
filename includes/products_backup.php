<?php

/**
 * products backup class
 */
class jibres_products
{

	public $jibres_stantard_product_array = [   'title'        => 'post_title',
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
												];

	private $where_backup;
	
	function __construct()
	{
		if (create_jibres_table() === true) 
		{

			$this->where_backup = (wis() == 'csv') ? 'products' : '/product/add';
			$this->get_product_data();
		}
	}

	function product_arr_sort($arr)
	{
			
		if ($arr["onsale"] == "1") 
		{
			$arr["onsale"] = 'available';
		}
		else
		{
			$arr["onsale"] = 'unavailable';
		}
	
		$changed = sort_arr($this->jibres_stantard_product_array, $arr);
		
		wis($this->where_backup, $changed);
		
	}
	
	function insert_product_in_jibres($id)
	{
		$data = ['item_id' => $id, 'type' => 'product'];
		insert_in_jibres($data);
	}
	
	function get_product_data()
	{
		global $wpdb;
	

		$table = $wpdb->posts;
		$jibres_ctable = JIBRES_CTABLE;
		$query = 
		"
			SELECT
				ID
			FROM
				$table
			WHERE
				post_type = 'product' AND
				ID NOT IN
				(
					SELECT item_id FROM $jibres_ctable WHERE type = 'product' AND backuped = 1
				)
		";

		$results = $wpdb->get_results($query);
	
		$arr_results = [];
		$ids = [];
	
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
	
			printf('<p>Backuping products...</p>');
			printf('<progress id="pprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="inof"></a><br><br>');
			printf('<script>
					function prsb(meq) {
						document.getElementById("pprog").value = meq;
						document.getElementById("inof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
				
				$i++;
				$this->insert_product_in_jibres($value);
				$query = 
				"
					SELECT
						*
					FROM 
						$table
					WHERE
						ID = $value
				";
				$post_results = $wpdb->get_results($query);
				foreach ($post_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				$table = $wpdb->prefix. 'wc_product_meta_lookup';
				$query = 
				"
					SELECT 
						*
					FROM 
						$table
					WHERE
						product_id = $value
				";
				$meta_results = $wpdb->get_results($query);
				foreach ($meta_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				$table = $wpdb->prefix. 'postmeta';
				$query = 
				"
					SELECT 
						*
					FROM 
						$table
					WHERE 
						post_id = $value
				";
				$meta_post = $wpdb->get_results($query);
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
							prsb('.$i.');
						</script>');
				$this->product_arr_sort($arr_results);
				ob_flush();
				flush();
	
			}
	
			printf("OK Your Products Backuped<br><br>");
		}
		else
		{
			printf("All Products Are Backuped<br><br>");
		}
	
	}

}

?>