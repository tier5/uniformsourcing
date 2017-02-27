<?php
require('Application.php');
require('../../header.php');
extract($_POST);
$id=$_POST['id'];
if($debug == "on"){
	echo "id IS $id<br>";
}
if($id == "1"){
	echo "You can not update this client. Please contact Interactive Ideas if any information needs to be changed.";
	require('../../trailer.php');
	exit;
}
$query1=("SELECT * ".
		 "FROM \"clientDB\" ".
		 "WHERE \"ID\" = '$id'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$accountmanager=$data1[0]['accountmanager'];

$query1=("SELECT * FROM tbl_carriers WHERE status= '1' ");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1 " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data_carrier[]=$row1;
}
$query2=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" = '$accountmanager'");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$query3=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" != '1'");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
echo "<font face=\"arial\">";
echo "<center>";
echo "<p>";
echo "<form action=\"editclient3.php\" method=\"post\">";
echo "<input type=\"hidden\" name=\"ID\" value=\"".$data1[0]['ID']."\">";
echo "<table cellspacing=\"6\">";
echo "<tr>";
echo "<td><b>Client:</b></td>";
echo "<td><input type=\"text\" name=\"client\" size=\"30\" value=\"".$data1[0]['client']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Contact:</b></td>";
echo "<td><input type=\"text\" name=\"contact\" size=\"30\" value=\"".$data1[0]['contact']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Phone:</b></td>";
echo "<td><input type=\"text\" name=\"phone\" size=\"15\" value=\"".$data1[0]['phone']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Fax:</b></td>";
echo "<td><input type=\"text\" name=\"fax\" size=\"15\" value=\"".$data1[0]['fax']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Address:</b></td>";
echo "<td><input type=\"text\" name=\"address\" size=\"30\" value=\"".$data1[0]['address']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Address 2:</b></td>";
echo "<td><input type=\"text\" name=\"address2\" size=\"30\" value=\"".$data1[0]['address2']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>City:</b></td>";
echo "<td><input type=\"text\" name=\"city\" size=\"30\" value=\"".$data1[0]['city']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>State:</b></td>";
echo "<td><input type=\"text\" name=\"state\" size=\"30\" value=\"".$data1[0]['state']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Zip:</b></td>";
echo "<td><input type=\"text\" name=\"zip\" size=\"30\" value=\"".$data1[0]['zip']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Country:</b></td>";
echo "<td><input type=\"text\" name=\"country\" size=\"30\" value=\"".$data1[0]['country']."\"";
echo "</tr>";
echo "<tr>";
echo "<td><b>Email:</b></td>";
echo "<td><input type=\"text\" name=\"email\" size=\"30\" value=\"".$data1[0]['email']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Url:</b></td>";
echo "<td><input type=\"text\" name=\"www\" value=\"".$data1[0]['www']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Client ID:</b></td>";
echo "<td><input type=\"text\" name=\"clientID\" value=\"".$data1[0]['clientID']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Class:</b></td>";
echo "<td><select name=\"class\">";
echo "<option value=\"".$data1[0]['class']."\">\"".$data1[0]['class']."\" is currently selected</option>";
echo "<option value=\"A\">A</option>";
echo "<option value=\"B\">B</option>";
echo "<option value=\"C\">C</option>";
echo "<option value=\"D\">D</option>";
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Account Manager:</b></td>";
echo "<td><select name=\"accountmanager\">";
echo "<option value=\"".$data2[0]['employeeID']."\">".$data2[0]['firstname']." ".$data2[0]['lastname']."</option>";
for($i=0; $i < count($data3); $i++){
	echo "<option value=\"".$data3[$i]['employeeID']."\">".$data3[$i]['firstname']." ".$data3[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Shipper #:</b></td>";
echo "<td><input type=\"text\" name=\"shipperno\" size=\"30\" value=\"".$data1[0]['shipperno']."\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Carrier:</b></td>";
echo "<td><select name=\"carrier\">";
for($i=0; $i < count($data_carrier); $i++){
	if($data_carrier[$i]['carrier_id'] == $data1[0]['carrier'])
		echo "<option value=\"".$data_carrier[$i]['carrier_id']."\" selected=\"selected\">".$data_carrier[$i]['carrier_name']."</option>";
	else 
		echo "<option value=\"".$data_carrier[$i]['carrier_id']."\" >".$data_carrier[$i]['carrier_name']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b>Notes:</b></td>";
echo "<td><textarea name=\"notes\" cols=\"25\" rows=\"4\">".$data1[0]['notes']."</textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><input type=\"submit\" name=\"update\" value=\"Update and Activate\"> <input type=\"submit\" name=\"deactivate\" value=\"Update and DeActivate\"></td>";
echo "</tr>";
echo "</table>";
echo "</center>";
require('../../trailer.php');
?>
