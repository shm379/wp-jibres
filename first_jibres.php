<?php 

printf('<h1>Jibres</h1>');
printf('<p>Welcome to the official Jibres plugin for worpress.</p>');
printf('<p>For backup your data like products, orders, posts, comments and categories into your store in Jibres use this plugin.</p>');
printf('<p>For backup your data like said examples into csv files again use this plugin.</p>');
printf('<p>Create your store in Jibres .Sell and Enjoy :)</p>');
printf('<p>More informations in <a href="https://jibres.com" target="_blank" style="font-weight: bold; text-decoration: none;">Jibres</a>.</p>');
printf('<p>For connect to Jibres api fill out the following information but if you want to backup into csv files without use jibres api only click submit.</p>');
printf('<p>Csv files path: wp-content/plugins/this plugin folder(wp-jibres)/backup</p>');
printf('<form action method="post">
		<label style="font-weight: bold;">Please Insert Your Jibres Informations: </label><br><br>
		<input type="text" name="store" placeholder="store"><br><br>
		<input type="text" name="apikey" placeholder="apikey"><br><br>
		<input type="text" name="appkey" placeholder="appkey"><br><br>
		<p>Where you want to save your backups?</p>
  		<input type="radio" id="csv" name="weris" value="csv" checked>
  		<label for="csv">csv file</label><br>
  		<input type="radio" id="api" name="weris" value="api">
  		<label for="api">your jibres store with api</label><br><br>
		<input type="submit" value="submit" class="bt">
		</form>');

?>