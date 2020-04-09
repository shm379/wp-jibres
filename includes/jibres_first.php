<h1>Jibres</h1>
<p>Welcome to the official Jibres plugin for worpress.</p>
<p>For backup your data like products, orders, posts, comments and categories into your store in Jibres use this plugin.</p>
<p>For backup your data like said examples into csv files again use this plugin.</p>
<p>Create your store in Jibres .Sell and Enjoy :)</p>
<p>More informations in <a href="https://jibres.com" target="_blank" style="font-weight: bold; text-decoration: none;">Jibres</a>.</p>
<p>For connect to Jibres api fill out the following information but if you want to backup into csv files without use jibres api only click submit.</p>
<p>Csv files path: wp-content/plugins/this plugin folder(wp-jibres)/backup</p>
<form action method="post">
	<p style="font-weight: bold;">Where you want to save your backups?</p>
	<input type="radio" id="csv" name="weris" value="csv" onclick="jibres_wapi()" checked>
	<label for="csv">csv file</label><br>
	<input type="radio" id="api" name="weris" value="api" onclick="jibres_wapi()">
	<label for="api">your jibres store with api</label><br><br>
	<div id="j_box" style="display: none;">
		<label style="font-weight: bold;">Please Insert Your Jibres Informations: </label><br><br>
		<input type="text" name="store" placeholder="store" autocomplete="off"><br><br>
		<input type="text" name="appkey" placeholder="appkey" autocomplete="off"><br><br>
		<input type="tel" name="phone" placeholder="Mobile like: 989121234657" autocomplete="off"><br><br>
	</div>
	<input type="submit" value="submit" class="button" style="vertical-align: unset;">
</form>
<script>
function jibres_wapi() 
{
	var j_radio = document.getElementById("api");
	var j_box = document.getElementById("j_box");
	if (j_radio.checked == true)
	{
		j_box.style.display = "block";
	} 
	else 
	{
		 j_box.style.display = "none";
	}
}
</script>
