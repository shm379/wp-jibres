<?php 

printf('<br><br>');
printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br><hr><br>');
printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  ');
printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  ');
printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  ');
printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  ');
printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a><br><br><hr><br>');


function jibres_print_infos()
{
	printf('<div class="infos">');
	informations_b('ID', 'posts', 'product', ['post_type'=>'product'], true);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'order', ['post_type'=>'shop_order']);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'post', ['post_type'=>'post']);
	printf('<br><br>');
	
	informations_b('comment_ID', 'comments', 'comment');
	printf('<br><br>');
	
	informations_b('term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat']);
	printf('</div>');
}

function jibres_csv_file_del($fname, $dname, $last = false)
{
	$last = ($last == false) ? '  |  ' : null;
	printf('<form onsubmit="return confirm(\'Do you really want to delete csv file of '.$fname.' backup?\');" action method="post" style="display: inline;">
			<input type="hidden" name="csvdel" value="'.$fname.'_'.$dname.'">
			<input type="submit" class="dbt" value="Delete '.$fname.' csv file">
			</form>'. $last);
}


if (jibres_wis() == 'csv') 
{
	jibres_csv_file_del('products', 'product');
	jibres_csv_file_del('orders', 'order');
	jibres_csv_file_del('posts', 'post');
	jibres_csv_file_del('comments', 'comment');
	jibres_csv_file_del('categories', 'category', true);
	printf('<br><br><hr><br><br>');
	jibres_print_infos();
}
else
{
	printf('<br><br>');
	jibres_print_infos();
	printf('<br><br>');
	printf('<form onsubmit="return confirm(\'Do you really want to delete your jibres api informations?\');" action method="post">
			<input type="hidden" name="changit" value="start_again">
			<input type="submit" class="jbt" value="Change my jibres api informations">
			</form>');
}


?>