<?php

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

	if (wis() == 'csv') 
	{
		wis('comments', $changed);
	}
	elseif (wis() == 'api') 
	{
		wis('/comment/add', $changed);
	}
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
		$i = 0;
		printf('<p>Backuping comments...</p>');
		printf('<progress id="pprog" value="0" max="'.count($ids).'" style="height: 3px;"></progress>  <a id="inof"></a><br><br>');
		printf('<script>
				function prsb(meq) {
					document.getElementById("pprog").value = meq;
					document.getElementById("inof").innerHTML = meq + " of '.count($ids).' backuped";
				}
				</script>');
		foreach ($ids as $value) 
		{
				
			$i++;
			insert_comment_in_jib($value);
			$post_results = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_ID = $value");
			foreach ($post_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			printf('<script>
						prsb('.$i.');
					</script>');
			comment_arr_sort($arr_results);
			ob_flush();
			flush();
		}

		printf("OK Your Comments Backuped<br><br>");
	}
	else
	{
		printf("All Comments Are Backuped<br><br>");
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