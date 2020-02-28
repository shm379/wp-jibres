<?php 

printf('<br><br>');
printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br><hr><br>');
printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  ');
printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  ');
printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  ');
printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  ');
printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a><br><br><hr><br>');


function print_infos($jwb)
{
	informations_b('ID', 'posts', 'product', $jwb, ['post_type'=>'product'], true);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'order', $jwb, ['post_type'=>'shop_order']);
	printf('<br><br>');
	
	informations_b('ID', 'posts', 'post', $jwb, ['post_type'=>'post']);
	printf('<br><br>');
	
	informations_b('comment_ID', 'comments', 'comment', $jwb);
	printf('<br><br>');
	
	informations_b('term_id', 'term_taxonomy', 'category', $jwb, ['taxonomy'=>'product_cat']);
}



printf('<br><br>');

printf('<div class="infos">');
print_infos('csv');
printf('</div>');


if (ch_jibres_store_data() == true) 
{
	printf('<br><br>');
	
	printf('<div class="infos" style="float: right;">');
	print_infos('api');
	printf('</div>');
}

?>