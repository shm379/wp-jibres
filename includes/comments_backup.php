<?php


/**
 * comments backup class
 */
class jibres_comments extends jibres_backup
{

	public static $jibres_stantard_comments_array = [  'post'         => 'comment_post_ID',
												'author'       => 'comment_author',
												'author_email' => 'comment_author_email',
												'date'         => 'comment_date',
												'content'      => 'comment_content',
												'approved'     => 'comment_approved'
												];
	
	private $where_backup;
	private $this_jibres_wis;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ($this->this_jibres_wis == 'csv') ? 'comments' : '/comment/add';
			$this->get_comment_data();
		}
	}

	
	function get_comment_data()
	{

		$data = $this->get_data('comment_ID', 'comments', 'comment');

	
		if (!empty($data)) 
		{
			$i = 0;
			printf('<p>Backuping comments...</p>');
			printf('<progress id="cprog" value="0" max="'.count($data).'" style="height: 3px;"></progress>  <a id="cinof"></a><br><br>');
			printf('<script>
					function crsb(meq) {
						document.getElementById("cprog").value = meq;
						document.getElementById("cinof").innerHTML = meq + " of '.count($data).' backuped";
					}
					</script>');
			foreach ($data as $value) 
			{
					
				$i++;


				// insert this product to jibres check table
				$this->insert_backup_in_jibres([$value['comment_ID'], 'comment']);
				
				// sort array by jibres products database design
				$changed = $this->backup_arr_sort($value, self::$jibres_stantard_comments_array);
				
				// backup this product
				jibres_wis($this->where_backup, $changed);
				
				// update progress bar
				printf('<script>
							crsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
			
			if ($this->this_jibres_wis == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Comments Backuped<br><br>");
		}
		else
		{
			if ($this->this_jibres_wis == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Comments Are Backuped<br><br>");
		}
	
	}

}


?>