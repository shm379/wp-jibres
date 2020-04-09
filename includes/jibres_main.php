<?php global $wpdb; ?>
<?php $this_wis = jibres_wis(); ?>


<?php function jibres_main_th() { ?>
	<?php $this_wis = jibres_wis(); ?>
	<th id="type" class="manage-column"><a><span>Type</span></a></th>
		<th id="count" class="manage-column"><a><span>Count</span></a></th>
		<th id="status" class="manage-column"><a><span>Status</span></a></th>
		<th id="status" class="manage-column"><a><span>Time</span></a></th>
		<th id="backup" class="manage-column"><a><span>Backup</span></a></th>
		<?php if ( $this_wis == 'csv' ) : ?>
			<th id="mail" class="manage-column"><a><span>Mail</span></a></th>
			<th id="delete" class="manage-column"><a><span>Delete</span></a></th>
		<?php endif; ?>
<?php } ?>


<?php function jibres_main_td( $j_info, $cat, $cats ) { ?>
	<?php $this_wis = jibres_wis(); ?>
	<td><?php echo $j_info['cat']; ?></td>
	<td><?php echo $j_info['all']; ?></td>
	<td><?php echo $j_info['status']; ?></td>
	<td><?php echo ( ! empty( $j_info['datetime'] ) ) ? $j_info['datetime'] : '-'; ?></td>
	<td><a href="?page=jibres&jibres=<?php echo $cats; ?>_backup"><button class="button" style="vertical-align: unset;" <?php echo ( $j_info['all'] != '0' ) ? null : 'disabled'; ?>>Backup</button></a></td>
	<?php if ( $this_wis == 'csv' ) : ?>
		<td>
			<form action="?page=jibres" method="post" style="display: inline;">
			<input type="hidden" name="mail_backup" value="<?php echo $cats; ?>">
			<input type="submit" value="Mail" class="button" style="vertical-align: unset;" <?php echo ( $j_info['not_becked_up'] != $j_info['all'] ) ? null : 'disabled'; ?>>
			</form>
		</td>
		<td>
			<form onsubmit="return confirm('Do you really want to delete csv file of <?php echo $cats; ?> backup?');" action="?page=jibres" method="post" style="display: inline;">
			<input type="hidden" name="csvdel" value="<?php echo $cats; ?>_<?php echo $cat; ?>">
			<input type="submit" class="dbt" value="Delete" <?php echo ( $j_info['not_becked_up'] != $j_info['all'] ) ? null : 'disabled'; ?>>
			</form>
		</td>
	<?php endif; ?>
<?php } ?>

<table class="jibreswt wp-list-table widefat fixed striped" cellspacing="0">
<thead>
	<tr>
		<?php jibres_main_th(); ?>
	</tr>
</thead>
<tbody>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'product', ['post_type'=>'product'] ); jibres_main_td( $info_b, 'product', 'products' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'order_item_id', 'woocommerce_order_items', 'order' ); jibres_main_td( $info_b, 'order', 'orders' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'ID', 'posts', 'post', ['post_type'=>'post'] ); jibres_main_td( $info_b, 'post', 'posts' ); ?>
</tr>
<tr>
	<?php $cwhere = ( $this_wis != 'csv' ) ? "comment_post_ID IN (SELECT ID FROM {$wpdb->prefix}posts WHERE post_type='product')" : []; ?>
	<?php $info_b = jibres_informations_b( 'comment_ID', 'comments', 'comment', $cwhere ); jibres_main_td( $info_b, 'comment', 'comments' ); ?>
</tr>
<tr>
	<?php $info_b = jibres_informations_b( 'term_id', 'term_taxonomy', 'category', ['taxonomy'=>'product_cat'] ); jibres_main_td( $info_b, 'category', 'categories' ); ?>
</tr>
</tbody>
<tfoot>
	<tr>
		<?php jibres_main_th(); ?>
	</tr>
</tfoot>
</table>
<br><br>
<a href="?page=jibres&jibres=backup_all"><button class="button">Backup All Data</button></a>
<?php if ( $this_wis == 'api' ) : ?>
	<form onsubmit="return confirm('Do you really want to delete your jibres api informations?');" action="?page=jibres" method="post" style="display: inline;">
	<input type="hidden" name="changit" value="start_again">
	<input type="submit" class="jbt" value="Change my jibres api informations">
	</form>
<?php endif; ?>
<?php if ( $this_wis == 'csv' ) : ?>
	<form action="?page=jibres" method="post" style="display: inline;">
	<?php $check_auto_mail = jibres_auto_mail(); ?>
	<?php if ( $check_auto_mail == true ) : ?>
		<input type="hidden" name="change_auto_mail" value="del">
		<input type="submit" class="bt" value="I dont want to send files as mail auto">
	<?php else : ?>
		<input type="hidden" name="change_auto_mail" value="add">
		<input type="submit" class="bt" value="I want to send files as mail auto">
	<?php endif; ?>
	</form>
<?php endif; ?>
<a style="float: right;" href="<?php echo get_site_url().'/wp-content/plugins/wp-jibres/error_log.txt'; ?>" target="_blank">error log</a>