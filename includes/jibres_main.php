<?php $this_wis = jibres_wis(); ?>

<?php if ( $this_wis == 'csv' ) : ?>

	<?php function csv_del( $items, $item, $dis = null ) { ?>
		<form onsubmit="return confirm('Do you really want to delete csv file of <?php echo $items; ?> backup?');" action="?page=jibres" method="post" style="display: inline;">
		<input type="hidden" name="csvdel" value="<?php echo $items; ?>_<?php echo $item; ?>">
		<input type="submit" class="dbt" value="Delete" <?php echo $dis ?>>
		</form>
	<?php } ?>

<?php else : ?>

	<?php function api_del() { ?>
		<form onsubmit="return confirm('Do you really want to delete your jibres api informations?');" action="?page=jibres" method="post" style="display: inline;">
		<input type="hidden" name="changit" value="start_again">
		<input type="submit" class="jbt" value="Change my jibres api informations">
		</form>
	<?php } ?>

<?php endif; ?>

<table class="jibreswt wp-list-table widefat fixed striped" cellspacing="0">
<thead>
	<tr>
		<th id="type" class="manage-column"><a><span>Type</span></a></th>
		<th id="count" class="manage-column"><a><span>Count</span></a></th>
		<th id="status" class="manage-column"><a><span>Status</span></a></th>
		<th id="backup" class="manage-column"><a><span>Backup</span></a></th>
		<?php if ( function_exists('csv_del') ) : ?>
		<th id="delete" class="manage-column"><a><span>Delete</span></a></th>
		<?php endif; ?>
	</tr>
</thead>
<tbody>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'product', ['post_type'=>'product'] ); ?>
	<td><?php echo $info_b['f']; ?></td>
	<td><?php echo $info_b['a']; ?></td>
	<td><?php echo $info_b['s']; ?></td>
	<?php if ( $info_b['a'] != 0 ) : ?>
		<td><a href="?page=jibres&jibres=products_backup"><button class="button" style="vertical-align: unset;">Backup</button></a></td>
	<?php else : ?>
		<td><a href="?page=jibres&jibres=products_backup"><button class="button" style="vertical-align: unset;" disabled>Backup</button></a></td>
	<?php endif; ?>
	<?php if ( function_exists('csv_del') ) : ?>
		<?php if ( $info_b['n'] != $info_b['a'] ) : ?>
			<td><?php csv_del( 'products', 'product' ); ?></td>
		<?php else : ?>
			<td><?php csv_del( 'products', 'product', 'disabled' ); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'order_item_id', 'woocommerce_order_items', 'order' ); ?>
	<td><?php echo $info_b['f']; ?></td>
	<td><?php echo $info_b['a']; ?></td>
	<td><?php echo $info_b['s']; ?></td>
	<?php if ( $info_b['a'] != 0 ) : ?>
		<td><a href="?page=jibres&jibres=orders_backup"><button class="button" style="vertical-align: unset;">Backup</button></a></td>
	<?php else : ?>
		<td><a href="?page=jibres&jibres=orders_backup"><button class="button" style="vertical-align: unset;" disabled>Backup</button></a></td>
	<?php endif; ?>
	<?php if ( function_exists('csv_del') ) : ?>
		<?php if ( $info_b['n'] != $info_b['a'] ) : ?>
			<td><?php csv_del( 'orders', 'order' ); ?></td>
		<?php else : ?>
			<td><?php csv_del( 'orders', 'order', 'disabled' ); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'post', ['post_type'=>'post'] ); ?>
	<td><?php echo $info_b['f']; ?></td>
	<td><?php echo $info_b['a']; ?></td>
	<td><?php echo $info_b['s']; ?></td>
	<?php if ( $info_b['a'] != 0 ) : ?>
		<td><a href="?page=jibres&jibres=posts_backup"><button class="button" style="vertical-align: unset;">Backup</button></a></td>
	<?php else : ?>
		<td><a href="?page=jibres&jibres=posts_backup"><button class="button" style="vertical-align: unset;" disabled>Backup</button></a></td>
	<?php endif; ?>
	<?php if ( function_exists('csv_del') ) : ?>
		<?php if ( $info_b['n'] != $info_b['a'] ) : ?>
			<td><?php csv_del( 'posts', 'post' ); ?></td>
		<?php else : ?>
			<td><?php csv_del( 'posts', 'post', 'disabled' ); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>
<tr>
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
	<?php $info_b = jibres_informations_b( 'comment_ID', 'comments', 'comment', $cwhere ); ?>
	<td><?php echo $info_b['f']; ?></td>
	<td><?php echo $info_b['a']; ?></td>
	<td><?php echo $info_b['s']; ?></td>
	<?php if ( $info_b['a'] != 0 ) : ?>
		<td><a href="?page=jibres&jibres=comments_backup"><button class="button" style="vertical-align: unset;">Backup</button></a></td>
	<?php else : ?>
		<td><a href="?page=jibres&jibres=comments_backup"><button class="button" style="vertical-align: unset;" disabled>Backup</button></a></td>
	<?php endif; ?>
	<?php if ( function_exists('csv_del') ) : ?>
		<?php if ( $info_b['n'] != $info_b['a'] ) : ?>
			<td><?php csv_del( 'comments', 'comment' ); ?></td>
		<?php else : ?>
			<td><?php csv_del( 'comments', 'comment', 'disabled' ); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat'] ); ?>
	<td><?php echo $info_b['f']; ?></td>
	<td><?php echo $info_b['a']; ?></td>
	<td><?php echo $info_b['s']; ?></td>
	<?php if ( $info_b['a'] != 0 ) : ?>
		<td><a href="?page=jibres&jibres=categories_backup"><button class="button" style="vertical-align: unset;">Backup</button></a></td>
	<?php else : ?>
		<td><a href="?page=jibres&jibres=categories_backup"><button class="button" style="vertical-align: unset;" disabled>Backup</button></a></td>
	<?php endif; ?>
	<?php if ( function_exists('csv_del') ) : ?>
		<?php if ( $info_b['n'] != $info_b['a'] ) : ?>
			<td><?php csv_del( 'categories', 'category' ); ?></td>
		<?php else : ?>
			<td><?php csv_del( 'categories', 'category', 'disabled' ); ?></td>
		<?php endif; ?>
	<?php endif; ?>
</tr>
</tbody>
<tfoot>
	<tr>
		<th id="type" class="manage-column"><a><span>Type</span></a></th>
		<th id="count" class="manage-column"><a><span>Count</span></a></th>
		<th id="status" class="manage-column"><a><span>Status</span></a></th>
		<th id="backup" class="manage-column"><a><span>Backup</span></a></th>
		<?php if ( function_exists('csv_del') ) : ?>
		<th id="delete" class="manage-column"><a><span>Delete</span></a></th>
		<?php endif; ?>
	</tr>
</tfoot>
</table>
<br><br>
<a href="?page=jibres&jibres=backup_all"><button class="button">Backup All Data</button></a>
<?php if ( function_exists('api_del') ) { api_del(); } ?>



