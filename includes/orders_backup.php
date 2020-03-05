<?php


/**
 * orders backup class
 */
class jibres_orders extends jibres_backup
{
	
	public static $jibres_stantard_order_array = [ 'key'            => '_order_key',
											'paid_date'      => '_paid_date',
											'completed_date' => '_completed_date',
											'currency'       => '_order_currency',
											'customer'       => '_customer_user'
											];

	private $where_backup;
	private $this_jibres_wis;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ($this->this_jibres_wis == 'csv') ? 'orders' : '/cart/add';
			$this->get_order_data();
		}
	}

	
	function get_order_data()
	{
	

		$where = ['post_type'=>'shop_order'];
		$excepts = 
		[
			'postmeta'=> 'post_id'
		];
		$data = $this->get_data('ID', 'posts', 'order', $where, $excepts);

		if (!empty($data)) 
		{
			$i = 0;
			printf('<p>Backuping orders...</p>');
			printf('<progress id="oprog" value="0" max="'.count($data).'" style="height: 3px;"></progress>  <a id="oinof"></a><br><br>');
			printf('<script>
					function orsb(meq) {
						document.getElementById("oprog").value = meq;
						document.getElementById("oinof").innerHTML = meq + " of '.count($data).' backuped";
					}
					</script>');
			foreach ($data as $value) 
			{
				
				$i++;
				
				// insert this product to jibres check table
				$this->insert_backup_in_jibres([$value['ID'], 'order']);
				
				// sort array by jibres products database design
				$changed = $this->backup_arr_sort($value, self::$jibres_stantard_order_array);
				
				// backup this product
				jibres_wis($this->where_backup, $changed);
				
				// update progress bar
				printf('<script>
							orsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
	
			if ($this->this_jibres_wis == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}	
			printf("OK Your Orders Backuped<br><br>");
		}
		else
		{
			if ($this->this_jibres_wis == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Orders Are Backuped<br><br>");
		}
	
	}

}

?>