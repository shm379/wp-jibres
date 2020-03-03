<?php


/**
 * comments backup class
 */
class jibres_comments
{

	public $jibres_stantard_comments_array = [  'post'         => 'comment_post_ID',
												'author'       => 'comment_author',
												'author_email' => 'comment_author_email',
												'date'         => 'comment_date',
												'content'      => 'comment_content',
												'approved'     => 'comment_approved'
												];
	
	private $where_backup;


	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->where_backup = (jibres_wis() == 'csv') ? 'comments' : '/comment/add';
			$this->get_comment_data();
		}
	}


	function comment_arr_sort($arr)
	{
			
		$changed = jibres_sort_arr($this->jibres_stantard_comments_array, $arr);
	
		jibres_wis($this->where_backup, $changed);
	}
	
	function insert_comment_in_jibres($id)
	{
		$data = array('item_id' => $id, 'type' => 'comment');
		insert_in_jibres($data);
	}
	
	function get_comment_data()
	{
		global $wpdb;
	

		$table = $wpdb->comments;
		$jibre_ctable = JIBRES_CTABLE;
		$query = 
		"
			SELECT 
				comment_ID 
			FROM 
				$table 
			WHERE 
				comment_ID NOT IN 
				(
					SELECT item_id FROM $jibre_ctable WHERE type = 'comment' AND backuped = 1
				)
		";
		$results = $wpdb->get_results($query);
	
		$arr_results = [];
		$ids = [];
	
		foreach ($results as $key => $value) 
		{
			foreach ($value as $key => $val) 
			{
				if ($key == "comment_ID") 
				{
					array_push($ids, $val);
				}
			}
	
		}
	
		if (!empty($results)) 
		{
			$i = 0;
			printf('<p>Backuping comments...</p>');
			printf('<progress id="cprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="cinof"></a><br><br>');
			printf('<script>
					function crsb(meq) {
						document.getElementById("cprog").value = meq;
						document.getElementById("cinof").innerHTML = meq + " of '.count($ids).' backuped";
					}
					</script>');
			foreach ($ids as $value) 
			{
					
				$i++;
				$this->insert_comment_in_jibres($value);
				$query = 
				"
					SELECT 
						* 
					FROM 
						$table 
					WHERE 
						comment_ID = $value
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
							crsb('.$i.');
						</script>');
				$this->comment_arr_sort($arr_results);
				ob_flush();
				flush();
			}
			
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Comments Backuped<br><br>");
		}
		else
		{
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Comments Are Backuped<br><br>");
		}
	
	}

}


?>