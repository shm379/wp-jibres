<?php $header_jw = jibres_wis(); ?>

<?php if ( $header_jw == 'csv' ) : ?>

	<?php if ( ch_jibres_store_data() == true ) : ?>

		<h1>Jibres | 
		<form id="fwis" action method="post" style="display: inline;">
		<input type="hidden" name="usas" value="api">
		<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to my jibres store</a>
		</form></h1><br>

	<?php else : ?>

		<h1>Jibres | 
		<form id="fwis" action method="post" style="display: inline;">
		<input type="hidden" name="usas" value="api">
		<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">I want to use jibres api</a>
		</form></h1><br>

	<?php endif; ?>

<?php elseif ( $header_jw == 'api' ) : ?>

	<h1>Jibres | 
	<form id="fwis" action method="post" style="display: inline;">
	<input type="hidden" name="usas" value="csv">
	<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to csv file</a>
	</form></h1><br>

<?php endif; ?>

<script>
	document.getElementById("subm").onclick = function() {
   		document.getElementById("fwis").submit();
	}
</script>