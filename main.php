<?php 

printf('<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a><br><br><hr><br>');
printf('<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  ');
printf('<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  ');
printf('<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  ');
printf('<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  ');
printf('<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a><br><br><hr><br>');


informations_b('ID', 'posts', 'product', ['post_type'=>'product'], true);
printf('<br><br>');

informations_b('ID', 'posts', 'order', ['post_type'=>'shop_order']);
printf('<br><br>');

informations_b('ID', 'posts', 'post', ['post_type'=>'post']);
printf('<br><br>');

informations_b('comment_ID', 'comments', 'comment');
printf('<br><br>');

informations_b('term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat']);

?>