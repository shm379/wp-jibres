<?php

function send_post($data)
{
	send_data_jibres("/post/add", $data);
}



function post_arr_sort($arr)
{
	$ch = array('title'       => 'post_title',
				'seotitle'    => '',
				'slug'        => '',
				'excerpt'     => 'post_excerpt',
				'subtitle'    => '',
				'content'     => 'post_content',
				'status'      => 'post_status',
				'publishdate' => 'post_modified',
				'datecreated' => 'post_date'
								);
		
	$changed = sort_arr($ch, $arr);
	create_csv('posts', $changed);
}

function insert_post_in_jib($id)
{
	$data = array('item_id' => $id, 'type' => 'post');
	insert_in_jibres($data);
}

function get_post_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE 
									post_type = 'post' AND ID NOT IN 
									(SELECT item_id FROM {$wpdb->prefix}jibres_check 
									WHERE type = 'post' AND backuped = 1)");

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
				
			insert_post_in_jib($value);
			$post_results = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $value");
			foreach ($post_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			post_arr_sort($arr_results);

		}

		printf("OK Your Posts Backuped<br><br>");
	}
	else
	{
		printf("All Posts Are Backuped<br><br>");
	}

}


function posts_b()
{
	
	if (create_jibres_table() === true) 
	{
		get_post_data();
	}

}

?>