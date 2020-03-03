<?php 
/**
 * @author Jibres
 */
class jibres_posts
{

	private $data = [];
	
	function __construct($entry)
	{
		$this->data = $entry;

		if (is_array($entry)) 
		{
			foreach ($entry as $key => $value) 
			{
				if (method_exists($this, $key)) 
				{
					$this->$key();
				}
			}
		}	
	}


	private function usas()
	{
		global $wpdb;
		if (isset($this->data['usas']) and $this->data['usas'] == 'api' and ch_jibres_store_data() == false) 
		{
			ch_jibres_store_data('start_again');
			header("Refresh:0");
		}
		else
		{
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'wis' => $this->data['usas'] ),
							array( 'id' => 1 )
						);
			header("Refresh:0");
		}
	}


	private function weris()
	{
		if (!empty($this->data['store']) and !empty($this->data['apikey']) and !empty($this->data['appkey'])) 
		{
			$data_posted = array('store' => $this->data['store'], 'apikey' => $this->data['apikey'], 'appkey' => $this->data['appkey'], 'wis' => $this->data['weris']);
		}
		else
		{
			$data_posted = array('wis' => 'csv');
		}
		insert_in_jibres($data_posted, JIBRES_TABLE);
		header("Refresh:0");
	}


	private function changit()
	{
		ch_jibres_store_data($this->data['changit']);
		header("Refresh:0");
	}


	private function csvdel()
	{
		global $wpdb;
		$del_data = explode("_", $this->data['csvdel']);
		$wpdb->delete( JIBRES_CTABLE, array( 'type' => $del_data[1], 'wers' => 'csv' ) );
		unlink(JIBRES_DIR. 'backup/'. $del_data[0]. '.csv');
	}
}


$jibres_answer_post = new jibres_posts($_POST);


?>