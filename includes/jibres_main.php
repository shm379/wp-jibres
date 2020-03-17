<?php $this_wis = jibres_wis(); ?>

<?php if ( $this_wis == 'csv' ) : ?>

	<?php function csv_del( $items, $item ) { ?>
		<form onsubmit="return confirm('Do you really want to delete csv file of <?php echo $items; ?> backup?');" action method="post" style="display: inline;">
		<input type="hidden" name="csvdel" value="<?php echo $items; ?>_<?php echo $item; ?>">
		<input type="submit" class="dbt" value="Delete <?php echo $items; ?> csv file">
		</form>
	<?php } ?>

<?php else : ?>

	<?php function api_del() { ?>
		<form onsubmit="return confirm('Do you really want to delete your jibres api informations?');" action method="post" style="display: inline;">
		<input type="hidden" name="changit" value="start_again">
		<input type="submit" class="jbt" value="Change my jibres api informations">
		</form>
	<?php } ?>

<?php endif; ?>

<table class="widefat" cellspacing="0">
<thead>
	<tr>
		<th colspan="3" data-export-label="Reports"><h2 style="margin: 0;">Reports</h2></th>
	</tr>
</thead>
<tbody>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'product', ['post_type'=>'product'], true ); ?>
	<td style="font-size: 1.3em;"><?php echo $info_b['f']; ?></td>
	<td style="font-size: 1.3em;"><?php echo $info_b['s']; ?></td>
	<td><a href="?page=jibres&jibres=products_backup"><button class="button" style="vertical-align: unset;">Backup Your Products</button></a></td>
	<?php if ( function_exists('csv_del') ) : ?>
		<td><?php csv_del( 'products', 'product' ); ?></td>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'order_item_id', 'woocommerce_order_items', 'order' ); ?>
	<td style="font-size: 1.3em;"><?php echo $info_b['f']; ?></td>
	<td style="font-size: 1.3em;"><?php echo $info_b['s']; ?></td>
	<td><a href="?page=jibres&jibres=orders_backup"><button class="button" style="vertical-align: unset;">Backup Your Orders</button></a></td>
	<?php if ( function_exists('csv_del') ) : ?>
		<td><?php csv_del( 'orders', 'order' ); ?></td>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'post', ['post_type'=>'post'] ); ?>
	<td style="font-size: 1.3em;"><?php echo $info_b['f']; ?></td>
	<td style="font-size: 1.3em;"><?php echo $info_b['s']; ?></td>
	<td><a href="?page=jibres&jibres=posts_backup"><button class="button" style="vertical-align: unset;">Backup Your Posts</button></a></td>
	<?php if ( function_exists('csv_del') ) : ?>
		<td><?php csv_del( 'posts', 'post' ); ?></td>
	<?php endif; ?>
</tr>
<?php 
	if ( $this_wis != 'csv' ) 
	{
		global $wpdb;
		$table = $wpdb->prefix. 'posts';
		$cwhere = "comment_post_ID IN (SELECT ID FROM $table WHERE post_type='product')";
	}
	else
	{
		$cwhere = [];
	}
?>
<tr>
	<?php $info_b = jibres_informations_b( 'comment_ID', 'comments', 'comment', $cwhere ); ?>
	<td style="font-size: 1.3em;"><?php echo $info_b['f']; ?></td>
	<td style="font-size: 1.3em;"><?php echo $info_b['s']; ?></td>
	<td><a href="?page=jibres&jibres=comments_backup"><button class="button" style="vertical-align: unset;">Backup Your Comments</button></a></td>
	<?php if ( function_exists('csv_del') ) : ?>
		<td><?php csv_del( 'comments', 'comment' ); ?></td>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat'] ); ?>
	<td style="font-size: 1.3em;"><?php echo $info_b['f']; ?></td>
	<td style="font-size: 1.3em;"><?php echo $info_b['s']; ?></td>
	<td><a href="?page=jibres&jibres=categories_backup"><button class="button" style="vertical-align: unset;">Backup Your Categories</button></a></td>
	<?php if ( function_exists('csv_del') ) : ?>
		<td><?php csv_del( 'categories', 'category' ); ?></td>
	<?php endif; ?>
</tr>
</tbody>
</table>
<br><br>
<a href="?page=jibres&jibres=backup_all"><button class="button">Backup All Data</button></a>
<?php if ( function_exists('api_del') ) { api_del(); } ?>



