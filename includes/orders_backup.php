<?php


/**
 * orders backup class
 */
class jibres_orders
{
	
	public $jibres_stantard_order_array = [ 'key'            => '_order_key',
											'paid_date'      => '_paid_date',
											'completed_date' => '_completed_date',
											'currency'       => '_order_currency',
											'customer'       => '_customer_user'
											];

	private $where_backup;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (jibres_wis() == 'csv') ? 'orders' : '/cart/add';
			$this->get_order_data();
		}
	}


	function order_arr_sort($arr)
	{
			
		$changed = sort_arr($this->jibres_stantard_order_array, $arr);
	
		jibres_wis($this->where_backup, $changed);
		
	}
	
	function insert_order_in_jibres($id)
	{
		$data = array('item_id' => $id, 'type' => 'order');
		insert_in_jibres($data);
	}
	
	function get_order_data()
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
				post_type = 'shop_order' AND 
				ID NOT IN 
				(
					SELECT item_id FROM $jibres_ctable WHERE type = 'order' AND backuped = 1
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
				$query = 
				"
					SELECT 
						* 
					FROM 
						$table 
					WHERE 
						ID = $value
				";
				$order_results = $wpdb->get_results($query);
				foreach ($order_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				$table = $wpdb->postmeta;
				$query = 
				"
					SELECT 
						* 
					FROM 
						$table 
					WHERE 
						post_id = $value
				";
				$meta_order = $wpdb->get_results($query);
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
	
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}	
			printf("OK Your Orders Backuped<br><br>");
		}
		else
		{
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Orders Are Backuped<br><br>");
		}
	
	}

}

?>