<?php

function send_comment($data)
{
	send_data_jibres("/comment/add", $data);
}



function comment_arr_sort($arr)
{
	$ch = array('post'         => 'comment_post_ID',
				'author'       => 'comment_author',
				'author_email' => 'comment_author_email',
				'date'         => 'comment_date',
				'content'      => 'comment_content',
				'approved'     => 'comment_approved'
				);
		
	$changed = sort_arr($ch, $arr);
	create_csv('comments', $changed);
}

function insert_comment_in_jib($id)
{
	$data = array('item_id' => $id, 'type' => 'comment');
	insert_in_jibres($data);
}

function get_comment_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT comment_ID FROM $wpdb->comments WHERE 
									comment_ID NOT IN 
									(SELECT item_id FROM {$wpdb->prefix}jibres_check 
									WHERE type = 'comment' AND backuped = 1)");

	$arr_results = array();
	$ids = array();

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
		foreach ($ids as $value) 
		{
				
			insert_comment_in_jib($value);
			$post_results = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_ID = $value");
			foreach ($post_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			comment_arr_sort($arr_results);

		}

		printf("ok<br><br>");
	}
	else
	{
		printf("All comments are backuped<br><br>");
	}

}


function comments_b()
{
	
	if (create_jibres_table() === true) 
	{
		get_comment_data();
	}

}

?>