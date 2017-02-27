<?php
	require('Application.php');
	require('header.php');
	if($debug == "on"){
		echo "count resultapp1 IS ".count($resultapp1)."<br>";
		echo "count dataapp1 IS ".count($dataapp1)."<br>";
		echo "_SESSION firstname IS ".$_SESSION['firstname']."<br>";
		echo "_SESSION lastname IS ".$_SESSION['lastname']."<br>";
		print_r ($dataapp1);
		print_r (mysql_fetch_array($resultapp1));
	}
	  echo "<center>";
	  echo "<h3><b>Patterns</b></h3>";	
  ?>
				<table width="50%">
				  <tr valign="top">
					<td align="center"><a href="gallery.php?gallery=Aprons"><img src="images/aprons.jpg" alt="aprons" width="165" height="99" border="0" /></a><br>
					</a></td>
					<td align="center"><a href="gallery.php?gallery=Dresses"><img src="images/dresses.jpg" alt="dreses" width="165" height="99" border="0" /></a><br>
					</a></td>
					<td align="center"><a href="gallery.php?gallery=Pants"><img src="images/pants.jpg" alt="pants" width="165" height="99" border="0" /></a></td>
				  </tr>
				</table>
				<table width="50%">
				  <tr valign="top">
					<td align="center"><a href="gallery.php?gallery=Skirts"><img src="images/skirts.jpg" alt="skirts" width="165" height="99" border="0" /></a><br />
					  </a></td>
					<td align="center"><a href="gallery.php?gallery=Tops"><img src="images/tops.jpg" alt="tops" border="0" /></a> </a></td>
					<td align="center"><a href="gallery.php?gallery=Vests"><img src="images/vests.jpg" alt="vests" border="0" /></a> </a></td>
				  </tr>
				</table>             
			  <?php
  
	  echo "</center>";
	  require('trailer.php');
  ?>
