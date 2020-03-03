<?php

/**
 * posts backup class
 */
class jibres_posts
{

	public $jibres_stantard_post_array = [  'title'       => 'post_title',
											'seotitle'    => '',
											'slug'        => '',
											'excerpt'     => 'post_excerpt',
											'subtitle'    => '',
											'content'     => 'post_content',
											'status'      => 'post_status',
											'publishdate' => 'post_modified',
											'datecreated' => 'post_date'
											];

	private $where_backup;
	
	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (jibres_wis() == 'csv') ? 'posts' : '/post/add';
			$this->get_post_data();
		}
	}

	function post_arr_sort($arr)
	{

		$changed = sort_arr($this->jibres_stantard_post_array, $arr);

		jibres_wis($this->where_backup, $changed);
	}
	
	function insert_post_in_jibres($id)
	{
		$data = ['item_id' => $id, 'type' => 'post'];
		insert_in_jibres($data);
	}
	
	function get_post_data()
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
				post_type = 'post' AND 
				ID NOT IN 
				(
					SELECT item_id FROM $jibres_ctable WHERE type = 'post' AND backuped = 1
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
			printf('<p>Backuping posts...</p>');
			printf('<progress id="sprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="sinof"></a><br><br>');
			printf('<script>
					function srsb(meq) {
						document.getElementById("sprog").value = meq;
						document.getElementById("sinof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
					
				$i++;
				$this->insert_post_in_jibres($value);
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
				printf('<script>
							srsb('.$i.');
						</script>');
				$this->post_arr_sort($arr_results);
				ob_flush();
				flush();
			}
	
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Posts Backuped<br><br>");
		}
		else
		{
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Posts Are Backuped<br><br>");
		}
	
	}

}

?>