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

<?php function jibres_main_td( $j_info, $cat, $cats ) { ?>
	<?php $this_wis = jibres_wis(); ?>
	<td><?php echo $j_info['cat']; ?></td>
	<td><?php echo $j_info['all']; ?></td>
	<td><?php echo $j_info['status']; ?></td>
	<td><?php echo ( ! empty( $j_info['datetime'] ) ) ? $j_info['datetime'] : '-'; ?></td>
	<td><a href="?page=jibres&jibres=<?php echo $cats; ?>_backup"><button class="button" style="vertical-align: unset;" <?php echo ( $j_info['all'] != '0' ) ? null : 'disabled'; ?>>Backup</button></a></td>
	<?php if ( $this_wis == 'csv' ) : ?>
		<td><form action="?page=jibres" method="post" style="display: inline;">
		<input type="hidden" name="mail_backup" value="<?php echo $cats; ?>">
		<input type="submit" value="Mail" class="button" style="vertical-align: unset;" <?php echo ( $j_info['not_becked_up'] != $j_info['all'] ) ? null : 'disabled'; ?>>
		</form></td>
		<td><?php csv_del( $cats, $cat, ( $j_info['not_becked_up'] != $j_info['all'] ) ? null : 'disabled' ); ?></td>
	<?php endif; ?>
<?php } ?>

<table class="jibreswt wp-list-table widefat fixed striped" cellspacing="0">
<thead>
	<tr>
		<th id="type" class="manage-column"><a><span>Type</span></a></th>
		<th id="count" class="manage-column"><a><span>Count</span></a></th>
		<th id="status" class="manage-column"><a><span>Status</span></a></th>
		<th id="status" class="manage-column"><a><span>Time</span></a></th>
		<th id="backup" class="manage-column"><a><span>Backup</span></a></th>
		<?php if ( $this_wis == 'csv' ) : ?>
		<th id="mail" class="manage-column"><a><span>Mail</span></a></th>
		<th id="delete" class="manage-column"><a><span>Delete</span></a></th>
		<?php endif; ?>
	</tr>
</thead>
<tbody>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'product', ['post_type'=>'product'] ); ?>
	<?php jibres_main_td( $info_b, 'product', 'products' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'order_item_id', 'woocommerce_order_items', 'order' ); ?>
	<?php jibres_main_td( $info_b, 'order', 'orders' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'post', ['post_type'=>'post'] ); ?>
	<?php jibres_main_td( $info_b, 'post', 'posts' ); ?>
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
	<?php jibres_main_td( $info_b, 'comment', 'comments' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat'] ); ?>
	<?php jibres_main_td( $info_b, 'category', 'categories' ); ?>
</tr>
</tbody>
<tfoot>
	<tr>
		<th id="type" class="manage-column"><a><span>Type</span></a></th>
		<th id="count" class="manage-column"><a><span>Count</span></a></th>
		<th id="status" class="manage-column"><a><span>Status</span></a></th>
		<th id="status" class="manage-column"><a><span>Time</span></a></th>
		<th id="backup" class="manage-column"><a><span>Backup</span></a></th>
		<?php if ( $this_wis == 'csv' ) : ?>
		<th id="mail" class="manage-column"><a><span>Mail</span></a></th>
		<th id="delete" class="manage-column"><a><span>Delete</span></a></th>
		<?php endif; ?>
	</tr>
</tfoot>
</table>
<br><br>
<a href="?page=jibres&jibres=backup_all"><button class="button">Backup All Data</button></a>
<?php if ( function_exists('api_del') ) { api_del(); } ?>
<a style="float: right;" href="<?php echo get_site_url().'/wp-content/plugins/wp-jibres/error_log.txt'; ?>" target="_blank">error log</a>



