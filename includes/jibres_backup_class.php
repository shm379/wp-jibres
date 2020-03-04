<?php
/**
 * jibres backup main class
 */
class jibres_backup
{
	

	function backup_arr_sort($arr, $stnd, $excepts = [])
	{
		if (!empty($excepts)) 
		{
			foreach ($excepts as $exkey => $exvalue) 
			{
				foreach ($exvalue as $key => $value) 
				{
					if ($arr[$exkey] == $key) 
					{
						$arr[$exkey] = $value;
					}
				}
			}
		}
	
		$changed = jibres_sort_arr($stnd, $arr);
		
		return $changed;		
	}
	
	// insert product to jibres table 
	function insert_backup_in_jibres($data = [])
	{
		$id = $data[0];
		$type = $data[1];

		$data = ['item_id' => $id, 'type' => $type];
		insert_in_jibres($data);
	}



	// get data from tables
	function get_data($id_column, $table, $type, $where = [], $excepts = [])
	{
		global $wpdb;
		
		$main_arr = [];

		$table = $wpdb->$table;
		$jibres_ctable = JIBRES_CTABLE;
		$wers = jibres_wis();
		
		if (!empty($where)) 
		{
			$i = 0;
			foreach ($where as $key => $value) 
			{
				if ($i == 0) 
				{
					$where = "$key='$value'";
				}
				$i++;
			}
		}
		else
		{
			$where = "1=1";
		}

		$query = 
		"
			SELECT
				$id_column
			FROM
				$table
			WHERE
				$where AND
				$id_column NOT IN
				(
					SELECT item_id FROM $jibres_ctable WHERE type = '$type' AND wers = '$wers' AND backuped = 1
				)
		";

		$results = $wpdb->get_results($query);
	
		$ids = [];
	
		foreach ($results as $key => $value) 
		{
			foreach ($value as $key => $val) 
			{
				if ($key == "$id_column") 
				{
					array_push($ids, $val);
				}
			}
	
		}
	
		if (!empty($results)) 
		{
			$i = 0;
	
			foreach ($ids as $value) 
			{
				
				$arr_results = [];
				
				$i++;

				$query = 
				"
					SELECT
						*
					FROM 
						$table
					WHERE
						$id_column = '$value'
				";
				$main_results = $wpdb->get_results($query);
				foreach ($main_results as $key => $val) 
				{
					foreach ($val as $key2 => $val2) 
					{
						$arr_results[$key2] = $val2;
					}
				}


				if (!empty($excepts)) 
				{
					foreach ($excepts as $exkey => $exvalue) 
					{
						$extable = $wpdb->prefix. $exkey;
						$exwhere = "$exvalue='$value'";
						
						$exquery = 
						"
							SELECT 
								*
							FROM 
								$extable
							WHERE
								$exwhere
						";
						$ex_results = $wpdb->get_results($exquery);
						foreach ($ex_results as $key => $val) 
						{
							if ($exkey == 'postmeta') 
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
							else
							{
								foreach ($val as $key2 => $val2) 
								{
									$arr_results[$key2] = $val2;
								}
							}
						}
					}
				}
	
				
				array_push($main_arr, $arr_results);

			}
	

			return $main_arr;
		}
		else
		{
			return [];
		}
	
	}



}



?>