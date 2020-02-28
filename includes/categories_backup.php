<?php

function category_arr_sort($arr)
{
	$ch = array('name'  => 'name',
				'slug'  => 'slug',
				'group' => 'term_group'
				);

	$changed = sort_arr($ch, $arr);

	if (wis() == 'csv') 
	{
		wis('categories', $changed);
	}
	elseif (wis() == 'api') 
	{
		wis('/category/add', $changed);
	}
}

function insert_category_in_jib($id)
{
	$data = array('item_id' => $id, 'type' => 'category');
	insert_in_jibres($data);
}

function get_category_data()
{
	global $wpdb;

	$results = $wpdb->get_results("SELECT term_id FROM $wpdb->term_taxonomy WHERE 
									taxonomy = 'product_cat' AND term_id NOT IN 
									(SELECT item_id FROM {$wpdb->prefix}jibres_check 
									WHERE type = 'category' AND backuped = 1)");

	$arr_results = array();
	$ids = array();

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
			insert_category_in_jib($value);
			$cat_results = $wpdb->get_results("SELECT * FROM $wpdb->terms WHERE term_id = $value");
			foreach ($cat_results as $key => $val) 
			{
				foreach ($val as $key2 => $val2) 
				{
					$arr_results[$key2] = $val2;
				}
			}

			printf('<script>
						prsb('.$i.');
					</script>');
			category_arr_sort($arr_results);
			ob_flush();
			flush();
		}

		printf("OK Your Categories Bacuped<br><br>");
	}
	else
	{
		printf("All Categories Are Backuped<br><br>");
	}

}

function categories_b()
{
	
	if (create_jibres_table() === true) 
	{
		get_category_data();
	}

}

?>