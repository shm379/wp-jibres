<?php
/**
 * @package           jibres
 * @author            jibres.com
 * @copyright         2020 Jibres
 * @license           GPL-2.0-or-later
 *
 * @wordpress-plugin
 * Plugin Name:       jibres
 * Plugin URI:        https://jibres.com
 * Description:       Backup of your data on jibres.
 */


add_action( 'admin_menu', 'menus' );
function menus() {
    add_menu_page(
        'JIBRES',
        'Jibres',
        'manage_options',
        'jibres',
        'jibres',
        plugin_dir_url(__FILE__) . 'admin/images/Jibres-Logo-icon-black-32.png',
        30
    );
}

function not()
{
    if ($_GET['page'] == 'jibres') 
    {
        echo "<h1>Jibres</h1>";
    }
}
add_action( 'admin_notices', 'not' );


function jib_css() {
	echo "
	<style type='text/css'>
	.jibres {

	}
	</style>
	";
}

add_action( 'admin_head', 'jib_css' );



function send_product($data)
{
    $store = '';
    $apikey = '';
    $appkey = '';
    $ch = curl_init();

    $headers =  array('Content-Type: application/json', 'appkey: '.$appkey, 'apikey: '.$apikey);
    
    curl_setopt($ch, CURLOPT_URL, "https://jibres.com/fa/api/v1/".$store."/product/add");
    curl_setopt($ch, CURLOPT_HEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    $get = curl_exec($ch);
    
    curl_close($ch);
}



function arr_sort($arr)
{
    $ch = array('title' => 'post_title',
                'slug' => '',
                'desc' => 'post_content',
                'barcode' => '',
                'barcode2' => '',
                'buyprice' => 'max_price',
                'price' => 'min_price',
                'discount' => '',
                'vat' => '',
                'sku' => 'sku',
                'infinite' => '',
                'gallery' => '',
                'weight' => '',
                'weightunit' => '',
                'seotitle' => '',
                'type' => 'post_type',
                'seodesc' => '',
                'saleonline' => '',
                'saletelegram' => '',
                'saleapp' => '',
                'company' => '',
                'scalecode' => '',
                'status' => '',
                'minsale' => '',
                'maxsale' => '',
                'salestep' => '',
                'oversale' => '',
                'unit' => '',
                'category' => '',
                'tag' => ''
                );
    foreach ($ch as $key => $value) {
        if ($key == 'status' and $arr["onsale"] == "1") 
        {
            $ch[$key] = 'available';
        }
        else
        {
            $ch[$key] = $arr[$value];
        }
    }
    send_product($ch);
}



function admin_jib() 
{
    global $wpdb;
    
	if ($_GET['page'] == 'jibres') 
    {
    	printf('<div class="jibres">');
    	if ($_GET['jibres'] == 'backup') 
    	{
	    	$results = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_type = 'product'"));
            $arr_results = array();
	    	foreach ($results as $key => $value) {
	    		foreach ($value as $key => $val) {
                    if ($key == "ID") 
                    {
                        $results2 = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->prefix}wc_product_meta_lookup WHERE product_id = $val"));
                        foreach ($results2 as $key2 => $value2) {
                            foreach ($value2 as $key3 => $val2) {
                                $arr_results[$key3] = $val2;
                            }
                        }
                    }
                    $arr_results[$key] = $val;
	    		}
                arr_sort($arr_results);
	    		printf("<br><br>");
	    	}
	    	printf('<a href="?page=jibres"><button>back</button></a>');
    	}
    	else
    	{
    		 printf('<a href="?page=jibres&jibres=backup"><button>Backup</button></a>');
    	}
    	printf('</div>');
    	
	    
    }
}



add_action( 'admin_notices', 'admin_jib' );
?>
