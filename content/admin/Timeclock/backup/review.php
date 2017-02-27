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
if($_GET['error'] == "startdate"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Please enter a valid STARTDATE.<br> Month, Day, and Year must all be valid";
	echo "</font>";
}
if($_GET['error'] == "enddate"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Please enter a valid ENDDATE.<br> Month, Day, and Year must all be valid";
	echo "</font>";
}
if($_GET['error'] == "invalid"){
	echo "<font face=\"arial\" color=\"red\" size=\"+1\">";
	echo "Your STARTDATE is after your ENDDATE.<br> Please make sure your ENDDATE is after your STARTDATE";
	echo "</font>";
}
echo "<form action=\"timeline.php\" method=\"post\">";
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
echo "<td><font face=\"arial\" size=\"-1\">Start Date:</font></td>";
echo "<td><select name=\"startmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$monthnum=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	$monthname=date("F", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$monthnum\">$monthname</option>";
}
echo "</select> / ";
echo "<select name=\"startday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$datenum=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$datenum\">$datenum</option>";
}
echo "</select> / ";
echo "<select name=\"startyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i <= 5; $i++){
	$yearnum=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$yearnum\">$yearnum</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\" size=\"-1\">End Date:</font></td>";
echo "<td><select name=\"endmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=1; $i <= 12; $i++){
	$monthnum1=date("m", mktime(1, 1, 1, $i, date("d"), date("Y")));
	$monthname1=date("F", mktime(1, 1, 1, $i, date("d"), date("Y")));
	echo "<option value=\"$monthnum1\">$monthname1</option>";
}
echo "</select> / ";
echo "<select name=\"endday\">";
echo "<option value=\"day\">Day</option>";
for($i=1; $i <= 31; $i++){
	$datenum1=date("d", mktime(1, 1, 1, date("m"), $i, date("Y")));
	echo "<option value=\"$datenum1\">$datenum1</option>";
}
echo "</select> / ";
echo "<select name=\"endyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i <= 5; $i++){
	$yearnum1=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$yearnum1\">$yearnum1</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><input type=\"Submit\" value=\"View Log (Payroll)\"><p><input type=\"submit\" name=\"forward\" value=\"View Log\"></td>";
echo "<td></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center>";
require('../../trailer.php');
?>
