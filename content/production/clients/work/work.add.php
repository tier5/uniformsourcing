<?php
require('Application.php');
require('../../../header.php');
$ID=$_GET['whoop'];
$query1=("SELECT \"ID\", \"clientID\", \"client\", \"active\" ".
		 "FROM \"clientDB\" ".
		 "WHERE \"active\" = 'yes' AND \"ID\" = '$ID' ".
		 "ORDER BY \"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
$query2=("SELECT \"firstname\", \"lastname\", \"employeeID\" ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"firstname\" ASC");
if(!($result2=pg_query($connection,$query2))){
	print("Failed query2: " . pg_last_error($connection));
	exit;
}
while($row2 = pg_fetch_array($result2)){
	$data2[]=$row2;
}
$query3=("SELECT * ".
		 "FROM \"billingcodes\" ".
		 "WHERE \"active\" = 'yes' ".
		 "ORDER BY \"description\" ASC");
if(!($result3=pg_query($connection,$query3))){
	print("Failed query3: " . pg_last_error($connection));
	exit;
}
while($row3 = pg_fetch_array($result3)){
	$data3[]=$row3;
}
echo "<font face=\"arial\">";
echo "<blockquote>";
echo "<font face=\"arial\" size=\"+2\"><b><center>New Workorder</center></b></font>";
echo "<font face=\"arial\"><center>For those of you adding workorders be specific in job notes.</center></font>";
echo "<p>";
echo "<form action=\"work.add1.php\" method=\"post\">";
echo "<table align=\"center\">";
echo "<tr>";
echo "<td>Choose Client:</td>";
echo "<td><select name=\"clientnum\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['ID']."\">".$data1[$i]['client']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Person Assigned To:</td>";
echo "<td><select name=\"employee\">";
for($i=0; $i < count($data2); $i++){
	echo "<option value=\"".$data2[$i]['employeeID']."\">".$data2[$i]['firstname']." ".$data2[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Type of Work to be done:</td>";
echo "<td><select name=\"workid\">";
for($i=0; $i < count($data3); $i++){
	echo "<option value=\"".$data3[$i]['ID']."\">".$data3[$i]['description']." - $".$data3[$i]['price']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Estimated Number of Hours:</td>";
echo "<td><input type=\"text\" name=\"hours\" size=\"3\" maxlength=\"6\"></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Completion Date:</td>";
echo "<td><select name=\"commonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$monthnum=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	$monthnam=date("F", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$monthnum\">$monthnam</option>";
}
echo "</select> / ";
echo "<select name=\"comday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$daynum=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$daynum\">$daynum</option>";
}
echo "</select> / ";
echo "<select name=\"comyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i <= 3; $i++){
	$yearnum=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")+$i));
	echo "<option value=\"$yearnum\">$yearnum</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td>Job Notes:</td>";
echo "<td><textarea wrap=\"physical\" name=\"jobnotes\" rows=\"7\" cols=\"35\"></textarea></td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\"><div align=\"center\"><input type=\"submit\" name=\"submit\" value=\" Enter Workorder \"></div></td>";
echo "</tr>";
echo "</table>";
require('../../../trailer.php');
?>
