<?php
require('Application.php');
require('../../header.php');
$workmonth=date("m");
$workday=date("d");
$workyear=date("Y");
$workhour=date("H");
$workmin=date("i");
$worksec=0;
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
echo "<form action=\"addrecord2.php\" method=\"post\">";
echo "<table>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\">Employee:</font></b></td>";
echo "<td><font face=\"arial\" size=\"-1\">";
echo "<select name=\"employee\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['employeeID']."\">".$data1[$i]['firstname']." ".$data1[$i]['lastname']."</option>";
}
echo "</select></font></td>";
echo "</tr>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\">Date:</font></b></td>";
echo "<td><select name=\"workmonth\">";
echo "<option value=\"$workmonth\">$workmonth</option>";
for($i=1; $i <= 12; $i++){
	$workmonth1=date("m", mktime(date("H"), date("i"), date("s"), $i, date("d"), date("Y")));
	echo "<option value=\"$workmonth1\">$workmonth1</option>";
}
echo "</select> / ";
echo "<select name=\"workday\">";
echo "<option value=\"$workday\">$workday</option>";
for($i=1; $i <= 31; $i++){
	$workday1=date("d", mktime(date("H"), date("i"), date("s"), date("m"), $i, date("Y")));
	echo "<option value=\"$workday1\">$workday1</option>";
}
echo "</select> / ";
echo "<select name=\"workyear\">";
echo "<option value=\"$workyear\">$workyear</option>";
for($i=0; $i <= 5; $i++){
	$workyear1=date("Y", mktime(date("H"), date("i"), date("s"), date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$workyear1\">$workyear1</option>";
}
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\">Clock In:</font></b></td>";
echo "<td>";
echo "<select name=\"clockinhour\">";
echo "<option value=\"$workhour\">$workhour</option>";
for($i=0; $i<=23; $i++){
	$workhour1=date("H", mktime($i, date("i"), date("s"), date("m"), date("d"), date("Y")));
	echo "<option value=\"$workhour1\">$workhour1</option>";
}
echo "</select> : ";
echo "<select name=\"clockinmin\">";
echo "<option value=\"$workmin\">$workmin</option>";
for($i=0; $i<=59; $i++){
	$workmin1=date("i", mktime(date("H"), $i, date("s"), date("m"), date("d"), date("Y")));
	echo "<option value=\"$workmin1\">$workmin1</option>";
}
echo "</select>";
echo "<input type=\"hidden\" name=\"clockinsec\" value=\"00\">";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><b><font face=\"arial\" size=\"-1\">Clock Out:</font></b></td>";
echo "<td>";
echo "<select name=\"clockouthour\">";
echo "<option value=\"$workhour\">$workhour</option>";
for($i=0; $i<=23; $i++){
	$workhour1=date("H", mktime($i, date("i"), date("s"), date("m"), date("d"), date("Y")));
	echo "<option value=\"$workhour1\">$workhour1</option>";
}
echo "</select> : ";
echo "<select name=\"clockoutmin\">";
echo "<option value=\"$workmin\">$workmin</option>";
for($i=0; $i<=59; $i++){
	$workmin1=date("i", mktime(date("H"), $i, date("s"), date("m"), date("d"), date("Y")));
	echo "<option value=\"$workmin1\">$workmin1</option>";
}
echo "</select>";
echo "<input type=\"hidden\" name=\"clockoutsec\" value=\"00\">";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "<input type=\"submit\" name=\"submit\" value=\"Add Record\">";
echo "</form>";
echo "</center>";
require('../../trailer.php');
?>
