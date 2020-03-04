<?php


/**
 * categories backup class
 */
class jibres_categories extends jibres_backup
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

	
	function get_category_data()
	{


		$where = ['taxonomy'=>'product_cat'];
		$data = $this->get_data('term_id', 'term_taxonomy', 'category', $where);



		if (!empty($data)) 
		{
			$i = 0;
			printf('<p>Backuping categories...</p>');
			printf('<progress id="tprog" value="0" max="'.count($data).'" style="height: 3px;"></progress>  <a id="tinof"></a><br><br>');
			printf('<script>
					function trsb(meq) {
						document.getElementById("tprog").value = meq;
						document.getElementById("tinof").innerHTML = meq + " of '.count($data).' backuped";
					}
					</script>');
			foreach ($data as $value) 
			{
				
				$i++;

				// insert this product to jibres check table
				$this->insert_backup_in_jibres([$value['term_id'], 'category']);
				
				// sort array by jibres products database design
				$changed = $this->backup_arr_sort($value, $this->jibres_stantard_category_array);
				
				// backup this product
				jibres_wis($this->where_backup, $changed);
				
				// update progress bar
				printf('<script>
							trsb('.$i.');
						</script>');
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