<?php 

if ($_POST['weris']) 
{
	if ($_POST['store'] and !empty($_POST['store']) and !empty($_POST['apikey']) and !empty($_POST['appkey'])) 
	{
		$data_posted = array('store' => $_POST['store'], 'apikey' => $_POST['apikey'], 'appkey' => $_POST['appkey'], 'wis' => $_POST['weris']);
	}
	else
	{
		$data_posted = array('wis' => 'csv');
	}
	insert_in_jibres($data_posted, 'jibres');
	header("Refresh:0");
}

if ($_POST['usas']) 
{
		
	if ($_POST['usas'] == 'api' and ch_jibres_store_data() == false) 
	{
		ch_jibres_store_data('start_again');
		header("Refresh:0");
	}
	else
	{
		$wpdb->update(
						$wpdb->prefix . 'jibres',
						array( 'wis' => $_POST['usas'] ),
						array( 'id' => 1 )
					);
		header("Refresh:0");
	}
}


if ($_POST['changit']) 
{
	ch_jibres_store_data($_POST['changit']);
	header("Refresh:0");
}

?>