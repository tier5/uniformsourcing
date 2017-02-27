<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT * ".
		 "FROM \"employeeDB\" ".
		 "WHERE \"employeeID\" != '1'");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<center>";
echo "<form action=\"edit2.php\" method=\"post\">";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Employee:</font></td>";
echo "<td><select name=\"employees\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['employeeID']."\">".$data1[$i]['firstname']." ".$data1[$i]['lastname']."</option>";
}
echo "</select></td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">Date:</font></td>";
echo "<td><select name=\"editmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$monthnum=date("m", mktime(0, 0, 0, $i, date("d"), date("Y")));
	$monthnam=date("F", mktime(0, 0, 0, $i, date("d"), date("Y")));
	echo "<option value=\"$monthnum\">$monthnam</option>";
}
echo "</select> / ";
echo "<select name=\"editday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$daynum=date("d", mktime(0, 0, 0, date("m"), $i, date("Y")));
	echo "<option value=\"$daynum\">$daynum</option>";
}
echo "</select> / ";
echo "<select name=\"edityear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i <= 15; $i++){
	$yearnum=date("Y", mktime(0, 0, 0, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$yearnum\">$yearnum</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"Edit Log\"></form></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center>";
require('../../trailer.php');
?>
