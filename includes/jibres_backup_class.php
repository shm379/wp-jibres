<?php
/**
 * jibres backup main class
 */
class jibres_backup
{
	
	function get_jibres_id( $self_id, $cat )
	{
		global $wpdb;

		$table = JIBRES_CTABLE;
		$query = 
		"
			SELECT
				*
			FROM
				$table
			WHERE
				item_id = '$self_id' AND
				type = '$cat' AND 
				wers = 'api'
		";

		$results = $wpdb->get_results( $query, 'ARRAY_A' );
	
		foreach ($results[0] as $key => $value) 
		{
			if ($key == "jibres_id") 
			{
				if ( $value != null ) 
				{
					$jibres_id = $value;
				}
				else
				{
					$jibres_id = null;
				}
			}
	
		}

		return $jibres_id;
	}

	function backup_arr_sort( $arr, $stnd, $excepts = [] )
	{

		if ( ! empty( $excepts ) ) 
		{
			foreach ( $excepts as $exkey => $exvalue ) 
			{
				foreach ( $exvalue as $key => $value ) 
				{
					if ( $arr[$exkey] == "$key" ) 
					{
						$arr[$exkey] = $value;
					}
				}
			}
		}

		$changed = jibres_sort_arr( $stnd, $arr );
		
		return $changed;		
	}
	
	// insert product to jibres table 
	function insert_backup_in_jibres( $data = [], $jibres_id = null )
	{
		$id = $data[0];
		$type = $data[1];

		$data = ['item_id' => $id, 'type' => $type];

		if ( $jibres_id != null ) 
		{
			$data['jibres_id'] = $jibres_id;	
		}
		
		insert_in_jibres( $data );
	}



	// get data from tables
	function get_data( $id_column, $table, $type, $where = [], $excepts = [] )
	{
		global $wpdb;
		
		$main_arr = [];

		$table = $wpdb->prefix. $table;
		$jibres_ctable = JIBRES_CTABLE;
		$wers = jibres_wis();
		
		$where = jibres_create_sql_where( $where );

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
			LIMIT 1000
		";

		$results = $wpdb->get_results( $query );
	
		$ids = [];
	
		foreach ( $results as $key => $value ) 
		{
			foreach ( $value as $key2 => $val ) 
			{
				if ( $key2 == "$id_column" ) 
				{
					array_push( $ids, $val );
				}
			}
	
		}
	
		if ( ! empty( $results ) ) 
		{
			$i = 0;
	
			foreach ( $ids as $value ) 
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
				$main_results = $wpdb->get_results( $query );
				foreach ( $main_results as $key => $val ) 
				{
					foreach ( $val as $key2 => $val2 ) 
					{
						$arr_results[$key2] = $val2;
					}
				}


				if ( ! empty( $excepts ) ) 
				{
					foreach ( $excepts as $exkey => $exvalue ) 
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
						$ex_results = $wpdb->get_results( $exquery );
						foreach ( $ex_results as $key => $val ) 
						{
							foreach ( $val as $key2 => $val2 ) 
							{
								if ( $key2 == 'meta_key' or $key2 == 'meta_value' ) 
								{
									if ( $key2 == 'meta_key' ) 
									{
										$this_key = $val2;
									}
									if ( $key2 == 'meta_value' ) 
									{
										$arr_results[$this_key] = $val2;
									}
								}
								else
								{
									$arr_results[$key2] = $val2;
								}
							}	
						}
					}
				}

				array_push( $main_arr, $arr_results );
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