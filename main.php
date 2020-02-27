<?php 

printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br><hr><br>');
printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  ');
printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  ');
printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  ');
printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  ');
printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a><br><br><hr><br>');

$cnt_product = $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'product'");
$cnt_product_b = $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'product' AND ID NOT IN (
									SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'product' AND backuped = 1)");
informations_b($cnt_product, $cnt_product_b, 'product', true);
printf('<br><br>');

$cnt_orders =  $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'shop_order'");
$cnt_orders_b = $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'shop_order' AND ID NOT IN 
								(SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'order' AND backuped = 1)");
informations_b($cnt_orders, $cnt_orders_b, 'order');
printf('<br><br>');

$cnt_posts = $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'post'");
$cnt_posts_b = $wpdb->get_results("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'post' AND ID NOT IN 
								(SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'post' AND backuped = 1)");
informations_b($cnt_posts, $cnt_posts_b, 'post');
printf('<br><br>');


$cnt_comments = $wpdb->get_results("SELECT COUNT(comment_ID) FROM $wpdb->comments");
$cnt_comments_b = $wpdb->get_results("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_ID NOT IN 
								(SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'comment' AND backuped = 1)");
informations_b($cnt_comments, $cnt_comments_b, 'comment');
printf('<br><br>');


$cnt_comments = $wpdb->get_results("SELECT COUNT(term_id) FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat'");
$cnt_comments_b = $wpdb->get_results("SELECT COUNT(term_id) FROM $wpdb->term_taxonomy WHERE taxonomy = 'product_cat' AND term_id NOT IN 
								(SELECT item_id FROM {$wpdb->prefix}jibres_check WHERE type = 'cat' AND backuped = 1)");
informations_b($cnt_comments, $cnt_comments_b, 'category');

?>