<?php 

if (jibres_wis() == 'csv') 
{
	if (ch_jibres_store_data() == true) 
	{
		printf('<h1>Jibres | 
				<form id="fwis" action method="post" style="display: inline;">
				<input type="hidden" name="usas" value="api">
				<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to my store in Jibres</a>
				</form></h1>');
	}
	else
	{
		printf('<h1>Jibres | 
				<form id="fwis" action method="post" style="display: inline;">
				<input type="hidden" name="usas" value="api">
				<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">I want to use jibres api</a>
				</form></h1>');
	}
}
elseif (jibres_wis() == 'api') 
{
	
	printf('<h1>Jibres | 
			<form id="fwis" action method="post" style="display: inline;">
			<input type="hidden" name="usas" value="csv">
			<a id="subm" style="cursor: pointer; font-size: 0.5em; font-weight: 400;">Save backups to csv file</a>
			</form></h1>');
	
}
printf('<script>
			document.getElementById("subm").onclick = function() {
   				document.getElementById("fwis").submit();
			}
		</script>');

?>