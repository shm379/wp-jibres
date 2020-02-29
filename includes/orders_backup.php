<?php


/**
 * orders backup class
 */
class jibres_orders
{
	
	public $jibres_stantard_order_array = array('key'            => '_order_key',
												'paid_date'      => '_paid_date',
												'completed_date' => '_completed_date',
												'currency'       => '_order_currency',
												'customer'       => '_customer_user'
												);

	private $where_backup;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (wis() == 'csv') ? 'orders' : '/cart/add';
			$this->get_order_data();
		}
	}


	function order_arr_sort($arr)
	{
			
		$changed = sort_arr($this->jibres_stantard_order_array, $arr);
	
		wis($this->where_backup, $changed);
		
	}
	
	function insert_order_in_jibres($id)
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
			$i = 0;
			printf('<p>Backuping orders...</p>');
			printf('<progress id="oprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="oinof"></a><br><br>');
			printf('<script>
					function orsb(meq) {
						document.getElementById("oprog").value = meq;
						document.getElementById("oinof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
				
				$i++;
				$this->insert_order_in_jibres($value);
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
	
				printf('<script>
							orsb('.$i.');
						</script>');
				$this->order_arr_sort($arr_results);
				ob_flush();
				flush();
			}
	
			printf("OK Your Orders Backuped<br><br>");
		}
		else
		{
			printf("All Orders Are Backuped<br><br>");
		}
	
	}

}

?>