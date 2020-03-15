
<br><br>
<a href="?page=jibres&jibres=backup_all"><button class="bt">Backup All Data</button></a>
<br><br><hr><br>
<a href="?page=jibres&jibres=products_backup"><button class="bt">Backup Your Products</button></a>  |  
<a href="?page=jibres&jibres=orders_backup"><button class="bt">Backup Your Orders</button></a>  |  
<a href="?page=jibres&jibres=posts_backup"><button class="bt">Backup Your Posts</button></a>  |  
<a href="?page=jibres&jibres=comments_backup"><button class="bt">Backup Your Comments</button></a>  |  
<a href="?page=jibres&jibres=categories_backup"><button class="bt">Backup Your Categories</button></a>
<br><br><hr><br>


<?php if ( jibres_wis() == 'csv' ) : ?>
	<?php
		$jibres_csv_file_del = ['products'=>'product',
								'orders'=>'order',
								'posts'=>'post',
								'comments'=>'comment',
								'categories'=>'category'];
	?>
	
	
	<?php $i = 0; ?>
	<?php foreach ($jibres_csv_file_del as $key => $value) : ?>
	
		<?php $i++; ?>
		<form onsubmit="return confirm('Do you really want to delete csv file of <?php echo $key; ?> backup?');" action method="post" style="display: inline;">
		<input type="hidden" name="csvdel" value="<?php echo $key; ?>_<?php echo $value; ?>">
		<input type="submit" class="dbt" value="Delete <?php echo $key; ?> csv file">
		</form>
		<?php if ( $i < count($jibres_csv_file_del) ) : ?> | <?php endif; ?>
	
	<?php endforeach; ?>

<?php else : ?>

	<form onsubmit="return confirm(\'Do you really want to delete your jibres api informations?\');" action method="post" style="display: inline;">
	<input type="hidden" name="changit" value="start_again">
	<input type="submit" class="jbt" value="Change my jibres api informations">
	</form>

<?php endif; ?>


<br><br><hr><br><br>
<div class="infos">

<?php jibres_informations_b('ID', 'posts', 'product', ['post_type'=>'product'], true); ?>
<br><br>

<?php jibres_informations_b('ID', 'posts', 'order', ['post_type'=>'shop_order']); ?>
<br><br>

<?php jibres_informations_b('ID', 'posts', 'post', ['post_type'=>'post']); ?>
<br><br>

<?php jibres_informations_b('comment_ID', 'comments', 'comment'); ?>
<br><br>

<?php jibres_informations_b('term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat']); ?>

</div>


