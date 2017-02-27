<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" != '1' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1 " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query1=("SELECT * FROM tbl_carriers WHERE status= '1' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1 " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_carrier[]=$row1;
}
echo "<font face=\"arial\">";
echo "<center>";
echo "<p>";
echo "<form action=\"addclient2.php\" method=\"post\">";
echo "<table cellspacing=\"6\">";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Client:</b></td>";
echo "<td><input type=\"text\" name=\"client\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Contact:</b></td>";
echo "<td><input type=\"text\" name=\"contact\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Phone:</b></td>";
echo "<td><input type=\"text\" name=\"phone\" size=\"15\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Fax:</b></td>";
echo "<td><input type=\"text\" name=\"fax\" size=\"15\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Address:</b></td>";
echo "<td><input type=\"text\" name=\"address\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Address 2:</b></td>";
echo "<td><input type=\"text\" name=\"address2\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>City:</b></td>";
echo "<td><input type=\"text\" name=\"city\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>State:</b></td>";
echo "<td><input type=\"text\" name=\"state\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Zip:</b></td>";
echo "<td><input type=\"text\" name=\"zip\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Country:</b></td>";
echo "<td><input type=\"text\" name=\"country\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Email:</b></td>";
echo "<td><input type=\"text\" name=\"email\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Url:</b></td>";
echo "<td><input type=\"text\" name=\"www\" value=\"http://\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" color=\"red\">*(r)</font><b>Client ID:</b></td>";
echo "<td><input type=\"text\" name=\"clientID\" size=\"30\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Class:</b></td>";
echo "<td><select name=\"class\">";
echo "<option value=\"A\">A</option>";
echo "<option value=\"B\">B</option>";
echo "<option value=\"C\">C</option>";
echo "<option value=\"D\">D</option>";
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Account Manager:</b></td>";
echo "<td><select name=\"accountmanager\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['employeeID']."\">".$data1[$i]['firstname']." ".$data1[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Shipper #:</b></td>";
echo "<td><input type=\"text\" name=\"shipperno\" size=\"30\"></td>";
echo "</tr>";

echo "<tr>";
echo "<td><b>Carrier:</b></td>";
echo "<td><select name=\"carrier\">";
for($i=0; $i < count($data_carrier); $i++){
	echo "<option value=\"".$data_carrier[$i]['carrier_id']."\">".$data_carrier[$i]['carrier_name']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Notes:</b></td>";
echo "<td><textarea name=\"notes\" cols=\"25\" rows=\"4\"></textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><div align=\"center\"><input type=\"submit\" name=\"Submit\" value=\"Submit\"></div></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center>";
echo "<p>";
echo "<center>";
echo "<spacer type=\"vertical\" size=\"30\">";
echo "<hr>";
echo "</font>";
require('../../trailer.php');
?>
