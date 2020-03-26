<?php


/**
 * orders backup class
 */
class jibres_orders extends jibres_backup
{
	
	public static $jibres_stantard_order_array = [  'product' => '_product_id',
													'count'   => '_qty'
												 ];

	private $where_backup;
	private $this_jibres_wis;
	private $last_i = 0;

	function __construct()
	{
		if (create_jibres_table() === true) 
		{
			$this->this_jibres_wis = jibres_wis();
			$this->where_backup = ( $this->this_jibres_wis == 'csv' ) ? 'orders' : '/cart/add';
			$this->create_pbr();
			$this->get_order_data();
		}
	}


	private function create_pbr()
	{
		$all = jibres_get_not_backuped( 'order_item_id', 'woocommerce_order_items', 'order' );

		if ( $all != '0' ) 
		{
			printf('<br><p>Backing up orders...</p>');
			printf('<progress id="oprog" value="0" max="'.$all.'" style="height: 3px;"></progress>  <a id="oinof"></a><br><br>');
			printf('<script>
					function orsb(meq) {
						document.getElementById("oprog").value = meq;
						document.getElementById("oinof").innerHTML = meq + " of '.$all.' backed up";
					}
					</script>');
		}
	
	}

	
	function get_order_data()
	{
	

		$where = [];
		$excepts = 
		[
			'woocommerce_order_itemmeta'=> 'order_item_id'
		];
		$data = $this->get_data( 'order_item_id', 'woocommerce_order_items', 'order', $where, $excepts );

		if ( ! empty( $data ) ) 
		{
			$i = $this->last_i;
			
			foreach ( $data as $value ) 
			{
				
				$i++;
				
				
				// sort array by jibres orders database design
				$changed = $this->backup_arr_sort( $value, self::$jibres_stantard_order_array );
				
				// backup this order
				if ( $this->this_jibres_wis == 'api' ) 
				{
					$changed['product'] = $this->get_jibres_id( $value['_product_id'], 'product' );
				}
				$get_data = jibres_wis( $this->where_backup, $changed );

				// insert this order to jibres check table
				if ( is_array( $get_data ) and !empty( $get_data ) ) 
				{
					if ( $get_data['ok'] == true ) 
					{
						$this->insert_backup_in_jibres( [$value['order_item_id'], 'order'] );
					}
					else
					{
						$error = 'order code: ' . $value['order_item_id'] . ' > ' . json_encode( $get_data, JSON_UNESCAPED_UNICODE );
						jibres_error_log( 'order_backup', $error );
						
						printf('<div class="updated" style="border-left-color: #c0392b;"><br>' . 
						 		$get_data['msg'][0]['text']	. 
						 		'<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
						exit();
					}
				}
				elseif ( $get_data == true )
				{
					$this->insert_backup_in_jibres( [$value['order_item_id'], 'order'] );
				}
				
				// update progress bar
				printf('<script>
							orsb('.$i.');
						</script>');
				ob_flush();
				flush();
			}
	

			$this->last_i = $i;
			$this->orb_start_again();
		}
		else
		{
			printf('<br><a href="?page=jibres" class="jibres_notif_close">close</a>');
			printf("All Orders Are Backed up");
			if ( $this->this_jibres_wis == 'csv' ) 
			{
				// csv download url
				printf(' | <a href="'.get_site_url().'/wp-content/plugins/wp-jibres/backup/'.$this->where_backup.'.csv" target="_blank">Download csv file</a>');
			}
			printf('<br><br>');

		}
	
	}


	function orb_start_again()
	{
		$this->get_order_data();
	}

}

?>