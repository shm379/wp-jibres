<?php 
if ( ! empty( $_GET ) ) 
{
	unset($_GET);	
}
/**
 * @author Jibres
 */
class jibres_posts
{

	public static $data;
	

	public static function usas()
	{
		global $wpdb;
		if ( isset(self::$data['usas']) and self::$data['usas'] == 'api' and ch_jibres_store_data() == false ) 
		{
			ch_jibres_store_data( 'start_again' );
			header( "Refresh:0" );
		}
		else
		{
			$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'wis' => self::$data['usas'] ),
							array( 'id' => 1 )
						);
			header( "Refresh:0" );
		}
	}


	public static function weris()
	{
		if ( ! empty( self::$data['store'] ) and ! empty( self::$data['appkey'] ) and ! empty( self::$data['phone'] ) ) 
		{
			if ( strlen( self::$data['store'] ) == 6 and strlen( self::$data['appkey'] ) == 32 and strlen( self::$data['phone'] ) == 12 and is_numeric( self::$data['phone'] ) ) 
			{
				$data_posted = ['store' => self::$data['store'], 'appkey' => self::$data['appkey'], 'phone_number' => self::$data['phone'], 'wis' => self::$data['weris']];
				require_once JIBRES_INC. 'jibres_api.php';
				Jibres::jibres_login( $data_posted );
			}
			else
			{
				printf('<div class="updated" style="border-left-color: #c0392b;"><br>Error for input data!<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
			}
		}
		elseif ( ! empty( self::$data['weris'] ) )
		{
			$data_posted = array('wis' => 'csv');
			insert_in_jibres( $data_posted, JIBRES_TABLE );
			header( "Refresh:0" );
			exit();
		}
		
	}

	public static function jibresverifycode()
	{
		require_once JIBRES_INC. 'jibres_api.php';
		Jibres::jibres_verify( self::$data['jibresverifycode'] );
	}


	public static function changit()
	{
		ch_jibres_store_data( self::$data['changit'] );
		header( "Refresh:0" );
		exit();
	}


	public static function csvdel()
	{
		global $wpdb;
		$del_data = explode("_", self::$data['csvdel']);
		$wpdb->delete( JIBRES_CTABLE, array( 'type' => $del_data[1], 'wers' => 'csv' ) );
		unlink(JIBRES_DIR. 'backup/'. $del_data[0]. '.csv');
		printf('<div class="updated"><br>' . $del_data[0] . ' csv file deleted<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
	}


	public static function mail_backup()
	{
		$mail_send = jibres_mail_backup( self::$data['mail_backup'] );
		if ( $mail_send == true ) 
		{
			printf('<div class="updated"><br>' . self::$data['mail_backup'] . ' csv file was sended successfully<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
		}
		else
		{
			printf('<div class="updated" style="border-left-color: #c0392b;"><br>You have an error will send mail<a href="?page=jibres" class="jibres_notif_close">close</a><br><br></div>');
		}
	}


	public static function change_auto_mail()
	{
		global $wpdb;

		$a_mail = ( self::$data['change_auto_mail'] == 'add' ) ? '1' : '0';
		$wpdb->update(
							$wpdb->prefix . 'jibres',
							array( 'a_mail' => $a_mail ),
							array( 'id' => 1 )
						);
	}
}



jibres_posts::$data = $_POST;

foreach ( $_POST as $key => $value ) 
{
	if (method_exists( 'jibres_posts', $key ) ) 
	{
		jibres_posts::$key();
	}
}


?>