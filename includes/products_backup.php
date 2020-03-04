<?php
require_once JIBRES_INC. 'jibres_backup_class.php';
/**
 * products backup class
 */
class jibres_products extends jibres_backup
{

	public $jibres_stantard_product_array = [   'title'        => 'post_title',
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
	
	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			// backup to csv file or jibres api
			$this->where_backup = (jibres_wis() == 'csv') ? 'products' : '/product/add';
			$this->get_product_data();
		}
	}
	
	// get procucts that are not backuped
	function get_product_data()
	{

		$where = ['post_type'=>'product'];
		$excepts = 
		[
			'wc_product_meta_lookup'=> 'product_id',
			'postmeta'=> 'post_id'
		];
		$data = $this->get_data('ID', 'posts', 'product', $where, $excepts);

	
		printf('<p>Backuping products...</p>');
		printf('<progress id="pprog" value="0" max="'.count($data).'" style="height: 3px;"></progress>  <a id="inof"></a><br><br>');
		printf('<script>
				function prsb(meq) {
					document.getElementById("pprog").value = meq;
					document.getElementById("inof").innerHTML = meq + " of '.count($data).' backuped";
				}
				</script>');

		foreach ($data as $value) 
		{
			
			$this->insert_backup_in_jibres([$value['ID'], 'product']);

			$this->backup_arr_sort($value, $this->jibres_stantard_product_array, ["onsale"=>["1"=>'available', "0"=>'unavailable']]);
			
			$changed = jibres_sort_arr($this->jibres_stantard_product_array, $arr);
		
			jibres_wis($this->where_backup, $changed);

			printf('<script>
						prsb('.$i.');
					</script>');
			$this->product_arr_sort($arr_results);
			ob_flush();
			flush();
		}

			
		
		
		
		if (jibres_wis() == 'csv') 
		{
			printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
		}
		printf("OK Your Products Backuped<br><br>");
			
		else
		{
			if (jibres_wis() == 'csv') 
			{
				printf('<a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a><br><br>');
			}
			printf("All Products Are Backuped<br><br>");
		}
	
	}

}

?>