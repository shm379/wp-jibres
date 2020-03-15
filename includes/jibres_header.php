<?php if ( jibres_wis() == 'csv' ) : ?>

	<?php if ( ch_jibres_store_data() == true ) : ?>

		<h1>Jibres | 
		<form id="fwis" action method="post" style="display: inline;">
		<input type="hidden" name="usas" value="api">
		<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to my store in Jibres</a>
		</form></h1>

	<?php else : ?>

		<h1>Jibres | 
		<form id="fwis" action method="post" style="display: inline;">
		<input type="hidden" name="usas" value="api">
		<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">I want to use jibres api</a>
		</form></h1>

	<?php endif; ?>

<?php elseif ( jibres_wis() == 'api' ) : ?>

	<h1>Jibres | 
	<form id="fwis" action method="post" style="display: inline;">
	<input type="hidden" name="usas" value="csv">
	<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to csv file</a>
	</form></h1>

<?php endif; ?>

<script>
	document.getElementById("subm").onclick = function() {
   		document.getElementById("fwis").submit();
	}
</script>