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
			printf('<p>Backuping categories...</p>');
			printf('<progress id="tprog" value="0" max="'.$all.'" style="height: 3px;"></progress>  <a id="tinof"></a><br><br>');
			printf('<script>
					function trsb(meq) {
						document.getElementById("tprog").value = meq;
						document.getElementById("tinof").innerHTML = meq + " of '.$all.' backuped";
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

				// insert this product to jibres check table
				$this->insert_backup_in_jibres( [$value['term_id'], 'category'] );
				
				// sort array by jibres products database design
				$changed = $this->backup_arr_sort( $value, self::$jibres_stantard_category_array );
				
				// backup this product
				jibres_wis( $this->where_backup, $changed );
				
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
			if ( $this->this_jibres_wis == 'csv' ) 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Categories Are Backuped<br><br>");
		}
	
	}


	function cab_start_again()
	{
		$this->get_category_data();
	}


}

?>