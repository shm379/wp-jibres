<?php
/**
 * products backup class
 */
class jibres_products extends jibres_backup
{

	public static $jibres_stantard_product_array = [   'title'        => 'post_title',
												'slug'         => '',
												'desc'         => 'post_content',
												'barcode'      => '',
												'barcode2'     => '',
												'buyprice'     => '_regular_price',
												'price'        => '_price',
												'discount'     => '',
												'vat'          => '',
												'sku'          => 'sku',
												'infinite'     => '',
												'gallery'      => '',
												'weight'       => '',
												'weightunit'   => '',
												'seotitle'     => '',
												'type'         => 'post_type',
												'seodesc'      => '',
												'saleonline'   => '',
												'saletelegram' => '',
												'saleapp'      => '',
												'company'      => '',
												'scalecode'    => '',
												'status'       => 'onsale',
												'minsale'      => '',
												'maxsale'      => '',
												'salestep'     => '',
												'oversale'     => '',
												'unit'         => '',
												'category'     => '',
												'tag'          => ''
												];

	private $where_backup;
	private $this_jibres_wis;
	private $last_i = 0;
	
	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			// backup to csv file or jibres api
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ($this->this_jibres_wis == 'csv') ? 'products' : '/product/add';
			$this->create_pbr();
			$this->get_product_data();
		}
	}
	

	private function create_pbr()
	{

		$all = jibres_get_not_backuped('ID', 'posts', 'product', ['post_type'=>'product']);
		if ($all != '0') 
		{
			printf('<p>Backuping products...</p>');
			printf('<progress id="pprog" value="0" max="'.$all.'" style="height: 3px;"></progress>  <a id="inof"></a><br><br>');
			printf('<script>
					function prsb(meq) {
						document.getElementById("pprog").value = meq;
						document.getElementById("inof").innerHTML = meq + " of '.$all.' backuped";
					}
					</script>');
		}
	
	}


	// get procucts that are not backuped
	function get_product_data()
	{
		// test plugin power
		/*// global $wpdb;

		for ($i=0; $i < 200000; $i++) { 
			// $wpdb->insert( 
			// 	$wpdb->prefix. 'posts', 
			// 	['post_content'=>'test', 'post_title'=>'test', 'post_status'=>'publish', 'post_name'=>'test', 'post_type'=>'product']
			// );
			$msql = new mysqli('localhost', 'root', 'divet', 'new_wp');
			$sql = "INSERT INTO 
						`wp_posts`(`post_content`, `post_title`, `post_name`, `post_type`, `post_excerpt`, `to_ping`, `pinged`, `post_content_filtered`) 
					VALUES 
						('test','test','test','product',' ',' ',' ',' ')";
			$msql->query($sql);

			ob_flush();
			flush();
		}
		exit();*/


		$where = ['post_type'=>'product'];
		$excepts = 
		[
			'wc_product_meta_lookup'=> 'product_id',
			'postmeta'=> 'post_id'
		];
		$data = $this->get_data('ID', 'posts', 'product', $where, $excepts);
	
		if (!empty($data)) 
		{
			$i = $this->last_i;

			foreach ($data as $value) 
			{
				$i++;
				
				// sort array by jibres products database design
				$changed = $this->backup_arr_sort($value, self::$jibres_stantard_product_array, ["onsale"=>["1"=>'available', "0"=>'unavailable']]);
				
				// backup this product
				$get_data = jibres_wis($this->where_backup, $changed);
				
				// insert this product to jibres check table
				if ( is_array( $get_data ) and !empty( $get_data ) and $get_data['ok'] == true ) 
				{
					$this->insert_backup_in_jibres( [$value['ID'], 'product'], $get_data['result']['id'] );
				}
				else
				{
					$this->insert_backup_in_jibres( [$value['ID'], 'product'] );
				}
				
				// update progress bar
				printf('<script>
							prsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}

			$this->last_i = $i;
			$this->pb_start_again();
			/*if ($this->this_jibres_wis == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("OK Your Products Backuped<br><br>");*/
			
		}
		else
		{
			if ($this->this_jibres_wis == 'csv') 
			{
				// csv download url
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Products Are Backuped<br><br>");
		}
	
	}


	function pb_start_again()
	{
		$this->get_product_data();
	}

}

?>