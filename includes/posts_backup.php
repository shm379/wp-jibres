<?php

/**
 * posts backup class
 */
class jibres_posts extends jibres_backup
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

	
	function get_post_data()
	{


		$where = ['post_type'=>'post'];
		$data = $this->get_data('ID', 'posts', 'post', $where);
	
		if (!empty($data)) 
		{
			$i = 0;
			printf('<p>Backuping posts...</p>');
			printf('<progress id="sprog" value="0" max="'.count($data).'" style="height: 3px;"></progress>  <a id="sinof"></a><br><br>');
			printf('<script>
					function srsb(meq) {
						document.getElementById("sprog").value = meq;
						document.getElementById("sinof").innerHTML = meq + " of '.count($data).' backuped";
					}
					</script>');
			foreach ($data as $value) 
			{
					
				$i++;

				// insert this post to jibres check table
				$this->insert_backup_in_jibres([$value['ID'], 'post']);
				
				// sort array by jibres posts database design
				$changed = $this->backup_arr_sort($value, $this->jibres_stantard_post_array);
				
				// backup this post
				jibres_wis($this->where_backup, $changed);
				
				// update progress bar
				printf('<script>
							srsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
	
			if (jibres_wis() == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Posts Backuped<br><br>");
		}
		else
		{
			if (jibres_wis() == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Posts Are Backuped<br><br>");
		}
	
	}

}

?>