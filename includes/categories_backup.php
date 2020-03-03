<?php


/**
 * categories backup class
 */
class jibres_categories
{

	public $jibres_stantard_category_array = [  'name'  => 'name',
												'slug'  => 'slug',
												'group' => 'term_group'
												];
	

	private $where_backup;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (jibres_wis() == 'csv') ? 'categories' : '/category/add';
			$this->get_category_data();
		}
	}


	function category_arr_sort($arr)
	{
	
		$changed = sort_arr($this->jibres_stantard_category_array, $arr);
	
		jibres_wis($this->where_backup, $changed);
	}
	
	function insert_category_in_jibres($id)
	{
		$data = array('item_id' => $id, 'type' => 'category');
		insert_in_jibres($data);
	}
	
	function get_category_data()
	{
		global $wpdb;
	

		$table = $wpdb->term_taxonomy;
		$jibres_ctable = JIBRES_CTABLE;
		$query = 
		"
			SELECT 
				term_id 
			FROM 
				$table 
			WHERE 
				taxonomy = 'product_cat' AND 
				term_id NOT IN 
				(
					SELECT item_id FROM $jibres_ctable WHERE type = 'category' AND backuped = 1
				)
		";
		$results = $wpdb->get_results($query);
	
		$arr_results = [];
		$ids = [];
	
		foreach ($results as $key => $value) 
		{
			foreach ($value as $key => $val) 
			{
				if ($key == "term_id") 
				{
					array_push($ids, $val);
				}
			}
	
		}
	
		if (!empty($results)) 
		{
			$i = 0;
			printf('<p>Backuping categories...</p>');
			printf('<progress id="tprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="tinof"></a><br><br>');
			printf('<script>
					function trsb(meq) {
						document.getElementById("tprog").value = meq;
						document.getElementById("tinof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
				
				$i++;
				$this->insert_category_in_jibres($value);
				$table = $wpdb->terms;
				$query = 
				"
					SELECT 
						* 
					FROM 
						$table 
					WHERE 
						term_id = $value
				";
				$cat_results = $wpdb->get_results($query);
				foreach ($cat_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}
	
				printf('<script>
							trsb('.$i.');
						</script>');
				$this->category_arr_sort($arr_results);
				ob_flush();
				flush();
			}
			
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Categories Bacuped<br><br>");
		}
		else
		{
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Categories Are Backuped<br><br>");
		}
	
	}


}

?>