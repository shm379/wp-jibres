<?php


/**
 * categories backup class
 */
class jibres_categories extends jibres_backup
{

	public static $jibres_stantard_category_array = [  'name'  => 'name',
												'slug'  => 'slug',
												'group' => 'term_group'
												];
	

	private $where_backup;
	private $this_jibres_wis;
	private $last_i = 0;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ( $this->this_jibres_wis == 'csv' ) ? 'categories' : '/category/add';
			$this->create_pbr();
			$this->get_category_data();
		}
	}


	private function create_pbr()
	{
		$all = jibres_get_not_backuped( 'term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat'] );

		if ( $all != '0' ) 
		{
			printf('<br><p>Backing up categories...</p>');
			printf('<progress id="tprog" value="0" max="'.$all.'" style="height: 3px;"></progress>  <a id="tinof"></a><br><br>');
			printf('<script>
					function trsb(meq) {
						document.getElementById("tprog").value = meq;
						document.getElementById("tinof").innerHTML = meq + " of '.$all.' backed up";
					}
					</script>');
		}
	
	}

	
	function get_category_data()
	{


		$where = ['taxonomy'=>'product_cat'];
		$excepts = 
		[
			'terms'=> 'term_id'
		];
		$data = $this->get_data( 'term_id', 'term_taxonomy', 'category', $where, $excepts );


		if ( ! empty( $data ) ) 
		{
			$i = $this->last_i;
			
			foreach ( $data as $value ) 
			{
				
				$i++;

				
				// sort array by jibres categories database design
				$changed = $this->backup_arr_sort( $value, self::$jibres_stantard_category_array );
				
				// backup this cat
				$get_data = jibres_wis( $this->where_backup, $changed );

				// insert this cat to jibres check table
				if ( is_array( $get_data ) and !empty( $get_data ) ) 
				{
					if ( $get_data['ok'] == true ) 
					{
						$this->insert_backup_in_jibres( [$value['term_id'], 'category'] );
					}
					else
					{
						$error = 'cat code: ' . $value['term_id'] . ' > ' . json_encode( $get_data, JSON_UNESCAPED_UNICODE );
						jibres_error_log( 'category_backup', $error );
						
						printf('<div class="updated" style="border-left-color: #c0392b;"><br>' . 
						 		$get_data['msg'][0]['text']	. 
						 		'<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
						exit();
					}
				}
				elseif ( $get_data == true ) 
				{
					$this->insert_backup_in_jibres( [$value['term_id'], 'category'] );
				}
				
				// update progress bar
				printf('<script>
							trsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
			
			
			$this->last_i = $i;
			$this->cab_start_again();
		}
		else
		{
			printf('<br><a href="?page=jibres" class="jibres_notif_close">close</a>');
			printf("All Categories Are Backed up");
			if ( $this->this_jibres_wis == 'csv' ) 
			{
				printf(' | <a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a>');

				if( jibres_auto_mail() == true )
				{
					jibres_mail_backup( 'categories' );
				}
			}
			printf('<br><br>');

		}
	
	}


	function cab_start_again()
	{
		$this->get_category_data();
	}


}

?>