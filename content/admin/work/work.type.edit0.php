<?php
require('Application.php');
$ID=$_GET['ID'];
$query1=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"ID\" = '$ID'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
require('../../header.php');
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>PRICING EDIT</center></b></font>";
echo "<p>";
echo "<form action=\"work.type.edit1.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>ITEM ID:</td>";
echo "<td><input type=\"text\" name=\"itemid\" value=\"".$data1[0]['itemid']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>DESCRIPTION:</td>";
echo "<td><input type=\"text\" name=\"description\" value=\"".$data1[0]['description']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>PRICE per HOUR:</td>";
echo "<td><input type=\"text\" name=\"price\" value=\"".$data1[0]['price']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<input type=\"hidden\" name=\"ID\" value=\"".$data1[0]['ID']."\">";
echo "<td colspan=\"2\"><div align=\"center\"><input type=\"submit\" name=\"submit\" value=\" Edit Pricing Item \"></div></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
require('../../trailer.php');
?>
