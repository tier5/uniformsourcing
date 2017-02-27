<?php
require('Application.php');
require('../../header.php');
$query1=("SELECT DISTINCT \"clientDB\".\"client\" AS \"name\", \"clientDB\".\"clientID\", \"clientDB\".\"ID\", \"invoices\".\"client\" ".
		 "FROM \"clientDB\", \"invoices\" ".
		 "WHERE \"invoices\".\"client\" = \"clientDB\".\"ID\" ".
		 "ORDER BY \"clientDB\".\"client\" ASC");
if(!($result1=pg_query($connection,$query1))){
	print("Failed query1: " . pg_last_error($connection));
	exit;
}
while($row1 = pg_fetch_array($result1)){
	$data1[]=$row1;
}
echo "<center>";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"verdana\" size=\"+1\"><b>Choose Client</b></font></td>";
echo "</tr>";
echo "<tr>";
echo "<td>";
echo "<form action=\"archived.workorderlist.php\" method=\"post\">";
echo "<select name=\"client\">";
for($i=0; $i < count($data1); $i++){
	echo "<option value=\"".$data1[$i]['client']."\">".$data1[$i]['name']."</option>";
}
echo "</select>";
echo "<br>";
echo "<input type=\"submit\" value=\"Next >\">";
echo "</form>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "<p>";
echo "<font face=\"verdana\" size=\"+1\"><b>Choose Date Range</b></font>";
echo "<form action=\"archived.range.php\" method=\"post\">";
echo "<table>";
echo "<tr>";
echo "<td><font face=\"arial\">Start Date</font></td>";
echo "<td><select name=\"startmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=0; $i < 12; $i++){
	$asdf=mktime(1, 1, 1, date("m")-$i, 1, 2005);
	$month1=date("m", $asdf);
	$month2=date("F", $asdf);
	echo "<option value=\"$month1\">$month2</option>";
}
echo "</select> / ";
echo "<select name=\"startday\">";
echo "<option value=\"day\">Day</option>";
for($i=0; $i < 31; $i++){
	$day1=date("d", mktime(1, 1, 1, 10, date("d")+$i, date("Y")));
	echo "<option value=\"$day1\">$day1</option>";
}
echo "</select> / ";
echo "<select name=\"startyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i < 10; $i++){
	$year1=date("Y", mktime(1, 1, 1, date("m"), date("d"), date("Y")-$i));
	echo "<option value=\"$year1\">$year1</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td><font face=\"arial\">End Date</font></td>";
echo "<td><select name=\"endmonth\">";
echo "<option value=\"month\">Month</option>";
for($i=0; $i < 12; $i++){
	$asdf1=mktime(1, 1, 1, date("m")-$i, 1, 2005);
	$emonth1=date("F", $asdf1);
	$emonth2=date("m", $asdf1);
	echo "<option value=\"$emonth2\">$emonth1</option>";
}
echo "</select> / ";
echo "<select name=\"endday\">";
echo "<option value=\"day\">Day</option>";
for($i=0;$i < 31; $i++){
	$eday1=date("d", mktime(1, 1, 1, 12, date("d")+$i, 2005));
	echo "<option value=\"$eday1\">$eday1</option>";
}
echo "</select> / ";
echo "<select name=\"endyear\">";
echo "<option value=\"year\">Year</option>";
for($i=0; $i < 10; $i++){
	$eyear1=date("Y", mktime(1, 1, 1, 1, 1, date("Y")-$i));
	echo "<option value=\"$eyear1\">$eyear1</option>";
}
echo "</select>";
echo "</td>";
echo "</tr>";
echo "<tr>";
echo "<td colspan=\"2\" align=\"center\"><input type=\"submit\" value=\"NEXT >>\"></td>";
echo "</tr>";
echo "</table>";
echo "</form>";
echo "</center>";
require('../../trailer.php');
?>
